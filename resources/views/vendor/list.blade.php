@include('layouts.app')

@include('layouts.header')

<div class="d-none">

    <div class="bg-primary p-3 d-flex align-items-center">

        <a class="toggle togglew toggle-2" href="#"><span></span></a>

        <h4 class="font-weight-bold m-0 text-white">{{trans('lang.category')}}</h4>

    </div>

</div>

<div class="siddhi-trending">

    <div class="container">

        <div class="most_popular py-5">

            <div class="d-flex align-items-center mb-4">

                <h3 class="font-weight-bold text-dark mb-0" id="category_name"></h3>

            </div>

            <div id="trendingList"></div>

        </div>

    </div>

</div>

@include('layouts.footer')

@include('layouts.nav')

<script src="{{ asset('js/geofirestore.js') }}"></script>

<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>

<script type="text/javascript">

    var geoFirestore = new GeoFirestore(firestore);

    var categoryId = "<?php echo $id; ?>";

    var foodCategoriesref = database.collection('vendor_categories').where('id', '==', categoryId);

    var vendorRef = database.collection('vendors').where('categoryID', "==", categoryId);

    var placeholderImageRef = database.collection('settings').doc('placeHolderImage');

    var placeholderImageSrc = '';

    var inValidVendors = [];

    placeholderImageRef.get().then(async function (placeholderImageSnapshots) {

        var placeHolderImageData = placeholderImageSnapshots.data();

        placeholderImageSrc = placeHolderImageData.image;

    })

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

        foodCategoriesref.get().then(async function (foodCategories) {

            foodCategories.docs.forEach((listval) => {

                var datas = listval.data();

                $("#category_name").text(datas.title);

            });

        });

        jQuery("#overlay").show();

        vendorRef.get().then(async function (snapshots) {

            if (snapshots != undefined) {

                var html = '';

                html = buildHTML(snapshots);

                if (html != '') {

                    var append_list = document.getElementById('trendingList');

                    append_list.innerHTML = html;

                    jQuery("#overlay").hide();

                }

            }

        });

    })

    var append_categories = '';

    var trendingStoreRef = '';



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

    function buildHTML(nearestRestauantSnapshot) {

        var html = '';

        var alldata = [];

        nearestRestauantSnapshot.docs.forEach((listval) => {

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

        alldata.forEach((listval) => {

            var val = listval;

            var rating = 0;

            var reviewsCount = 0;

            if (val.hasOwnProperty('reviewsSum') && val.reviewsSum != 0 && val.hasOwnProperty('reviewsCount') && val.reviewsCount != 0) {

                rating = (val.reviewsSum / val.reviewsCount);

                rating = Math.round(rating * 10) / 10;

                reviewsCount = val.reviewsCount;

            }

            var status = 'Closed';

            var statusclass = "closed";

            if (val.hasOwnProperty('reststatus') && val.reststatus) {

                status = 'Open';

                statusclass = "open";

            }

            var vendor_id_single = val.id;

            var view_vendor_details = "{{ route('vendor',':id')}}";

            view_vendor_details = view_vendor_details.replace(':id', vendor_id_single);

            count++;

            html = html + '<div class="col-md-3 pb-3"><div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm"><div class="list-card-image">';

            if (val.photo) {

                photo = val.photo;

            } else {

                photo = placeholderImageSrc;

            }

            html = html + '<div class="member-plan position-absolute"><span class="badge badge-dark ' + statusclass + '">' + status + '</span></div><a href="' + view_vendor_details + '"><img alt="#" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" class="img-fluid item-img w-100"></a></div><div class="p-3 position-relative"><div class="list-card-body"><h6 class="mb-1"><a href="' + view_vendor_details + '" class="text-black">' + val.title + '</a></h6>';

            html = html + '<p class="text-gray mb-1 small"><span class="fa fa-map-marker"></span> ' + val.location + '</p>';

            if (rating > 0) {

                html = html + '<div class="star position-relative mt-3"><span class="badge badge-success"><i class="feather-star"></i>' + rating + ' (' + reviewsCount + '+)</span></div>';

            }

            html = html + '</div>';

            html = html + '</div></div></div>';

        });

        html = html + '</div>';

        return html;

    }

</script>