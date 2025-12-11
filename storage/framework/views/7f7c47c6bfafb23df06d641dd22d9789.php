<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->make('layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="vendor-page bg-white ecom-vendor-page category-listing-page">

    <div class="offer-section py-3 resturant-banner">

        <div class="container position-relative">

            <div class="resturant-banner-inner">

                <div class="row">

                    <div class="col-md-8 resturant-banner-left" id="vendor_image"></div>

                    <div class="col-md-4 resturant-banner-right" id="vendor-gallery"></div>

                </div>

            </div>

            <div id="popup-gallary" style="display:none"></div>

        </div>

    </div>

    <div class="container position-relative pb-5">

        <div class="d-flex align-items-start rest-basic-detail mt-3 mb-3">

            <div class="text-dark">

                <h2 class="font-weight-bold h6" id="vendor_title"></h2>

                <div class="d-flex">

                    <p class="text-dark m-0" id="vendor_address"><span class="fa fa-map-marker "></span></p>

                    <div class="rest-time">

                        <span class="text-dark-50 font-weight-bold m-0 pl-3 time"></span>

                        <spanclass

                        ="text-dark m-0 font-weight-bold" id="vendor_open_time1"></span>

                    </div>

                </div>

                <div class="rating-wrap d-flex align-items-center mt-2" id="vendor_ratings"></div>

            </div>

            <div class="feather_icon ml-auto">

                <div class="row fu-review">

                    <?php if (Auth::check()) : ?>

                    <a href="javascript:void(0)" class="text-decoration-none mx-1 p-2 rest-right-btn addToFavorite"><i class="font-weight-bold feather-heart"></i></a>

                    <?php else : ?>

                    <a href="javascript:void(0)" class="text-decoration-none mx-1 p-2 rest-right-btn loginAlert"><i class="font-weight-bold feather-heart"></i></a>

                    <?php endif; ?>

                    <a class="text-decoration-none mx-1 p-2 rest-right-btn vendor_location_btn" target="_blank"><i class="font-weight-bold feather-map-pin"></i></a>

                    <a href="<?php echo e(route('contact_us')); ?>" class="btn"><?php echo e(trans('lang.contact')); ?></a>

                </div>

                <?php if ($_COOKIE['service_type'] != 'Ecommerce Service') { ?>

                <div class="row fu-time">

                    <a class="text-decoration-none mx-1 p-2 rest-right-btn time" style="pointer-events: none">

                        <span class="text-dark-50 font-weight-bold m-0 pl-3 time "><?php echo e(trans('lang.time')); ?> : </span>

                        <span class="text-dark m-0 font-weight-bold" id="vendor_open_time"></span>

                    </a>

                </div>

                <div class="row fu-status">

                    <a class="text-decoration-none mx-1 p-2 rest-right-btn">

                        <span class="text-dark m-0 font-weight-bold" style="pointer-events: none" id="vendor_shop_status"></span>

                    </a>

                </div>

                <?php } ?>

            </div>

        </div>

        <div class="foodies-detail-coupon">

            <div class="coupon_detail">

            </div>

        </div>

        <div class="ecom-vendor-product-section">

            <div class="row">

                <div class="col-md-3 vendor-detail-left">

                    <div id="category-list"></div>
                    <div id="subcategory-list" style="display:none;"></div>

                </div>

                <div class="col-md-9 vendor-detail-right">

                    <div id="product-list" class="row"></div>

                </div>

            </div>

        </div>

    </div>

</div>

<input type="hidden" name="vendor_id" id="vendor_id" value="<?php echo $id; ?>">

<input type="hidden" name="vendor_name" id="vendor_name" value="">

<input type="hidden" name="vendor_location" id="vendor_location" value="">

<input type="hidden" name="vendor_latitude" id="vendor_latitude" value="">

<input type="hidden" name="vendor_longitude" id="vendor_longitude" value="">

<input type="hidden" name="vendor_image" id="vendor_image" value="">

</div>

<?php echo $__env->make('layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->make('layouts.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script type="text/javascript">
    var section_id = "<?php echo @$_COOKIE['section_id']; ?>";

    var vendorID = '<?php echo $id; ?>';

    var service_type = "<?php echo @$_COOKIE['service_type']; ?>"

    var currentCurrency = '';

    var currencyAtRight = false;

    var specialOfferVendor = [];

    let specialOfferForHour = [];

    var enableSpecialOffer = false;

    var enableDinein = false;

    var isProductDetailEnable = false;

    var sectionRef = database.collection('sections').where('id', '==', section_id);

    sectionRef.get().then(async function(sectionsnapShots) {

        var sectiondata = sectionsnapShots.docs[0].data();

        if (sectiondata.dine_in_active) {

            enableDinein = sectiondata.dine_in_active;

        }

        if (sectiondata.hasOwnProperty('is_product_details') && sectiondata.is_product_details != null && sectiondata.is_product_details != '' && sectiondata.is_product_details) {

            isProductDetailEnable = true;



        }

    });

    var specialOfferRef = database.collection('settings').doc('specialDiscountOffer');

    specialOfferRef.get().then(async function(snapShots) {

        var specialOfferData = snapShots.data();

        if (specialOfferData.isEnable) {

            enableSpecialOffer = specialOfferData.isEnable;

        }

    });

    var catsRef = database.collection('vendor_categories').where('section_id', '==', section_id).where("publish", "==", true);

    var vendorDetailsRef = database.collection('vendors').where('id', "==", vendorID);

    var vendorProductsRef = database.collection('vendor_products').where('vendorID', "==", vendorID).where("publish", "==", true);

    // Location radius configuration
    var locationRadiusKm = 50; // Default to 50km if not configured (more lenient)
    var radiusUnit = 'Km';
    var radiusUnitRef = database.collection('settings').doc('DriverNearBy');
    var vendorNearByRef = database.collection('sections').doc(section_id);
    
    // Function to calculate distance between two coordinates (Haversine formula)
    function calculateDistance(lat1, lon1, lat2, lon2) {
        var R = 6371; // Radius of the earth in km
        var dLat = deg2rad(lat2 - lat1);
        var dLon = deg2rad(lon2 - lon1);
        var a = 
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
            Math.sin(dLon/2) * Math.sin(dLon/2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        var d = R * c; // Distance in km
        return d;
    }

    function deg2rad(deg) {
        return deg * (Math.PI/180);
    }

    // Function to check if vendor location matches user location
    // Uses configurable radius from section settings (locationRadiusKm)
    function isVendorInUserLocation(vendor) {
        var userLat = parseFloat(address_lat);
        var userLng = parseFloat(address_lng);
        
        if (!userLat || !userLng || isNaN(userLat) || isNaN(userLng)) {
            return false; // No user location, don't show vendors
        }

        var vendorLat = null;
        var vendorLng = null;

        // Check if vendor has coordinates field (GeoPoint)
        if (vendor.hasOwnProperty('coordinates') && vendor.coordinates) {
            vendorLat = vendor.coordinates.latitude;
            vendorLng = vendor.coordinates.longitude;
        } 
        // Check if vendor has latitude/longitude fields
        else if (vendor.hasOwnProperty('latitude') && vendor.hasOwnProperty('longitude')) {
            vendorLat = parseFloat(vendor.latitude);
            vendorLng = parseFloat(vendor.longitude);
        }

        if (!vendorLat || !vendorLng || isNaN(vendorLat) || isNaN(vendorLng)) {
            return false; // Vendor has no location data
        }

        // Calculate actual distance using Haversine formula
        var distanceKm = calculateDistance(userLat, userLng, vendorLat, vendorLng);
        
        // Check if vendor is within configured radius
        return distanceKm <= locationRadiusKm;
    }

    var placeholderImageSrc = '';
    
    // Load placeholder image
    var placeholderImageRef = database.collection('settings').doc('placeHolderImage');
    placeholderImageRef.get().then(async function(placeholderImageSnapshots) {
        var placeHolderImageData = placeholderImageSnapshots.data();
        placeholderImageSrc = placeHolderImageData.image;
    });

    var decimal_degits = 0;

    var refCurrency = database.collection('currencies').where('isActive', '==', true);

    refCurrency.get().then(async function(snapshots) {

        var currencyData = snapshots.docs[0].data();

        currentCurrency = currencyData.symbol;

        currencyAtRight = currencyData.symbolAtRight;

        if (currencyData.decimal_degits) {

            decimal_degits = currencyData.decimal_degits;

        }

    });

    var placeholderImageRef = database.collection('settings').doc('placeHolderImage');

    var placeholderImageSrc = '';

    placeholderImageRef.get().then(async function(placeholderImageSnapshots) {

        var placeHolderImageData = placeholderImageSnapshots.data();

        placeholderImageSrc = placeHolderImageData.image;

    })

    jQuery("#overlay").show();

    $(document).ready(async function() {



        inValidProductIds = await getUserItemLimit(vendorID);



        priceData = await fetchVendorPriceData();

        getVendorDetails();

        getCategories();

        $(document).on("click", ".category-item", function() {

            if (!$(this).hasClass('active')) {

                $(this).addClass('active').siblings().removeClass('active');

                getProductsOriginal($(this).data('category-id'));

            }

        });

        getCouponDetails();

    });



    async function getCategories() {
        var vendorCategoryIds = [];
        await vendorProductsRef.get().then(async function(snapshots) {

            snapshots.docs.forEach((listval) => {
                var datas = listval.data();
                datas.id = listval.id;
                if (inValidProductIds.length == 0 || !inValidProductIds.includes(datas.id)) {
                    if (jQuery.inArray(datas.categoryID, vendorCategoryIds) == -1) {
                        vendorCategoryIds.push(datas.categoryID);
                    }
                }
            });
        });
        catsRef.get().then(async function(snapshots) {

            if (snapshots != undefined) {

                var html = '';

                var alldata = [];

                snapshots.docs.forEach((listval) => {

                    var datas = listval.data();

                    datas.id = listval.id;
                    for (var i = 0; i < vendorCategoryIds.length; i++) {
                        if (vendorCategoryIds[i] == datas.id) {

                            alldata.push(datas);
                        }
                    }



                });

                var cats = [];

                // If no products found, show all categories for this section
                if (vendorCategoryIds.length === 0) {
                    snapshots.docs.forEach((listval) => {
                        var datas = listval.data();
                        datas.id = listval.id;
                        cats.push(datas);
                    });
                } else {
                    // Show only categories that have products
                    for (i = 0; i < alldata.length; i++) {

                        var countProduct = await vendorProductsRef.where('categoryID', '==', alldata[i].id).get().then(function(snapshots) {

                            return snapshots.docs.length;

                        });

                        if (countProduct > 0) {

                            cats.push(alldata[i]);

                        }

                    }
                }

                html = html + '<div class="vandor-sidebar">';

                html = html + '<h3><?php echo e(trans("lang.categories")); ?></h3>';

                if (cats.length > 0) {

                    html = html + '<ul class="vandorcat-list">';

                    cats.forEach((listval) => {

                        var val = listval;

                        if (val.photo) {

                            photo = val.photo;

                        } else {

                            photo = placeholderImageSrc;

                        }

                        html = html + '<li onclick="getSubcategories(\'' + val.id + '\')" class="category-item" data-category-id="' + val.id + '" >';

                        html = html + '<a href="javascript:void(0)"><span><img src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" style="width: 100%; height: 100%; object-fit: cover; object-position: center;"></span>' + val.title + '</a>';

                        html = html + '</li>';

                    });

                    html = html + '</ul>';

                } else {

                    html = html + '<p class="text-center font-weight-bold mt-2"><?php echo e(trans('lang.no_results')); ?></p>';

                }

                if (html != '') {

                    var append_list = document.getElementById('category-list');

                    append_list.innerHTML = html;

                    // Don't auto-select the first category - let user click

                }

            }

            jQuery("#overlay").hide();

        });

    }

    // Function to get subcategories for a selected category
    async function getSubcategories(categoryId) {
        // Hide subcategory list first
        $('#subcategory-list').hide();
        
        // Update active category
        $('#category-list .category-item').removeClass('active');
        $('#category-list .category-item[data-category-id="' + categoryId + '"]').addClass('active');
        
        // Get all published subcategories and filter by category_id
        var subcatsRef = database.collection('vendor_subcategories')
            .where('publish', '==', true);
            
        subcatsRef.get().then(async function(snapshots) {
            var subcategoryHtml = '';
            
            // Debug logging
            console.log('Vendor page - Category ID:', categoryId);
            console.log('Vendor page - Total subcategories found:', snapshots.docs.length);
            
            // Filter subcategories by category_id
            var filteredSubcategories = snapshots.docs.filter(function(doc) {
                var data = doc.data();
                console.log('Vendor page - Subcategory category_id:', data.category_id, 'looking for:', categoryId);
                return data.category_id == categoryId;
            });
            
            console.log('Vendor page - Filtered subcategories:', filteredSubcategories.length);
            
            if (filteredSubcategories.length > 0) {
                subcategoryHtml = '<div class="vandor-sidebar">';
                
                // Move the back button to existing categories header by replacing the main header
                // We'll hide the original and show this modified one
                subcategoryHtml += '<div class="d-flex justify-content-between align-items-center mb-3">';
                subcategoryHtml += '<h3><?php echo e(trans("lang.categories")); ?></h3>';
                subcategoryHtml += '<button onclick="hideSubcategories()" class="btn btn-sm btn-outline-primary">← Back</button>';
                subcategoryHtml += '</div>';
                
                subcategoryHtml += '<ul class="vandorcat-list">';
                
                filteredSubcategories.forEach((subcat) => {
                    var subcatData = subcat.data();
                    var subcatPhoto = subcatData.photo || placeholderImageSrc;
                    
                    console.log('Creating subcategory item:', subcatData.title, 'ID:', subcat.id);
                    
                    subcategoryHtml += '<li class="subcategory-item" data-subcategory-id="' + subcat.id + '" data-category-id="' + categoryId + '" data-title="' + subcatData.title + '">';
                    subcategoryHtml += '<a href="javascript:void(0)"><span><img src="' + subcatPhoto + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" style="width: 100%; height: 100%; object-fit: cover; object-position: center;"></span>' + subcatData.title + '</a>';
                    subcategoryHtml += '</li>';
                });
                
                subcategoryHtml += '</ul>';
                subcategoryHtml += '</div>';
                
                // Replace the categories list with subcategories instead of adding a new section
                $('#category-list').html(subcategoryHtml);
                $('#subcategory-list').hide();
                
                // Auto-select first subcategory
                var firstSubcategoryId = filteredSubcategories[0].id;
                getProducts(categoryId, firstSubcategoryId);
            } else {
                // No subcategories, show products directly for this category
                $('#subcategory-list').hide();
                getProducts(categoryId, null);
            }
        });
    }

    // Function to get products for a category/subcategory combination
    function getProducts(categoryId, subcategoryId) {
        console.log('=== getProducts CALLED ===');
        console.log('CategoryId:', categoryId);
        console.log('SubcategoryId:', subcategoryId);
        console.log('========================');
        
        try {
        
        // Update active subcategory (now they're in category-list)
        if (subcategoryId) {
            $('#category-list .subcategory-item').removeClass('active');
            $('#category-list .subcategory-item[data-subcategory-id="' + subcategoryId + '"]').addClass('active');
        }
        
        var productsRef = database.collection('vendor_products')
            .where('vendorID', '==', vendorID)
            .where('categoryID', '==', categoryId)
            .where('publish', '==', true);
            
        // Add subcategoryID filter if specified
        if (subcategoryId) {
            productsRef = productsRef.where('subcategoryID', '==', subcategoryId);
            console.log('Adding subcategoryID filter:', subcategoryId);
        }
        
        productsRef.get().then(async function(snapshots) {
            var productHtml = '';
            
            // Debug logging
            console.log('Vendor page - Getting products for category:', categoryId, 'subcategory:', subcategoryId);
            console.log('Vendor page - Products found:', snapshots.docs.length);
            
            // Log all products to see their subcategoryID values
            console.log('=== ALL PRODUCTS IN CATEGORY ===');
            snapshots.docs.forEach(function(productDoc) {
                var product = productDoc.data();
                console.log('Product:', product.name, 'categoryID:', product.categoryID, 'subcategoryID:', product.subcategoryID);
            });
            console.log('=== END PRODUCT LIST ===');
            
            // Filter out unpublished products (defensive check)
            var filteredProducts = snapshots.docs.filter(function(productDoc) {
                var product = productDoc.data();
                return product.publish === true;
            });
            console.log('Products returned from database:', filteredProducts.length);
            
            // Verify all products have correct subcategoryID
            if (subcategoryId) {
                filteredProducts.forEach(function(productDoc) {
                    var product = productDoc.data();
                    console.log('Product:', product.name.substring(0, 30) + '...', '| subcategoryID:', product.subcategoryID, '| expected:', subcategoryId);
                });
            }
            
            // If no filtered products found and subcategory was specified, try showing count info
            if (subcategoryId && filteredProducts.length === 0) {
                console.log('Warning: No products found for subcategory:', subcategoryId);
                console.log('This might mean products are not assigned to subcategories properly');
            }
            
            // Final summary
            console.log('=== FINAL RESULT ===');
            console.log('Category:', categoryId);
            console.log('Subcategory:', subcategoryId);
            console.log('Products to display:', filteredProducts.length);
            console.log('==================');
            
            if (filteredProducts.length > 0) {
                // Build product HTML - you may need to customize this based on your existing product display
                filteredProducts.forEach((productDoc) => {
                    var product = productDoc.data();
                    product.id = productDoc.id;
                    
                    // Build individual product HTML here
                    productHtml += buildProductHTML(product);
                });
            } else {
                productHtml = '<p class="text-center font-weight-bold mt-2"><?php echo e(trans("lang.no_results")); ?></p>';
            }
            
            $('#product-list').html(productHtml);
        }).catch(function(error) {
            console.error('Firestore query error:', error);
        });
        
        } catch (error) {
            console.error('Error in getProducts function:', error);
        }
    }

    // Helper function to build individual product HTML
    function buildProductHTML(product) {
        var html = '<div class="col-md-4 product-list">';
        html += '<div class="list-card position-relative">';
        html += '<div class="list-card-image">';
        
        var photo = product.photo || placeholderImageSrc;
        
        // Check if product detail is enabled for veg/non-veg badges
        if (product.hasOwnProperty('nonveg')) {
            if (product.nonveg == true) {
                html += '<div class="member-plan position-absolute"><span class="badge badge-danger">non veg</span></div>';
            } else {
                html += '<div class="member-plan position-absolute"><span class="badge badge-success">Pure veg</span></div>';
            }
        }
        
        // Create product detail link
        var product_detail_link = "<?php echo e(route('productdetail', ':id')); ?>".replace(':id', product.id);
        
        // Make image clickable with proper link
        html += '<a href="' + product_detail_link + '"><img alt="#" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" class="img-fluid item-img w-100"></a>';
        html += '</div>';
        
        html += '<div class="py-2 position-relative">';
        html += '<div class="list-card-body position-relative">';
        
        // Make product name clickable
        html += '<h6 class="product-title mb-1"><a href="' + product_detail_link + '" class="text-black">' + product.name + '</a></h6>';
        
        html += '<h6 class="mb-1 popular_food_category_ pro-cat" id="popular_food_category_' + product.categoryID + '_' + product.id + '"></h6>';
        
        // Price section - simplified version
        if (product.price) {
            html += '<span class="pro-price">₹' + product.price + '</span>';
        }
        
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        
        return html;
    }

    // Function to hide subcategories and go back to categories
    function hideSubcategories() {
        $('#subcategory-list').hide();
        $('#category-list .category-item').removeClass('active');
        $('#product-list').html('');
        
        // Restore the original categories by reloading them
        getCategories();
    }

    // Click handler for subcategories
    $(document).on('click', '.subcategory-item', function() {
        var categoryId = $(this).data('category-id');
        var subcategoryId = $(this).data('subcategory-id');
        var title = $(this).data('title');
        
        console.log('DOCUMENT CLICK HANDLER: Subcategory item clicked');
        console.log('CategoryId:', categoryId, 'SubcategoryId:', subcategoryId, 'Title:', title);
        
        // Call getProducts function
        if (categoryId && subcategoryId) {
            console.log('Calling getProducts with categoryId:', categoryId, 'subcategoryId:', subcategoryId);
            getProducts(categoryId, subcategoryId);
        } else {
            console.error('Missing categoryId or subcategoryId');
        }
    });

    async function getCouponDetails() {

        var date = new Date();

        var couponRef = database.collection('coupons').where('isPublic', '==', true).where('vendorID', '==', vendorID).where('expiresAt', '>=', date);

        var couponHtml = '';

        let menuHtmlx = couponRef.get().then(async function(couponRefSnapshots) {

            if (couponRefSnapshots.docs.length > 0) {

                couponHtml += '<div class="coupon-code"><label><?php echo e(trans('lang.available_coupon')); ?></label><span></span></div>';

                couponHtml += '<div class="copupon-list">';

                couponHtml += '<ul>';

                couponRefSnapshots.docs.forEach((doc) => {

                    coupon = doc.data();

                    if (coupon.expiresAt) {

                        var date1 = coupon.expiresAt.toDate().toDateString();

                        var date = new Date(date1);

                        var dd = String(date.getDate()).padStart(2, '0');

                        var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!

                        var yyyy = date.getFullYear();

                        var expiresDate = yyyy + '-' + mm + '-' + dd;

                    }

                    if (coupon.discountType == 'Percentage') {

                        var discount = coupon.discount + '%'

                    } else {

                        coupon.discount = parseFloat(coupon.discount);

                        if (currencyAtRight) {

                            var discount = coupon.discount.toFixed(decimal_degits) + "" + currentCurrency;

                        } else {

                            var discount = currentCurrency + "" + coupon.discount.toFixed(decimal_degits);

                        }

                    }

                    if (coupon.isEnabled == true) {

                        couponHtml += '<li value="' + coupon.code + '"><span class="per-off">' + discount + ' OFF </span><span>' + coupon.code + ' | Valid till ' + expiresDate + '</span></li>';

                    }

                });

                couponHtml += '</ul></div>';

            }

            return couponHtml;

        })

        let menuHtml = await menuHtmlx.then(function(html) {

            if (html != undefined) {

                return html;

            }

        })

        $('.coupon_detail').html(menuHtml);

    }



    async function getProductsOriginal(category_id) {

        jQuery("#overlay").show();

        var product_list = document.getElementById('product-list');

        product_list.innerHTML = '';

        var html = '';

        vendorProductsRef.where('categoryID', '==', category_id).orderBy('name').get().then(async function(snapshots) {

            html = buildProductsHTML(snapshots);

            if (html != '') {

                product_list.innerHTML = html;

                jQuery("#overlay").hide();

            }

        });

    }



    function buildProductsHTML(snapshots) {

        var html = '';

        var alldata = [];

        snapshots.docs.forEach((listval) => {

            var datas = listval.data();

            datas.id = listval.id;

            // Filter out unpublished products
            if (datas.publish !== true) {
                return;
            }

            if (inValidProductIds.length == 0 || !inValidProductIds.includes(datas.id)) {

                alldata.push(datas);

            }

        });

        var count = 0;

        var popularFoodCount = 0;
        if (alldata.length > 0) {
            html = html + '<div class="row">';

            alldata.forEach((listval) => {

                var val = listval;

                var vendor_id_single = val.id;

                var view_vendor_details = "<?php echo e(route('productdetail', ':id')); ?>";

                view_vendor_details = view_vendor_details.replace(':id', vendor_id_single);

                var rating = 0;

                var reviewsCount = 0;

                if (val.hasOwnProperty('reviewsSum') && val.reviewsSum != 0 && val.hasOwnProperty('reviewsCount') && val.reviewsCount != 0) {

                    rating = (val.reviewsSum / val.reviewsCount);

                    rating = Math.round(rating * 10) / 10;

                    reviewsCount = val.reviewsCount;

                }

                html = html + '<div class="col-md-4 product-list"><div class="list-card position-relative"><div class="list-card-image">';

                if (val.photo) {

                    photo = val.photo;

                } else {

                    photo = placeholderImageSrc;

                }

                if (isProductDetailEnable) {

                    if (val.hasOwnProperty('nonveg')) {

                        if (val.nonveg == true) {

                            html = html + '<div class="member-plan position-absolute"><span class="badge badge-danger">non veg</span></div>';

                        } else {

                            html = html + '<div class="member-plan position-absolute"><span class="badge badge-success">Pure veg</span></div>';

                        }

                    }

                }

                html = html + '<a href="' + view_vendor_details + '"><img alt="#" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" class="img-fluid item-img w-100"></a></div><div class="py-2 position-relative"><div class="list-card-body position-relative"><h6 class="product-title mb-1"><a href="' + view_vendor_details + '" class="text-black">' + val.name + '</a></h6>';

                html = html + '<h6 class="mb-1 popular_food_category_ pro-cat" id="popular_food_category_' + val.categoryID + '_' + val.id + '" ></h6>';



                let final_price = priceData[val.id];



                if (val.item_attribute && val.item_attribute.variants?.length > 0) {

                    let variantPrices = val.item_attribute.variants.map(v => v.variant_price);

                    let minPrice = Math.min(...variantPrices);

                    let maxPrice = Math.max(...variantPrices);

                    let or_price = minPrice !== maxPrice ?

                        `${getProductFormattedPrice(final_price.min)} - ${getProductFormattedPrice(final_price.max)}` :

                        getProductFormattedPrice(final_price.max);

                    if (val.priceUnit == "Hourly") {

                        html = html + '<span class="pro-price">' + or_price + "/hr" + '</span>'

                    } else {

                        html = html + '<span class="pro-price">' + or_price + '</span>'

                    }

                } else if (val.hasOwnProperty('disPrice') && val.disPrice != '' && val.disPrice != '0') {

                    var or_price = getProductFormattedPrice(parseFloat(final_price.price));

                    var dis_price = getProductFormattedPrice(parseFloat(final_price.dis_price));

                    if (val.priceUnit == "Hourly") {

                        html = html + '<span class="pro-price">' + dis_price + "/hr" + '  <s>' + or_price + "/hr" + '</s></span>';

                    } else {

                        html = html + '<span class="pro-price">' + dis_price + '  <s>' + or_price + '</s></span>';

                    }

                } else {

                    var or_price = getProductFormattedPrice(parseFloat(final_price.price));

                    if (val.priceUnit == "Hourly") {

                        html = html + '<span class="pro-price">' + or_price + "/hr" + '</span>'

                    } else {

                        html = html + '<span class="pro-price">' + or_price + '</span>'

                    }

                }



                html = html + '<div class="star position-relative mt-3"><span class="badge badge-success"><i class="feather-star"></i>' + rating + ' (' + reviewsCount + ')</span></div>';

                html = html + '</div>';

                html = html + '</div></div></div>';

            });

            html = html + '</div>';
        }
        return html;

    }



    async function getVendorDetails() {

        vendorDetailsRef.get().then(async function(vendorSnapshots) {

            var vendorDetails = vendorSnapshots.docs[0].data();
            
            // Check if vendor is in user's location
            if (!isVendorInUserLocation(vendorDetails)) {
                // Vendor is not in user's location - hide products and show message
                $("#product-list").html('<div class="col-12 text-center py-5"><h5><?php echo e(trans("lang.vendor_not_available_in_your_location")); ?></h5><p><?php echo e(trans("lang.please_change_your_location")); ?></p></div>');
                $("#category-list").html('');
                jQuery("#overlay").hide();
                return;
            }

            $("#vendor_title").append(vendorDetails.title);

            $("#vendor_address").append(vendorDetails.location);

            $("#vendor_shop_status").html("<?php echo e(trans('lang.closed')); ?>");

            $("#vendor_shop_status").addClass('close');

            var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

            var currentdate = new Date();

            var currentDay = days[currentdate.getDay()];

            var hour = currentdate.getHours();

            var minute = currentdate.getMinutes();

            if (hour < 10) {

                hour = '0' + hour

            }

            if (minute < 10) {

                minute = '0' + minute

            }

            var currentHours = hour + ':' + minute;

            if (vendorDetails.hasOwnProperty('workingHours')) {

                for (i = 0; i < vendorDetails.workingHours.length; i++) {

                    var day = vendorDetails.workingHours[i]['day'];

                    if (vendorDetails.workingHours[i]['day'] == currentDay) {

                        if (vendorDetails.workingHours[i]['timeslot'].length != 0) {

                            for (j = 0; j < vendorDetails.workingHours[i]['timeslot'].length; j++) {

                                var timeslot = vendorDetails.workingHours[i]['timeslot'][j];

                                var TimeslotHourVar = {

                                    'from': timeslot[`from`],

                                    'to': timeslot[`to`],

                                    'closeingType': timeslot[`closeingType`]

                                };

                                var [h, m] = timeslot[`from`].split(":");

                                var from = ((h % 12 ? h % 12 : 12) + ":" + m, h >= 12 ? 'PM' : 'AM');

                                var [h2, m2] = timeslot[`to`].split(":");

                                var to = ((h2 % 12 ? h2 % 12 : 12) + ":" + m2, h2 >= 12 ? 'PM' : 'AM');

                                $('#vendor_open_time').append(timeslot[`from`] + ' ' + from + ' - ' + timeslot[`to`] + ' ' + to + '<br/><span class="margine" style="margin-right: 65px;"></span>');

                                if (currentHours >= timeslot[`from`] && currentHours <= timeslot[`to`]) {

                                    $("#vendor_shop_status").html("<?php echo e(trans('lang.open')); ?>");

                                    $("#vendor_shop_status").removeClass('close');

                                    $("#vendor_shop_status").addClass('open');

                                }

                            }

                        } else {

                            $('.time').html('');

                        }

                    }

                }

            }

            if (vendorDetails.hasOwnProperty('reststatus') && vendorDetails.reststatus == true) {

                vendorOpen = vendorDetails.reststatus;

            } else {}

            var newdeliveryCharge = [];

            try {

                if (deliveryChargemain.vendor_can_modify) {

                    if (vendorDetails.deliveryCharge) {

                        if (vendorDetails.deliveryCharge.delivery_charges_per_km && vendorDetails.deliveryCharge.minimum_delivery_charges && vendorDetails.deliveryCharge.minimum_delivery_charges_within_km) {

                            deliveryChargemain = vendorDetails.deliveryCharge;

                        }

                    }

                }

            } catch (error) {}

            if (vendorDetails.hasOwnProperty('specialDiscount')) {

                specialOfferVendor = vendorDetails.specialDiscount;

            }

            var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

            var currentdate = new Date();

            var currentDay = days[currentdate.getDay()];

            var currentTime = currentdate.getHours() + ":" + currentdate.getMinutes();

            if (enableSpecialOffer) {

                if (specialOfferVendor.length != 0) {

                    for (i = 0; i < specialOfferVendor.length; i++) {

                        if (specialOfferVendor[i]['day'] == currentDay) {

                            if (specialOfferVendor[i]['timeslot'].length > 0) {

                                for (j = 0; j < specialOfferVendor[i]['timeslot'].length; j++) {

                                    if (currentTime >= specialOfferVendor[i]['timeslot'][j]['from'] && currentTime <= specialOfferVendor[i]['timeslot'][j]['to']) {

                                        if (specialOfferVendor[i]['timeslot'][j]['discount_type'] == 'delivery') {

                                            specialOfferForHour = [];

                                            specialOfferForHour.push(specialOfferVendor[i]['timeslot'][j]);

                                        }

                                    }

                                }

                            }

                        }

                    }

                }

            }

            setCookie('specialOfferForHourMain', JSON.stringify(specialOfferForHour), 365);

            $(".vendor_location_btn").attr("href", "http://maps.google.com?q=" + vendorDetails.latitude + "," + vendorDetails.longitude);

            if (vendorDetails.hasOwnProperty('photo') && vendorDetails.photo != '') {

                photoss = vendorDetails.photo;

            } else {

                photoss = placeholderImageSrc

            }

            $("#vendor_image").html('<img class="restaurant-pic" src="' + photoss + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'">');

            if (vendorDetails.photos.length > 0) {

                var gallery = '<div class="row">';

                gallery += '<div class="col-md-6">';

                gallery += '<div class="resturant-banner-right-block">';

                if (vendorDetails.photos[0]) {

                    gallery += '<img src="' + vendorDetails.photos[0] + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" class="banner-small-pic">';

                } else {

                    gallery += '<img src="' + placeholderImageSrc + '" class="banner-small-pic">';

                }

                gallery += '</div>';

                gallery += '<div class="resturant-banner-right-block">';

                if (vendorDetails.photos[1]) {

                    gallery += '<img src="' + vendorDetails.photos[1] + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" class="banner-small-pic">';

                } else {

                    gallery += '<img src="' + placeholderImageSrc + '" class="banner-small-pic">';

                }

                gallery += '</div>';

                gallery += '</div>';

                gallery += '<div class="col-md-6">';

                gallery += '<div class="resturant-banner-right-block view-all-blc">';

                gallery += '<span class="see-gallary"><?php echo e(trans('lang.see_gallary')); ?></span>';

                if (vendorDetails.photos[2]) {

                    gallery += '<img src="' + vendorDetails.photos[2] + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" class="banner-small-pic">';

                } else {

                    gallery += '<img src="' + placeholderImageSrc + '" class="banner-small-pic">';

                }

                gallery += '</div>';

                gallery += '</div>';

                gallery += '</div>';

                $("#vendor-gallery").html(gallery);

                var popup_gallery = '';

                $.each(vendorDetails.photos, function(key, value) {

                    popup_gallery += '<a href="' + value + '"><img src="' + value + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'"></a>';

                });

                $("#popup-gallary").html(popup_gallery);

                $('#popup-gallary').slickLightbox();

                $('.see-gallary').click(function() {

                    $('#popup-gallary a:first-child').click();

                });

            } else {

                var gallery = '<div class="row">';

                gallery += '<div class="col-md-6">';

                gallery += '<div class="resturant-banner-right-block">';

                gallery += '<img src="' + placeholderImageSrc + '" class="banner-small-pic">';

                gallery += '</div>';

                gallery += '<div class="resturant-banner-right-block">';

                gallery += '<img src="' + placeholderImageSrc + '" class="banner-small-pic">';

                gallery += '</div>';

                gallery += '</div>';

                gallery += '<div class="col-md-6">';

                gallery += '<div class="resturant-banner-right-block view-all-blc">';

                gallery += '<img src="' + placeholderImageSrc + '" class="banner-small-pic">';

                gallery += '</div>';

                gallery += '</div>';

                gallery += '</div>';

                $("#vendor-gallery").html(gallery);

            }

            if (vendorDetails.hasOwnProperty('reviewsCount') && vendorDetails.reviewsCount != '') {

                rating = vendorDetails.reviewsSum / vendorDetails.reviewsCount;

                rating = Math.round(rating * 10) / 10;

                rating = parseInt(rating);

                reviewsCount = vendorDetails.reviewsCount;

            } else {

                reviewsCount = 0;

                rating = 0;

            }

            var html_rating = '<ul class="rating" data-rating="' + rating + '">';

            html_rating = html_rating + '<li class="rating__item"></li>';

            html_rating = html_rating + '<li class="rating__item"></li>';

            html_rating = html_rating + '<li class="rating__item"></li>';

            html_rating = html_rating + '<li class="rating__item"></li>';

            html_rating = html_rating + '<li class="rating__item"></li>';

            let review_text = reviewsCount > 1 ? 'Reviews' : 'Review';

            html_rating = html_rating + '</ul><p class="label-rating ml-2 small" id="vendor_reviews">(' + reviewsCount + ' ' + review_text + ')</p>';

            $("#vendor_ratings").html(html_rating);

            if ($("#vendor_place").length) {

                $("#vendor_name_place").html(vendorDetails.title);

                if (vendorDetails.photo) {

                    $("#vendor_image_place").attr('src', vendorDetails.photo);

                    setTimeout(function() {

                        $("#vendor_image_place").show()

                    }, 1000);

                } else {

                    $("#vendor_image_place").remove();

                }

                $("#vendor_location_place").html('<i class="feather-map-pin"></i>' + vendorDetails.location);

                $("#vendor_place").show();

            }

        })

    }



    /* Add to favorite Code start*/

    $(document).ready(async function() {





        var store_id = vendorID;

        if (user_uuid != undefined) {

            var user_id = user_uuid;

        } else {

            var user_id = '';

        }

        database.collection('favorite_vendor').where('store_id', '==', store_id).where('user_id', '==', user_id).get().then(async function(favoritevendorsnapshots) {

            if (favoritevendorsnapshots.docs.length > 0) {

                $('.addToFavorite').html('<i class="font-weight-bold fa fa-heart" style="color:red"></i>');

            } else {

                $('.addToFavorite').html('<i class="font-weight-bold feather-heart" ></i>');

            }

        });

        $('.loginAlert').on('click', function() {

            Swal.fire({

                text: "<?php echo e(trans('lang.login_to_add_favorite')); ?>",

                icon: "error"

            });

        });

        $('.addToFavorite').on('click', function() {

            var section_id = "<?php echo @$_COOKIE['section_id']; ?>";

            if (section_id != undefined) {

                var section_id = section_id;

            } else {

                var section_id = '';

            }

            var user_id = user_uuid;

            database.collection('favorite_vendor').where('store_id', '==', store_id).where('user_id', '==', user_id).get().then(async function(favoritevendorsnapshots) {

                if (favoritevendorsnapshots.docs.length > 0) {

                    var id = favoritevendorsnapshots.docs[0].id;

                    database.collection('favorite_vendor').doc(id).delete().then(function() {

                        $('.addToFavorite').html('<i class="font-weight-bold feather-heart" ></i>');

                    });

                } else {

                    var id = "<?php echo uniqid(); ?>";

                    database.collection('favorite_vendor').doc(id).set({

                        'store_id': store_id,

                        'section_id': section_id,

                        'user_id': user_id,

                        'id': id

                    }).then(function(result) {

                        $('.addToFavorite').html('<i class="font-weight-bold fa fa-heart" style="color:red"></i>');

                    });

                }

            });

        });

        /* Add to favorite Code End*/

    });
</script>
<?php /**PATH E:\ondemand_grihasth-master\ondemand_grihasth-master\resources\views/vendor/vendor.blade.php ENDPATH**/ ?>