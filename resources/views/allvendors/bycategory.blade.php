@include('layouts.app')

@include('layouts.header')

<div class="st-brands-page pt-5 category-listing-page category">

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

                <div id="store-list"></div>

            </div>

        </div>

    </div>

</div>

@include('layouts.footer')

<script type="text/javascript">

    var id = '<?php echo $id; ?>';

    var idRef = database.collection('vendor_categories').doc(id);

    var catsRef = database.collection('vendor_categories').where('section_id', '==', section_id).where("publish", "==", true);
    var placeholderImageRef = database.collection('settings').doc('placeHolderImage');

    var placeholderImageSrc = '';

    var inValidVendors = [];

    var locationRadiusKm = 50; // Default to 50km if not configured (more lenient)
    var radiusUnit = 'Km';
    var radiusUnitRef = database.collection('settings').doc('DriverNearBy');
    var vendorNearByRef = database.collection('sections').doc(section_id);

    placeholderImageRef.get().then(async function (placeholderImageSnapshots) {

        var placeHolderImageData = placeholderImageSnapshots.data();

        placeholderImageSrc = placeHolderImageData.image;

    })

    idRef.get().then(async function (idRefSnapshots) {

        var idRefData = idRefSnapshots.data();

        $("#title").text(idRefData.title + ' ' + "{{trans('lang.stores')}}");

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
        inValidVendors = await getInvaidUserIds();
        
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
        
        getCategories();

        $(document).on("click", ".category-item", function () {

            if (!$(this).hasClass('active')) {

                $(this).addClass('active').siblings().removeClass('active');

                getStores($(this).data('category-id'));

            }

        });

    });



    async function getCategories() {

        catsRef.get().then(async function (snapshots) {

            if (snapshots != undefined) {

                var html = '';

                html = buildCategoryHTML(snapshots);

                if (html != '') {

                    var append_list = document.getElementById('category-list');

                    append_list.innerHTML = html;

                    var category_id = $('#category-list .category-item').first().addClass('active').data('category-id');

                    if (category_id) {

                        getStores(category_id);

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

            html = html + '<li class="category-item" data-category-id="' + val.id + '">';

            html = html + '<a href="javascript:void(0)"><span><img src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'"></span>' + val.title + '</a>';

            html = html + '</li>';

        });

        html = html + '</ul>';

        return html;

    }



    async function getStores(id) {

        jQuery("#overlay").show();

        var store_list = document.getElementById('store-list');

        store_list.innerHTML = '';

        var html = '';

        var storesRef = database.collection('vendors').where('categoryID', '==', id);

        var idRef = database.collection('vendor_categories').doc(id);

        idRef.get().then(async function (idRefSnapshots) {

            var idRefData = idRefSnapshots.data();

            $("#title").text(idRefData.title + ' ' + "{{trans('lang.stores')}}");

        })

        storesRef.get().then(async function (snapshots) {

            html = buildStoresHTML(snapshots);

            if (html != '') {

                store_list.innerHTML = html;

                jQuery("#overlay").hide();

            }

        });

    }



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

    function buildStoresHTML(snapshots) {

        var html = '';

        var alldata = [];

        snapshots.docs.forEach((listval) => {

            var datas = listval.data();

            datas.id = listval.id;
            // Filter by location - only show vendors in user's location
            if(!inValidVendors.includes(datas.author) && isVendorInUserLocation(datas)) { 
                alldata.push(datas);
            }
        });

        var count = 0;

        var popularFoodCount = 0;

        html = html + '<div class="row">';

        if (alldata.length) {

            alldata.forEach((listval) => {

                var val = listval;

                var rating = 0;

                var reviewsCount = 0;

                if (val.hasOwnProperty('reviewsSum') && val.reviewsSum != 0 && val.hasOwnProperty('reviewsCount') && val.reviewsCount != 0) {

                    rating = (val.reviewsSum / val.reviewsCount);

                    rating = Math.round(rating * 10) / 10;

                    reviewsCount = val.reviewsCount;

                }

                // Check store status using working hours
                var status = 'Closed';
                var statusclass = "closed";
                
                // First check if reststatus is explicitly set to false
                if (val.hasOwnProperty('reststatus') && val.reststatus === false) {
                    status = 'Closed';
                    statusclass = "closed";
                } else if (val.hasOwnProperty('workingHours') && val.workingHours) {
                    // Use actual working hours to determine status
                    if (isStoreCurrentlyOpen(val.workingHours)) {
                        status = 'Open';
                        statusclass = "open";
                    } else {
                        status = 'Closed';
                        statusclass = "closed";
                    }
                } else if (val.hasOwnProperty('reststatus') && val.reststatus) {
                    // Fallback to reststatus if no working hours
                    status = 'Open';
                    statusclass = "open";
                }

                html = html + '<div class="col-md-4 pb-3 product-list"><div class="list-card position-relative"><div class="list-card-image position-relative">';

                if (val.photo) {

                    photo = val.photo;

                } else {

                    photo = placeholderImageSrc;

                }

                var view_vendor_details = "{{ route('vendor', ':id')}}";

                view_vendor_details = view_vendor_details.replace(':id', val.id);
                html = html + '<a href="' + view_vendor_details + '"><img alt="#" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" class="img-fluid item-img w-100"></a>';
                
                // Add store status overlay on top-right of image
                html = html + '<div class="store-status-overlay"><span class="status-indicator ' + statusclass + '"></span><span class="status-text ' + statusclass + '">' + status + '</span></div>';
                
                html = html + '</div><div class="py-2 position-relative"><div class="list-card-body"><h6 class="mb-1"><a href="' + view_vendor_details + '" class="text-black">' + val.title + '</a></h6><h6>' + val.location + '</h6>';

                html = html + '<div class="star position-relative mt-3"><span class="badge badge-success"><i class="feather-star"></i>' + rating + ' (' + reviewsCount + ')</span></div>';

                html = html + '</div>';

                html = html + '</div></div></div>';

            });

        } else {

            html = html + "<h5>{{trans('lang.no_results')}}</h5>";

        }

        html = html + '</div>';

        return html;

    }

</script>

@include('layouts.nav')