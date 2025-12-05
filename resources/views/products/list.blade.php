@include('layouts.app')
@include('layouts.header')
<div class="st-brands-page pt-5 category-listing-page <?php echo $type; ?>">
    <div class="container">
        <div class="d-flex align-items-center mb-3 page-title">
            <h3 class="font-weight-bold text-dark" id="title"></h3>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div id="brand-list"></div>
                <div id="category-list"></div>
            </div>
            <div class="col-md-9">
                <div id="product-list"></div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
<script type="text/javascript">
    var type = '<?php echo $type; ?>';
    var id = '<?php echo $id; ?>';
    if (type == "category") {
        var idRef = database.collection('vendor_categories').doc(id);
        var catsRef = database.collection('vendor_categories').where('section_id', '==', section_id).where("publish", "==", true);
    } else {
        var idRef = database.collection('brands').doc(id);
        var brandRef = database.collection('brands').where('sectionId', '==', section_id).where("is_publish", "==", true);
    }
    var placeholderImageRef = database.collection('settings').doc('placeHolderImage');
    var placeholderImageSrc = '';
    placeholderImageRef.get().then(async function (placeholderImageSnapshots) {
        var placeHolderImageData = placeholderImageSnapshots.data();
        placeholderImageSrc = placeHolderImageData.image;
    })
    idRef.get().then(async function (idRefSnapshots) {
        var idRefData = idRefSnapshots.data();
        $("#title").text(idRefData.title + ' ' + "{{trans('lang.products')}}");
    })
    var decimal_degits = 0;
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function (snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
    });
    jQuery("#overlay").show();
    $(document).ready(async function () {
        priceData=await fetchVendorPriceData();

        // Load radius configuration before filtering
        await Promise.all([
            radiusUnitRef.get().then(async function(radiusSnapshots) {
                var radiusUnitData = radiusSnapshots.data();
                if (radiusUnitData && radiusUnitData.distanceType) {
                    radiusUnit = radiusUnitData.distanceType;
                }
            }),
            vendorNearByRef.get().then(async function(vendorNearByRefSnapshots) {
                var vendorNearByRefData = vendorNearByRefSnapshots.data();
                if (vendorNearByRefData && vendorNearByRefData.hasOwnProperty('nearByRadius') && vendorNearByRefData.nearByRadius != '') {
                    locationRadiusKm = parseInt(vendorNearByRefData.nearByRadius);
                    if (radiusUnit == 'Miles') {
                        locationRadiusKm = parseInt(locationRadiusKm * 1.60934);
                    }
                }
            })
        ]);

        if (type == "category") {
            getCategories();
        } else {
            getBrands(id);
        }
        $(document).on("click", ".category-item", function () {
            if (!$(this).hasClass('active')) {
                $(this).addClass('active').siblings().removeClass('active');
                getProducts(type, $(this).data('category-id'));
            }
        });
        $(document).on("click", ".brand-item", function () {
            if (!$(this).hasClass('active')) {
                $(this).addClass('active').siblings().removeClass('active');
                getProducts(type, $(this).data('brand-id'));
            }
        });
    })

    async function getCategories() {
        catsRef.get().then(async function (snapshots) {
            if (snapshots != undefined) {
                var html = '';
                html = buildCategoryHTML(snapshots);
                if (html != '') {
                    var append_list = document.getElementById('category-list');
                    append_list.innerHTML = html;
                    var category_id = $('#category-list .active').data('category-id');
                    if (category_id) {
                        getProducts(type, category_id);
                        jQuery("#overlay").hide();
                    }
                }
            }
        });
    }

    function buildCategoryHTML(snapshots) {
        var html = '';
        var alldata = [];
        snapshots.docs.forEach((listval) => {
            var datas = listval.data();
            datas.id = listval.id;
            alldata.push(datas);
        });
        html = html + '<div class="vandor-sidebar">';
        html = html + '<h3>{{trans("lang.categories")}}</h3>';
        html = html + '<ul class="vandorcat-list">';
        alldata.forEach((listval) => {
            var val = listval;
            if (val.photo) {
                photo = val.photo;
            } else {
                photo = placeholderImageSrc;
            }
            if (id == val.id) {
                html = html + '<li class="category-item active" data-category-id="' + val.id + '">';
            } else {
                html = html + '<li class="category-item" data-category-id="' + val.id + '">';
            }
            html = html + '<a href="javascript:void(0)"><span><img src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" ></span>' + val.title + '</a>';
            html = html + '</li>';
        });
        html = html + '</ul>';
        return html;
    }

    async function getBrands(id) {
        brandRef.get().then(async function (snapshots) {
            if (snapshots != undefined) {
                var html = '';
                html = buildBrandsHTML(snapshots);
                jQuery("#overlay").hide();
                if (html != '') {
                    var append_list = document.getElementById('brand-list');
                    append_list.innerHTML = html;
                    brandId = $('#brand-list .brand-item').data('brand-id');
                    var brand_id = $('#brand-list .active').data('brand-id');
                    if (brand_id) {
                        getProducts(type, brand_id);
                        jQuery("#overlay").hide();
                    }
                }
            }
        });
    }

    function buildBrandsHTML(snapshots) {
        var html = '';
        var alldata = [];
        snapshots.docs.forEach((listval) => {
            var datas = listval.data();
            datas.id = listval.id;
            alldata.push(datas);
        });
        html = html + '<div class="vandor-sidebar">';
        html = html + '<h3>{{trans("lang.brands")}}</h3>';
        html = html + '<ul class="vandorcat-list">';
        alldata.forEach((listval) => {
            var val = listval;
            if (val.photo) {
                photo = val.photo;
            } else {
                photo = placeholderImageSrc;
            }
            if (id == val.id) {
                html = html + '<li class="brand-item active" data-brand-id="' + val.id + '">';
            } else {
                html = html + '<li class="brand-item" data-brand-id="' + val.id + '">';
            }
            html = html + '<li class="brand-item" data-brand-id="' + val.id + '">';
            html = html + '<a href="javascript:void(0)"><span><img src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'"></span>' + val.title + '</a>';
            html = html + '</li>';
        });
        html = html + '</ul>';
        html = html + '</div>';
        return html;
    }

    async function getProducts(type, id) {
        jQuery("#overlay").show();
        var product_list = document.getElementById('product-list');
        product_list.innerHTML = '';
        var html = '';
        if (type == "category") {
            var productsRef = database.collection('vendor_products').where('categoryID', '==', id).where("publish", "==", true);
            var idRef = database.collection('vendor_categories').doc(id);
        } else {
            var productsRef = database.collection('vendor_products').where('brandID', '==', id).where("publish", "==", true);
            var idRef = database.collection('brands').doc(id);
        }
        idRef.get().then(async function (idRefSnapshots) {
            var idRefData = idRefSnapshots.data();
            $("#title").text(idRefData.title + ' ' + "{{trans('lang.products')}}");
        })
        productsRef.get().then(async function (snapshots) {
            html = await buildProductsHTML(snapshots);
            if (html != '') {
                product_list.innerHTML = html;
                jQuery("#overlay").hide();
            }
        });
    }

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

   async function buildProductsHTML(snapshots) {
        var html = '';
        var alldata = [];
        var groupedData = {};
        snapshots.docs.forEach((listval) => {
            var datas = listval.data();
            datas.id = listval.id;
            // Filter out unpublished products
            if (datas.publish !== true) {
                return;
            }
            if (!groupedData[datas.vendorID]) {
                groupedData[datas.vendorID] = [];
            }
            groupedData[datas.vendorID].push(datas);
        });
        
        // Filter vendors by location first
        var vendorsInLocation = [];
        for (const vendorID of Object.keys(groupedData)) {
            try {
                var vendorDoc = await database.collection('vendors').doc(vendorID).get();
                if (vendorDoc.exists) {
                    var vendorData = vendorDoc.data();
                    if (isVendorInUserLocation(vendorData)) {
                        vendorsInLocation.push(vendorID);
                    }
                }
            } catch (error) {
                console.error('Error fetching vendor:', error);
            }
        }
        
        await Promise.all(vendorsInLocation.map(async (vendorID) => {
            if (groupedData[vendorID]) {
            let products = groupedData[vendorID];
            inValidProductIds = await getUserItemLimit(vendorID);
            products = products.filter(product => !inValidProductIds.includes(product.id));
            alldata=alldata.concat(products);
            }
        }));
       
        var count = 0;
        var popularFoodCount = 0;
        if (alldata.length) {
            html = html + '<div class="row">';
            alldata.forEach((listval) => {
                var val = listval;
                var rating = 0;
                var reviewsCount = 0;
                if (val.hasOwnProperty('reviewsSum') && val.reviewsSum != 0 && val.hasOwnProperty('reviewsCount') && val.reviewsCount != 0) {
                    rating = (val.reviewsSum / val.reviewsCount);
                    rating = Math.round(rating * 10) / 10;
                    reviewsCount = val.reviewsCount;
                }
                html = html + '<div class="col-md-4 pb-3 product-list"><div class="list-card position-relative"><div class="list-card-image">';
                if (val.photo) {
                    photo = val.photo;
                } else {
                    photo = placeholderImageSrc;
                }
                var view_product_details = "{{ route('productdetail',':id')}}";
                view_product_details = view_product_details.replace(':id', val.id);
                html = html + '<a href="' + view_product_details + '"><img alt="#" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" class="img-fluid item-img w-100"></a></div><div class="py-2 position-relative"><div class="list-card-body"><h6 class="mb-1"><a href="' + view_product_details + '" class="text-black">' + val.name + '</a></h6>';
                
                var final_price=priceData[val.id];
                if (val.item_attribute && val.item_attribute.variants?.length > 0) {
                    let variantPrices = val.item_attribute.variants.map(v => v.variant_price);
                    let minPrice = Math.min(...variantPrices);
                    let maxPrice = Math.max(...variantPrices);
                    let or_price = minPrice !== maxPrice ?
                        `${getProductFormattedPrice(final_price.min)} - ${getProductFormattedPrice(final_price.max)}` :
                        getProductFormattedPrice(final_price.max);
                    html += `<span class="pro-price">${or_price}</span>`;
                } 
                else if (val.hasOwnProperty('disPrice') && val.disPrice != '' && val.disPrice != '0') {
                    var or_price = getProductFormattedPrice(parseFloat(final_price.price));
                    var dis_price = getProductFormattedPrice(parseFloat(final_price.dis_price));
                    html = html + '<span class="pro-price">' + dis_price + '  <s>' + or_price + '</s></span>';
                } else {
                    var or_price = getProductFormattedPrice(parseFloat(final_price.price));
                    html = html + '<span class="pro-price">' + or_price + '</span>'
                }

                html = html + '<div class="star position-relative mt-3"><span class="badge badge-success"><i class="feather-star"></i>' + rating + ' (' + reviewsCount + ')</span></div>';
                html = html + '</div>';
                html = html + '</div></div></div>';
            });
            html = html + '</div>';
        } else {
            html = html + "<p class='font-weight-bold text-center h5'>{{trans('lang.no_results')}}</p>";
        }
        return html;
    }
</script>
@include('layouts.nav')