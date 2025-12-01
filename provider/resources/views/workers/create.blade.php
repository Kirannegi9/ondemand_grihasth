@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">

        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.worker_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>


                <li class="breadcrumb-item"><a href="{!! route('workers') !!}">{{trans('lang.worker_table')}}</a>
                </li>

                <li class="breadcrumb-item">{{trans('lang.worker_create')}}</li>
            </ol>
        </div>
    </div>

    <div class="card-body">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
            {{trans('lang.processing')}}
        </div>
        <div class="error_top"></div>
        <div class="row vendor_payout_create">
            <div class="vendor_payout_create-inner">
                <fieldset>
                    <legend>{{trans('lang.worker_create')}}</legend>

                    <div class="form-group row width-50">
                        <input type="hidden" class="form-control author_profile">
                        <label class="col-3 control-label">{{trans('lang.first_name')}}</label>
                        <div class="col-7">
                            <input type="text" class="form-control first_name">
                            <div class="form-text text-muted" min="0">
                                {{ trans("lang.user_first_name_help") }}
                            </div>
                        </div>
                    </div>


                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{ trans('lang.last_name')}}</label>
                        <div class="col-7">
                            <input type="text" class="form-control last_name">
                            <div class="form-text text-muted" min="0">
                                {{ trans("lang.user_last_name_help") }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.email')}}</label>
                        <div class="col-7">
                            <input type="text" class="form-control email">
                            <div class="form-text text-muted">
                                {{ trans("lang.user_email_help") }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.password')}}</label>
                        <div class="col-7">
                            <input type="password" class="form-control password">
                            <div class="form-text text-muted">
                                {{ trans("lang.user_password_help") }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.user_phone')}}</label>
                        <div class="col-7">
                            <input type="text" class="form-control phone"
                                onkeypress="return chkAlphabets2(event,'error1')">
                            <div id="error1" class="err"></div>
                            <div class="form-text text-muted w-50">
                                {{ trans("lang.user_phone_help") }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.salary')}}</label>
                        <div class="col-7">
                            <input type="number" class="form-control salary">
                            <div class="form-text text-muted">
                                {{ trans("lang.user_salary_help") }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.address')}}</label>
                        <div class="col-7">
                            <input type="text" class="form-control address" id="address" autocomplete="on">

                        </div>
                    </div>


                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.user_profile_picture')}}</label>
                        <input type="file" onChange="handleFileSelectowner(event)" class="col-7">
                        <div id="uploding_image_owner"></div>
                        <div class="uploaded_image_owner" style="display:none;"><img id="uploaded_image_owner" src=""
                                width="150px" height="150px;"></div>
                    </div>
                    <div class="form-group row width-50">
                        <div class="form-check">
                            <input type="checkbox" class="item_publish" id="item_publish">
                            <label class="col-3 control-label" for="item_publish">{{trans('lang.active')}}</label>
                        </div>
                    </div>

                </fieldset>
            </div>
        </div>
    </div>

    <div class="form-group col-12 text-center btm-btn">
        <button type="button" class="btn btn-primary save_worker_btn"><i class="fa fa-save"></i>
            {{trans('lang.save')}}
        </button>
        <a href="{!! route('workers') !!}" class="btn btn-default"><i
                class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
    </div>
</div>
</div>
</div>

@endsection

@section('scripts')

<script>

    var database = firebase.firestore();

    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;
    var createdAt = firebase.firestore.FieldValue.serverTimestamp();
    var workerImagesCount = 0;
    var ownerphoto = '';
    var ownerFileName = '';

    var photo = "";
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function (snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        if (currencyData.decimal_degits) {
            decimal_degits = currencyData.decimal_degits;
        }
    });

    $(document).ready(function () {

        $(".save_worker_btn").click(async function () {

            var id = database.collection("tmp").doc().id;
            var providerId = "<?php echo $id; ?>";
            var userFirstName = $(".first_name").val();
            var userLastName = $(".last_name").val();
            var email = $(".email").val();
            var password = $(".password").val();
            var userPhone = $(".phone").val();
            var salary = $(".salary").val();
            var address = $(".address").val();
            var longitude = parseFloat($('.address').attr('lng'));
            var latitude = parseFloat($('.address').attr('lat'));
            var itemPublish = $(".item_publish").is(":checked");
            var authorProfilePic = $('.author_profile').val();

            if (userFirstName == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_worker_first_name_error')}}</p>");
                window.scrollTo(0, 0);
            } else if (userLastName == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_worker_last_name_error')}}</p>");
                window.scrollTo(0, 0);
            }else if (email == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_worker_email_error')}}</p>");
                window.scrollTo(0, 0);
            }else if (password == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_worker_password_error')}}</p>");
                window.scrollTo(0, 0);
            }else if (userPhone == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_worker_userphone_error')}}</p>");
                window.scrollTo(0, 0);
            }else if (salary == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_worker_salary_error')}}</p>");
                window.scrollTo(0, 0);
            } else if ( isNaN(latitude) || isNaN(longitude)) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.service_select_address_error')}}</p>");
                window.scrollTo(0, 0);

            } else {

                jQuery("#data-table_processing").show();

                firebase.auth().createUserWithEmailAndPassword(email, password).then(async function (firebaseUser) {

                    user_id = firebaseUser.user.uid;
                    
                    await storeImageData().then(async (IMG) => {
                        
                            geoFirestore.collection('providers_workers').doc(user_id).set({
                                'firstName': userFirstName,
                                'lastName': userLastName,
                                'email': email,
                                'phoneNumber': userPhone,
                                'email': email,
                                'salary': salary,
                                "address": address,
                                'profilePictureURL': IMG,
                                'active': itemPublish,
                                'reviewsCount': 0,
                                'id': user_id,
                                'createdAt': createdAt,
                                'providerId': providerId,
                                'reviewsSum': 0,
                                'latitude': latitude,
                                'longitude': longitude,
                                'online': false,
                                coordinates: new firebase.firestore.GeoPoint(latitude, longitude),

                            }).then(function (result) {
                                window.location.href = '{{ route("workers")}}';
                            });
                        
                    }).catch(err => {
                        jQuery("#data-table_processing").hide();
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>" + err + "</p>");
                        window.scrollTo(0, 0);
                    })

                }).catch(err => {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>" + err + "</p>");
                    window.scrollTo(0, 0);
                });
            }
        })

    })

    function initialize(id) {

        var input = document.getElementById(id);
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            var placeaddress = autocomplete.getPlace().address_components;
            var city = place.address_components.filter(f => JSON.stringify(f.types) === JSON.stringify(['locality', 'political']))[0].long_name;
            var state = place.address_components.filter(f => JSON.stringify(f.types) === JSON.stringify(['administrative_area_level_1', 'political']))[0].long_name;
            var country = place.address_components.filter(f => JSON.stringify(f.types) === JSON.stringify(['country', 'political']))[0].long_name;
            $("#" + id).val(place.formatted_address).attr('lat', place.geometry.location.lat()).attr('lng', place.geometry.location.lng()).attr('city', city).attr('state', state).attr('country', country)
        });
    }

    $(document).on("click", "#address", function () {
        var id = $(this).attr('id');
        initialize(id);
    });
    
    var storageRef = firebase.storage().ref('images');
    
    function handleFileSelectowner(evt) {
        var f = evt.target.files[0];
        var reader = new FileReader();
        reader.onload = (function (theFile) {
            return function (e) {

                var filePayload = e.target.result;
                var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                var val = f.name;
                var ext = val.split('.')[1];
                var docName = val.split('fakepath')[1];
                var filename = (f.name).replace(/C:\\fakepath\\/i, '')

                var timestamp = Number(new Date());
                var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                ownerphoto = filePayload;
                ownerFileName = filename;
                $("#uploaded_image_owner").attr('src', ownerphoto);
                $(".uploaded_image_owner").show();
                var uploadTask = storageRef.child(filename).put(theFile);

            };
        })(f);
        reader.readAsDataURL(f);
    }

    async function storeImageData() {

        newPhoto = '';

        try {
            if (ownerphoto != '') {
                ownerphoto = ownerphoto.replace(/^data:image\/[a-z]+;base64,/, "")
                var uploadTask = await storageRef.child(ownerFileName).putString(ownerphoto, 'base64', { contentType: 'image/jpg' });
                var downloadURL = await uploadTask.ref.getDownloadURL();
                newPhoto = downloadURL;
                ownerphoto = downloadURL;
            }

        } catch (error) {
            console.log("ERR ===", error);
        }
        return newPhoto;
    }

    function chkAlphabets(event, msg) {
        if (!(event.which >= 97 && event.which <= 122) && !(event.which >= 65 && event.which <= 90)) {
            document.getElementById(msg).innerHTML = "Accept only Alphabets";
            return false;
        } else {
            document.getElementById(msg).innerHTML = "";
            return true;
        }
    }

    function chkAlphabets2(event, msg) {
        if (!(event.which >= 48 && event.which <= 57)
        ) {
            document.getElementById(msg).innerHTML = "Accept only Number";
            return false;
        } else {
            document.getElementById(msg).innerHTML = "";
            return true;
        }
    }

    function chkAlphabets3(event, msg) {
        if (!((event.which >= 48 && event.which <= 57) || (event.which >= 97 && event.which <= 122))) {
            document.getElementById(msg).innerHTML = "Special characters not accepted ";
            return false;
        } else {
            document.getElementById(msg).innerHTML = "";
            return true;
        }
    }
</script>

@endsection