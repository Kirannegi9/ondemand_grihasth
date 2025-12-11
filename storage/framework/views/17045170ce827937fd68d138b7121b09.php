<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"/>

<header>
  
  <style>
    /* Logo sizing */
    .logo-wrapper img { max-height: 60px; width: auto; }
    @media (min-width: 992px) { .logo-wrapper img { max-height: 80px; } }
    @media (max-width: 768px) { .logo-wrapper img { max-height: 50px; } }

    /* Header container pinned and reserve right space for toggle */
    .header-main {
      position: relative !important; /* anchor for absolute toggle */
      min-height: 84px !important;
      display: flex !important;
      align-items: center !important;
      box-sizing: border-box !important;
      padding-right: 140px !important; /* reserve space for toggle so items don't go under it */
      background: #ffffff;
      z-index: 20;
    }
    .header-main .container { width:100% !important; }

    /* Right area layout */
    .header-right {
      display:flex !important;
      align-items:center !important;
      justify-content:flex-end !important;
      width:100% !important;
    }
    .header-right > .d-flex {
      flex-wrap: nowrap !important;
      gap: 12px !important;
      align-items: center !important;
      box-sizing: border-box !important;
    }

    /* Make interactive elements shrink and truncate to avoid pushing */
    .header-right .widget-header,
    .header-right .widget-header .icon,
    .header-right .dropdown,
    .header-right .icon,
    .header-right .takeaway-div,
    .header-right .language-list,
    .header-right .toggle {
      min-width: 0 !important;
      white-space: nowrap !important;
    }

    .header-right .widget-header .icon span,
    .header-right .offer-link span,
    .header-right .language-list .language-options select,
    .header-right .takeaway-div span {
      display:inline-block !important;
      max-width:120px !important;
      overflow:hidden !important;
      text-overflow:ellipsis !important;
      white-space:nowrap !important;
      vertical-align:middle !important;
    }

    /* user location input bounded */
    #user_locationnew {
      max-width:260px !important;
      min-width:120px !important;
      width:100%;
      display:inline-block;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
    }
    .head-search .dropdown { min-width: 0; }

    /* language selector compact */
    #language_dropdown_box {
      display:inline-flex;
      align-items:center;
      min-width:110px;
      max-width:160px;
      overflow:hidden;
      white-space:nowrap;
    }
    #language_dropdown_box .language-options { min-width:0; flex:1 1 auto; }
    #language_dropdown_box select { width:100%; max-width:140px; }

    /* ===== Force toggle to the right and block any left/float overrides ===== */
    .header-main > .toggle,
    .header-main .toggle {
      position: absolute !important;
      right: 270px !important;
      left: auto !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
      z-index: 99999 !important;
      display: inline-block !important;
      float: none !important;
      margin: 0 !important;
      padding: 6px !important;
      background: transparent !important;
      border: none !important;
      pointer-events: auto !important;
    }

    /* Safety: override any theme rules that might float/left .toggle */
    .toggle { float: none !important; text-align: center !important; }

    /* Small screens: flow inline */
    @media (max-width: 576px) {
      .header-main { padding-right: 12px !important; min-height: 68px !important; }
      .header-main > .toggle { position: static !important; transform: none !important; right: auto !important; }
      .header-right > .d-flex { padding-right: 0; }
    }

    /* Dropdown hover smoothness */
    .dropdown:hover > .dropdown-menu { display:block !important; opacity:1 !important; visibility:visible !important; }
    .dropdown-menu { margin-top: 10px; transition: opacity .12s ease; }

    /* Responsive safety */
    @media (max-width: 768px) {
      #user_locationnew { max-width:140px; }
      #language_dropdown_box { min-width:90px; max-width:110px; }
    }
  </style>

  <!-- ========== JS: defensive positioning + language dir ========== -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      try {
        document.documentElement.lang = '<?php echo e(session("locale", "en")); ?>';
        document.documentElement.dir = 'ltr';
        document.body.style.direction = 'ltr';
      } catch(e){ console.warn(e); }

      // Defensive: ensure toggle is absolutely on the right (in case another stylesheet changes it)
      var t = document.querySelector('.header-main > .toggle') || document.querySelector('.header-main .toggle');
      if (t) {
        t.style.position = 'absolute';
        t.style.right = '12px';
        t.style.left = 'auto';
        t.style.top = '50%';
        t.style.transform = 'translateY(-50%)';
      }
    });

    <?php if (Session::get('takeawayOption') == 'true' || Session::get('takeawayOption') === true) { ?>
    var takeaway_options = true;                
    <?php } else { ?>
    var takeaway_options = false;
    <?php } ?>

    function takeAwayOnOff(takeAway) {
      var check_val;
      var spanElement = takeAway.parentElement.querySelector('span');

      if (takeAway.checked == true) {
        check_val = true;
        takeaway_options = true;
        spanElement.textContent = ' <?php echo e(trans("lang.take_away")); ?> ';
      } else {
        check_val = false;
        takeaway_options = false;
        spanElement.textContent = ' <?php echo e(trans("lang.delivery")); ?> ';
      }

      $.ajax({
        data: {
          takeawayOption: check_val,
          "_token": "<?php echo e(csrf_token()); ?>",
        },
        url: 'takeaway',
        type: 'POST',
        success: function (result) {
          try { result = $.parseJSON(result); } catch(e){}
          if (check_val) {
            spanElement.textContent = ' <?php echo e(trans("lang.take_away")); ?> ';
          } else {
            spanElement.textContent = ' <?php echo e(trans("lang.delivery")); ?> ';
          }
        }
      });
    }
  </script>

  <section class="header-main shadow-sm bg-white">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-2">
          <a href="<?php echo e(url('/')); ?>" class="logo-wrapper mb-0">
            <img alt="#" class="img-fluid" src="<?php echo e(asset('img/logo_web.png')); ?>" id="logo_web">
          </a>
        </div>

        <div class="col-3 d-flex align-items-center m-none head-search">
          <div class="dropdown ml-4">
            <a class="text-dark dropdown-toggle d-flex align-items-center p-0" href="#" id="navbarDropdown"
               role="button" aria-haspopup="true" aria-expanded="false">
              <div class="head-loc" onclick="getCurrentLocation('reload')">
                <i class="feather-map-pin mr-2 bg-light rounded-pill p-2 icofont-size"></i>
              </div>
              <div>
                <input id="user_locationnew" type="text" size="50" class="pac-target-input">
              </div>
            </a>
          </div>
        </div>

        <div class="col-7 header-right">
          <div class="d-flex align-items-center justify-content-end pr-5">
            <?php if (@$_COOKIE['service_type'] == 'On Demand Service'){ ?>
            <a href="<?php echo e(url('ondemand-search')); ?>" class="widget-header mr-4 text-dark">
              <div class="icon d-flex align-items-center">
                <i class="feather-search h6 mr-2 mb-0"></i> <span><?php echo e(trans('lang.search')); ?></span>
              </div>
            </a>
            <?php } ?>
            <?php if (@$_COOKIE['service_type'] == 'Multivendor Delivery Service' || @$_COOKIE['service_type'] == 'Ecommerce Service'){ ?>
            <a href="<?php echo e(url('search')); ?>" class="widget-header mr-4 text-dark">
              <div class="icon d-flex align-items-center">
                <i class="feather-search h6 mr-2 mb-0"></i> <span><?php echo e(trans('lang.search')); ?></span>
              </div>
            </a>
            <?php } ?>

            <a href="<?php echo e(url('offers')); ?>" class="widget-header mr-4 text-dark offer-link">
              <div class="icon d-flex align-items-center">
                <img alt="#" class="img-fluid mr-2" src="<?php echo e(asset('img/discount.png')); ?>">
                <span><?php echo e(trans('lang.offers')); ?></span>
              </div>
            </a>

            <?php if(auth()->guard()->check()): ?>
            <?php else: ?>
            <a href="<?php echo e(url('login')); ?>" class="widget-header mr-4 text-dark m-none">
              <div class="icon d-flex align-items-center">
                <i class="feather-user h6 mr-2 mb-0"></i> <span><?php echo e(trans('lang.sign_in')); ?></span>
              </div>
            </a>
            <?php endif; ?>

            <div class="dropdown mr-4 m-none">
              <a href="#" class="dropdown-toggle text-dark py-3 d-block" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                <?php if(auth()->guard()->check()): ?>
                  <a class="dropdown-item" href="<?php echo e(url('profile')); ?>"><?php echo e(trans('lang.my_account')); ?></a>
                  <?php if (@$_COOKIE['service_type'] == "Multivendor Delivery Service" || @$_COOKIE['service_type'] == 'Ecommerce Service') { ?>
                    <a class="dropdown-item" href="<?php echo e(url('vendors')); ?>"><?php echo e(trans('lang.all_store')); ?></a>
                  <?php } ?>
                  <?php if (@$_COOKIE['service_type'] == "On Demand Service") { ?>
                    <a class="dropdown-item" href="<?php echo e(url('ondemand-services')); ?>"><?php echo e(trans('lang.all_services')); ?></a>
                  <?php } ?>
                  <?php if(@$_COOKIE['dine_in_active'] && @$_COOKIE['dine_in_active'] == 'true'): ?>
                    <a class="dropdown-item dine_in_menu" href="<?php echo e(url('vendors')); ?>?dinein=1"><?php echo e(trans('lang.dine_in_vendor')); ?></a>
                  <?php endif; ?>
                  <a class="dropdown-item" href="<?php echo e(route('faq')); ?>"><?php echo e(trans('lang.delivery_support')); ?></a>
                  <a class="dropdown-item" href="<?php echo e(route('contact_us')); ?>"><?php echo e(trans('lang.contact_us')); ?></a>
                  <a class="dropdown-item" href="<?php echo e(route('terms')); ?>"><?php echo e(trans('lang.terms_use')); ?></a>
                  <a class="dropdown-item" href="<?php echo e(route('privacy')); ?>"><?php echo e(trans('lang.privacy_policy')); ?></a>
                  <a class="dropdown-item" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><?php echo e(trans('lang.logout')); ?></a>
                <?php else: ?>
                  <?php if (@$_COOKIE['service_type'] == "Multivendor Delivery Service" || @$_COOKIE['service_type'] == 'Ecommerce Service') { ?>
                    <a class="dropdown-item" href="<?php echo e(url('vendors')); ?>"><?php echo e(trans('lang.all_store')); ?></a>
                  <?php } ?>
                  <?php if (@$_COOKIE['service_type'] == "On Demand Service") { ?>
                    <a class="dropdown-item" href="<?php echo e(url('ondemand-services')); ?>"><?php echo e(trans('lang.all_services')); ?></a>
                  <?php } ?>
                  <?php if(@$_COOKIE['dine_in_active'] && @$_COOKIE['dine_in_active'] == 'true'): ?>
                    <a class="dropdown-item dine_in_menu" href="<?php echo e(url('vendors')); ?>?dinein=1"><?php echo e(trans('lang.dine_in_vendor')); ?></a>
                  <?php endif; ?>
                  <a class="dropdown-item" href="<?php echo e(route('faq')); ?>"><?php echo e(trans('lang.delivery_support')); ?></a>
                  <a class="dropdown-item" href="<?php echo e(route('contact_us')); ?>"><?php echo e(trans('lang.contact_us')); ?></a>
                  <a class="dropdown-item" href="<?php echo e(route('terms')); ?>"><?php echo e(trans('lang.terms_use')); ?></a>
                  <a class="dropdown-item" href="<?php echo e(route('privacy')); ?>"><?php echo e(trans('lang.privacy_policy')); ?></a>
                <?php endif; ?>
              </div>
            </div>

            <?php if (@$_COOKIE['service_type'] == "Multivendor Delivery Service" || @$_COOKIE['service_type'] == 'Ecommerce Service'){ ?>
            <a href="<?php echo e(url('/checkout')); ?>" class="widget-header mr-4 text-dark">
              <div class="icon d-flex align-items-center">
                <i class="feather-shopping-cart h6 mr-2 mb-0"></i> <span><?php echo e(trans('lang.cart')); ?></span>
              </div>
            </a>
            <?php } ?>

            <?php if (@$_COOKIE['service_type'] == 'Multivendor Delivery Service') { ?>
            <div class="icon d-flex align-items-center text-dark takeaway-div">
              <span class="takeaway-btn">
                <i class="fa fa-car h6 mr-1 mb-0"></i> <span> <?php echo e(trans('lang.delivery')); ?> </span>
                <input type="checkbox" onclick="takeAwayOnOff(this)" <?php if (Session::get('takeawayOption') == "true") { ?> checked <?php } ?>> <span class="slider round"></span>
              </span>
            </div>
            <?php } ?>

            <div class="language-list icon d-flex align-items-center text-dark ml-2" id="language_dropdown_box">
              <div class="language-select"><i class="feather-globe"></i></div>
              <div class="language-options"><select class="form-control changeLang text-dark" id="language_dropdown"></select></div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <!-- Toggle MUST be direct child of header-main so absolute right works reliably -->
    <a class="toggle" href="#"><span></span></a>
  </section>
</header>

<!-- mobile hidden block (kept similar to original) -->
<div class="d-none">
  <div class="p-3 d-flex align-items-center" style="background-color: #c06e02 !important;">
    <a class="toggle togglew toggle-2" href="#"><span></span></a>
    <a href="<?php echo e(url('/')); ?>" class="mobile-logo logo-wrapper mb-0">
      <img alt="#" class="img-fluid" src="<?php echo e(asset('img/logo_web.png')); ?>">
    </a>
    <div class="language-list icon d-flex align-items-center text-dark ml-2" id="language_dropdown_box_mobile">
      <div class="language-select"><i class="feather-globe"></i></div>
      <div class="language-options"><select class="form-control changeLang text-dark" id="language_dropdown2"></select></div>
    </div>
  </div>
</div>
<?php /**PATH E:\ondemand_grihasth-master\ondemand_grihasth-master\resources\views/layouts/header.blade.php ENDPATH**/ ?>