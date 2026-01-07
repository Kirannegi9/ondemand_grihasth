<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body>

<style type="text/css">
    .form-group.default-admin {
        padding: 10px;
        font-size: 14px;
        color: #000;
        font-weight: 600;
        border-radius: 10px;
        box-shadow: 0 0px 6px rgba(0,0,0,0.5);
        margin: 20px 10px 10px 10px;
    }

    .login-register {
        background-color: #FF683A;
    }

    <?php if(isset($_COOKIE['admin_panel_color'])){ ?>
        .login-register {
            background-color: <?php echo $_COOKIE['admin_panel_color']; ?>;
        }

        .form-material .form-control,
        .form-material .form-control:focus {
            background-image: linear-gradient(
                <?php echo $_COOKIE['admin_panel_color']; ?>,
                <?php echo $_COOKIE['admin_panel_color']; ?>
            ), linear-gradient(rgba(120,130,140,.13), rgba(120,130,140,.13));
        }
    <?php } ?>

    /* ✅ SHOW / HIDE PASSWORD CSS */
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
        color: #3b383830;
        z-index: 10;
        user-select: none;
    }
</style>

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

                <form class="form-horizontal form-material" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="box-title m-b-20">{{ __('Login') }}</div>

                    <!-- Email -->
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   placeholder="{{ __('Email Address') }}"
                                   required autofocus>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <div class="col-xs-12">
                            <div class="password-wrapper">
                                <input id="password"
                                       type="password"
                                       name="password"
                                       class="form-control"
                                       placeholder="{{ __('Password') }}"
                                       required>

                                <span class="password-toggle" data-target="#password">Show</span>
                            </div>
                        </div>
                    </div>

                    <!-- Remember -->
                    <div class="form-group text-center">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">{{ __('Remember Me') }}</label>
                    </div>

                    <!-- Submit -->
                    <div class="form-group">
                        <button type="submit"
                                class="btn btn-primary btn-lg btn-block">
                            {{ __('Login') }}
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</section>

<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>

<!-- ✅ SHOW / HIDE PASSWORD JS -->
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
