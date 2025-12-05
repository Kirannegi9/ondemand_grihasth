@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">

            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.user_profile')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{!! route('dashboard') !!}">{{trans('lang.dashboard')}}</a>
                    </li>
                    <li class="breadcrumb-item active">{{trans('lang.user_profile_edit')}}</li>
                </ol>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="resttab-sec">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default"
                             style="display: none;">{{ trans('lang.processing')}}
                        </div>
                        <div class="error_top"></div>
                        <div class="row vendor_payout_create">
                            <div class="vendor_payout_create-inner">

                                <fieldset>
                                    <legend>{{trans('lang.admin_area')}}</legend>

                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.first_name')}}</label>
                                        <div class="col-7">
                                            <input type="text" class="form-control user_first_name" required
                                                   onkeypress="return chkAlphabets(event,'error1')">
                                            <div id="error1" class="err"></div>
                                            <div class="form-text text-muted">
                                                {{ trans("lang.user_first_name_help") }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.last_name')}}</label>
                                        <div class="col-7">
                                            <input type="text" class="form-control user_last_name"
                                                   onkeypress="return chkAlphabets(event,'error2')">
                                            <div id="error2" class="err"></div>
                                            <div class="form-text text-muted">
                                                {{ trans("lang.user_last_name_help") }}
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.email')}}</label>
                                        <div class="col-7">
                                            <input type="email" class="form-control user_email" required>
                                            <div class="form-text text-muted">
                                                {{ trans("lang.user_email_help") }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.user_phone')}}</label>
                                        <div class="col-7">
                                            <input type="text" class="form-control user_phone"
                                                   onkeypress="return chkAlphabets2(event,'error3')">
                                            <div id="error3" class="err"></div>
                                            <div class="form-text text-muted w-50">
                                                {{ trans("lang.user_phone_help") }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.user_profile_picture')}}</label>
                                        <div class="col-9">
                                            <input type="file" onChange="handleFileSelectowner(event)">
                                            <div id="uploding_image_owner"></div>
                                            <div class="uploaded_image_owner" style="display:none;"><img
                                                        id="uploaded_image_owner" src="" width="150px" height="150px;">
                                            </div>
                                            <div class="form-text text-muted">
                                                {{ trans("lang.vendor_image_help") }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.wallet_amount')}}</label>
                                        <div class="col-7 user_wallet">

                                        </div>
                                    </div>

                                </fieldset>

                                <fieldset id="password_section">
                                    <legend>{{trans('lang.password')}}</legend>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.old_password')}}</label>
                                        <div class="col-7">
                                            <input type="password" class="form-control user_old_password" required>
                                            <div class="form-text text-muted">
                                                {{ trans("lang.user_password_help") }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.new_password')}}</label>
                                        <div class="col-7">
                                            <input type="password" class="form-control user_new_password" required>
                                            <div class="form-text text-muted">
                                                {{ trans("lang.user_password_help") }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-12 text-center">
                                        <button type="button" class="btn btn-primary  change_user_password"><i
                                                    class="fa fa-save"></i>{{trans('lang.change_password')}}
                                        </button>
                                    </div>

                                </fieldset>
                                <fieldset>
                                    <legend>{{trans('lang.bankdetails')}}</legend>

                                    <div class="form-group row">

                                        <div class="form-group row width-100">
                                            <label class="col-4 control-label">{{
                                            trans('lang.bank_name')}}</label>
                                            <div class="col-7">
                                                <input type="text" name="bank_name" class="form-control" id="bankName">
                                            </div>
                                        </div>

                                        <div class="form-group row width-100">
                                            <label class="col-4 control-label">{{
                                            trans('lang.branch_name')}}</label>
                                            <div class="col-7">
                                                <input type="text" name="branch_name" class="form-control"
                                                       id="branchName">
                                            </div>
                                        </div>
                                        <div class="form-group row width-100">
                                            <label class="col-4 control-label">{{
                                            trans('lang.holder_name')}}</label>
                                            <div class="col-7">
                                                <input type="text" name="holer_name" class="form-control"
                                                       id="holderName">
                                            </div>
                                        </div>

                                        <div class="form-group row width-100">
                                            <label class="col-4 control-label">{{
                                            trans('lang.account_number')}}</label>
                                            <div class="col-7">
                                                <input type="text" name="account_number" class="form-control"
                                                       id="accountNumber"
                                                       onkeypress="return chkAlphabets2(event,'error5')">
                                                <div id="error5" class="err"></div>
                                            </div>
                                        </div>

                                        <div class="form-group row width-100">
                                            <label class="col-4 control-label">{{
                                            trans('lang.other_information')}}</label>
                                            <div class="col-7">
                                                <input type="text" name="other_information" class="form-control"
                                                       id="otherDetails">
                                            </div>
                                        </div>

                                    </div>
                                </fieldset>


                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary  save_vendor_btn"><i class="fa fa-save"></i>
                    {{trans('lang.save')}}
                </button>
                <a href="{!! route('dashboard') !!}" class="btn btn-default"><i
                            class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
            </div>

        </div>
    </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    <script>

        var loginType = getCookie('login_type');

        if(loginType=="email"){
            $('.user_email').attr('disabled',true);
        }
        if(loginType=="phone"){
            $('.user_phone').attr('disabled',true);
            $("#password_section").hide();
        }
        var database = firebase.firestore();
        var geoFirestore = new GeoFirestore(database);
        var photo = "";
        var vendorUserId = "<?php echo $id; ?>";
        var ref_sections = database.collection('sections');
        var placeholder = database.collection('settings').doc('placeHolderImage');
        placeholder.get().then(async function (snapshotsimage) {
            var placeholderImageData = snapshotsimage.data();
            placeholderImage = placeholderImageData.image;
        })
        var currentCurrency = '';
        var currencyAtRight = false;
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
        database.collection('users').doc(vendorUserId).get().then(async function (userSnapshots) {
            var userData = userSnapshots.data();
            vendorId = userData.vendorID;
            id = vendorId;

            $(document).ready(function () {
                jQuery("#data-table_processing").hide();

                database.collection('users').where("id", "==", vendorUserId).get().then(async function (snapshots) {
                    snapshots.docs.forEach((listval) => {
                        var user = listval.data();
                        ownerId = user.id;
                        ownerphoto = user.profilePictureURL;
                        vendorOwnerPhoto = user.profilePictureURL;
                        $(".user_first_name").val(user.firstName);
                        $(".user_last_name").val(user.lastName);
                        $(".user_email").val(user.email);
                        $(".user_phone").val(user.phoneNumber);
                        var user_wallet = user.wallet_amount;
                        if (currencyAtRight) {
                            user_wallet = parseFloat(user_wallet).toFixed(decimal_degits) + "" + currentCurrency;
                        } else {
                            user_wallet = currentCurrency + "" + parseFloat(user_wallet).toFixed(decimal_degits);
                        }
                        $(".user_wallet").text(user_wallet);

                        if (user.profilePictureURL != '') {
                            $("#uploaded_image_owner").attr('src', user.profilePictureURL);
                        } else {

                            $("#uploaded_image_owner").attr('src', placeholderImage);
                        }

                        $(".uploaded_image_owner").show();

                        if (user.userBankDetails) {
                            if (user.userBankDetails.bankName != undefined) {
                                $("#bankName").val(user.userBankDetails.bankName);
                            }
                            if (user.userBankDetails.branchName != undefined) {
                                $("#branchName").val(user.userBankDetails.branchName);
                            }
                            if (user.userBankDetails.holderName != undefined) {
                                $("#holderName").val(user.userBankDetails.holderName);
                            }
                            if (user.userBankDetails.accountNumber != undefined) {
                                $("#accountNumber").val(user.userBankDetails.accountNumber);
                            }
                            if (user.userBankDetails.otherDetails != undefined) {
                                $("#otherDetails").val(user.userBankDetails.otherDetails);
                            }
                        }


                    })
                });
            });
        })
        $(".change_user_password").click(function () {
            var userOldPassword = $(".user_old_password").val();
            var userNewPassword = $(".user_new_password").val();
            var userEmail = $(".user_email").val();

            if (userOldPassword == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.old_password_error')}}</p>");
                window.scrollTo(0, 0);
            } else if (userNewPassword == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.new_password_error')}}</p>");
                window.scrollTo(0, 0);
            } else {

                var user = firebase.auth().currentUser;

                firebase.auth().signInWithEmailAndPassword(userEmail, userOldPassword)
                    .then((userCredential) => {
                        var user = userCredential.user;
                        user.updatePassword(userNewPassword).then(() => {
                            $(".error_top").show();
                            $(".error_top").html("");
                            $(".error_top").append("<p>{{trans('lang.password_updated_successfully')}}</p>");
                            window.scrollTo(0, 0);
                        }).catch((error) => {
                            $(".error_top").show();
                            $(".error_top").html("");
                            $(".error_top").append("<p>" + error + "</p>");
                            window.scrollTo(0, 0);
                        });
                    })
                    .catch((error) => {
                        var errorCode = error.code;
                        var errorMessage = error.message;
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>" + errorMessage + "</p>");
                        window.scrollTo(0, 0);
                    });


            }
        })


        $(".save_vendor_btn").click(function () {
            var address = $(".vendor_address").val();
            var phonenumber = $(".vendor_phone").val();
            var userFirstName = $(".user_first_name").val();
            var userLastName = $(".user_last_name").val();
            var email = $(".user_email").val();
            var userPhone = $(".user_phone").val();



            if (userFirstName == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_owners_name_error')}}</p>");
                window.scrollTo(0, 0);
            } else if (email == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_owners_email')}}</p>");
                window.scrollTo(0, 0);
            } else if (userPhone == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_owners_phone')}}</p>");
                window.scrollTo(0, 0);
            } else if (address == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.vendor_address_error')}}</p>");
                window.scrollTo(0, 0);
            }
            else {

                var bankName = $("#bankName").val();
                var branchName = $("#branchName").val();
                var holderName = $("#holderName").val();
                var accountNumber = $("#accountNumber").val();
                var otherDetails = $("#otherDetails").val();
                var userBankDetails = {
                    'bankName': bankName,
                    'branchName': branchName,
                    'holderName': holderName,
                    'accountNumber': accountNumber,
                    'accountNumber': accountNumber,
                    'otherDetails': otherDetails,
                };

                database.collection('users').doc(ownerId).update({
                    'firstName': userFirstName,
                    'lastName': userLastName,
                    'email': email,
                    'phoneNumber': userPhone,
                    'profilePictureURL': ownerphoto,
                    'userBankDetails': userBankDetails,
                }).then(function (result) {

                    window.location.href = '{{ route("user.profile")}}';

                })
            }
        })
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
                    var uploadTask = storageRef.child(filename).put(theFile);
                    uploadTask.on('state_changed', function (snapshot) {

                        var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
                        jQuery("#uploding_image_owner").text("Image is uploading...");
                    }, function (error) {
                    }, function () {
                        uploadTask.snapshot.ref.getDownloadURL().then(function (downloadURL) {
                            jQuery("#uploding_image_owner").text("Upload is completed");
                            ownerphoto = downloadURL;

                            $("#uploaded_image_owner").attr('src', ownerphoto);
                            $(".uploaded_image_owner").show();

                        });
                    });

                };
            })(f);
            reader.readAsDataURL(f);
        }
        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
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
        function chkAlphabets(event, msg) {
            if (!(event.which >= 97 && event.which <= 122) && !(event.which >= 65 && event.which <= 90)) {
                document.getElementById(msg).innerHTML = "Accept only Alphabets";
                return false;
            } else {
                document.getElementById(msg).innerHTML = "";
                return true;
            }
        }
    </script>
@endsection
