<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body>

@if (isset($_COOKIE['store_panel_color']))
<style>
    a, a:hover, a:focus { color: {{ $_COOKIE['store_panel_color'] }}; }
    .login-register { background-color: {{ $_COOKIE['store_panel_color'] }}; }
</style>
@endif

<section id="wrapper">

    <div class="login-register">
        <div class="login-logo text-center py-3">
            <a href="#" style="background:#fff;padding:10px;border-radius:5px;">
                <img src="{{ asset('images/logo_web.png') }}">
            </a>
        </div>

        <div class="login-box card">
            <div class="card-body">

                @if ($errors->any())
                    @foreach ($errors->all() as $message)
                        <div class="alert alert-danger">{{ $message }}</div>
                    @endforeach
                @endif

                <!-- EMAIL LOGIN -->
                <form class="form-horizontal form-material" id="login-box" action="#">
                    @csrf

                    <div class="box-title m-b-20">{{ __('Login') }}</div>

                    <!-- Email -->
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input id="email"
                                   type="email"
                                   class="form-control"
                                   name="email"
                                   placeholder="{{ __('Email Address') }}"
                                   required autofocus>
                        </div>
                    </div>

                    <!-- PASSWORD WITH SHOW / HIDE -->
                    <div class="form-group">
                        <div class="col-xs-12">
                            <div class="password-wrapper">
                                <input id="password"
                                       type="password"
                                       class="form-control"
                                       name="password"
                                       placeholder="{{ __('Password') }}"
                                       required>

                                <span class="password-toggle" data-target="#password">
                                    Show
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="forgot-password">
                        <a href="{{ url('forgot-password') }}" target="_blank">
                            {{ trans('lang.forgot_password') }}?
                        </a>
                    </div>

                    <div id="password_required"></div>

                    <div class="form-group text-center mt-4">
                        <button type="button"
                                onclick="loginClick()"
                                class="btn btn-primary btn-lg btn-block">
                            {{ __('Login') }}
                        </button>

                        <button type="button"
                                onclick="loginWithPhoneClick()"
                                class="btn btn-primary btn-lg btn-block mt-2">
                            {{ __('Login With Phone') }}
                        </button>

                        <div class="or-line my-3"><span>OR</span></div>

                        <a href="{{ route('register') }}"
                           class="btn btn-primary btn-lg btn-block">
                            {{ trans('lang.sign_up') }}
                        </a>

                        <a href="{{ route('register.phone') }}"
                           class="btn btn-primary btn-lg btn-block">
                            {{ trans('lang.signup_with_phone') }}
                        </a>
                    </div>
                </form>

                <!-- PHONE LOGIN -->
                <form class="form-horizontal form-material"
                      id="login-with-phone-box"
                      style="display:none;">
                    @csrf

                    <div class="box-title m-b-20">{{ __('Login') }}</div>

                    <div class="form-group" id="phone-box">
                        <div class="row">
                            <div class="col-md-4">
                                <select id="country_selector" class="form-control"></select>
                            </div>
                            <div class="col-md-8">
                                <input id="phone" class="form-control" placeholder="Phone">
                            </div>
                        </div>
                    </div>

                    <div id="otp-box" style="display:none;">
                        <input id="verificationcode" class="form-control" placeholder="OTP">
                    </div>

                    <div id="password_required_new"></div>
                    <div id="recaptcha-container"></div>

                    <button type="button" id="sendotp_btn" onclick="sendOTP()" class="btn btn-primary btn-block">
                        Send OTP
                    </button>

                    <button type="button" id="verify_btn" onclick="applicationVerifier()" class="btn btn-primary btn-block" style="display:none;">
                        Verify OTP
                    </button>

                    <button type="button" onclick="loginBackClick()" class="btn btn-secondary btn-block">
                        Login With Email
                    </button>
                </form>

            </div>
        </div>
    </div>

</section>

<!-- JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>

<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.2.0/firebase-firestore.js"></script>

<!-- SHOW / HIDE PASSWORD -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".password-toggle").forEach(function (btn) {
        btn.addEventListener("click", function () {
            const input = document.querySelector(this.dataset.target);
            if (!input) return;

            if (input.type === "password") {
                input.type = "text";
                this.innerText = "Hide";
            } else {
                input.type = "password";
                this.innerText = "Show";
            }
        });
    });
});
</script>

</body>
</html>
