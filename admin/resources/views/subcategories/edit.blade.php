@extends('layouts.app')

@section('content')

<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.subcategory_plural')}}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('subcategories') !!}">{{trans('lang.subcategory_plural')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.subcategory_edit')}}</li>
            </ol>
        </div>

    </div>

    <div class="container-fluid">
        <div class="cat-edite-page max-width-box">
            <div class="card pb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                        <li role="presentation" class="nav-item">
                            <a href="#subcategory_information" aria-controls="subcategory_information" role="tab"
                               data-toggle="tab" class="nav-link active">{{trans('lang.subcategory_information')}}</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">

                    <div class="error_top" style="display:none"></div>

                    <div class="row vendor_payout_create" role="tabpanel">
                        <div class="vendor_payout_create-inner tab-content subcategory_edit_div">

                            <div role="tabpanel" class="tab-pane active" id="subcategory_information">

                                <fieldset>
                                    <legend>{{trans('lang.subcategory_edit')}}</legend>

                                    <div class="form-group row width-100">
                                        <label class="col-3 control-label">{{trans('lang.subcategory_name')}}</label>
                                        <div class="col-7">
                                            <input type="text" class="form-control subcat-name">
                                            <div class="form-text text-muted">{{ trans("lang.subcategory_name_help") }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group row width-100">
                                        <label class="col-3 control-label ">{{trans('lang.subcategory_description')}}</label>
                                        <div class="col-7">
                                            <textarea rows="7" class="subcategory_description form-control"
                                                      id="subcategory_description"></textarea>
                                            <div class="form-text text-muted">{{ trans("lang.subcategory_description_help") }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group row width-100">
                                        <label class="col-3 control-label ">{{trans('lang.select_category')}}</label>
                                        <div class="col-7">
                                            <select name="category_id" id="category_id" class="form-control">
                                                <option value="">{{trans('lang.select')}}</option>
                                            </select>
                                            <p style="color: red;font-size: 13px;">
                                                {{trans('lang.subcategory_select_category_note')}}</p>
                                        </div>
                                    </div>

                                    <div class="form-group row width-100">
                                        <label class="col-3 control-label">{{trans('lang.subcategory_image')}}</label>
                                        <div class="col-7">
                                            <input type="file" id="subcategory_image">
                                            <div class="placeholder_img_thumb subcat_image"></div>
                                            <div id="uploding_image"></div>
                                            <div class="form-text text-muted w-50">{{ trans("lang.subcategory_image_help") }}</div>
                                        </div>
                                    </div>

                                    <div class="form-check width-100">
                                        <input type="checkbox" class="item_publish" id="item_publish">
                                        <label class="col-3 control-label" for="item_publish">{{trans('lang.item_publish')}}</label>
                                    </div>

                                </fieldset>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary edit-subcategory-btn"><i class="fa fa-save"></i>
                        {{trans('lang.save')}}
                    </button>
                    <a href="{!! route('subcategories') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">

    var id = "<?php echo $id; ?>";
    var database = firebase.firestore();
    var ref = database.collection('vendor_subcategories').where("id", "==", id);
    var ref_categories = database.collection('vendor_categories');
    var subcategory = '';
    var photo = "";
    var fileName = '';
    var subcatImageFile = "";
    var placeholderImage = '';
    var placeholder = database.collection('settings').doc('placeHolderImage');
    var storage = firebase.storage();
    var storageRef = firebase.storage().ref('images');

    placeholder.get().then(async function (snapshotsimage) {
        placeholderImage = snapshotsimage.data().image;
    })

    $(document).ready(function () {

        jQuery("#data-table_processing").show();

        // Load categories in dropdown
        ref_categories.get().then(async function (snapshots) {
            snapshots.docs.forEach((listval) => {
                var data = listval.data();
                $('#category_id').append($("<option></option>")
                    .attr("value", data.id)
                    .text(data.title));
            })
        })

        // Load subcategory data
        ref.get().then(async function (snapshots) {
            if(snapshots.docs.length > 0){
                subcategory = snapshots.docs[0].data();

                $(".subcat-name").val(subcategory.title);
                $(".subcategory_description").val(subcategory.description);
                $("#category_id").val(subcategory.category_id);

                photo = subcategory.photo;
                if (photo != '' && photo != null) {
                    subcatImageFile = subcategory.photo;
                    $(".subcat_image").append('<img class="rounded" style="width:50px" src="' + photo + '" alt="image" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">');
                } else {
                    $(".subcat_image").append('<img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');
                }

                if (subcategory.publish) {
                    $(".item_publish").prop('checked', true);
                }
            }
            jQuery("#data-table_processing").hide();
        })

        // Save button click
        $(".edit-subcategory-btn").click(async function () {
            var title = $(".subcat-name").val();
            var description = $(".subcategory_description").val();
            var category_id = $("#category_id").val();
            var itemPublish = $(".item_publish").is(":checked");

            if (title == '') {
                $(".error_top").show().html("<p>{{trans('lang.enter_subcat_title_error')}}</p>");
                window.scrollTo(0, 0);
            } else if (category_id == '') {
                $(".error_top").show().html("<p>{{trans('lang.set_category_error')}}</p>");
                window.scrollTo(0, 0);
            } else if (photo == '') {
                $(".error_top").show().html("<p>{{trans('lang.upload_image_error')}}</p>");
                window.scrollTo(0, 0);
            } else {
                jQuery("#data-table_processing").show();

                storeImageData().then(IMG => {
                    database.collection('vendor_subcategories').doc(id).update({
                        'title': title,
                        'description': description,
                        'photo': IMG,
                        'category_id': category_id,
                        'publish': itemPublish,
                    }).then(function (result) {
                        window.location.href = '{{ route("subcategories")}}';
                    });
                }).catch(function (error) {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show().html("<p>" + error + "</p>");
                })
            }
        });
    });

    // Image upload
    $("#subcategory_image").resizeImg({
        callback: function (base64str) {
            var val = $('#subcategory_image').val().toLowerCase();
            var ext = val.split('.')[1];
            var filename = $('#subcategory_image').val().replace(/C:\\fakepath\\/i, '')
            var timestamp = Number(new Date());
            var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
            photo = base64str;
            fileName = filename;
            $(".subcat_image").empty().append('<img class="rounded" style="width:50px" src="' + photo + '" alt="image">');
            $("#subcategory_image").val('');
        }
    });

    async function storeImageData() {
        var newPhoto = '';
        try {
            if (subcatImageFile != "" && photo != subcatImageFile) {
                var subcatOldImageUrlRef = await storage.refFromURL(subcatImageFile);
                var imageBucket = subcatOldImageUrlRef.bucket;
                var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                if (imageBucket == envBucket) {
                    await subcatOldImageUrlRef.delete().catch((error) => {
                        console.log("ERR File delete ===", error);
                    });
                }
            }
            if (photo != subcatImageFile) {
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
