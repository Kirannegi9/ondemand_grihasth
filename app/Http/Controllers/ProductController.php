<?php

namespace App\Http\Controllers;

use App\Models\VendorUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\Storage;
use Google\Client as Google_Client;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (!isset($_COOKIE['section_id']) && !isset($_COOKIE['address_name'])) {
            \Redirect::to('set-location')->send();
        }
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function productList($type, $id)
    {
        
        return view('products.list', ['type' => $type, 'id' => $id]);
    }

    public function productListAll()
    {
        return view('products.list_arrivals');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function productDetail($id)
    {
        $cart = session()->get('cart', []);
        return view('products.detail', ['id' => $id, 'cart' => $cart]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function cart()
    {
        return view('checkout');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function addToCart(Request $request)
    {
        $req = $request->all();
        $id = $req['id'];
        $vendor_id = $req['vendor_id'];
        $cart = Session::get('cart', []);
        if (@$cart['item'] && isset($cart['item'][$vendor_id])) {
        } else {
            $cart['item'] = array();
            Session::put('cart', $cart);
            Session::save();
        }
        // Get address and pickup location coordinates from cookies/request
        // Delivery charge should be based on pickup location to delivery location, NOT vendor location
        $cart['vendor_latitude'] = $req['vendor_latitude'];
        $cart['vendor_longitude'] = $req['vendor_longitude'];
        $cart['distanceType'] = $req['distanceType'];
        $deliveryChargemain = @$_COOKIE['deliveryChargemain'];
        $address_lat = @$_COOKIE['address_lat'];  // Delivery address (destination)
        $address_lng = @$_COOKIE['address_lng'];   // Delivery address (destination)
        
        // Get pickup location (warehouse/central pickup point) - check multiple possible sources
        // IMPORTANT: Pickup location should be FIXED and NOT vendor-specific to ensure same delivery charge for all vendors
        $pickup_latitude = null;
        $pickup_longitude = null;
        
        // Priority 1: Check if pickup location is in request (highest priority)
        if (isset($req['pickup_latitude']) && isset($req['pickup_longitude']) && 
            $req['pickup_latitude'] != '' && $req['pickup_longitude'] != '') {
            $pickup_latitude = floatval($req['pickup_latitude']);
            $pickup_longitude = floatval($req['pickup_longitude']);
        }
        // Priority 2: Check if pickup location is in cookies (section-wide pickup location)
        else if (isset($_COOKIE['pickup_latitude']) && isset($_COOKIE['pickup_longitude']) && 
                 $_COOKIE['pickup_latitude'] != '' && $_COOKIE['pickup_longitude'] != '') {
            $pickup_latitude = floatval($_COOKIE['pickup_latitude']);
            $pickup_longitude = floatval($_COOKIE['pickup_longitude']);
        }
        // Priority 3: Check if section has pickup location stored
        else if (isset($_COOKIE['section_pickup_lat']) && isset($_COOKIE['section_pickup_lng']) && 
                 $_COOKIE['section_pickup_lat'] != '' && $_COOKIE['section_pickup_lng'] != '') {
            $pickup_latitude = floatval($_COOKIE['section_pickup_lat']);
            $pickup_longitude = floatval($_COOKIE['section_pickup_lng']);
        }
        // Priority 4: Check if there's a stored pickup location in cart from previous calculation
        else if (isset($cart['pickup_latitude']) && isset($cart['pickup_longitude']) && 
                 $cart['pickup_latitude'] != '' && $cart['pickup_longitude'] != '') {
            // Reuse the pickup location from cart (ensures consistency across vendors)
            $pickup_latitude = floatval($cart['pickup_latitude']);
            $pickup_longitude = floatval($cart['pickup_longitude']);
        }
        // NO FALLBACK TO VENDOR LOCATION - This ensures delivery charge is same for all vendors
        // If pickup location is not configured, delivery charge cannot be calculated properly
        
        // Delivery charge calculation block (calculate ONCE by cart/destination based on distance from pickup to delivery)
        // CRITICAL: Delivery charge should NOT change when vendor changes - only when pickup or delivery address changes
        $shouldRecalculateDelivery = false;
        if (!isset($cart['deliverychargemain']) || $cart['deliverychargemain'] == '') {
            $shouldRecalculateDelivery = true;
        } else {
            // Check if pickup location or delivery address changed
            $currentPickupLat = isset($cart['pickup_latitude']) ? $cart['pickup_latitude'] : '';
            $currentPickupLng = isset($cart['pickup_longitude']) ? $cart['pickup_longitude'] : '';
            $currentAddressLat = isset($cart['address_lat']) ? $cart['address_lat'] : '';
            $currentAddressLng = isset($cart['address_lng']) ? $cart['address_lng'] : '';
            
            // Only recalculate if pickup location or delivery address actually changed
            // NOTE: Vendor change should NOT trigger recalculation
            if (($currentPickupLat != '' && $currentPickupLat != $pickup_latitude) || 
                ($currentPickupLng != '' && $currentPickupLng != $pickup_longitude) || 
                ($currentAddressLat != '' && $currentAddressLat != $address_lat) || 
                ($currentAddressLng != '' && $currentAddressLng != $address_lng)) {
                $shouldRecalculateDelivery = true;
            }
        }
        
        if ($shouldRecalculateDelivery) {
            if (isset($_COOKIE['service_type']) && $_COOKIE['service_type'] == "Ecommerce Service" && isset($_COOKIE['ecommerce_delivery_charge'])) {
                $cart['deliverychargemain'] = @$_COOKIE['ecommerce_delivery_charge'];
                $cart['deliverykm'] = '';
            } else if (@$deliveryChargemain && @$address_lat && @$address_lng && @$pickup_latitude && @$pickup_longitude) {
                $deliveryChargemainObj = json_decode($deliveryChargemain);
                if (!empty($deliveryChargemainObj)) {
                    $distanceType = !empty($req['distanceType']) ? $req['distanceType'] : 'km';
                    $delivery_charges_per_km = $deliveryChargemainObj->delivery_charges_per_km;
                    $minimum_delivery_charges = $deliveryChargemainObj->minimum_delivery_charges;
                    $minimum_delivery_charges_within_km = $deliveryChargemainObj->minimum_delivery_charges_within_km;
                    // CRITICAL: Calculate distance from PICKUP location to DELIVERY address (NOT vendor location)
                    // This ensures same delivery charge for all vendors delivering to the same address
                    $kmradius = $this->distance($pickup_latitude, $pickup_longitude, $address_lat, $address_lng, $distanceType);
                    if ($minimum_delivery_charges_within_km > $kmradius) {
                        $cart['deliverychargemain'] = $minimum_delivery_charges;
                    } else {
                        $cart['deliverychargemain'] = round(($kmradius * $delivery_charges_per_km), 2);
                    }
                    $cart['deliverykm'] = $kmradius;
                }
            } else {
                // If pickup location is not available, set delivery charge to 0 or minimum
                // This prevents incorrect calculation based on vendor location
                if (isset($deliveryChargemain)) {
                    $deliveryChargemainObj = json_decode($deliveryChargemain);
                    if (!empty($deliveryChargemainObj)) {
                        $cart['deliverychargemain'] = $deliveryChargemainObj->minimum_delivery_charges ?? 0;
                    } else {
                        $cart['deliverychargemain'] = 0;
                    }
                } else {
                    $cart['deliverychargemain'] = 0;
                }
                $cart['deliverykm'] = 0;
            }
            // Store reference coordinates to prevent unnecessary recalculation for next item
            // This ensures delivery charge stays the same when adding items from different vendors
            if ($pickup_latitude && $pickup_longitude) {
                $cart['pickup_latitude'] = $pickup_latitude;
                $cart['pickup_longitude'] = $pickup_longitude;
            }
            $cart['address_lat'] = $address_lat;
            $cart['address_lng'] = $address_lng;
        } else {
            // If not recalculating, ensure pickup location is preserved in cart
            // This prevents it from being lost when adding items from different vendors
            if (!isset($cart['pickup_latitude']) && $pickup_latitude && $pickup_longitude) {
                $cart['pickup_latitude'] = $pickup_latitude;
                $cart['pickup_longitude'] = $pickup_longitude;
            }
        }
        
        // Set delivery option and charge (same for all items in cart)
        if (Session::get('takeawayOption') == "true") {
            $req['delivery_option'] = "takeaway";
        } else {
            $req['delivery_option'] = "delivery";
        }
        if (@$req['delivery_option'] == "delivery") {
            $cart['deliverycharge'] = isset($cart['deliverychargemain']) ? $cart['deliverychargemain'] : 0;
        } else {
            $cart['deliverycharge'] = 0;
            $cart['tip_amount'] = 0;
        }
        $cart['delivery_option'] = $req['delivery_option'];
        $cart['tip_amount'] = 0;    
        /*by thm*/
        // Create unique cart item ID including variant ID to ensure different variants are stored separately
        $original_id = $id;
        $variant_id_for_cart = null;
        
        // Extract variant_id from multiple possible sources
        if (isset($req['variant_id']) && !empty($req['variant_id'])) {
            $variant_id_for_cart = $req['variant_id'];
        } else if (isset($req['variant_info'])) {
            // Handle variant_info - might be array, JSON string, or object
            $variant_info_check = $req['variant_info'];
            if (is_string($variant_info_check)) {
                $decoded = @json_decode($variant_info_check, true);
                if (is_array($decoded) && isset($decoded['variant_id'])) {
                    $variant_id_for_cart = $decoded['variant_id'];
                } else {
                    $decoded_base64 = @json_decode(@base64_decode($variant_info_check), true);
                    if (is_array($decoded_base64) && isset($decoded_base64['variant_id'])) {
                        $variant_id_for_cart = $decoded_base64['variant_id'];
                    }
                }
            } else if (is_array($variant_info_check) && isset($variant_info_check['variant_id'])) {
                $variant_id_for_cart = $variant_info_check['variant_id'];
            } else if (is_object($variant_info_check) && isset($variant_info_check->variant_id)) {
                $variant_id_for_cart = $variant_info_check->variant_id;
            }
        }
        
        // Create unique cart item ID with variant ID
        if ($variant_id_for_cart) {
            $id = $id . 'PV' . $variant_id_for_cart;
        }
        // Normalize numeric inputs and compute unit prices
        $quantity = isset($req['quantity']) ? floatval($req['quantity']) : 1;
        // Variant price logic - ALWAYS use variant-specific price if variant exists
        $unit_item_price = 0;
        $unit_dis_price = 0;
        $unit_extra_price = isset($req['extra_price']) ? floatval($req['extra_price']) : 0;

        // CRITICAL: Always prioritize variant price if variant_info exists - this ensures each variant gets its own price
        // Handle variant_info - it might be an array, JSON string, or object
        $variant_info_array = null;
        if (isset($req['variant_info'])) {
            if (is_string($req['variant_info'])) {
                // Try to decode if it's a JSON string
                $decoded = json_decode($req['variant_info'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $variant_info_array = $decoded;
                } else {
                    // If not JSON, try to use as-is (might be base64 encoded)
                    $decoded_base64 = @json_decode(base64_decode($req['variant_info']), true);
                    if (is_array($decoded_base64)) {
                        $variant_info_array = $decoded_base64;
                    }
                }
            } else if (is_array($req['variant_info'])) {
                $variant_info_array = $req['variant_info'];
            } else if (is_object($req['variant_info'])) {
                $variant_info_array = (array)$req['variant_info'];
            }
        }
        
        // Extract variant price from variant_info - check multiple possible field names
        // This is CRITICAL to ensure each variant gets its correct price
        if ($variant_info_array && !empty($variant_info_array)) {
            // Priority 1: variant_price (most common)
            if (isset($variant_info_array['variant_price']) && 
                $variant_info_array['variant_price'] !== '' && 
                $variant_info_array['variant_price'] !== null && 
                $variant_info_array['variant_price'] !== '0' &&
                is_numeric($variant_info_array['variant_price'])) {
                $unit_item_price = floatval($variant_info_array['variant_price']);
            }
            // Priority 2: price field in variant_info
            else if (isset($variant_info_array['price']) && 
                     $variant_info_array['price'] !== '' && 
                     $variant_info_array['price'] !== null && 
                     $variant_info_array['price'] !== '0' &&
                     is_numeric($variant_info_array['price'])) {
                $unit_item_price = floatval($variant_info_array['price']);
            }
            // Priority 3: Check variantPrice (camelCase)
            else if (isset($variant_info_array['variantPrice']) && 
                     $variant_info_array['variantPrice'] !== '' && 
                     $variant_info_array['variantPrice'] !== null && 
                     $variant_info_array['variantPrice'] !== '0' &&
                     is_numeric($variant_info_array['variantPrice'])) {
                $unit_item_price = floatval($variant_info_array['variantPrice']);
            }
            // Priority 4: Check if price is in variant_options or other nested structure
            else if (isset($variant_info_array['variant_options']) && is_array($variant_info_array['variant_options'])) {
                // Sometimes price might be in variant_options
                foreach ($variant_info_array['variant_options'] as $key => $value) {
                    if ((strtolower($key) === 'price' || strtolower($key) === 'variant_price') && is_numeric($value)) {
                        $unit_item_price = floatval($value);
                        break;
                    }
                }
            }
            // Priority 5: Check all keys in variant_info for price-like fields
            if ($unit_item_price <= 0) {
                foreach ($variant_info_array as $key => $value) {
                    // Check if key contains 'price' and value is numeric
                    if (stripos($key, 'price') !== false && is_numeric($value) && floatval($value) > 0) {
                        $unit_item_price = floatval($value);
                        break;
                    }
                }
            }
            
            // Extract variant discount price - check multiple possible field names
            if (isset($variant_info_array['variant_dis_price']) && 
                $variant_info_array['variant_dis_price'] !== '' && 
                $variant_info_array['variant_dis_price'] !== '0' && 
                $variant_info_array['variant_dis_price'] !== null) {
                $unit_dis_price = floatval($variant_info_array['variant_dis_price']);
            }
            // Check for discount price field
            else if (isset($variant_info_array['dis_price']) && 
                     $variant_info_array['dis_price'] !== '' && 
                     $variant_info_array['dis_price'] !== '0' && 
                     $variant_info_array['dis_price'] !== null) {
                $unit_dis_price = floatval($variant_info_array['dis_price']);
            }
            // Check for discount_price
            else if (isset($variant_info_array['discount_price']) && 
                     $variant_info_array['discount_price'] !== '' && 
                     $variant_info_array['discount_price'] !== '0' && 
                     $variant_info_array['discount_price'] !== null) {
                $unit_dis_price = floatval($variant_info_array['discount_price']);
            }
        }
        
        // Fallback: Check direct request fields for variant price (sometimes sent separately)
        // Check for variant_price or variantPrice in request (multiple possible field names)
        if ($unit_item_price <= 0) {
            // Check all possible variant price field names
            $variant_price_fields = ['variant_price', 'variantPrice', 'vprice', 'v_price', 'variantPrice', 'variant_price_value'];
            foreach ($variant_price_fields as $field) {
                if (isset($req[$field]) && $req[$field] > 0 && is_numeric($req[$field])) {
                    $unit_item_price = floatval($req[$field]);
                    break;
                }
            }
        }
        
        // IMPORTANT: If we have variant_id but no variant price yet, check direct request fields
        // This handles cases where variant price is sent separately from variant_info
        // CRITICAL: For variants, we MUST get a variant-specific price, not the base product price
        if ($unit_item_price <= 0 && $variant_id_for_cart) {
            // Check for variant-specific price fields in request
            // Priority 1: item_price (if sent with variant_id, it should be variant-specific)
            if (isset($req['item_price']) && $req['item_price'] > 0 && is_numeric($req['item_price'])) {
                $unit_item_price = floatval($req['item_price']);
            } 
            // Priority 2: price field (might be total price, need to divide by quantity)
            else if (isset($req['price']) && $req['price'] > 0 && is_numeric($req['price'])) {
                $sent_price = floatval($req['price']);
                $calculated_unit_price = $quantity > 0 ? ($sent_price / $quantity) : $sent_price;
                $unit_item_price = $calculated_unit_price;
            }
            // Priority 3: Check if price is in a variant-specific field like variant_{id}_price
            else {
                $variant_specific_price_field = 'variant_' . $variant_id_for_cart . '_price';
                if (isset($req[$variant_specific_price_field]) && $req[$variant_specific_price_field] > 0 && is_numeric($req[$variant_specific_price_field])) {
                    $unit_item_price = floatval($req[$variant_specific_price_field]);
                }
            }
            
            // If still no price found, check if maybe the price is in the product's variant list
            // This would require database lookup, but for now, we'll log and use fallback
            if ($unit_item_price <= 0) {
                // Check if there's a variants array in the request with prices
                if (isset($req['variants']) && is_array($req['variants'])) {
                    foreach ($req['variants'] as $variant) {
                        if (isset($variant['variant_id']) && $variant['variant_id'] == $variant_id_for_cart) {
                            if (isset($variant['variant_price']) && $variant['variant_price'] > 0) {
                                $unit_item_price = floatval($variant['variant_price']);
                                break;
                            } else if (isset($variant['price']) && $variant['price'] > 0) {
                                $unit_item_price = floatval($variant['price']);
                                break;
                            }
                        }
                    }
                }
            }
        }
        
        // Fallback: use price from request ONLY if no variant_id exists (non-variant products)
        // CRITICAL: Never use base product price for variants - each variant MUST have its own price
        if ($unit_item_price <= 0 && !$variant_id_for_cart) {
            if (isset($req['item_price']) && $req['item_price'] > 0 && is_numeric($req['item_price'])) {
                $unit_item_price = floatval($req['item_price']);
            } else if (isset($req['price']) && $req['price'] > 0 && is_numeric($req['price'])) {
                // If price is total, divide by quantity to get unit price
                $sent_price = floatval($req['price']);
                $unit_item_price = $quantity > 0 ? ($sent_price / $quantity) : $sent_price;
            }
        }
        
        // Final check: If we have variant_id but still no price, this is a critical error
        // We should NOT proceed with a base product price for variants
        if ($unit_item_price <= 0 && $variant_id_for_cart) {
            error_log("CRITICAL: Variant ID exists ($variant_id_for_cart) but NO variant price could be extracted. Product: $original_id");
            // Don't set a fallback price - let it be 0 so the error is visible
        }
        
        // Fallback for discount price - check direct request fields first
        if ($unit_dis_price <= 0) {
            if (isset($req['variant_dis_price']) && $req['variant_dis_price'] !== '' && $req['variant_dis_price'] !== '0') {
                $unit_dis_price = floatval($req['variant_dis_price']);
            } else if (isset($req['variantDisPrice']) && $req['variantDisPrice'] !== '' && $req['variantDisPrice'] !== '0') {
                $unit_dis_price = floatval($req['variantDisPrice']);
            }
        }
        
        // Fallback for discount price if not set from variant
        if ($unit_dis_price <= 0 && isset($req['dis_price']) && $req['dis_price'] !== '' && $req['dis_price'] !== '0') {
            $sent_dis_price = floatval($req['dis_price']);
            $unit_dis_price = $quantity > 0 ? ($sent_dis_price / $quantity) : $sent_dis_price;
        }
        
        // Ensure we have a valid price - if still 0, log or use a default (should not happen)
        if ($unit_item_price <= 0) {
            // This should not happen - variant price should always be provided
            // But to prevent cart errors, we'll use a minimal fallback
            $variant_id_debug = $variant_id_for_cart ? $variant_id_for_cart : 'none';
            $debug_info = [
                'product_id' => $original_id,
                'variant_id' => $variant_id_debug,
                'has_variant_info' => isset($req['variant_info']),
                'variant_info_type' => isset($req['variant_info']) ? gettype($req['variant_info']) : 'not_set',
                'variant_info_array_keys' => $variant_info_array ? array_keys($variant_info_array) : 'not_set',
                'variant_price_in_req' => isset($req['variant_price']) ? $req['variant_price'] : 'not_set',
                'item_price_in_req' => isset($req['item_price']) ? $req['item_price'] : 'not_set',
                'price_in_req' => isset($req['price']) ? $req['price'] : 'not_set',
                'quantity' => $quantity,
            ];
            // Only log first few characters of variant_info to avoid huge logs
            if (isset($req['variant_info']) && is_string($req['variant_info'])) {
                $debug_info['variant_info_preview'] = substr($req['variant_info'], 0, 200);
            } else if (isset($req['variant_info']) && is_array($req['variant_info'])) {
                $debug_info['variant_info_preview'] = json_encode($req['variant_info']);
            }
            error_log("Warning: No valid variant price found. Debug: " . json_encode($debug_info));
        } else if ($variant_id_for_cart) {
            // Log successful variant price extraction for debugging (only in development)
            // This helps verify that variant prices are being extracted correctly
            if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] == 'true') {
                error_log("Variant price extracted successfully - Product: $original_id, Variant: $variant_id_for_cart, Price: $unit_item_price");
            }
        }

        // Prepare variant_info with correct prices for storage
        // Use the processed variant_info_array if available, otherwise use original
        $variant_info_storage = $variant_info_array ? $variant_info_array : (isset($req['variant_info']) ? $req['variant_info'] : []);
        
        // Ensure variant_info includes the correct prices we calculated
        if (is_array($variant_info_storage)) {
            // Always update with the calculated prices to ensure consistency
            $variant_info_storage['variant_price'] = $unit_item_price;
            if ($unit_dis_price > 0) {
                $variant_info_storage['variant_dis_price'] = $unit_dis_price;
            }
            // Preserve variant_id - use the one we extracted earlier
            if ($variant_id_for_cart) {
                $variant_info_storage['variant_id'] = $variant_id_for_cart;
            } else if (!isset($variant_info_storage['variant_id']) && isset($req['variant_id'])) {
                $variant_info_storage['variant_id'] = $req['variant_id'];
            }
            // Preserve variant_options if they exist (check both original req and processed array)
            if (isset($req['variant_info']['variant_options'])) {
                $variant_info_storage['variant_options'] = $req['variant_info']['variant_options'];
            } else if (isset($variant_info_array['variant_options'])) {
                $variant_info_storage['variant_options'] = $variant_info_array['variant_options'];
            }
        } else {
            // If variant_info is not an array, create a new structure
            $variant_info_storage = [
                'variant_price' => $unit_item_price,
                'variant_id' => $variant_id_for_cart ? $variant_id_for_cart : (isset($req['variant_id']) ? $req['variant_id'] : ''),
            ];
            if ($unit_dis_price > 0) {
                $variant_info_storage['variant_dis_price'] = $unit_dis_price;
            }
        }
        
        // CRITICAL: Validate that variant has unique price before saving
        // If we have variant_id but price seems wrong, log detailed info
        if ($variant_id_for_cart && $unit_item_price > 0) {
            // Check if this price might be a duplicate (same as another variant of same product)
            $existing_variants = [];
            if (isset($cart['item'][$vendor_id])) {
                foreach ($cart['item'][$vendor_id] as $existing_id => $existing_item) {
                    if (strpos($existing_id, $original_id) === 0 && isset($existing_item['variant_info']['variant_id'])) {
                        $existing_variants[$existing_item['variant_info']['variant_id']] = $existing_item['item_price'];
                    }
                }
            }
            
            // If another variant of same product has same price, this might be wrong
            foreach ($existing_variants as $existing_variant_id => $existing_price) {
                if ($existing_variant_id != $variant_id_for_cart && abs($existing_price - $unit_item_price) < 0.01) {
                    error_log("WARNING: Variant $variant_id_for_cart has same price ($unit_item_price) as variant $existing_variant_id. Product: $original_id");
                }
            }
        }
        
        // Save correct pricing - each variant will have its unique ID and price
        // CRITICAL: The cart item ID MUST include variant_id to ensure uniqueness
        $cart['item'][$vendor_id][$id] = [
            "name" => $req['name'],
            "quantity" => $req['quantity'],
            "stock_quantity" => $req['stock_quantity'],
            "item_price" => $unit_item_price,      // unit price for THIS specific variant
            "price" => $unit_item_price,           // keep unit price here for consistency
            "dis_price" => $unit_dis_price,        // unit discount price for THIS specific variant (if any)
            "extra_price" => $unit_extra_price,    // unit extra price
            "extra" => @$req['extra'],
            "size" => @$req['size'],
            "image" => @$req['image'],
            "veg" => @$req['veg'],
            "variant_info" => $variant_info_storage,  // Store variant info with correct prices
            "category_id" => @$req['category_id'],
        ];
        
        // Debug log: Log the cart item being saved (only in debug mode)
        if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] == 'true' && $variant_id_for_cart) {
            error_log("Cart item saved - Product: $original_id, Variant: $variant_id_for_cart, Cart ID: $id, Price: $unit_item_price");
        }
        $cart['vendor']['id'] = @$vendor_id;
        $cart['vendor']['name'] = @$req['vendor_name'];
        $cart['vendor']['location'] = @$req['vendor_location'];
        $cart['vendor']['image'] = @$req['vendor_image'];
        $cart['taxValue'] = @$req['taxValue'];
        $tax = 0;
        $tax_label = '';
        $total_item_price = 0;
        foreach ($cart['item'][$vendor_id] as $key_cart => $value_cart) {
            $total_one_item_price = $value_cart['item_price'] * $value_cart['quantity'];
            if (@$value_cart['extra_price']) {
                $total_one_item_price = $total_one_item_price + ($value_cart['extra_price'] * $value_cart['quantity']);
            }
            $total_item_price = $total_item_price + $total_one_item_price;
        }
        $discount_amount = 0;
        if (@$cart['coupon'] && $cart['coupon']['discountType']) {
            $discountType = $cart['coupon']['discountType'];
            $coupon_code = $cart['coupon']['coupon_code'];
            $coupon_id = @$cart['coupon']['coupon_id'];
            $discount = $cart['coupon']['discount'];
            if ($discountType == "Fix Price") {
                $discount_amount = $cart['coupon']['discount'];
                if ($discount_amount > $total_item_price) {
                    $discount_amount = $total_item_price;
                }
            } else {
                $discount_amount = $cart['coupon']['discount'];
                $discount_amount = ($total_item_price * $discount_amount) / 100;
                if ($discount_amount > $total_item_price) {
                    $discount_amount = $total_item_price;
                }
            }
        }
        /*Special Offer Disctount*/
        $specialOfferDiscount = 0;
        $specialOfferType = '';
        $specialOfferDiscountVal = 0;
        if (@$req['specialOfferForHour']) {
            $specialOfferForHour = $req['specialOfferForHour'];
            if (count($specialOfferForHour) > 0) {
                foreach ($specialOfferForHour as $key => $value) {
                    $specialOfferType = $value['type'];
                    $specialOfferDiscountVal = $value['discount'];
                    if ($value['type'] == 'percentage') {
                        $specialOfferDiscount = ($total_item_price * $value['discount']) / 100;
                    } else {
                        $specialOfferDiscount = $value['discount'];
                    }
                }
            }
        }
        $cart['specialOfferDiscount'] = $specialOfferDiscount;
        $cart['specialOfferDiscountVal'] = $specialOfferDiscountVal;
        $cart['specialOfferType'] = $specialOfferType;
        $total_item_price = $total_item_price - $discount_amount - $specialOfferDiscount;
        $totalTaxAmount = 0;
        if (is_array($cart['taxValue'])) {
            foreach ($cart['taxValue'] as $val) {
                if ($val['type'] == 'percentage') {
                    $tax = ($val['tax'] * $total_item_price) / 100;
                } else {
                    $tax = $val['tax'];
                }
                $totalTaxAmount += floatval($tax);
            }
            $tax = $totalTaxAmount;
            $tax_label = '';
        }
        $cart['tax_label'] = $tax_label;
        $cart['tax'] = $tax;
        $cart['decimal_degits'] = $req['decimal_degits'];
        Session::put('cart', $cart);
        Session::save();
        $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart])->render());
        echo json_encode($res);
        exit;
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        
        if ($unit == "KM") {
          
            return ($miles * 1.609344);
        }  else {
            return $miles;
        }
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function reorderaddToCart(Request $request)
    {
        $req = $request->all();
        $vendor_id = $req['vendor_id'];
        $cart = Session::get('cart', []);
        $cart['item'] = array();
        Session::put('cart', $cart);
        Session::save();
        if (@$req['deliveryCharge']) {
            $cart['deliverychargemain'] = $req['deliveryCharge'];
        } else {
            $cart['deliverychargemain'] = 0;
        }
        if (Session::get('takeawayOption') == "true") {
            $req['delivery_option'] = "takeaway";
        } else {
            $req['delivery_option'] = "delivery";
        }
        if (@$req['delivery_option'] == "delivery") {
            $cart['deliverycharge'] = $cart['deliverychargemain'];
        } else {
            $cart['deliverycharge'] = 0;
            $cart['tip_amount'] = 0;
        }
        $cart['delivery_option'] = $req['delivery_option'];
        $cart['tip_amount'] = 0;
        foreach ($req['item'] as $key => $value) {
            $id = 0;
            $name = '';
            $quantity = 0;
            $stock_quantity = 0;
            $item_price = 0;
            $price = 0;
            $extra_price = 0;
            $extra = '';
            $size = 0;
            $image = '';
            if ($value['id']) {
                $id = $value['id'];
            }
            if ($value['name']) {
                $name = $value['name'];
            }
            if ($value['quantity']) {
                $quantity = $value['quantity'];
            }
            if ($value['stock_quantity']) {
                $stock_quantity = $value['stock_quantity'];
            }
            if ($value['item_price']) {
                $item_price = $value['item_price'];
            }
            if ($value['price']) {
                $price = $value['price'];
            }
            if ($value['extra_price']) {
                $extra_price = $value['extra_price'];
            }
            if ($value['extra']) {
                $extra = explode(',', $value['extra']);
            }
            if ($value['size']) {
                $size = $value['size'];
            }
            if ($value['image']) {
                $image = $value['image'];
            }
            /*by thm*/
            $variant_info = '';
            if ($value['variant_info']) {
                $variant_info = $value['variant_info'];
            }
            if ($value['category_id']) {
                $category_id = $value['category_id'];
            }
            // compute numeric values
            $quantity = floatval($quantity);
            $price_val = floatval($price);
            $item_price_val = floatval($item_price);
            $extra_price_val = floatval($extra_price);
            $dis_price_val = (isset($value['dis_price']) && $value['dis_price'] !== '') ? floatval($value['dis_price']) : 0;

            // Determine unit price
            if ($item_price_val > 0) {
                $unit_price = $item_price_val;
            } elseif ($quantity > 0) {
                $unit_price = $price_val / max(1, $quantity);
            } else {
                $unit_price = $price_val;
            }

            // Determine unit discount price
            $unit_dis_price = ($dis_price_val > 0 && $quantity > 0) ? ($dis_price_val / max(1, $quantity)) : ($dis_price_val > 0 ? $dis_price_val : '');

            // Determine unit extra price (assume per-unit; if your reorder payload sends total extra, divide here)
            $unit_extra_price = $extra_price_val;

            $cart['item'][$vendor_id][$id] = [
                "name" => @$name,
                "quantity" => @$quantity,
                "stock_quantity" => @$stock_quantity,
                "item_price" => $unit_price,
                "price" => $unit_price,
                "extra_price" => $unit_extra_price,
                "extra" => @$extra,
                "size" => @$size,
                "image" => @$image,
                "variant_info" => $variant_info,
                "category_id" => $category_id,
            ];
        }
        $cart['vendor']['id'] = @$vendor_id;
        $cart['vendor']['name'] = @$req['vendor_name'];
        $cart['vendor']['location'] = @$req['vendor_location'];
        $cart['vendor']['image'] = @$req['vendor_image'];
        $cart['taxValue'] = @$req['taxValue'];
        $tax = 0;
        $tax_label = '';
        $total_item_price = 0;
        foreach ($cart['item'][$vendor_id] as $key_cart => $value_cart) {
            $total_one_item_price = $value_cart['item_price'] * $value_cart['quantity'];
            if ($value_cart['extra_price']) {
                $total_one_item_price = $total_one_item_price + ($value_cart['extra_price'] * $value_cart['quantity']);
            }
            $total_item_price = $total_item_price + $total_one_item_price;
        }
        $discount_amount = 0;
        /*Special Offer Disctount*/
        $specialOfferDiscount = 0;
        $specialOfferType = '';
        $specialOfferDiscountVal = 0;
        if (@$req['specialOfferForHour']) {
            $specialOfferForHour = $req['specialOfferForHour'];
            if (count($specialOfferForHour) > 0) {
                foreach ($specialOfferForHour as $key => $value) {
                    $specialOfferType = $value['type'];
                    $specialOfferDiscountVal = $value['discount'];
                    if ($value['type'] == 'percentage') {
                        $specialOfferDiscount = ($total_item_price * $value['discount']) / 100;
                    } else {
                        $specialOfferDiscount = $value['discount'];
                    }
                }
            }
        }
        $total_item_price = $total_item_price - $discount_amount - $specialOfferDiscount;
        $cart['specialOfferDiscount'] = $specialOfferDiscount;
        $cart['specialOfferDiscountVal'] = $specialOfferDiscountVal;
        $cart['specialOfferType'] = $specialOfferType;
        $totalTaxAmount = 0;
        if (is_array($cart['taxValue'])) {
            foreach ($cart['taxValue'] as $val) {
                if ($val['type'] == 'percentage') {
                    $tax = ($val['tax'] * $total_item_price) / 100;
                } else {
                    $tax = $val['tax'];
                }
                $totalTaxAmount += floatval($tax);
            }
            $tax = $totalTaxAmount;
            $tax_label = '';
        }
        $cart['tax_label'] = $tax_label;
        $cart['tax'] = $tax;
        $cart['decimal_degits'] = $req['decimal_degits'];
        Session::put('cart', $cart);
        Session::save();
        $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart])->render());
        echo json_encode($res);
        exit;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function orderTipAdd(Request $request)
    {
        $req = $request->all();
        $cart = Session::get('cart', []);
        $type = $req['type'];
        if ($type == 'plus') {
            $cart['tip_amount'] = $req['tip'];
        } else {
            $cart['tip_amount'] = 0;
        }
        Session::put('cart', $cart);
        Session::save();
        if (@$req['is_checkout']) {
            $email = Auth::user()->email;
            $user = VendorUsers::where('email', $email)->first();
            $res = array('status' => true, 'html' => view('vendor.cart_item', ['is_checkout' => 1, 'id' => $user->uuid, 'cart' => $cart])->render());
        } else {
            $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart])->render());
        }
        echo json_encode($res);
        exit;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function orderDeliveryOption(Request $request)
    {
        $req = $request->all();
        $cart = Session::get('cart', []);
        $cart['delivery_option'] = $req['delivery_option'];
        if ($req['delivery_option'] == "takeaway") {
            //deliveryCharge
            $cart['tip_amount'] = 0;
            $cart['deliverycharge'] = 0;
        } else {
            //delivery
            if (isset($cart['deliverychargemain'])) {
                $cart['deliverycharge'] = $cart['deliverychargemain'];
            } else if (isset($req['deliveryCharge'])) {
                $cart['deliverychargemain'] = $req['deliveryCharge'];
                $cart['deliverycharge'] = $cart['deliverychargemain'];
            }
        }
        Session::put('cart', $cart);
        Session::save();
        if (@$req['is_checkout']) {
            $email = Auth::user()->email;
            $user = VendorUsers::where('email', $email)->first();
            $res = array('status' => true, 'html' => view('vendor.cart_item', ['is_checkout' => 1, 'id' => $user->uuid, 'cart' => $cart])->render());
        } else {
            $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart])->render());
        }
        echo json_encode($res);
        exit;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function changeQuantityCart(Request $request)
    {
        $req = $request->all();
        $id = $req['id'];
        $vendor_id = $req['vendor_id'];
        $cart = Session::get('cart');
        if (isset($cart['item'][$vendor_id][$id])) {
            if ($req['quantity'] == 0) {
                if (isset($cart['item'][$vendor_id][$id])) {
                    unset($cart['item'][$vendor_id][$id]);
                    Session::put('cart', $cart);
                    Session::save();
                }
            } else {
                $cart['item'][$vendor_id][$id]['quantity'] = $req['quantity'];
                $cart['item'][$vendor_id][$id]['price'] = $cart['item'][$vendor_id][$id]['item_price'] * $cart['item'][$vendor_id][$id]['quantity'];
                $tax = 0;
                $tax_label = '';
                $total_item_price = 0;
                foreach ($cart['item'][$vendor_id] as $key_cart => $value_cart) {
                    $total_one_item_price = $value_cart['item_price'] * $value_cart['quantity'];
                    if (@$value_cart['extra_price']) {
                        $total_one_item_price = $total_one_item_price + ($value_cart['extra_price'] * $value_cart['quantity']);
                    }
                    $total_item_price = $total_item_price + $total_one_item_price;
                }
                $discount_amount = 0;
                /*Disctount*/
                if (@$cart['coupon'] && $cart['coupon']['discountType']) {
                    $discountType = $cart['coupon']['discountType'];
                    $coupon_code = $cart['coupon']['coupon_code'];
                    $coupon_id = @$cart['coupon']['coupon_id'];
                    $discount = $cart['coupon']['discount'];
                    if ($discountType == "Fix Price") {
                        $discount_amount = $cart['coupon']['discount'];
                        if ($discount_amount > $total_item_price) {
                            $discount_amount = $total_item_price;
                        }
                    } else {
                        $discount_amount = $cart['coupon']['discount'];
                        $discount_amount = ($total_item_price * $discount_amount) / 100;
                        if ($discount_amount > $total_item_price) {
                            $discount_amount = $total_item_price;
                        }
                    }
                }
                /*Special Offer Disctount*/
                $specialOfferDiscount = 0;
                if (@$cart['specialOfferType'] && $cart['specialOfferType']) {
                    $specialOfferType = $cart['specialOfferType'];
                    $specialOfferDiscountVal = $cart['specialOfferDiscountVal'];
                    if ($specialOfferType == "amount") {
                        $specialOfferDiscount = $cart['specialOfferDiscount'];
                    } else {
                        $specialOfferDiscount = ($total_item_price * $specialOfferDiscountVal) / 100;
                    }
                }
                $total_item_price = $total_item_price - $discount_amount - $specialOfferDiscount;
                $totalTaxAmount = 0;
                if (is_array($cart['taxValue'])) {
                    foreach ($cart['taxValue'] as $val) {
                        if ($val['type'] == 'percentage') {
                            $tax = ($val['tax'] * $total_item_price) / 100;
                        } else {
                            $tax = $val['tax'];
                        }
                        $totalTaxAmount += floatval($tax);
                    }
                    $tax = $totalTaxAmount;
                    $tax_label = '';
                }
                $cart['tax_label'] = $tax_label;
                $cart['tax'] = $tax;
                Session::put('cart', $cart);
                Session::save();
            }
        }
        $cart = Session::get('cart');
        $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart])->render());
        echo json_encode($res);
        exit;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function changeQuantityCartOLD(Request $request)
    {
        $req = $request->all();
        $id = $req['id'];
        $vendor_id = $req['vendor_id'];
        $quantity = $req['quantity'];
        $cart = Session::get('cart');
        if (isset($cart['item'][$vendor_id][$id])) {
            if ($req['quantity'] == 0) {
                if (isset($cart['item'][$vendor_id][$id])) {
                    unset($cart['item'][$vendor_id][$id]);
                    Session::put('cart', $cart);
                    Session::save();
                }
            } else {
                $cart['item'][$vendor_id][$id]['quantity'] = $req['quantity'];
                $cart['item'][$vendor_id][$id]['price'] = $cart['item'][$vendor_id][$id]['item_price'] * $cart['item'][$vendor_id][$id]['quantity'];
                Session::put('cart', $cart);
                Session::save();
            }
        }
        $cart = Session::get('cart');
        $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart])->render());
        echo json_encode($res);
        exit;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = Session::get('cart');
            $cart['item'][$request->id]["quantity"] = $request->quantity;
            $tax = 0;
            $tax_label = '';
            $total_item_price = 0;
            foreach ($cart['item'][$vendor_id] as $key_cart => $value_cart) {
                $total_one_item_price = $value_cart['item_price'] * $value_cart['quantity'];
                if (@$value_cart['extra_price']) {
                    $total_one_item_price = $total_one_item_price + ($value_cart['extra_price'] * $value_cart['quantity']);
                }
                $total_item_price = $total_item_price + $total_one_item_price;
            }
            $discount_amount = 0;
            /*Disctount*/
            if (@$cart['coupon'] && $cart['coupon']['discountType']) {
                $discountType = $cart['coupon']['discountType'];
                $coupon_code = $cart['coupon']['coupon_code'];
                $coupon_id = @$cart['coupon']['coupon_id'];
                $discount = $cart['coupon']['discount'];
                if ($discountType == "Fix Price") {
                    $discount_amount = $cart['coupon']['discount'];
                    if ($discount_amount > $total_item_price) {
                        $discount_amount = $total_item_price;
                    }
                } else {
                    $discount_amount = $cart['coupon']['discount'];
                    $discount_amount = ($total_item_price * $discount_amount) / 100;
                    if ($discount_amount > $total_item_price) {
                        $discount_amount = $total;
                    }
                }
            }
            /*Special Offer Disctount*/
            $specialOfferDiscount = 0;
            if (@$cart['specialOfferType'] && $cart['specialOfferType']) {
                $specialOfferType = $cart['specialOfferType'];
                $specialOfferDiscountVal = $cart['specialOfferDiscountVal'];
                if ($specialOfferType == "amount") {
                    $specialOfferDiscount = $cart['specialOfferDiscount'];
                } else {
                    $specialOfferDiscount = ($total_item_price * $specialOfferDiscountVal) / 100;
                }
            }
            $total_item_price = $total_item_price - $discount_amount - $specialOfferDiscount;
            $totalTaxAmount = 0;
            if (is_array($cart['taxValue'])) {
                foreach ($cart['taxValue'] as $val) {
                    if ($val['type'] == 'percentage') {
                        $tax = ($val['tax'] * $total_item_price) / 100;
                    } else {
                        $tax = $val['tax'];
                    }
                    $totalTaxAmount += floatval($tax);
                }
                $tax = $totalTaxAmount;
                $tax_label = '';
            }
            $cart['tax_label'] = $tax_label;
            $cart['tax'] = $tax;
            Session::put('cart', $cart);
            Session::save();
            $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart])->render());
            echo json_encode($res);
            exit;
        }
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function applyCoupon(Request $request)
    {
        if ($request->coupon_code) {
            $cart = Session::get('cart');
            $cart['coupon']['coupon_code'] = $request->coupon_code;
            $cart['coupon']['coupon_id'] = $request->coupon_id;
            $cart['coupon']['discount'] = $request->discount;
            $cart['coupon']['discountType'] = $request->discountType;
            $tax = 0;
            $tax_label = '';
            $total_item_price = 0;
            $id = array_key_first($cart['item']);
            $vendor_id = $id;
            if ($vendor_id) {
                foreach ($cart['item'][$vendor_id] as $key_cart => $value_cart) {
                    $total_one_item_price = $value_cart['item_price'] * $value_cart['quantity'];
                    if (@$value_cart['extra_price']) {
                        $total_one_item_price = $total_one_item_price + ($value_cart['extra_price'] * $value_cart['quantity']);
                    }
                    $total_item_price = $total_item_price + $total_one_item_price;
                }
                $discount_amount = 0;
                /*Disctount*/
                if (@$cart['coupon'] && $cart['coupon']['discountType']) {
                    $discountType = $cart['coupon']['discountType'];
                    $coupon_code = $cart['coupon']['coupon_code'];
                    $coupon_id = @$cart['coupon']['coupon_id'];
                    $discount = $cart['coupon']['discount'];
                    if ($discountType == "Fix Price") {
                        $discount_amount = $cart['coupon']['discount'];
                        if ($discount_amount > $total_item_price) {
                            $discount_amount = $total_item_price;
                        }
                    } else {
                        $discount_amount = $cart['coupon']['discount'];
                        $discount_amount = ($total_item_price * $discount_amount) / 100;
                        if ($discount_amount > $total_item_price) {
                            $discount_amount = $total_item_price;
                        }
                    }
                }
                /*Special Offer Disctount*/
                $specialOfferDiscount = 0;
                if (@$cart['specialOfferType'] && $cart['specialOfferType']) {
                    $specialOfferType = $cart['specialOfferType'];
                    $specialOfferDiscountVal = $cart['specialOfferDiscountVal'];
                    if ($specialOfferType == "amount") {
                        $specialOfferDiscount = $cart['specialOfferDiscount'];
                    } else {
                        $specialOfferDiscount = ($total_item_price * $specialOfferDiscountVal) / 100;
                    }
                }
                $total_item_price = $total_item_price - $discount_amount - $specialOfferDiscount;
                $totalTaxAmount = 0;
                if (is_array($cart['taxValue'])) {
                    foreach ($cart['taxValue'] as $val) {
                        if ($val['type'] == 'percentage') {
                            $tax = ($val['tax'] * $total_item_price) / 100;
                        } else {
                            $tax = $val['tax'];
                        }
                        $totalTaxAmount += floatval($tax);
                    }
                    $tax = $totalTaxAmount;
                    $tax_label = '';
                }
            }
            $cart['tax_label'] = $tax_label;
            $cart['tax'] = $tax;
            Session::put('cart', $cart);
            Session::save();
            $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart])->render());
            echo json_encode($res);
            exit;
        }
    }

    public function orderComplete(Request $request)
    {

        $cart = array();
        Session::put('cart', $cart);
        Session::put('payfast_payment_token', '');
        Session::put('success', 'Your order has been successful!');

        if(Storage::disk('local')->has('firebase/credentials.json')){
            
            $client= new Google_Client();
            $client->setAuthConfig(storage_path('app/firebase/credentials.json'));
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->refreshTokenWithAssertion();
            $client_token = $client->getAccessToken();
            $access_token = $client_token['access_token'];

            $fcm_token = $request->fcm;
            
            if(!empty($access_token) && !empty($fcm_token)){

                $projectId = env('FIREBASE_PROJECT_ID');
                $url = 'https://fcm.googleapis.com/v1/projects/'.$projectId.'/messages:send';

                $data = [
                    'message' => [
                        'notification' => [
                            'title' => $request->subject,
                            'body' => $request->message,
                        ],
                        'data' => [
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'id' => '1',
                            'status' => 'done',
                        ],
                        'token' => $fcm_token,
                    ],
                ];

                $headers = array(
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$access_token
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                
                $result = curl_exec($ch);
                if ($result === FALSE) {
                    die('FCM Send Error: ' . curl_error($ch));
                }
                curl_close($ch);
                $result=json_decode($result);

                $response = array();
                $response['success'] = true;
                $response['message'] = 'Notification successfully sent.';
                $response['result'] = $result;

            }else{
                $response = array();
                $response['success'] = false;
                $response['message'] = 'Missing sender id or token to send notification.';
            }

        }else{
            $response = array();
            $response['success'] = false;
            $response['message'] = 'Firebase credentials file not found.';
        }

        Session::save();

        $order_response = array('status' => true, 'order_complete' => true, 'html' => view('vendor.cart_item', ['cart' => $cart, 'order_complete' => true, 'is_checkout' => 1])->render(), 'response' => $response);
       
        return response()->json($order_response);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove(Request $request)
    {
        if ($request->id && $request->vendor_id) {
            $cart = Session::get('cart');
            if (isset($cart['item'][$request->vendor_id][$request->id])) {
                unset($cart['item'][$request->vendor_id][$request->id]);
                $total_item_price = 0;
                $id = array_key_first($cart['item']);
                $vendor_id = $id;
                if ($vendor_id) {
                    foreach ($cart['item'][$vendor_id] as $key_cart => $value_cart) {
                        $total_one_item_price = $value_cart['item_price'] * $value_cart['quantity'];
                        if ($value_cart['extra_price']) {
                            $total_one_item_price = $total_one_item_price + ($value_cart['extra_price'] * $value_cart['quantity']);
                        }
                        $total_item_price = $total_item_price + $total_one_item_price;
                    }
                    $discount_amount = 0;
                    /*Disctount*/
                    if (@$cart['coupon'] && $cart['coupon']['discountType']) {
                        $discountType = $cart['coupon']['discountType'];
                        $coupon_code = $cart['coupon']['coupon_code'];
                        $coupon_id = @$cart['coupon']['coupon_id'];
                        $discount = $cart['coupon']['discount'];
                        if ($discountType == "Fix Price") {
                            $discount_amount = $cart['coupon']['discount'];
                            if ($discount_amount > $total_item_price) {
                                $discount_amount = $total_item_price;
                            }
                        } else {
                            $discount_amount = $cart['coupon']['discount'];
                            $discount_amount = ($total_item_price * $discount_amount) / 100;
                            if ($discount_amount > $total_item_price) {
                                $discount_amount = $total;
                            }
                        }
                    }
                    $specialOfferDiscount = 0;
                    if (@$cart['specialOfferType'] && $cart['specialOfferType']) {
                        $specialOfferType = $cart['specialOfferType'];
                        $specialOfferDiscountVal = $cart['specialOfferDiscountVal'];
                        if ($specialOfferType == "amount") {
                            $specialOfferDiscount = $cart['specialOfferDiscount'];
                        } else {
                            $specialOfferDiscount = ($total_item_price * $specialOfferDiscountVal) / 100;
                        }
                    }
                    $total_item_price = $total_item_price - $discount_amount - $specialOfferDiscount;
                    $tax_label = '';
                    $tax = 0;
                    if (is_array($cart['taxValue'])) {
                        $totalTaxAmount = 0;
                        foreach ($cart['taxValue'] as $val) {
                            if ($val['type'] == 'percentage') {
                                $tax = ($val['tax'] * $total_item_price) / 100;
                            } else {
                                $tax = $val['tax'];
                            }
                            $totalTaxAmount += floatval($tax);
                        }
                        $tax = $totalTaxAmount;
                        $tax_label = '';
                    }
                    $cart['tax_label'] = $tax_label;
                    $cart['tax'] = $tax;
                }
            }
            Session::put('cart', $cart);
            Session::save();
        }
        $cart = Session::get('cart');
        session()->flash('success', 'Product removed successfully');
        $res = array('status' => true, 'html' => view('vendor.cart_item', ['cart' => $cart])->render());
        echo json_encode($res);
        exit;
    }
}