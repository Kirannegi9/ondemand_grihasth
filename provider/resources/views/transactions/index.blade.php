@extends('layouts.app')

@section('content')

<div class="page-wrapper">


    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">{{trans('lang.wallet_transaction_plural')}} <span class="userTitle"></span>
            </h3>

        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.wallet_transaction_plural')}}</li>
            </ol>
        </div>

        <div> 

        </div>

    </div>


    <div class="container-fluid">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">
            {{trans('lang.processing')}}
        </div>
        <div class="row">

            <div class="col-12">

                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                            <li class="nav-item">
                                <a class="nav-link active" href="{!! url()->current() !!}"><i
                                            class="fa fa-list mr-2"></i>{{trans('lang.wallet_transaction_table')}}
                                </a>
                            </li>

                        </ul>
                    </div>
                    <div class="card-body">


                        <div class="table-responsive m-t-10">


                            <table id="transactionsTable"
                                   class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                   cellspacing="0" width="100%">

                                <thead>

                                <tr>
                                    <th>{{trans('lang.amount')}}</th>
                                    <th>{{trans('lang.date')}}</th>
                                    <th>{{trans('lang.payment_methods')}}</th>
                                    <th>{{trans('lang.vendors_payout_note')}}</th>
                                    <th>{{trans('lang.payment_status')}}</th>
                                    <th>{{trans('lang.actions')}}</th>
                                </tr>

                                </thead>

                                <tbody id="append_list1">


                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</div>
</div>

@endsection

@section('scripts')

<script>
    var database = firebase.firestore();
    var offest = 1;
    var pagesize = 10;
    var end = null;
    var endarray = [];
    var start = null;
    var user_number = [];
    var provider_id = "<?php echo $id; ?>";
    var refData = database.collection('wallet').where('user_id', '==', provider_id).orderBy('date', 'desc');

    var append_list = '';
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

    $(document).ready(function () {

        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });

        var inx = parseInt(offest) * parseInt(pagesize);
        jQuery("#data-table_processing").show();

        append_list = document.getElementById('append_list1');
        append_list.innerHTML = '';

        refData.get().then(async function (snapshots) {

            var html = '';
            html = await buildHTML(snapshots);

            if (html != '') {
                append_list.innerHTML = html;
                start = snapshots.docs[snapshots.docs.length - 1];
                endarray.push(snapshots.docs[0]);
                if (snapshots.docs.length < pagesize) {
                    jQuery("#data-table_paginate").hide();
                }
            }

            $('#transactionsTable').DataTable({
                order: [],
                columnDefs: [{
                    targets: 1,
                    type: 'date',
                    render: function (data) {

                        return data;
                    }
                },
                    {
                        orderable: false,
                        targets: [2, 4,5]
                    },
                    {targets: 0, type: "html-num-fmt"},
                ],
                order: [
                    ['1', 'desc']
                ],
                "language": {
                    "zeroRecords": "{{trans('lang.no_record_found')}}",
                    "emptyTable": "{{trans('lang.no_record_found')}}"
                },
                responsive: true
            });

            jQuery("#data-table_processing").hide();
        });

    });

    async function buildHTML(snapshots) {
        var html = '';
        await Promise.all(snapshots.docs.map(async (listval) => {
            var datas = listval.data();
            var getData = await getListData(datas);
            html += getData;

        }));
        return html;
    }

    async function getListData(val) {
        var html = '';
        html = html + '<tr>';

        amount = val.amount;
        if (!isNaN(amount)) {
            amount = parseFloat(amount).toFixed(decimal_degits);

        }

        var note = '';
        if (val.hasOwnProperty('isTopUp') && val.isTopUp) {

            if (currencyAtRight) {
                html = html + '<td class="text-green" data-html="true" data-order="' + amount + '">' + parseFloat(amount).toFixed(decimal_degits) + '' + currentCurrency + '</td>';

            } else {
                html = html + '<td class="text-green" data-html="true" data-order="' + amount + '">' + currentCurrency + '' + parseFloat(amount).toFixed(decimal_degits) + '</td>';
            }
        } else if (val.hasOwnProperty('isTopUp') && !val.isTopUp) {

            if (currencyAtRight) {
                html = html + '<td class="text-red" data-html="true" data-order="' + amount + '">(' + parseFloat(amount).toFixed(decimal_degits) + '' + currentCurrency + ')</td>';

            } else {
                html = html + '<td class="text-red" data-html="true" data-order="' + amount + '">(' + currentCurrency + '' + parseFloat(amount).toFixed(decimal_degits) + ')</td>';
            }

        } else {
            if (currencyAtRight) {
                html = html + '<td class="" data-html="true" data-order="' + amount + '">' + parseFloat(amount).toFixed(decimal_degits) + '' + currentCurrency + '</td>';

            } else {
                html = html + '<td class="" data-html="true" data-order="' + amount + '">' + currentCurrency + '' + parseFloat(amount).toFixed(decimal_degits) + '</td>';
            }
        }

        var date = "";
        var time = "";
        try {
            if (val.hasOwnProperty("date")) {
                date = val.date.toDate().toDateString();
                time = val.date.toDate().toLocaleTimeString('en-US');
            }
        } catch (err) {

        }

        html = html + '<td>' + date + ' ' + time + '</td>';

        var payment_method = '';

        if (val.payment_method) {


            if (val.payment_method == "Stripe") {
                image = '{{asset("images/payment/stripe.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';

            } else if (val.payment_method == "RazorPay") {
                image = '{{asset("images/payment/razorepay.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';

            } else if (val.payment_method == "Paypal") {
                image = '{{asset("images/payment/paypal.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';

            } else if (val.payment_method == "PayFast") {
                image = '{{asset("images/payfast.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';

            } else if (val.payment_method == "PayStack") {
                image = '{{asset("images/payment/paystack.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';

            } else if (val.payment_method == "FlutterWave") {
                image = '{{asset("images/payment/flutter_wave.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';

            } else if (val.payment_method == "Mercado Pago") {
                image = '{{asset("images/payment/marcado_pago.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';

            } else if (val.payment_method == "Wallet") {
                image = '{{asset("images/payment/emart_wallet.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';

            } else if (val.payment_method == "Paytm") {
                image = '{{asset("images/payment/paytm.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';

            } else if (val.payment_method == "Cancelled Order Payment") {
                image = '{{asset("images/payment/cancel_order.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';

            } else if (val.payment_method == "Refund Amount") {
                image = '{{asset("images/payment/refund_amount.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else if (val.payment_method == "Referral Amount") {
                image = '{{asset("images/payment/reffral_amount.png")}}';
                payment_method = '<img alt="image" src="' + image + '" >';
            } else {
                payment_method = val.payment_method;
            }
        }


        html = html + '<td class="payment_images">' + payment_method + '</td>';
        html = html + '<td>' + val.note + '</td>';

        if (val.payment_status == 'success') {
            html = html + '<td class="success"><span class="badge badge-success py-2 px-3">{{trans("lang.success")}}</sapn></td>';
        } else if (val.payment_status == 'undefined') {
            html = html + '<td class="undefined"><span class="badge badge-warning py-2 px-3">{{trans("lang.success")}}</sapn></td>';
        } else if (val.payment_status == 'Refund success') {
            html = html + '<td class="refund_success"><span>' + val.payment_status + '</span></td>';

        } else {
            html = html + '<td><span>' + val.payment_status + '</span></td>';
        }


        if (val.hasOwnProperty('order_id') && val.order_id != null && val.order_id != "") {
            var order_view = '{{route("bookings.edit",":id")}}';
            order_view = order_view.replace(':id', val.order_id);

            html = html + '<td class="action-btn"><a href="' + order_view + '"><i class="fa fa-eye"></i></a></td>';

        } else {
            html = html + '<td></td>';
        }


        html = html + '</tr>';

        return html;

    }

</script>


@endsection