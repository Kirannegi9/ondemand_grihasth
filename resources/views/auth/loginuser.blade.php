@include('auth.default')

<?php
$countries = file_get_contents(public_path('countriesdata.json'));
$countries = json_decode($countries);
$countries = (array)$countries;
$newcountries = [];
$newcountriesjs = [];
foreach ($countries as $valuecountry) {
    $newcountries[$valuecountry->phoneCode] = $valuecountry;
    $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
}
?>

<?php if (isset($_COOKIE['section_color'])){ ?>
<style>
.btn-primary {
    background: <?php echo $_COOKIE['section_color']; ?>;
    border-color: <?php echo $_COOKIE['section_color']; ?>;
}
.btn-primary:hover,
.btn-primary:focus {
    background: <?php echo $_COOKIE['section_color']; ?>;
    border-color: <?php echo $_COOKIE['section_color']; ?>;
}
</style>
<?php } ?>

<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('/css/font-awesome.min.css') }}" rel="stylesheet">

<!-- PASSWORD TOGGLE CSS -->
<style>
.password-wrapper {
    position: relative;
}
.password-wrapper input {
    padding-right: 65px !important;
}
.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: #6c757d;
    user-select: none;
}
.password-toggle:hover {
    color: #495057;
}
</style>

<div class="login-page vh-100">
<div class="d-flex align-items-center justify-content-center min-vh-100">
<div class="col-md-6">
<div class="col-10 mx-auto card p-3">

<div class="text-center mb-4">
<a href="{{ url('/') }}">
<img src="{{ asset('img/logo_web.png') }}" style="max-height:80px;">
</a>
</div>

<h3 class="text-dark text-center">{{ trans('lang.login') }}</h3>
<p class="text-center">{{ trans('lang.sign_in_to_continue') }}</p>

<!-- EMAIL LOGIN -->
<form onsubmit="return loginClick()" id="login-box">
<div class="form-group">
<label>{{ trans('lang.user_email') }}</label>
<input type="email" class="form-control" id="email"
       placeholder="{{ trans('lang.user_email_help_2') }}">
</div>

<!-- PASSWORD WITH SHOW / HIDE -->
<div class="form-group">
<label>{{ trans('lang.password') }}</label>
<div class="password-wrapper">
<input type="password"
       id="password"
       class="form-control"
       placeholder="{{ trans('lang.user_password_help_2') }}">
<span class="password-toggle" data-target="#password">Show</span>
</div>
<div class="error" id="password_required"></div>
</div>

<div class="forgot-password">
<a href="{{ url('forgot-password') }}" target="_blank">
{{ trans('lang.forgot_password') }}?
</a>
</div>

<div id="password_required_new"></div>

<button type="submit" class="btn btn-primary btn-lg btn-block">
{{ trans('lang.log_in') }}
</button>

<a href="{{ route('signup') }}" class="btn btn-primary btn-lg btn-block">
{{ trans('lang.sign_up') }}
</a>

<div class="or-line my-3"><span>OR</span></div>

<button type="button" onclick="loginWithPhoneClick()"
class="btn btn-primary btn-lg btn-block">
<i class="fa fa-phone mr-2"></i> {{ __('Login') }} {{ trans('lang.with_phone') }}
</button>
</form>

<!-- PHONE LOGIN -->
<form id="login-with-phone-box" style="display:none;">
<div class="form-group" id="phone-box">
<select id="country_selector">
@foreach($newcountries as $key => $country)
<option value="{{ $key }}" {{ $country->code == 'IN' ? 'selected' : '' }}>
+{{ $country->phoneCode }} {{ $country->countryName }}
</option>
@endforeach
</select>
<input class="form-control" id="phone" placeholder="{{ trans('lang.user_phone') }}">
</div>

<div class="form-group" id="otp-box" style="display:none;">
<input class="form-control" id="verificationcode" placeholder="{{ trans('lang.otp') }}">
</div>

<div id="recaptcha-container" style="display:none"></div>
<div id="password_required_new1"></div>

<button type="button" id="sendotp_btn" style="display:none"
onclick="sendOTP()" class="btn btn-primary btn-lg btn-block">
{{ trans('lang.otp_send') }}
</button>

<button type="button" id="verify_btn" style="display:none"
onclick="applicationVerifier()" class="btn btn-primary btn-lg btn-block">
{{ trans('lang.otp_verify') }}
</button>

<button type="button" onclick="loginBackClick()"
class="btn btn-primary btn-lg btn-block">
{{ __('Login') }} {{ trans('lang.with_email') }}
</button>
</form>

</div>
</div>
</div>
</div>

<!-- JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-firestore.js"></script>

<!-- SHOW / HIDE PASSWORD SCRIPT -->
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
