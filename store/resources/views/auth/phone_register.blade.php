<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">


        <!-- CSRF Token -->

        <meta name="csrf-token" content="{{ csrf_token() }}">


        <title id="app_name"><?php echo @$_COOKIE['meta_title']; ?></title>
        <link rel="icon" id="favicon" type="image/x-icon" href="<?php echo str_replace('images/', 'images%2F', @$_COOKIE['favicon']); ?>">


        <!-- Fonts -->

        <link rel="dns-prefetch" href="//fonts.gstatic.com">

        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles -->

        <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

        <link href="{{ asset('css/style.css') }}" rel="stylesheet">

        <link href="{{ asset('assets/plugins/toast-master/css/jquery.toast.css') }}" rel="stylesheet">

        <link href="{{ asset('css/colors/blue.css') }}" rel="stylesheet">

        <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">

        <!--  @yield('style')-->

        <?php if (isset($_COOKIE['store_panel_color'])){ ?>
        <style type="text/css">
            a,
            a:hover,
            a:focus {
                color: <?php echo $_COOKIE['store_panel_color']; ?>;
            }

            .form-group.default-admin {
                padding: 10px;
                font-size: 14px;
                color: #000;
                font-weight: 600;
                border-radius: 10px;
                box-shadow: 0 0px 6px 0px rgba(0, 0, 0, 0.5);
                margin: 20px 10px 10px 10px;
            }

            .form-group.default-admin .crediantials-field {
                position: relative;
                padding-right: 15px;
                text-align: left;
                padding-top: 5px;
                padding-bottom: 5px;
            }

            .form-group.default-admin .crediantials-field>a {
                position: absolute;
                right: 0;
                top: 0;
                bottom: 0;
                margin: auto;
                height: 20px;
            }

            .btn-primary,
            .btn-primary.disabled,
            .btn-primary:hover,
            .btn-primary.disabled:hover {
                background: <?php echo $_COOKIE['store_panel_color']; ?>;
                border: 1px solid<?php echo $_COOKIE['store_panel_color']; ?>;
            }

            [type="checkbox"]:checked+label::before {
                border-right: 2px solid<?php echo $_COOKIE['store_panel_color']; ?>;
                border-bottom: 2px solid<?php echo $_COOKIE['store_panel_color']; ?>;
            }

            .form-material .form-control,
            .form-material .form-control.focus,
            .form-material .form-control:focus {
                background-image: linear-gradient(<?php echo $_COOKIE['store_panel_color']; ?>, <?php echo $_COOKIE['store_panel_color']; ?>), linear-gradient(rgba(120, 130, 140, 0.13), rgba(120, 130, 140, 0.13));
            }

            .btn-primary.active,
            .btn-primary:active,
            .btn-primary:focus,
            .btn-primary.disabled.active,
            .btn-primary.disabled:active,
            .btn-primary.disabled:focus,
            .btn-primary.active.focus,
            .btn-primary.active:focus,
            .btn-primary.active:hover,
            .btn-primary.focus:active,
            .btn-primary:active:focus,
            .btn-primary:active:hover,
            .open>.dropdown-toggle.btn-primary.focus,
            .open>.dropdown-toggle.btn-primary:focus,
            .open>.dropdown-toggle.btn-primary:hover,
            .btn-primary.focus,
            .btn-primary:focus,
            .btn-primary:not(:disabled):not(.disabled).active:focus,
            .btn-primary:not(:disabled):not(.disabled):active:focus,
            .show>.btn-primary.dropdown-toggle:focus {
                background: <?php echo $_COOKIE['store_panel_color']; ?>;
                border-color: <?php echo $_COOKIE['store_panel_color']; ?>;
                box-shadow: 0 0 0 0.2rem<?php echo $_COOKIE['store_panel_color']; ?>;
            }

            <?php } ?>
        </style>

        <!-- Additional styles for phone input visibility and layout -->
        <style type="text/css">
            /* Phone input field styling */
            #phone {
                color: #000 !important;
                background-color: #fff !important;
                border: 1px solid #ddd !important;
                padding: 10px !important;
                font-size: 16px !important;
                width: 100% !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                z-index: 1 !important;
                position: relative !important;
            }
            
            #phone:focus {
                border-color: #007bff !important;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
                outline: none !important;
            }
            
            #phone::placeholder {
                color: #6c757d !important;
                opacity: 1 !important;
            }
            
            /* Country selector styling */
            #country_selector {
                width: 100% !important;
                z-index: 2 !important;
                position: relative !important;
                height: 45px !important;
            }
            
            /* Phone box container */
            #phone-box {
                position: relative !important;
                z-index: 1 !important;
            }
            
            /* Bootstrap grid spacing */
            #phone-box .row {
                margin: 0 !important;
            }
            
            #phone-box .col-md-4,
            #phone-box .col-md-8 {
                padding-left: 5px !important;
                padding-right: 5px !important;
            }
            
            #phone-box .col-md-4 {
                padding-right: 10px !important;
            }
            
            #phone-box .col-md-8 {
                padding-left: 10px !important;
            }
            
            /* Select2 dropdown styling */
            .select2-container {
                z-index: 9999 !important;
            }
            
            .select2-dropdown {
                z-index: 9999 !important;
            }
            
            /* Ensure proper spacing */
            .form-group {
                margin-bottom: 15px !important;
            }
        </style>


    </head>

    <body>

        <?php
        $countries = file_get_contents(public_path('countriesdata.json'));
        $countries = json_decode($countries);
        $countries = (array) $countries;
        $newcountries = [];
        $newcountriesjs = [];
        foreach ($countries as $keycountry => $valuecountry) {
            $newcountries[$valuecountry->phoneCode] = $valuecountry;
            $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
        }
        ?>


        <section id="wrapper">

            <?php if (isset($_COOKIE['store_panel_color'])){ ?>

            <div class="login-register" style="background-color:<?php echo $_COOKIE['store_panel_color']; ?>;">
                <?php } else{ ?>

                <div class="login-register" style="background-color:#FF683A;">
                    <?php } ?>


                    <div class="login-logo text-center py-3" style="margin-top:5%;">

                        <a href="#"
                            style="display: inline-block;background: #fff ; padding: 10px;border-radius: 5px;"><img
                                src="{{ asset('images/logo_web.png') }}" onerror="this.onerror=null; this.src='{{ asset('images/logo_web.png') }}';"> </a>

                    </div>

                    <div class="login-box card" style="margin-bottom:0%;">


                        <div class="card-body">
                            <div class="error_top"></div>
                            <div class="alert alert-success" style="display:none;"></div>

                            <form class="form-horizontal form-material" name="loginwithphon" id="login-with-phone-box"
                                action="#">
                                @csrf
                                <div class="box-title m-b-20">{{ trans('lang.sign_up_with_us') }}</div>
                                <div class="form-group" id="firstName_div">

                                    <label for="firstName" class="text-dark">{{ trans('lang.first_name') }}</label>

                                    <input type="text" placeholder="Enter FirstName" class="form-control"
                                        id="firstName" required>
                                    <input type="hidden" id="hidden_fName" />
                                </div>

                                <div class="form-group" id="lastName_div">

                                    <label for="lastName" class="text-dark">{{ trans('lang.last_name') }}</label>

                                    <input type="text" placeholder="Enter LastName" class="form-control"
                                        id="lastName" required>
                                    <input type="hidden" id="hidden_lName" />
                                </div>

                                <div class="form-group" id="email_div">
                                    <label class="text-dark">{{trans('lang.email')}}</label>                                    
                                        <input type="email" placeholder="Enter Email" class="form-control user_email" id="email" required>
                                        <input type="hidden" id="hidden_email" />
                                </div>

                                <div class="form-group" id="phone-box">
                                    <label class="text-dark">{{ trans('lang.phone_number') }}</label>
                                    <div class="col-xs-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <select name="country" id="country_selector" class="form-control">
                                                    <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                                     <?php $selected = ''; ?><?php $selected = ($valuecy->code == "IN") ? "selected" : ""; ?>
                                                    <option <?php echo $selected; ?> code="<?php echo $valuecy->code; ?>"
                                                        value="<?php echo $keycy; ?>">
                                                        +<?php echo $valuecy->phoneCode; ?> {{ $valuecy->countryName }}</option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <input class="form-control" placeholder="Enter Phone Number" id="phone" type="tel"
                                                    name="phone" value="{{ old('phone') }}" required
                                                    autocomplete="tel" autofocus>
                                            </div>
                                        </div>
                                    </div>
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group " id="otp-box" style="display:none;">
                                    <input class="form-control" placeholder="OTP" id="verificationcode" type="text"
                                        class="form-control" name="otp" value="{{ old('otp') }}" required
                                        autocomplete="otp" autofocus>
                                    <div class="otp_error">

                                    </div>
                                </div>
                                <div id="recaptcha-container" ></div>

                                <div class="form-group text-center m-t-20">
                                    <div class="col-xs-12">
                                        <button type="button" style="display:none;" onclick="applicationVerifier()"
                                            id="verify_btn"
                                            class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                            {{ trans('lang.otp_verify') }}
                                        </button>
                                        <button type="button" onclick="sendOTP()" id="send-code"
                                            class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                            {{ trans('lang.otp_send') }}
                                        </button>


                                    </div>
                                </div>
                            </form>
                            <div class="new-acc d-flex align-items-center justify-content-center mt-4 mb-3">

                                <a href="{{ url('login') }}">

                                    <p class="text-center m-0"> {{ trans('lang.already_an_account') }}
                                        {{ trans('lang.sign_in') }}</p>

                                </a>

                            </div>


                        </div>

                        <!-- </div>

            </div>

        </div> -->

                    </div>

                </div>
            </div>

        </section>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>
        <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
        <script src="{{ asset('js/waves.js') }}"></script>
        <script src="{{ asset('js/sidebarmenu.js') }}"></script>
        <script src="{{ asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
        <script src="{{ asset('js/custom.min.js') }}"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-firestore.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-storage.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-auth.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-database.js"></script>
        <script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
        <script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
        <script src="{{ asset('js/crypto-js.js') }}"></script>
        <script src="{{ asset('js/jquery.cookie.js') }}"></script>
        <script src="{{ asset('js/jquery.validate.js') }}"></script>

        <script type="text/javascript">
            var database = firebase.firestore();
            var geoFirestore = new GeoFirestore(database);

            var createdAt = firebase.firestore.FieldValue.serverTimestamp();

            var vendor_active = false;
            var autoAprroveVendor = database.collection('settings').doc("vendor");
            autoAprroveVendor.get().then(async function(snapshots) {
                var vendordata = snapshots.data();
                if (vendordata.auto_approve_vendor == true) {
                    vendor_active = true;
                }
            });

            var adminEmail = '';

            var emailSetting = database.collection('settings').doc('emailSetting');
            var email_templates = database.collection('email_templates').where('type', '==', 'new_vendor_signup');

            var emailTemplatesData = null;

            var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
            var newcountriesjs = JSON.parse(newcountriesjs);

            function formatState(state) {

                if (!state.id) {
                    return state.text;
                }
                var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/";
                var $state = $(
                    '<span><img src="' + baseUrl + '/' + newcountriesjs[state.element.value].toLowerCase() +
                    '.png" class="img-flag" /> ' + state.text + '</span>'
                );
                return $state;
            }

            function formatState2(state) {
                if (!state.id) {
                    return state.text;
                }

                var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/"
                var $state = $(
                    '<span><img class="img-flag" /> <span></span></span>'
                );
                $state.find("span").text(state.text);
                $state.find("img").attr("src", baseUrl + "/" + newcountriesjs[state.element.value].toLowerCase() + ".png");

                return $state;
            }

            jQuery(document).ready(async function() {

                await email_templates.get().then(async function(snapshots) {
                    emailTemplatesData = snapshots.docs[0].data();
                });

                await emailSetting.get().then(async function(snapshots) {
                    var emailSettingData = snapshots.data();

                    adminEmail = emailSettingData.userName;
                });

                jQuery("#country_selector").select2({
                    templateResult: formatState,
                    templateSelection: formatState2,
                    placeholder: "Select Country",
                    allowClear: true
                });
            });


            function sendOTP() {
                // Clear any existing reCAPTCHA verifier to prevent session conflicts
                if (window.recaptchaVerifier) {
                    window.recaptchaVerifier.clear();
                    window.recaptchaVerifier = null;
                }
                
                // Clear the reCAPTCHA container
                jQuery("#recaptcha-container").empty().show();

                // Create new reCAPTCHA verifier
                window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                    'size': 'invisible',
                    'callback': (response) => {
                        console.log('reCAPTCHA verified successfully');
                    },
                    'expired-callback': () => {
                        console.log('reCAPTCHA expired, please try again');
                        $('.otp_error').html('reCAPTCHA expired, please try again');
                    }
                });
                var firstName = $('#firstName').val();
                var lastName = $('#lastName').val();
                var email = $('#email').val();

                if (firstName == '') {

                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.enter_owners_name_error') }}</p>");
                    window.scrollTo(0, 0);
                } else if (jQuery("#phone").val() == "") {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.enter_owners_phone') }}</p>");
                    window.scrollTo(0, 0);
                } else if (email == "") {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.enter_owners_email') }}</p>");
                    window.scrollTo(0, 0);
                } else {
                    var phoneNumber = '+' + jQuery("#country_selector").val() + jQuery("#phone").val();

                    // Check if this is a resend after session expiry
                    var isResendAfterSessionExpiry = window.phoneNumberValidated === true;
                    
                    if (!isResendAfterSessionExpiry) {
                        // Only check for existing phone number on first attempt
                        database.collection("users").where('phoneNumber', '==', phoneNumber).get().then(async function(snapshots) {
                            if (snapshots.docs.length > 0) {
                                $(".error_top").show();
                                $(".error_top").html("");
                                $(".error_top").append("<p>You already have an account with this phone number. Please use a different number or try logging in.</p>");
                                window.scrollTo(0, 0);
                                return false;
                            } else {
                                // Mark phone number as validated for this session
                                window.phoneNumberValidated = true;
                                proceedWithOTP();
                            }
                        });
                    } else {
                        // This is a resend after session expiry, proceed directly
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>Resending OTP after session expiry...</p>");
                        proceedWithOTP();
                    }
                    
                    function proceedWithOTP() {
                            $('#hidden_fName').val(firstName);
                            $('#hidden_lName').val(lastName);
                            $('#hidden_email').val(email);


                            firebase.auth().signInWithPhoneNumber(phoneNumber, window.recaptchaVerifier)
                                .then(function(confirmationResult) {
                                    window.confirmationResult = confirmationResult;
                                    if (confirmationResult.verificationId) {
                                        $('#firstName_div').hide();
                                        $('#lastName_div').hide();
                                        $('#email_div').hide();

                                        $('#phone-box').hide();


                                        jQuery("#recaptcha-container").hide();
                                        jQuery("#verify_btn").show();
                                        jQuery("#otp-box").show();
                                        
                                        // Clear any previous error messages
                                        $('.otp_error').html('');
                                        $(".error_top").hide();
                                    }
                                })
                                .catch(function(error) {
                                    console.error('Error sending OTP:', error);
                                    if (error.code === 'auth/too-many-requests') {
                                        $(".error_top").show();
                                        $(".error_top").html("");
                                        $(".error_top").append("<p>Too many requests. Please try again later.</p>");
                                    } else if (error.code === 'auth/invalid-phone-number') {
                                        $(".error_top").show();
                                        $(".error_top").html("");
                                        $(".error_top").append("<p>Invalid phone number. Please check and try again.</p>");
                                    } else if (error.code === 'auth/captcha-check-failed') {
                                        $(".error_top").show();
                                        $(".error_top").html("");
                                        $(".error_top").append("<p>reCAPTCHA verification failed. Please try again.</p>");
                                        // Reset reCAPTCHA
                                        if (window.recaptchaVerifier) {
                                            window.recaptchaVerifier.clear();
                                            window.recaptchaVerifier = null;
                                        }
                                    } else {
                                        $(".error_top").show();
                                        $(".error_top").html("");
                                        $(".error_top").append("<p>Failed to send OTP. Please try again.</p>");
                                    }
                                    window.scrollTo(0, 0);
                                });
                    }
                }


            }

            function applicationVerifier() {
                var code = $('#verificationcode').val();
                if (code == "") {
                    $('.otp_error').html('Please Enter OTP')
                } else {
                    // Check if confirmationResult still exists
                    if (!window.confirmationResult) {
                        $('.otp_error').html('Session expired. Please request OTP again.');
                        return;
                    }
                    
                    window.confirmationResult.confirm(document.getElementById("verificationcode").value)
                        .then(async function(result) {
                            var phoneNumber = result.user.phoneNumber;
                            var firstName = $('#hidden_fName').val();
                            var lastName = $('#hidden_lName').val();
                            var email = $('#hidden_email').val();

                            var password = "";

                            var uuid = result.user.uid;

                            // Show immediate success message for OTP verification
                            $(".alert-success").show();
                            $(".alert-success").html("");
                            $(".alert-success").append("<p><strong>✓ OTP Verified Successfully!</strong><br>Creating your account...</p>");
                            window.scrollTo(0, 0);
                            
                            coordinates = new firebase.firestore.GeoPoint(0, 0);
                            geoFirestore.collection("users").doc(uuid).set({
                                'email': email,
                                'firstName': firstName,
                                'lastName': lastName,
                                'id': uuid,
                                'phoneNumber': phoneNumber,
                                'role': "vendor",
                                'profilePictureURL': "",
                                'vendorID': '',
                                'active': vendor_active,
                                'coordinates': coordinates,
                                'createdAt': createdAt
                            }).then(async function(result) {
                                autoAprroveVendor.get().then(async function(snapshots) {
                                    var formattedDate = new Date();
                                    var month = formattedDate.getMonth() + 1;
                                    var day = formattedDate.getDate();
                                    var year = formattedDate.getFullYear();

                                    month = month < 10 ? '0' + month : month;
                                    day = day < 10 ? '0' + day : day;

                                    formattedDate = day + '-' + month + '-' + year;

                                    var message = emailTemplatesData.message;
                                    message = message.replace(/{userid}/g, uuid);
                                    message = message.replace(/{username}/g, firstName + ' ' +
                                        lastName);
                                    message = message.replace(/{useremail}/g, "");
                                    message = message.replace(/{userphone}/g, phoneNumber);
                                    message = message.replace(/{date}/g, formattedDate);

                                    emailTemplatesData.message = message;

                                    var url = "{{ url('send-email') }}";

                                    var sendEmailStatus = await sendEmail(url,
                                        emailTemplatesData.subject, emailTemplatesData
                                        .message, [adminEmail]);

                                    if (sendEmailStatus) {

                                        var vendordata = snapshots.data();
                                        // Always show admin approval message for vendor registration
                                        $(".alert-success").show();
                                        $(".alert-success").html("");
                                        $(".alert-success").append(
                                            "<p><strong>🎉 Registration Successful!</strong><br>⏳ Please wait for admin approval before you can login.</p>"
                                        );
                                        window.scrollTo(0, 0);
                                        setTimeout(function() {
                                            window.location.href = '{{ route('login') }}';
                                        }, 8000); // Increased timeout to give users time to read the message

                                    } else {
                                        // Email sending failed but account created successfully
                                        $(".alert-success").show();
                                        $(".alert-success").html("");
                                        $(".alert-success").append(
                                            "<p><strong>🎉 Registration Successful!</strong><br>⏳ Please wait for admin approval before you can login.</p>"
                                        );
                                        window.scrollTo(0, 0);
                                        setTimeout(function() {
                                            window.location.href = '{{ route('login') }}';
                                        }, 8000);
                                    }


                                }).catch((error) => {

                                    console.error("Error writing document: ", error);
                                    $(".alert-success").show();
                                    $(".alert-success").html("");
                                    $(".alert-success").append(
                                        "<p><strong>🎉 Registration Successful!</strong><br>⏳ Please wait for admin approval before you can login.</p>"
                                    );
                                    window.scrollTo(0, 0);
                                    setTimeout(function() {
                                        window.location.href = '{{ route('login') }}';
                                    }, 8000);

                                });
                            });


                        }).catch((error) => {
                            console.error('OTP verification error:', error);
                            if (error.code === 'auth/session-expired') {
                                $(".otp_error").html("Session expired. Please request OTP again.");
                                // Reset the form to allow new OTP request
                                resetFormForNewOTP();
                            } else if (error.code === 'auth/invalid-verification-code') {
                                $(".otp_error").html("Invalid OTP. Please check and try again.");
                            } else if (error.code === 'auth/code-expired') {
                                $(".otp_error").html("OTP expired. Please request a new OTP.");
                                resetFormForNewOTP();
                            } else {
                                $(".otp_error").html("OTP verification failed. Please try again.");
                            }
                        });
                }
            }

            function resetFormForNewOTP() {
                // Clear confirmation result
                window.confirmationResult = null;
                
                // Clear reCAPTCHA verifier
                if (window.recaptchaVerifier) {
                    window.recaptchaVerifier.clear();
                    window.recaptchaVerifier = null;
                }
                
                // Reset form visibility
                $('#firstName_div').show();
                $('#lastName_div').show();
                $('#email_div').show();
                $('#phone-box').show();
                $('#otp-box').hide();
                $('#verify_btn').hide();
                $('#send-code').show();
                
                // Clear OTP input
                $('#verificationcode').val('');
                
                // Hide reCAPTCHA container
                jQuery("#recaptcha-container").hide();
                
                // Clear any error messages
                $('.otp_error').html('');
                $('.error_top').hide();
                
                // IMPORTANT: Clear the phone number validation state
                // This allows the user to resend OTP even if the same phone number was used
                window.phoneNumberValidated = false;
                window.phoneNumberChecked = false;
            }

            async function sendEmail(url, subject, message, recipients) {

                var checkFlag = false;

                await $.ajax({

                    type: 'POST',
                    data: {
                        subject: subject,
                        message: message,
                        recipients: recipients
                    },
                    url: url,
                    headers: {

                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        checkFlag = true;
                    },
                    error: function(xhr, status, error) {
                        checkFlag = true;
                    }
                });

                return checkFlag;

            }
        </script>
