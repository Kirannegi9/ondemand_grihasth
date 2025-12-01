@include('auth.default')


<div class="container">
    <div class="row page-titles ">

        <div class="col-md-12 align-self-center text-center">
            <h3 class="text-themecolor  ">{{trans('lang.sign_up_with_us')}}</h3>
        </div>

        <div class="card-body">
            <div id="data-table_processing" class="dataTables_processing panel panel-default"
                 style="display: none;">{{trans('lang.processing')}}
            </div>
            <div class="error_top"></div>
            <div class="alert alert-success" style="display:none;"></div>
            <div class="row restaurant_payout_create">
                <div class="restaurant_payout_create-inner">
                    <fieldset>
                        <legend>{{trans('lang.provider_info')}}</legend>

                        <div class="form-group row width-50">   
                            <label class="col-3 control-label">{{trans('lang.first_name')}}</label>
                            <div class="col-7">
                                <input type="text" class="form-control user_first_name"
                                       onkeypress="return chkAlphabets(event,'error')" required>
                                <div id="error" class="err"></div>
                                <div class="form-text text-muted">
                                    {{ trans("lang.user_first_name_help") }}
                                </div>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{trans('lang.last_name')}}</label>
                            <div class="col-7">
                                <input type="text" class="form-control user_last_name"
                                       onkeypress="return chkAlphabets(event,'error1')">
                                <div id="error1" class="err"></div>
                                <div class="form-text text-muted">
                                    {{ trans("lang.user_last_name_help") }}
                                </div>
                            </div>
                        </div>


                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{trans('lang.email')}}</label>
                            <div class="col-7">
                                <input id="email" type="email" class="form-control user_email"
                                 required>
                                <div class="form-text text-muted">   
                                    {{ trans("lang.user_email_help") }}
                                </div>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{trans('lang.password')}}</label>
                            <div class="col-7">
                                <input id="password" type="password" class="form-control user_password" required>
                                <div class="form-text text-muted">
                                    {{ trans("lang.user_password_help") }}
                                </div>
                            </div>
                        </div>

                        <div class="form-group row width-50">
                            <label class="col-3 control-label">{{trans('lang.user_phone')}}</label>
                            <div class="col-7">
                                <input type="text" class="form-control user_phone"
                                       onkeypress="return chkAlphabets2(event,'error2')">
                                <div id="error2" class="err"></div>
                                <div class="form-text text-muted w-50">
                                    {{ trans("lang.user_phone_help") }}
                                </div>
                            </div>
                        </div>


                        <div class="form-group row width-100">
                            <label class="col-3 control-label">{{trans('lang.user_profile_picture')}}</label>
                            <input type="file" onChange="handleFileSelectowner(event)" class="col-7">
                            <div id="uploding_image_owner"></div>
                            <div class="uploaded_image_owner" style="display:none;"><img
                                        id="uploaded_image_owner"
                                        src="" width="150px"
                                        height="150px;"></div>
                        </div>

                    </fieldset>
                    <fieldset>
                        <legend>{{trans('lang.bankdetails')}}</legend>
                        <div class="form-group row width-100" style="display: none;" id="companyDriverShowDiv">
                            <div class="col-12">
                                <h6><a href="#">{{ trans("lang.driver_add_by_company_info") }}</a>
                                </h6>
                            </div>
                        </div>
                        <div class="form-group row" id="companyDriverHideDiv">

                            <div class="form-group row width-100">
                                <label class="col-4 control-label">{{trans('lang.bank_name')}}</label>
                                <div class="col-7">
                                    <input type="text" name="bank_name" class="form-control" id="bankName">
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-4 control-label">{{trans('lang.branch_name')}}</label>
                                <div class="col-7">
                                    <input type="text" name="branch_name" class="form-control" id="branchName">
                                </div>
                            </div>


                            <div class="form-group row width-100">
                                <label class="col-4 control-label">{{trans('lang.holder_name')}}</label>
                                <div class="col-7">
                                    <input type="text" name="holer_name" class="form-control" id="holderName">
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-4 control-label">{{trans('lang.account_number')}}</label>
                                <div class="col-7">
                                    <input type="text" name="account_number" class="form-control"
                                           onkeypress="return chkAlphabets2(event,'error5')"
                                           id="accountNumber">
                                    <div id="error5" class="err"></div>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-4 control-label">IFSC Code</label>
                                <div class="col-7">
                                    <input type="text" name="ifsc_code" class="form-control"
                                           id="ifscCode" placeholder="e.g., SBIN0001234">
                                    <div class="form-text text-muted">
                                        Enter your bank's IFSC code
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-4 control-label">Account Type</label>
                                <div class="col-7">
                                    <select name="account_type" class="form-control" id="accountType">
                                        <option value="">Select Account Type</option>
                                        <option value="savings">Savings Account</option>
                                        <option value="current">Current Account</option>
                                    </select>
                                    <div class="form-text text-muted">
                                        Select your account type
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row width-100">
                                <label class="col-4 control-label">{{trans('lang.other_information')}}</label>
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

        <div class="form-group col-12 text-center">
            <button type="button" class="btn btn-primary  create_vendor_btn"><i class="fa fa-save"></i>
                {{trans('lang.save')}}
            </button>

            <div class="or-line mb-4 ">
                <span>OR</span>
            </div>

            <div class="new-acc d-flex align-items-center justify-content-center">

                <a href="{{route('register.phone')}}" class="btn btn-primary" id="btn-signup-phone">

                    <i class="fa fa-phone"> </i> {{trans('lang.signup_with_phone')}}

                </a>

            </div>
            <a href="{{route('login')}}">

                <p class="text-center m-0"> {{trans('lang.already_an_account')}} {{trans('lang.sign_in')}}</p>

            </a>
        </div>


    </div>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.1.1/compressor.min.js"
        integrity="sha512-VaRptAfSxXFAv+vx33XixtIVT9A/9unb1Q8fp63y1ljF+Sbka+eMJWoDAArdm7jOYuLQHVx5v60TQ+t3EA8weA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

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

<script>
    var database = firebase.firestore();
    var geoFirestore = new GeoFirestore(database);
    var autoApproveProvider = database.collection('settings').doc("provider"); 
    var photo = "";
    var vendorOwnerId = "";
    var vendorOwnerOnline = false;
    var photocount = 0;
    var restaurnt_photos = [];
    var ownerphoto = ''; 
    var createdAt = firebase.firestore.FieldValue.serverTimestamp();
   

   $(".create_vendor_btn").click(function () {  
        $(".error_top").hide();
        var latitude = parseFloat(0.01);
        var longitude = parseFloat(0.01);

        var userFirstName = $(".user_first_name").val();
        var userLastName = $(".user_last_name").val();
        var email = $(".user_email").val();
        var password = $(".user_password").val();
        var userPhone = $(".user_phone").val();
        
        var location = {'latitude': latitude, 'longitude': longitude};
        var user_name = userFirstName + " " + userLastName;
        var user_id = "<?php echo uniqid(); ?>";

        var active = false;
        var isactive = false;
		
		
		var atposition=email.indexOf("@");  
		var dotposition=email.lastIndexOf(".");   
		
        autoApproveProvider.get().then(async function (snapshots) {
            var providerdata = snapshots.data();
            if (providerdata.auto_approve_provider == true) {
                active = true;
                var isactive = true;
            }  
        });
	   
        if (userFirstName == '') {

            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_owners_name_error')}}</p>");
            window.scrollTo(0, 0);
        } else if (userLastName == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_owners_last_name_error')}}</p>");
            window.scrollTo(0, 0);
        } else if (email == ''){
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_owners_email')}}</p>");
            window.scrollTo(0, 0);
		} else if(atposition<1 || dotposition<atposition+2 || dotposition+2>=email.length) {
				$(".error_top").show();
				$(".error_top").html("");	
				$(".error_top").append("<p>{{trans('lang.enter_owners_validemail')}}</p>");
				window.scrollTo(0, 0);
        } else if (password == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_owners_password_error')}}</p>");
            window.scrollTo(0, 0);
		} else if (password.length <8) {
				$(".error_top").show();
				$(".error_top").html("");	
				$(".error_top").append("<p>{{trans('lang.enter_password_length_error')}}</p>");
				window.scrollTo(0, 0);
        } else if (userPhone == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.enter_owners_phone')}}</p>");
            window.scrollTo(0, 0);

        } else {

            database.collection("users").where('email', '==', email).get().then(async function (snapshots) {
                if (snapshots.docs.length > 0) {
                    alert('You already have account with this Email')
                    return false;
                }
            })

            var bankName = $("#bankName").val();
            var branchName = $("#branchName").val();
            var holderName = $("#holderName").val();
            var accountNumber = $("#accountNumber").val();
            var ifscCode = $("#ifscCode").val();
            var accountType = $("#accountType").val();
            var otherDetails = $("#otherDetails").val();
            var userBankDetails = {
                'bankName': bankName,
                'branchName': branchName,
                'holderName': holderName,
                'accountNumber': accountNumber,
                'ifscCode': ifscCode,
                'accountType': accountType,
                'otherDetails': otherDetails,
            };

            firebase.auth().createUserWithEmailAndPassword(email, password)
                .then(function (firebaseUser) {
                    user_id = firebaseUser.user.uid;
                    database.collection('users').doc(user_id).set({
                        'firstName': userFirstName,
                        'lastName': userLastName,
                        'email': email,
                        'phoneNumber': userPhone,
                        'profilePictureURL': ownerphoto,
                        'role': 'provider',
                        'id': user_id,
                        'location': location,
                        'active': active,
                        'isActive':active,
                        createdAt: createdAt,
                        'userBankDetails': userBankDetails,
                        'wallet_amount':0,
                        'reviewsCount':0,
                        'reviewsSum':0,

                    }).then(function (result) {
                        autoApproveProvider.get().then(async function (snapshots) {
                            var providerdata = snapshots.data();
                                    if (providerdata.auto_approve_provider == false) {
                                        $(".alert-success").show();
                                        $(".alert-success").html("");
                                        $(".alert-success").append("<p>{{trans('lang.signup_waiting_approval')}}</p>");
                                        window.scrollTo(0, 0);
                                        setTimeout(function () {
                                            window.location.href = '{{ route("login")}}';
                                        }, 5000);
                                    } else {
                                        $(".alert-success").show();
                                        $(".alert-success").html("");
                                        $(".alert-success").append("<p>{{trans('lang.thank_you_signup_msg')}}</p>");
                                        window.scrollTo(0, 0);
                                        setTimeout(function () {
                                            window.location.href = '{{ route("login")}}';
                                        }, 5000);
                                    }
                                })
                            
                               

                }).catch(function (error) {

                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>" + error + "</p>");
            });
        

    })

        }
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