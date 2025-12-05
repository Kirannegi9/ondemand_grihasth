@extends('layouts.app')

@section('content')

<div class="page-wrapper">
    <div class="row page-titles">

        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.payout_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>


                <li class="breadcrumb-item"><a href="{!! route('payouts') !!}">{{trans('lang.payout_table')}}</a>
                </li>

                <li class="breadcrumb-item">{{trans('lang.payout_create')}}</li>
            </ol>
        </div>
    </div>

    <div class="card-body">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
            {{trans('lang.processing')}}
        </div>
        <div class="error_top"></div>
        <div class="row vendor_payout_create">
            <div class="vendor_payout_create-inner">
                <fieldset>
                    <legend>{{trans('lang.payout_create')}}</legend>

                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{trans('lang.payout_amount')}}</label>
                        <div class="col-7">
                            <input type="number" class="form-control payout_amount">
                            <div class="form-text text-muted" min="0">
                                {{ trans("lang.payout_amount_placeholder") }}
                            </div>
                        </div>
                    </div>


                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{ trans('lang.payout_note')}}</label>
                        <div class="col-7">
                            <textarea type="text" rows="8" class="form-control payout_note"></textarea>
                        </div>
                    </div>

                </fieldset>
            </div>
        </div>
    </div>

    <div class="form-group col-12 text-center btm-btn">
        <button type="button" class="btn btn-primary save_vendor_payout_btn"><i class="fa fa-save"></i>
            {{trans('lang.save')}}
        </button>
        <a href="{!! route('payouts') !!}" class="btn btn-default"><i
                class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
    </div>
</div>
</div>
</div>

@endsection

@section('scripts')

<script>

    var database = firebase.firestore();
    var email_templates = database.collection('email_templates').where('type', '==', 'payout_request');

    var emailTemplatesData = null;

    var adminEmail = '';

    var emailSetting = database.collection('settings').doc('emailSetting');

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

    async function remainingPrice(providerId) {
        var remaining = 0;

        await database.collection('users').where("id", "==", providerId).get().then(async function (snapshotss) {
            if (snapshotss.docs.length) {
                userdata = snapshotss.docs[0].data();
                if (isNaN(userdata.wallet_amount) || userdata.wallet_amount == undefined) {
                    remaining = 0;
                } else {
                    remaining = userdata.wallet_amount;
                }
            }
        });
        return remaining;
    }

    var userName = '';
    var userContact = '';

    $(document).ready(function () {
        $("#data-table_processing").show();

        email_templates.get().then(async function (snapshots) {
            emailTemplatesData = snapshots.docs[0].data();

        });


        emailSetting.get().then(async function (snapshots) {
            var emailSettingData = snapshots.data();

            adminEmail = emailSettingData.userName;
        });

        $("#data-table_processing").hide();

        
        $(".save_vendor_payout_btn").click(async function () {

            var payoutId =  database.collection('tmp').doc().id;
            var providerId = "<?php echo $id; ?>";
            var providerEmail= await getProviderEmail(providerId);
            var remaining = 0;

            remainingPrice(providerId).then(data => {
                var remaining = data;

                if (remaining > 0) {

                    var amount = parseFloat($(".payout_amount").val());
                    var note = $(".payout_note").val();
                    var date = new Date(Date.now());
                    
                    if (providerId != '' && $(".payout_amount").val() != '') {

                        price = remaining - amount;
                    
                        database.collection('users').doc(providerId).update({ 'wallet_amount': price }).then(function (result) {

                            database.collection('payouts').doc(payoutId).set({
                                'vendorID': providerId,
                                'amount': amount,
                                'note': note,
                                'id': payoutId,
                                'paymentStatus': 'Pending',
                                'paidDate': date,
                                'adminNote':'',
                                'role':'provider'
                            }).then(async function () {

                                if (currencyAtRight) {
                                    amount = parseInt(amount).toFixed(decimal_degits) + "" + currentCurrency;
                                } else {
                                    amount = currentCurrency + "" + parseInt(amount).toFixed(decimal_degits);
                                }

                                var formattedDate = new Date();
                                var month = formattedDate.getMonth() + 1;
                                var day = formattedDate.getDate();
                                var year = formattedDate.getFullYear();

                                month = month < 10 ? '0' + month : month;
                                day = day < 10 ? '0' + day : day;

                                formattedDate = day + '-' + month + '-' + year;

                                var subject = emailTemplatesData.subject;
                                subject = subject.replace(/{userid}/g, providerId);

                                emailTemplatesData.subject = subject;

                                var message = emailTemplatesData.message;
                                message = message.replace(/{userid}/g, providerId);
                                message = message.replace(/{date}/g, formattedDate);
                                message = message.replace(/{amount}/g, amount);
                                message = message.replace(/{payoutrequestid}/g, payoutId);
                                message = message.replace(/{username}/g, userName);
                                message = message.replace(/{usercontactinfo}/g, userContact);

                                emailTemplatesData.message = message;

                                var url = "{{url('send-email')}}";

                                function removeInvalidEmails(emails) {
                                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                                    return emails.filter(email => emailRegex.test(email));
                                }
                                let emails = [adminEmail, providerEmail];
                                let filteredEmails = removeInvalidEmails(emails);
                                var sendEmailStatus = await sendEmail(url, emailTemplatesData.subject, emailTemplatesData.message,filteredEmails);

                                if (sendEmailStatus) {
                                    window.location.href = "{{route('payouts')}}";
                                }
                            });
                        });

                    } else {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(window).scrollTop(0);
                        $(".error_top").append("<p>{{trans('lang.please_enter_details')}}</p>");

                    }
                } else {

                    $(".error_top").show();
                    $(window).scrollTop(0);
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.insufficient_payment_error')}}</p>");
                }

            });
        })
    })

    async function getProviderEmail(providerUser) {
        var userEmail = '';
        await database.collection('users').where('id', "==", providerUser).get().then(async function (providerSnapshots) {
            if (providerSnapshots.docs[0]) {
                var providerData = providerSnapshots.docs[0].data();
                userEmail = providerData.email;
                userName = providerData.firstName + " " + providerData.lastName;
                userContact = providerData.phoneNumber;
            }
        });
        return userEmail;
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
            success: function (data) {
                checkFlag = true;
            },
            error: function (xhr, status, error) {
                checkFlag = true;
            }
        });

        return checkFlag;

    }

</script>

@endsection