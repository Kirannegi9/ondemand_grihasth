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
                <li class="breadcrumb-item active">{{trans('lang.payout_plural')}}</li>
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
                                        class="fa fa-list mr-2"></i>{{trans('lang.payout_table')}}</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{!! route('payouts.create') !!}"><i
                                        class="fa fa-plus mr-2"></i>{{trans('lang.payout_create')}}</a>

                            </li>

                        </ul>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive m-t-10">

                            <table id="payoutsTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">

                                <thead>

                                    <tr>
                                        <th>{{trans('lang.paid_amount')}}</th>
                                        <th>{{trans('lang.date')}}</th>
                                        <th>{{trans('lang.payout_note')}}</th>
                                        <th>{{trans('lang.admin_note')}}</th>
                                        <th>{{trans('lang.status')}}</th>
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
</div>

@endsection

@section('scripts')

<script>

    var database = firebase.firestore();
    var providerId = "<?php echo $id; ?>";

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

    var append_list = '';
    var ref = database.collection('payouts').where('vendorID', '==', providerId).orderBy('paidDate', 'desc');
    
    $(document).ready(function () {

        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });

        jQuery("#data-table_processing").show();

        append_list = document.getElementById('append_list1');
        append_list.innerHTML = '';

        ref.get().then(async function (snapshots) {
            var html = '';
            html = await buildHTML(snapshots);

            if (html != '') {
                append_list.innerHTML = html;
            }

            $('#payoutsTable').DataTable({
                order: [],
                columnDefs: [
                    {
                        targets: 1,
                        type: 'date',
                        render: function (data) {

                            return data;
                        }
                    },
                    { orderable: false, targets: [4] },
                ],
                order: [['1', 'desc']],

                "language": {
                    "zeroRecords": "{{trans("lang.no_record_found")}}",
                    "emptyTable": "{{trans("lang.no_record_found")}}"
                    },
                responsive: true
            });
            jQuery("#data-table_processing").hide();
        });

    });


    async function buildHTML(snapshots) {
        var html = '';
        await Promise.all(snapshots.docs.map(async (listval) => {
            var val = listval.data();
            var getData = await getListData(val);
            html += getData;
        }));
        return html;
    }

    async function getListData(val) {

        var html = '';
        var price_val = 0;
        html = html + '<tr>';
        if (currencyAtRight) {
            price_val = parseFloat(val.amount).toFixed(decimal_degits) + "" + currentCurrency;
        } else {
            price_val = currentCurrency + "" + parseFloat(val.amount).toFixed(decimal_degits);
        }

        html = html + '<td>' + price_val + '</td>';
        var date = val.paidDate.toDate().toDateString();
        var time = val.paidDate.toDate().toLocaleTimeString('en-US');
        html = html + '<td>' + date + ' ' + time + '</td>';
        if (val.note != undefined) {
            html = html + '<td>' + val.note + '</td>';
        } else {
            html = html + '<td></td>';
        }
        if (val.adminNote != undefined) {
            html = html + '<td>' + val.adminNote + '</td>';
        } else {
            html = html + '<td></td>';
        }
        if (val.paymentStatus == 'Success') {
                html = html + '<td class="order_completed"><span>' + val.paymentStatus + '</span></td>';
        }else if(val.paymentStatus == 'Pending'){
            html = html + '<td class="driver_pending"><span>' + val.paymentStatus + '</span></td>';
        }else if(val.paymentStatus == 'Reject'){
            html = html + '<td class="driver_rejected"><span>' + val.paymentStatus + '</span></td>';
        }
        html = html + '</tr>';
        
        return html;
    }

</script>

@endsection