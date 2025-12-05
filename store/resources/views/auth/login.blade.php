<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->

        <link rel="dns-prefetch" href="//fonts.gstatic.com">

        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

        <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">

        <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    </head>

    <body>
        <?php if (isset($_COOKIE['store_panel_color'])) { ?>
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
        </style>
        <?php } ?>

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

            <div class="login-register" <?php if (isset($_COOKIE['store_panel_color'])){ ?>
                style="background-color:<?php echo $_COOKIE['store_panel_color']; ?>; <?php } ?>">

                <div class="login-logo text-center py-3">

                    <a href="#"
                        style="display: inline-block;background: #fff;padding: 10px;border-radius: 5px;"><img
                            src="{{ asset('images/logo_web.png') }}" onerror="this.onerror=null; this.src='{{ asset('images/logo_web.png') }}';"> </a>

                </div>

                <div class="login-box card" style="margin-bottom:0%;">

                    <div class="card-body">

                        @if (count($errors) > 0)
                            @foreach ($errors->all() as $message)
                                <div class="alert alert-danger display-hide">
                                    <button class="close" data-close="alert"></button>
                                    <span>{{ $message }}</span>
                                </div>
                            @endforeach
                        @endif

                        <form class="form-horizontal form-material" name="login" id="login-box" action="#">
                            @csrf
                            <div class="box-title m-b-20">{{ __('Login') }}</div>
                            <div class="form-group ">
                                <div class="col-xs-12">
                                    <input class="form-control" placeholder="{{ __('Email Address') }}" id="email"
                                        type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required autocomplete="email"
                                        autofocus>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <input id="password" placeholder="{{ __('Password') }}" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password">
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="forgot-password">
                                <p><a href="{{ url('forgot-password') }}" class="standard-link"
                                        target="_blank">{{ trans('lang.forgot_password') }}?</a></p>
                            </div>


                            <div class="error" id="password_required"></div>

                            <div class="form-group text-center m-t-20">


                                <div class="col-xs-12">
                                    <button type="button" onclick="loginClick()" id="login_btn"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                        {{ __('Login') }}
                                    </button>

                                    <button type="button" onclick="loginWithPhoneClick()" id="loginphon_btn"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                        {{ __('Login') }} With Phone
                                    </button>

                                    <div class="or-line mb-4 ">
                                        <span>OR</span>
                                    </div>
                                    <a href="{{ route('register') }}" id="signup_btn"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                        {{ trans('lang.sign_up') }}
                                    </a>
                                    <a href="{{ route('register.phone') }}"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary"
                                        id="btn-signup-phone">

                                        <i class="fa fa-phone"> </i> {{ trans('lang.signup_with_phone') }}

                                    </a>



                                </div>
                            </div>
                        </form>

                        <form class="form-horizontal form-material" name="loginwithphon" id="login-with-phone-box"
                            action="#" style="display:none;">
                            @csrf
                            <div class="box-title m-b-20">{{ __('Login') }}</div>
                            <div class="form-group" id="phone-box">
                                <label class="text-dark">{{ trans('lang.phone') }}</label>
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
                            </div>
                            <div id="recaptcha-container" style="display:none;"></div>

                            <div class="form-group text-center m-t-20">
                                <div class="col-xs-12">
                                    <button type="button" style="display:none;" onclick="applicationVerifier()"
                                        id="verify_btn"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                        OTP Verify
                                    </button>
                                    <button type="button" style="display:none;" onclick="sendOTP()"
                                        id="sendotp_btn"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                        Send OTP
                                    </button>
                                    <button type="button" onclick="loginBackClick()"
                                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                        {{ __('Login') }} With Email
                                    </button>
                                    <div class="error" id="password_required_new"></div>

                                </div>
                            </div>
                        </form>

                    </div>
                </div>

            </div>

        </section>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-firestore.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-storage.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-auth.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-database.js"></script>
        <script src="{{ asset('js/crypto-js.js') }}"></script>
        <script src="{{ asset('js/jquery.cookie.js') }}"></script>
        <script src="{{ asset('js/jquery.validate.js') }}"></script>

        <script type="text/javascript">
            var database = firebase.firestore();
            var subscriptionModel = false;

            var businessModel = database.collection('settings').doc("vendor");

            businessModel.get().then(async function(snapshots) {

                var businessModelSettings = snapshots.data();

                if (businessModelSettings.hasOwnProperty('subscription_model') &&

                    businessModelSettings.subscription_model == true) {

                    subscriptionModel = true;

                }

            });
            var commissionModel = false;

            function loginClick() {

                var email = $("#email").val();
                var password = $("#password").val();

                firebase.auth().signInWithEmailAndPassword(email, password).then(function(result) {
                        var userEmail = result.user.email;
                        database.collection("users").where("email", "==", userEmail).get().then(async function(snapshots) {
                            var userData = snapshots.docs[0].data();
                            if (userData.active == true) {
                                if (userData.role == "vendor") {
                                    if (userData.hasOwnProperty('section_id') && userData.section_id != null &&
                                        userData.section_id != '') {
                                        await database.collection('sections').where('id', '==', userData
                                            .section_id).get().then(async function(snapshots) {
                                            var section_data = snapshots.docs[0].data();
                                            if (section_data.adminCommision != null && section_data
                                                .adminCommision != '') {
                                                if (section_data.adminCommision.enable) {
                                                    commissionModel = true;
                                                }
                                            }
                                        });
                                    }
                                    var userToken = result.user.getIdToken();
                                    var uid = result.user.uid;
                                    var user = userData.id;
                                    var firstName = userData.firstName;
                                    var lastName = userData.lastName;
                                    var imageURL = userData.profilePictureURL;
                                    if (subscriptionModel || commissionModel) {
                                        if (userData.hasOwnProperty('subscriptionPlanId') && userData
                                            .subscriptionPlanId != '' && userData.subscriptionPlanId != null
                                        ) {
                                            var isSubscribed = 'true';
                                        } else {

                                            var isSubscribed = 'false';
                                        }
                                    } else {

                                        var isSubscribed = '';
                                    }
                                    var url = "{{ route('setToken') }}";
                                    $.ajax({
                                        type: 'POST',
                                        url: url,
                                        data: {
                                            id: uid,
                                            userId: user,
                                            email: email,
                                            password: password,
                                            firstName: firstName,
                                            lastName: lastName,
                                            profilePicture: imageURL,
                                            isSubscribed: isSubscribed
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function(data) {
                                            if (data.access) {
                                                if (userData.hasOwnProperty('subscriptionPlanId') &&
                                                    userData.subscriptionPlanId != '' && userData
                                                    .subscriptionPlanId != null) {
                                                    window.location = "{{ route('dashboard') }}";
                                                } else {
                                                    if (subscriptionModel || commissionModel) {

                                                        window.location =
                                                            "{{ route('subscription-plan.show') }}";

                                                    } else {
                                                        window.location =
                                                            "{{ route('dashboard') }}";
                                                    }
                                                }

                                            }
                                        }
                                    })

                                } else {

                                }
                            } else {
                                $("#password_required").css('color', 'black').html(
                                    "<p>{{ trans('lang.waiting_for_approval') }}</p>");
                                return false;
                            }

                        })

                    })
                    .catch(function(error) {
                        console.error("Login error:", error);
                        console.error("Error code:", error.code);
                        console.error("Error message:", error.message);
                        
                        // Handle different error object structures
                        let errorCode = error.code;
                        let errorMessage = error.message;
                        
                        // If error is wrapped in a different structure, try to extract it
                        if (error.error && error.error.code) {
                            errorCode = error.error.code;
                            errorMessage = error.error.message;
                        }
                        
                        // Handle specific Firebase authentication errors
                        switch(errorCode) {
                            case 'auth/invalid-login-credentials':
                            case 'auth/user-not-found':
                            case 'auth/wrong-password':
                            case 'INVALID_LOGIN_CREDENTIALS':
                                $("#password_required").html("❌ Invalid email or password. Please check your credentials and try again.");
                                break;
                            case 'auth/invalid-email':
                            case 'INVALID_EMAIL':
                                $("#password_required").html("❌ Please enter a valid email address.");
                                break;
                            case 'auth/user-disabled':
                            case 'USER_DISABLED':
                                $("#password_required").html("❌ This account has been disabled. Please contact support.");
                                break;
                            case 'auth/too-many-requests':
                            case 'TOO_MANY_ATTEMPTS_TRY_LATER':
                                $("#password_required").html("❌ Too many failed login attempts. Please try again later.");
                                break;
                            case 'auth/network-request-failed':
                            case 'NETWORK_REQUEST_FAILED':
                                $("#password_required").html("❌ Network error. Please check your internet connection and try again.");
                                break;
                            default:
                                // Check if the error message contains specific keywords
                                if (errorMessage && errorMessage.includes('INVALID_LOGIN_CREDENTIALS')) {
                                    $("#password_required").html("❌ Invalid email or password. Please check your credentials and try again.");
                                } else if (errorMessage && errorMessage.includes('INVALID_EMAIL')) {
                                    $("#password_required").html("❌ Please enter a valid email address.");
                                } else if (errorMessage && errorMessage.includes('USER_DISABLED')) {
                                    $("#password_required").html("❌ This account has been disabled. Please contact support.");
                                } else {
                                    $("#password_required").html("❌ Login failed. Please check your credentials and try again.");
                                }
                        }
                    });
                return false;
            }

            var provider = new firebase.auth.PhoneAuthProvider();

            function loginWithPhoneClick() {
                jQuery("#login-box").hide();
                jQuery("#login-with-phone-box").show();
                jQuery("#phone-box").show();
                jQuery("#recaptcha-container").show();
                jQuery("#sendotp_btn").show();
                window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                    'size': 'invisible',
                    'callback': (response) => {}
                });
            }

            function loginBackClick() {
                jQuery("#login-box").show();
                jQuery("#login-with-phone-box").hide();
                jQuery("#sendotp_btn").hide();
            }

            function sendOTP() {

                if (jQuery("#phone").val() && jQuery("#country_selector").val()) {
                    var phoneNumber = '+' + jQuery("#country_selector").val() + '' + jQuery("#phone").val();
                    database.collection("users").where("phoneNumber", "==", phoneNumber).where("role", "==", 'vendor').where(
                        "active", "==", true).get().then(async function(snapshots) {
                        if (snapshots.docs.length) {
                            var userData = snapshots.docs[0].data();
                            firebase.auth().signInWithPhoneNumber(phoneNumber, window.recaptchaVerifier)
                                .then(function(confirmationResult) {
                                    window.confirmationResult = confirmationResult;
                                    if (confirmationResult.verificationId) {
                                        jQuery("#phone-box").hide();
                                        jQuery("#recaptcha-container").hide();
                                        jQuery("#otp-box").show();
                                        jQuery("#verify_btn").show();
                                    }
                                });
                        } else {
                            jQuery("#password_required_new").html("❌ User account is inactive or not found. Please contact support.");
                        }
                    });
                }
            }

            function applicationVerifier() {
                window.confirmationResult.confirm(document.getElementById("verificationcode").value)
                    .then(function(result) {
                        database.collection("users").where('phoneNumber', "==", result.user.phoneNumber).get().then(
                            async function(snapshots_login) {
                                userData = snapshots_login.docs[0].data();
                                if (userData) {
                                    if (userData.role == "vendor" && userData.active == true) {
                                        if (userData.hasOwnProperty('section_id') && userData.section_id != null &&
                                            userData.section_id != '') {
                                            await database.collection('sections').where('id', '==', userData
                                                .section_id).get().then(async function(snapshots) {
                                                var section_data = snapshots.docs[0].data();
                                                if (section_data.adminCommision != null && section_data
                                                    .adminCommision != '') {
                                                    if (section_data.adminCommision.enable) {
                                                        commissionModel = true;
                                                    }
                                                }
                                            });
                                        }
                                        var uid = userData.id;
                                        var user = userData.id;
                                        var firstName = userData.firstName;
                                        var phoneNumber = userData.phoneNumber;
                                        var lastName = userData.lastName;
                                        var imageURL = '';
                                        if (subscriptionModel || commissionModel) {
                                            if (userData.hasOwnProperty('subscriptionPlanId') && userData
                                                .subscriptionPlanId != '' && userData.subscriptionPlanId != null
                                            ) {
                                                var isSubscribed = 'true';
                                            } else {

                                                var isSubscribed = 'false';
                                            }
                                        } else {

                                            var isSubscribed = '';
                                        }
                                        var url = "{{ route('setToken') }}";

                                        $.ajax({
                                            type: 'POST',
                                            url: url,
                                            data: {
                                                id: uid,
                                                userId: user,
                                                email: phoneNumber,
                                                password: '',
                                                firstName: firstName,
                                                lastName: lastName,
                                                profilePicture: imageURL,
                                                isSubscribed: isSubscribed
                                            },
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            },
                                            success: function(data) {
                                                if (data.access) {
                                                    if (userData.hasOwnProperty('subscriptionPlanId') &&
                                                        userData.subscriptionPlanId != '' && userData
                                                        .subscriptionPlanId != null) {
                                                        window.location = "{{ route('dashboard') }}";
                                                    } else {
                                                        if (subscriptionModel || commissionModel) {

                                                            window.location =
                                                                "{{ route('subscription-plan.show') }}";

                                                        } else {
                                                            if (userData.hasOwnProperty('section_id') &&
                                                                userData.section_id != null &&
                                                                userData.section_id != '') {
                                                                window.location =
                                                                    "{{ route('dashboard') }}";
                                                            } else {
                                                                window.location =
                                                                    "{{ route('store') }}";
                                                            }

                                                        }
                                                    }
                                                }
                                            }
                                        });

                                    } else {
                                        jQuery("#password_required_new").html("❌ User account is inactive or not found. Please contact support.");
                                    }
                                }
                            })
                    }).catch(function(error) {
                        console.error("Phone login error:", error);
                        console.error("Error code:", error.code);
                        console.error("Error message:", error.message);
                        
                        // Handle different error object structures
                        let errorCode = error.code;
                        let errorMessage = error.message;
                        
                        // If error is wrapped in a different structure, try to extract it
                        if (error.error && error.error.code) {
                            errorCode = error.error.code;
                            errorMessage = error.error.message;
                        }
                        
                        // Handle specific Firebase authentication errors
                        switch(errorCode) {
                            case 'auth/invalid-login-credentials':
                            case 'auth/user-not-found':
                            case 'auth/wrong-password':
                            case 'INVALID_LOGIN_CREDENTIALS':
                                jQuery("#password_required_new").html("❌ Invalid phone number or OTP. Please check your credentials and try again.");
                                break;
                            case 'auth/invalid-phone-number':
                            case 'INVALID_PHONE_NUMBER':
                                jQuery("#password_required_new").html("❌ Please enter a valid phone number.");
                                break;
                            case 'auth/user-disabled':
                            case 'USER_DISABLED':
                                jQuery("#password_required_new").html("❌ This account has been disabled. Please contact support.");
                                break;
                            case 'auth/too-many-requests':
                            case 'TOO_MANY_ATTEMPTS_TRY_LATER':
                                jQuery("#password_required_new").html("❌ Too many failed login attempts. Please try again later.");
                                break;
                            case 'auth/network-request-failed':
                            case 'NETWORK_REQUEST_FAILED':
                                jQuery("#password_required_new").html("❌ Network error. Please check your internet connection and try again.");
                                break;
                            case 'auth/session-expired':
                            case 'SESSION_EXPIRED':
                                jQuery("#password_required_new").html("❌ Session expired. Please request a new OTP.");
                                break;
                            case 'auth/invalid-verification-code':
                            case 'INVALID_VERIFICATION_CODE':
                                jQuery("#password_required_new").html("❌ Invalid OTP. Please enter the correct verification code.");
                                break;
                            case 'auth/code-expired':
                            case 'CODE_EXPIRED':
                                jQuery("#password_required_new").html("❌ OTP expired. Please request a new verification code.");
                                break;
                            default:
                                // Check if the error message contains specific keywords
                                if (errorMessage && errorMessage.includes('INVALID_LOGIN_CREDENTIALS')) {
                                    jQuery("#password_required_new").html("❌ Invalid phone number or OTP. Please check your credentials and try again.");
                                } else if (errorMessage && errorMessage.includes('SESSION_EXPIRED')) {
                                    jQuery("#password_required_new").html("❌ Session expired. Please request a new OTP.");
                                } else if (errorMessage && errorMessage.includes('INVALID_VERIFICATION_CODE')) {
                                    jQuery("#password_required_new").html("❌ Invalid OTP. Please enter the correct verification code.");
                                } else if (errorMessage && errorMessage.includes('CODE_EXPIRED')) {
                                    jQuery("#password_required_new").html("❌ OTP expired. Please request a new verification code.");
                                } else {
                                    jQuery("#password_required_new").html("❌ Login failed. Please check your credentials and try again.");
                                }
                        }
                    });
            }

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

            jQuery(document).ready(function() {

                jQuery("#country_selector").select2({
                    templateResult: formatState,
                    templateSelection: formatState2,
                    placeholder: "Select Country",
                    allowClear: true
                });

            });
            var ref = database.collection('settings').doc("globalSettings");

            $(document).ready(function() {
                ref.get().then(async function(snapshots) {
                    var globalSettings = snapshots.data();
                    store_panel_color = globalSettings.store_panel_color;
                    setCookie('store_panel_color', store_panel_color, 365);
                })

            });

            function setCookie(cname, cvalue, exdays) {
                const d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                let expires = "expires=" + d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            }
        </script>

    </body>

</html>
