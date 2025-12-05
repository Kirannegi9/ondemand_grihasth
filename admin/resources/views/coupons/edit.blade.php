@extends('layouts.app')

@section('content')
<div class="page-wrapper">
  <div class="row page-titles">
    <div class="col-md-5 align-self-center">
      <h3 class="text-themecolor">{{trans('lang.coupon_plural')}}</h3>
    </div>

    <div class="col-md-7 align-self-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
        <?php if (isset($_GET['eid']) && $_GET['eid'] != '') { ?>
          <li class="breadcrumb-item"><a
              href="{{route('vendors.coupons',$_GET['eid'])}}">{{trans('lang.coupon_plural')}}</a></li>
        <?php } else { ?>
          <li class="breadcrumb-item"><a href="{!! route('coupons') !!}">{{trans('lang.coupon_plural')}}</a></li>
        <?php } ?>
        <li class="breadcrumb-item active">{{trans('lang.coupon_edit')}}</li>
      </ol>
    </div>

  </div>
  <div>

    <div class="card-body">

      <div class="error_top" style="display:none"></div>

      <div class="row vendor_payout_create">

        <div class="vendor_payout_create-inner">

          <fieldset>
            <legend>{{trans('lang.coupon_edit')}}</legend>

            <div class="form-group row width-50">
              <label class="col-3 control-label">{{trans('lang.coupon_code')}}</label>
              <div class="col-7">
                <input type="text" type="text" class="form-control coupon_code">
                <div class="form-text text-muted">{{ trans("lang.coupon_code_help") }} </div>
              </div>
            </div>

            <div class="form-group row width-50">
              <label class="col-3 control-label">{{trans('lang.coupon_discount_type')}}</label>
              <div class="col-7">
                <select id="coupon_discount_type" class="form-control">
                  <option value="Percentage">{{trans('lang.coupon_percent')}}</option>
                  <option value="Fix Price">{{trans('lang.coupon_fixed')}}</option>
                </select>
                <div class="form-text text-muted">{{ trans("lang.coupon_discount_type_help") }}</div>
              </div>
            </div>

            <div class="form-group row width-50">
              <label class="col-3 control-label">{{trans('lang.coupon_discount')}}</label>
              <div class="col-7">
                <input type="number" type="text" class="form-control coupon_discount">
                <div class="form-text text-muted">{{ trans("lang.coupon_discount_help") }}</div>
              </div>
            </div>

            <div class="form-group row width-50">
              <label class="col-3 control-label">{{trans('lang.coupon_expires_at')}}</label>
              <div class="col-7">
                <div class='input-group date' id='datetimepicker1'>
                  <input type='text' class="form-control date_picker input-group-addon" />
                  <span class="">
                  </span>
                </div>
                <div class="form-text text-muted">
                  {{ trans("lang.coupon_expires_at_help") }}
                </div>
              </div>
            </div>

            <div class="form-group row width-100">
              <label class="col-3 control-label">{{trans('lang.coupon_vendor_id')}}</label>
              <div class="col-7">
                <select id="vendor_vendor_select" class="form-control" multiple="multiple" style="width: 100%;">
                </select>
                <div class="form-text text-muted">
                  {{ trans("lang.coupon_vendor_id_help") }}
                </div>
              </div>
            </div>

            <div class="form-group row width-100">
              <label class="col-3 control-label">{{trans('lang.coupon_description')}}</label>
              <div class="col-7">
                <textarea rows="12" class="form-control coupon_description" id="coupon_description"></textarea>
                <div class="form-text text-muted">{{ trans("lang.coupon_description_help") }}</div>
              </div>
            </div>

            <div class="form-group row width-100">
              <label class="col-3 control-label">{{trans('lang.category_image')}}</label>
              <div class="col-7">
                <input type="file" onChange="handleFileSelect(event)">
                <div class="placeholder_img_thumb coupon_image"></div>
                <div id="uploding_image"></div>
              </div>
            </div>

            <div class="form-group row width-100">
              <div class="form-check">
                <input type="checkbox" class="coupon_enabled" id="coupon_enabled">
                <label class="col-3 control-label" for="coupon_enabled">{{trans('lang.coupon_enabled')}}</label>

              </div>
            </div>

            <div class="form-group row width-100">
              <div class="form-check">
                <input type="checkbox" class="coupon_public" id="coupon_public">
                <label class="col-3 control-label" for="coupon_public">{{trans('lang.coupon_public')}}</label>
              </div>
            </div>

          </fieldset>
        </div>

      </div>

    </div>
    <div class="form-group col-12 text-center btm-btn">
      <button type="button" class="btn btn-primary edit-form-btn"><i class="fa fa-save"></i> {{
        trans('lang.save')}}</button>
      <?php if (isset($_GET['eid']) && $_GET['eid'] != '') { ?>
        <a href="{{route('vendors.coupons',$_GET['eid'])}}" class="btn btn-default"><i
            class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
      <?php } else { ?>
        <a href="{!! route('coupons') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
      <?php } ?>
    </div>

  </div>

  @endsection

  @section('scripts')

  <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
  <link href="{{ asset('css/bootstrap-datepicker.min.css') }}" rel="stylesheet">

  <!-- Choices.js -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

  <style>
    .choices__inner {
      min-height: 38px;
      padding: 5px 7.5px;
      border: 1px solid #d2d6de;
      border-radius: 4px;
      background-color: #ffffff;
    }
    .choices__list--multiple .choices__item {
      background-color: #3c8dbc;
      border: 1px solid #367fa9;
      color: #fff;
      padding: 4px 10px;
      margin: 2px;
      border-radius: 3px;
    }
    .choices__list--multiple .choices__item.is-highlighted {
      background-color: #367fa9;
      border: 1px solid #2f6c8e;
    }
    .choices[data-type*=select-multiple] .choices__button {
      border-left: 1px solid rgba(255,255,255,0.3);
      opacity: 1;
    }
    .choices__list--dropdown .choices__item--selectable.is-highlighted {
      background-color: #3c8dbc;
    }
    .choices.is-focused .choices__inner {
      border-color: #3c8dbc;
    }
  </style>

  <script type="text/javascript">

    var id = "<?php echo $id; ?>";
    var database = firebase.firestore();
    var ref = database.collection('coupons').where("id", "==", id);
    var photo = "";
    var fileName = "";
    var oldImageFile = "";
    var placeholderImage = '';
    var placeholder = database.collection('settings').doc('placeHolderImage');
    var storage = firebase.storage();
    var storageRef = firebase.storage().ref('images');
    var vendorChoices;

    placeholder.get().then(async function (snapshotsimage) {
      var placeholderImageData = snapshotsimage.data();
      placeholderImage = placeholderImageData.image;
    })

    $(document).ready(function () {

      $(function () {
        $('#datetimepicker1 .date_picker').datepicker({
          dateFormat: 'mm/dd/yyyy',
          startDate: new Date(),
        });
      });

      jQuery("#data-table_processing").show();
      ref.get().then(async function (snapshots) {
        var coupon = snapshots.docs[0].data();

        // Get vendor IDs from coupon (check both old and new field)
        var selectedVendorIDs = [];
        if (coupon.vendorIDs && Array.isArray(coupon.vendorIDs)) {
          selectedVendorIDs = coupon.vendorIDs;
        } else if (coupon.vendorID) {
          selectedVendorIDs = [coupon.vendorID];
        }

        // Initialize Choices.js for multi-select
        vendorChoices = new Choices('#vendor_vendor_select', {
          removeItemButton: true,
          placeholder: true,
          placeholderValue: "{{trans('lang.select_vendor')}}",
          searchPlaceholderValue: "{{trans('lang.search')}}",
          noResultsText: "{{trans('lang.no_results_found')}}",
          itemSelectText: "{{trans('pres to select')}}",
        });

        await database.collection('vendors').get().then(async function (snapshots) {

          let vendorChoicesArray = [];
          
          snapshots.docs.forEach((listval) => {
            var data = listval.data();
            var isSelected = selectedVendorIDs.includes(data.id);
            
            vendorChoicesArray.push({
              value: data.id,
              label: data.title,
              selected: isSelected,
              customProperties: {
                sectionId: data.section_id
              }
            });
          })
          
          // Set choices
          vendorChoices.setChoices(vendorChoicesArray, 'value', 'label', true);
        });

        if (coupon.image != '' && coupon.image != null) {
          photo = coupon.image;
          oldImageFile = coupon.image;
          $(".coupon_image").append('<img class="rounded" style="width:50px" src="' + photo + '" alt="image" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">');
        } else {

          $(".coupon_image").append('<img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');
        }
        $(".coupon_code").val(coupon.code);
        $("#coupon_discount_type").val(coupon.discountType);
        $(".coupon_discount").val(parseInt(coupon.discount));
        $(".coupon_description").val(coupon.description);
        const expireDate = new Date(coupon.expiresAt.toDate()); 
        const currentDate = new Date();
        const isExpired = expireDate < currentDate;
        if (isExpired){
            $(".coupon_enabled").prop("disabled", true);
        }
        else if (coupon.isEnabled) { 
            $(".coupon_enabled").prop("checked", true);
        }
        if (coupon.isPublic) {
          $(".coupon_public").prop("checked", true);
        }

        if (coupon.hasOwnProperty("expiresAt")) {

          try {
            var date1 = coupon.expiresAt.toDate().toDateString();
            var date = new Date(date1);
            var dd = String(date.getDate()).padStart(2, '0');
            var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = date.getFullYear();
            var expiresDate = mm + '/' + dd + '/' + yyyy;
          }
          catch (err) {

            var date1 = '';
            var date = '';
            var dd = '';
            var mm = '';
            var yyyy = '';
            var expiresDate = '';

          }
          var $datepicker = $('.date_picker');
          $datepicker.datepicker();
          $datepicker.datepicker('setDate', expiresDate);
        }

        var resturant = "<?php echo $id; ?>";

        jQuery("#data-table_processing").hide();

      })
 

      $(".edit-form-btn").click(function () {

        var code = $(".coupon_code").val();
        var discount = $(".coupon_discount").val();
        var description = $(".coupon_description").val();
        var newdate = new Date($(".date_picker").val());
        var expiresAt = new Date(newdate.setHours(23, 59, 59, 999));
        var isEnabled = $(".coupon_enabled").is(":checked");
        var discountType = $("#coupon_discount_type").val();
        var vendorIDs = vendorChoices.getValue(true) || [];
        var isPublic = $(".coupon_public").is(":checked");

        // Get section ID from first selected vendor
        var sectionId = '';
        if (vendorIDs.length > 0) {
          var selectedItems = vendorChoices._store.choices.filter(choice => choice.value === vendorIDs[0]);
          if (selectedItems.length > 0 && selectedItems[0].customProperties) {
            sectionId = selectedItems[0].customProperties.sectionId || '';
          } else {
            sectionId = '';
          }
        }

        var codeAlreadyExist = false;

        database.collection('coupons').where('id','!=',id).where('code', '==', code).get().then(async function (snapshot) {
              if (!snapshot.empty && snapshot.docs.length > 0) {
                  codeAlreadyExist = true;
              }
              if (code == '') {
                  $(".error_top").show();
                  $(".error_top").html("");
                  $(".error_top").append("<p>{{trans('lang.enter_coupon_code_error')}}</p>");
                  window.scrollTo(0, 0);  
              } else if (discount == '') {
                  $(".error_top").show();
                  $(".error_top").html("");
                  $(".error_top").append("<p>{{trans('lang.enter_coupon_discount_error')}}</p>");
                  window.scrollTo(0, 0);
              } else if (discountType == '') {
                  $(".error_top").show();
                  $(".error_top").html("");
                  $(".error_top").append("<p>{{trans('lang.select_coupon_discountType_error')}}</p>");
                  window.scrollTo(0, 0);
              } else if (vendorIDs.length == 0) {
                  $(".error_top").show();
                  $(".error_top").html("");
                  $(".error_top").append("<p>{{trans('lang.select_store_error')}}</p>");
                  window.scrollTo(0, 0);
              } else if (newdate == 'Invalid Date') {
                  $(".error_top").show();
                  $(".error_top").html("");
                  $(".error_top").append("<p>{{trans('lang.select_coupon_expdate_error')}}</p>");
                  window.scrollTo(0, 0);
              }  else if (codeAlreadyExist == true) {
                  $(".error_top").show();
                  $(".error_top").html("");
                  $(".error_top").append("<p>{{trans('lang.enter_coupon_code_already_exist_error')}}</p>");
                  window.scrollTo(0, 0);
              } else if (discountType == "Percentage" && (discount >= 100 || discount < 0)) {
                  $(".error_top").show();
                  $(".error_top").html("");
                  $(".error_top").append("<p>{{trans('lang.enter_coupon_percentage_discount_error')}}</p>");
                  window.scrollTo(0, 0);
              } else {
                  jQuery("#data-table_processing").show();
                  storeImageData().then(IMG => {
                      database.collection('coupons').doc(id).update({ 'code': code, 'description': description, 'discount': discount, 'expiresAt': expiresAt, 'isEnabled': isEnabled, 'id': id, 'discountType': discountType, 'image': IMG, 'vendorID': vendorIDs[0], 'vendorIDs': vendorIDs, 'section_id': sectionId, 'isPublic': isPublic }).then(function (result) {

                          <?php if (isset($_GET['eid']) && $_GET['eid'] != '') { ?>

                          window.location.href = "{{ route('vendors.coupons',$_GET['eid']) }}";
                          <?php } else { ?>

                          window.location.href = '{{ route("coupons")}}';

                          <?php } ?>

                      });
                  }).catch(err => {
                      jQuery("#data-table_processing").hide();
                      $(".error_top").show();
                      $(".error_top").html("");
                      $(".error_top").append("<p>" + err + "</p>");
                      window.scrollTo(0, 0);
                  });
              }
          });
      })
    })


    function handleFileSelect(evt) {
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
          fileName = filename;
          photo = filePayload;
          $(".coupon_image").empty();
          $(".coupon_image").append('<img class="rounded" style="width:50px" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="image">');
        };
      })(f);
      reader.readAsDataURL(f);
    }
    async function storeImageData() {
        var newPhoto = '';
        try {
            if (oldImageFile != "" && photo != oldImageFile) {
                var oldImageUrl = await storage.refFromURL(oldImageFile);
                imageBucket = oldImageUrl.bucket;
                var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
            if (imageBucket == envBucket) {
              await oldImageUrl.delete().then(() => {
                console.log("Old file deleted!")
              }).catch((error) => {
                console.log("ERR File delete ===", error);
              });
            } else {
              console.log('Bucket not matched');
            }
          }
          if (photo != oldImageFile) {
            photo = photo.replace(/^data:image\/[a-z]+;base64,/, "")
            var uploadTask = await storageRef.child(fileName).putString(photo, 'base64', { contentType: 'image/jpg' });
            var downloadURL = await uploadTask.ref.getDownloadURL();
            newPhoto = downloadURL;
            photo = downloadURL;

          } else {
            newPhoto = photo;
          }
        } catch (error) {
          console.log("ERR ===", error);
        }
        return newPhoto;
      }

  </script>
  @endsection