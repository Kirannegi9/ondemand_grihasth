
@extends('layouts.app')
<style>
    .business-analytics .card-box {
    background-color: #fff;
    border-radius: 10px;
    padding: 40px 30px 20px;
    position: relative;
    border: 1px solid rgba(180, 208, 224, .5);
    box-shadow: 0 5px 10px rgb(0 0 0 / 5%);
    height: 100%;
    transition: all .3s ease;
    cursor: pointer;
}
.order-status {
    background-color: rgba(110, 137, 175, .0509803922);
    border-radius: 10px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    height: 100%;
    transition: all .3s ease;
}

element.style {
}
.business-analytics_list > div {
    padding-bottom: 10px;
}
@media (min-width: 992px)
.col-lg-3 {
    -webkit-box-flex: 0;
    -ms-flex: 0 0 25%;
    flex: 0 0 25%;
    max-width: 25%;
}
</style>

@section('content')

<div id="main-wrapper" class="page-wrapper" style="min-height: 207px;">

	<div class="container-fluid">

		<div id="data-table_processing" class="dataTables_processing panel panel-default"
             style="display: none;margin-top:20px;">{{trans('lang.processing')}}
        </div>

		<div class="card mb-3 business-analytics">

			<div class="card-body">

				<div class="row flex-between align-items-center g-2 mb-3 order_stats_header">
					<div class="col-sm-6">
						<h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">{{trans('lang.dashboard_business_analytics')}}</h4>
					</div>
				</div>

				<div class="row business-analytics_list">

					<div class="col-sm-6 col-lg-3 mb-3">
                     <a href="{{route('wallettransaction')}}">
						<div class="card-box" >
							<h5>{{trans('lang.dashboard_total_earnings')}}</h5>
							<h2 id="earnings_count"></h2>
							<i class="mdi mdi-cash-usd"></i>
						</div>
                        </a>
					</div>

					<div class="col-sm-6 col-lg-3 mb-3">
                        <a href="{{route('bookings')}}">
						<div class="card-box" >
							<h5>{{trans('lang.dashboard_total_bookings')}}</h5>
							<h2 id="booking_count"></h2>
							<i class="mdi mdi-cart"></i>
						</div>
                        </a>
					</div>

					<div class="col-sm-6 col-lg-3 mb-3">
                    <a href="{{route('services')}}">
						<div class="card-box" >
							<h5>{{trans('lang.dashboard_total_service')}}</h5>
							<h2 id="service_count"></h2>
							<i class="mdi mdi-buffer"></i>
						</div>
                    </a>
					</div>
                    <div class="col-sm-6 col-lg-3 mb-3">
                     <a href="{{route('workers')}}">
						<div class="card-box" >
							<h5>{{trans('lang.dashboard_total_worker')}}</h5>
							<h2 id="worker_count"></h2>
							<i class="mdi mdi-worker"></i>
						</div>
                        </a>
					</div>
                    <div class="col-sm-6 col-lg-3 mb-3 d-none">
                     <a href="#">
						<div class="card-box" >
							<h5>{{trans('lang.dashboard_total_admin_commission')}}</h5>
							<h2 id="commission_count"></h2>
							<i class="mdi mdi-worker"></i>
						</div>
                        </a>
					</div>
                   
                    </div>
                    <div class="row">
					<div class="col-sm-6 col-lg-3">
						<a class="order-status assigned" href="{{url('bookings?status=order-placed')}}" >
						<div class="data">
							<i class="mdi mdi-lan-pending"></i>
							<h6 class="status">{{trans('lang.dashboard_order_placed')}}</h6>
						</div> <span class="count" id="placed_count"></span> </a>
					</div>

					<div class="col-sm-6 col-lg-3">
						<a class="order-status accepted" href="{{url('bookings')}}" >
						<div class="data">
							<i class="mdi mdi-check-circle"></i>
							<h6 class="status">{{trans('lang.dashboard_order_accepted')}}</h6>
						</div> <span class="count" id="accepted_count"></span> </a>
					</div>
                    <div class="col-sm-6 col-lg-3">
						<a class="order-status assigned" href="{{url('bookings')}}" >
							<div class="data">
								<i class="mdi mdi-arrow-right-bold-circle-outline"></i>
								<h6 class="status">{{trans('lang.dashboard_order_assigned')}}</h6>
							</div>
							<span class="count" id="assigned_count"></span>
						</a>
					</div>
					<div class="col-sm-6 col-lg-3">
						<a class="order-status ongoing" href="{{url('bookings?status=order-ongoing')}}" >
						<div class="data">
							<i class="mdi  mdi-arrow-down-bold-circle-outline"></i>
							<h6 class="status">{{trans('lang.dashboard_order_ongoing')}}</h6>
						</div> <span class="count" id="ongoing_count"></span> </a>
					</div>

					<div class="col-sm-6 col-lg-3 mt-3">
						<a class="order-status delivered" href="{{url('bookings?status=order-completed')}}" >
							<div class="data">
								<i class="mdi mdi-check-circle-outline"></i>
								<h6 class="status">{{trans('lang.dashboard_order_completed')}}</h6>
							</div>
							<span class="count" id="completed_count"></span>
						</a>
					</div>

					<div class="col-sm-6 col-lg-3 mt-3">
						<a class="order-status canceled" href="{{url('bookings?status=order-canceled')}}" >
							<div class="data">
								<i class="mdi mdi-window-close"></i>
								<h6 class="status">{{trans('lang.dashboard_order_canceled')}}</h6>
							</div>
							<span class="count" id="canceled_count"></span>
						</a>
					</div>
                    
                    <div class="col-sm-6 col-lg-3 mt-3">
                            <a class="order-status today" href="{!! url('bookings?status=order-today') !!}">
                                <div class="data">
                                    <i class="mdi mdi-calendar-today"></i>
                                    <h6 class="status">{{trans('lang.dashboard_ondemand_order_today')}}</h6>
                                </div>
                                <span class="count" id="today_count"></span>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3 mt-3">
                            <a class="order-status upcoming" href="{!! url('bookings?status=order-upcoming') !!}">
                                <div class="data">
                                    <i class="mdi mdi-calendar-clock"></i>
                                    <h6 class="status">{{trans('lang.dashboard_ondemand_order_upcoming')}}</h6>
                                </div>
                                <span class="count" id="upcoming_count"></span>
                            </a>
                        </div>


				</div>

			</div>

		</div>

		<div class="row">

			<div class="col-lg-4">
                <div class="card">
                	<div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">{{trans('lang.total_sales')}}</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="position-relative">
                            <canvas id="sales-chart" height="200"></canvas>
                        </div>

                        <div class="d-flex flex-row justify-content-end">
                            <span class="mr-2"> <i class="fa fa-square" style="color:#2EC7D9"></i> {{trans('lang.dashboard_this_year')}} </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
            	<div class="card">
                	<div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">{{trans('lang.service_overview')}}</h3>
                        </div>
                    </div>
					<div class="card-body">
	                    <div class="flex-row">
							<canvas id="visitors" height="222"></canvas>
	                    </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
            	<div class="card">
                	<div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">{{trans('lang.sales_overview')}}</h3>
                        </div>
                    </div>
					<div class="card-body">
            	        <div class="flex-row">
							<canvas id="commissions" height="222"></canvas>
	                    </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row daes-sec-sec mb-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header no-border d-flex justify-content-between">
                        <h3 class="card-title">{{trans('lang.recent_bookings')}}</h3>
                        <div class="card-tools">
                            <a href="#" class="btn btn-tool btn-sm"><i class="fa fa-bars"></i> </a>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <table class="table table-striped table-valign-middle" id="orderTable">
                            <thead>
                            <tr>
                                <th>{{trans('lang.booking_id')}}</th>
                                <th>{{trans('lang.order_user_id')}}</th>
                                <th>{{trans('lang.total_amount')}}</th>
                                <th>{{trans('lang.booking_date')}}</th>
                                <th>{{trans('lang.status')}}</th>
                            </tr>
                            </thead>
                            <tbody id="append_list_recent_order">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>


@endsection

@section('scripts')

<script src="{{asset('js/chart.js')}}"></script>

<script>


var db = firebase.firestore();
    var currency = db.collection('settings');

    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;
    var refCurrency = db.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function (snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;

        if(currencyData.decimal_degits){
            decimal_degits = currencyData.decimal_degits;
        }
    });


	var providerId = "<?php echo $id; ?>";
	
    jQuery("#data-table_processing").show();

	$(document).ready(function () {
            var  currentDateTime = new Date();
            var startOfToday = new Date(currentDateTime);
            startOfToday.setHours(0, 0, 0, 0);
            var endOfToday = new Date(currentDateTime);
            endOfToday.setHours(23, 59, 59, 999);
            var startTimestamp = firebase.firestore.Timestamp.fromDate(startOfToday);
            var endTimestamp = firebase.firestore.Timestamp.fromDate(endOfToday);

	    	db.collection('provider_orders').where('provider.author',"==",providerId).get().then(
	        (snapshot) => {
	            jQuery("#booking_count").empty();
	            jQuery("#booking_count").text(snapshot.docs.length);
	        });

	        db.collection('providers_services').where('author',"==",providerId).get().then(
	        (snapshot) => {
	            jQuery("#service_count").empty();
	            jQuery("#service_count").text(snapshot.docs.length);
	            
	        });

			getTotalEarnings();
            db.collection('providers_workers').where('providerId',"==",providerId).get().then(
	        (snapshot) => {
	            jQuery("#worker_count").empty();
	            jQuery("#worker_count").text(snapshot.docs.length);
	            setVisitors();
	        });
	        db.collection('provider_orders').where('provider.author',"==",providerId).where('status','==', "Order Placed").get().then(
	        (snapshot) => {
	            jQuery("#placed_count").empty();
	            jQuery("#placed_count").text(snapshot.docs.length);
	        });

	        db.collection('provider_orders').where('provider.author',"==",providerId).where('status', '==', "Order Accepted").get().then(
	        (snapshot) => {
	            jQuery("#accepted_count").empty();
	            jQuery("#accepted_count").text(snapshot.docs.length);
	        });

	        db.collection('provider_orders').where('newScheduleDateTime', '>=',startTimestamp).where('newScheduleDateTime', '<=',endTimestamp).where('provider.author',"==",providerId).where('status', '==', "Order Ongoing").get().then(
	        (snapshot) => {
	            jQuery("#ongoing_count").empty();
	            jQuery("#ongoing_count").text(snapshot.docs.length);
	        });

	        db.collection('provider_orders').where('provider.author',"==",providerId).where('status', '==', "Order Completed").get().then(
	        (snapshot) => {
	            jQuery("#completed_count").empty();
	            jQuery("#completed_count").text(snapshot.docs.length);
	        });

	        db.collection('provider_orders').where('provider.author',"==",providerId).where('status', 'in', ["Order Rejected","Order Cancelled"]).get().then(
	        (snapshot) => {
	            jQuery("#canceled_count").empty();
	            jQuery("#canceled_count").text(snapshot.docs.length);
	        });

	       	db.collection('provider_orders').where('provider.author',"==",providerId).where('status', '==', "Order Assigned").get().then(
	        (snapshot) => {
	            jQuery("#assigned_count").empty();
	            jQuery("#assigned_count").text(snapshot.docs.length);
	        });

            db.collection('provider_orders').where('newScheduleDateTime', '>=',startTimestamp).where('newScheduleDateTime', '<=',endTimestamp).where('status','in',['Order Accepted','Order Assigned','Order Ongoing']).where('provider.author',"==",providerId).get().then(
                (snapshot) => {
                    jQuery("#today_count").empty();
                    jQuery("#today_count").text(snapshot.docs.length);
                });
            db.collection('provider_orders').where('status','in',['Order Accepted','Order Assigned']).where('newScheduleDateTime', '>=',endTimestamp).where('provider.author',"==",providerId).get().then(
                (snapshot) => {
                    jQuery("#upcoming_count").empty();
                    jQuery("#upcoming_count").text(snapshot.docs.length);
                });


	        var placeholder = db.collection('settings').doc('placeHolderImage');
	        placeholder.get().then(async function (snapshotsimage) {
	            var placeholderImageData = snapshotsimage.data();
	            placeholderImage = placeholderImageData.image;

	        })

	        var offest = 1;
	        var pagesize = 10;
	        var start = null;
	        var end = null;
	        var endarray = [];
	        var inx = parseInt(offest) * parseInt(pagesize);
	        var append_listrecent_order = document.getElementById('append_list_recent_order');
	        append_listrecent_order.innerHTML = '';

	        ref = db.collection('provider_orders');
	        ref.orderBy('createdAt', 'desc').where('provider.author',"==",providerId).limit(inx).get().then((snapshots) => {
	            html = '';
	            html = buildBookingHTML(snapshots);
	            if (html != '') {
	                append_listrecent_order.innerHTML = html;
	                start = snapshots.docs[snapshots.docs.length - 1];
	                endarray.push(snapshots.docs[0]);
	            }
                    $('#orderTable').DataTable({
                        order: [],
                        columnDefs: [
                            {
                                targets: 2,
                                render: function (data) {

                                    return data;
                                }
                            },
                            {orderable: false, targets: [0]},
                        ],
                        order: [['2', 'asc']],
                        "language": {
                            "zeroRecords": "{{trans("lang.no_record_found")}}",
                            "emptyTable": "{{trans("lang.no_record_found")}}"
                        },
                        responsive: true
                    });
	        });

	    })
	
    async function getTotalEarnings() {
        var intRegex = /^\d+$/;
        var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
        var v01 = 0;
        var v02 = 0;
        var v03 = 0;
        var v04 = 0;
        var v05 = 0;
        var v06 = 0;
        var v07 = 0;
        var v08 = 0;
        var v09 = 0;
        var v10 = 0;
        var v11 = 0;
        var v12 = 0;
        var currentYear = new Date().getFullYear();
        await db.collection('provider_orders').where('provider.author',"==",providerId).where('status', '==', "Order Completed").get().then(async function (orderSnapshots) {
            var paymentData = orderSnapshots.docs;
            var totalEarning = 0;
            var adminCommission = 0;
            paymentData.forEach((order) => {
                var orderData = order.data();
                var price = 0;
                var minprice = 0;
                var minprice = parseFloat(orderData.provider.price);

            if (orderData.provider.disPrice != null && orderData.provider.disPrice != undefined && orderData.provider.disPrice != '' && orderData.provider.disPrice != '0') {
                minprice = parseFloat(orderData.provider.disPrice)
            }
            var price=minprice;
           
           
                minprice=parseFloat(orderData.quantity)*minprice;               
               
                discount = orderData.discount;
                if ((intRegex.test(discount) || floatRegex.test(discount)) && !isNaN(discount)) {
                    discount = parseFloat(discount).toFixed(decimal_degits);
                    price = price - parseFloat(discount);
                    minprice = minprice - parseFloat(discount);
                }

                tax = 0;
                totalTaxAmount=0;
                if (orderData.hasOwnProperty('taxSetting') && orderData.taxSetting.length>0) {
                    for (var i = 0; i < orderData.taxSetting.length; i++) {
                         var data = orderData.taxSetting[i];
                         if (data.type && data.tax) {
                            if (data.type == "percentage") {
                                tax = (parseFloat(data.tax) * minprice) / 100;
                            }else {
                                 tax = parseFloat(data.tax);
                                 }
                            totalTaxAmount=totalTaxAmount+parseFloat(tax);     
                        }
                    }
                }

                if (!isNaN(totalTaxAmount)) {
                    minprice = minprice + totalTaxAmount;
                }

                if (orderData.adminCommission != undefined && orderData.adminCommissionType != undefined && orderData.adminCommission > 0 && price > 0) {
                    var commission = 0;
                    if (orderData.adminCommissionType == "percentage") {
                        commission = (price * parseFloat(orderData.adminCommission)) / 100;

                    } else {
                        commission = parseFloat(orderData.adminCommission);
                    }

                    adminCommission = commission + adminCommission;
                } 

                totalEarning = parseFloat(totalEarning) + parseFloat(minprice);

                try {

                    if (orderData.createdAt) {
                        var orderMonth = orderData.createdAt.toDate().getMonth() + 1;
                        var orderYear = orderData.createdAt.toDate().getFullYear();
                        if (currentYear == orderYear) {
                            switch (parseInt(orderMonth)) {
                                case 1:
                                    v01 = parseInt(v01) + price;
                                    break;
                                case 2:
                                    v02 = parseInt(v02) + price;
                                    break;
                                case 3:
                                    v03 = parseInt(v03) + price;
                                    break;
                                case 4:
                                    v04 = parseInt(v04) + price;
                                    break;
                                case 5:
                                    v05 = parseInt(v05) + price;
                                    break;
                                case 6:
                                    v06 = parseInt(v06) + price;
                                    break;
                                case 7:
                                    v07 = parseInt(v07) + price;
                                    break;
                                case 8:
                                    v08 = parseInt(v08) + price;
                                    break;
                                case 9:
                                    v09 = parseInt(v09) + price;
                                    break;
                                case 10:
                                    v10 = parseInt(v10) + price;
                                    break;
                                case 11:
                                    v11 = parseInt(v11) + price;
                                    break;
                                default :
                                    v12 = parseInt(v12) + price;
                                    break;
                            }
                        }
                    }

                } catch (err) {


                    var datas = new Date(orderData.createdAt._seconds * 1000);

                    var dates = firebase.firestore.Timestamp.fromDate(datas);

                    db.collection('provider_orders').doc(orderData.id).update({'createdAt': dates}).then(() => {

                        console.log('Provided document has been updated in Firestore');

                    }, (error) => {

                        console.log('Error: ' + error);

                    });

                }


            })

            if (currencyAtRight) {
                totalEarning = parseFloat(totalEarning).toFixed(decimal_degits) + "" + currentCurrency;
                adminCommission = parseFloat(adminCommission).toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                totalEarning = currentCurrency + "" + parseFloat(totalEarning).toFixed(decimal_degits);
                adminCommission = currentCurrency + "" + parseFloat(adminCommission).toFixed(decimal_degits);
            }

            $("#earnings_count").append(totalEarning);
            $("#earnings_count_graph").append(totalEarning);
            $("#admincommission_count_graph").append(adminCommission);
            $("#admincommission_count").append(adminCommission);
            $("#total_earnings_header").text(totalEarning);
            $(".earnings_over_time").append(totalEarning);
            $('#commission_count').append(adminCommission);
            var data = [v01, v02, v03, v04, v05, v06, v07, v08, v09, v10, v11, v12];
            var labels = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
            var $salesChart = $('#sales-chart');
            var salesChart = renderChart($salesChart, data, labels);
            setCommision();
        })
        jQuery("#data-table_processing").hide();

    }

    function buildBookingHTML(snapshots) {
        var html = '';
        var count = 1;
        snapshots.docs.forEach((listval) => {
            val = listval.data();
            val.id = listval.id;
            var route = '{{route("bookings.edit",":id")}}';
            route = route.replace(':id', val.id);
  
            html = html + '<tr>';

            html = html + '<td data-url="' + route + '" class="redirecttopage">' + val.id + '</td>';

            html = html + '<td>' + val.author.firstName +' '+val.author.lastName+'</td>';

            var price = 0;
           
            var date = val.createdAt.toDate().toDateString();
            var time = val.createdAt.toDate().toLocaleTimeString('en-US');

            var price = buildHTMLProductstotal(val);

             if(val.provider.priceUnit!='Hourly'){
            html = html + '<td>' + price + '</td>';
         }else{
                if(val.status!='Order Completed'){
                    var perHourPrice=parseFloat(val.provider.price);
                    if (val.provider.disPrice != null && val.provider.disPrice != undefined && val.provider.disPrice != '' && val.provider.disPrice != '0') {
                            perHourPrice = parseFloat(val.provider.disPrice)
                        }
                        if (currencyAtRight) {
                            perHourPrice = perHourPrice.toFixed(decimal_degits) + "" + currentCurrency;
                        } else {
                            perHourPrice = currentCurrency + "" + perHourPrice.toFixed(decimal_degits);
                        }
                    html = html + '<td>' + perHourPrice + ' / {{trans("lang.hour")}}</td>';
                }else{
                    html = html + '<td>' + price + '</td>';
                }
        }
            html = html + '<td>' + date + ' ' + time + '</td>';
            if (val.status == 'Order Placed') {
            html = html + '<td class="order_placed"><span>' + val.status + '</span></td>';

                }else if (val.status == 'Order Assigned') {
                    html = html + '<td class="order_assigned"><span>' + val.status + '</span></td>';
                }
                else if (val.status == 'Order Ongoing') {
                    html = html + '<td class="order_ongoing"><span>' + val.status + '</span></td>';

                }
                else if (val.status == 'Order Accepted') {
                    html = html + '<td class="order_accept"><span>' + val.status + '</span></td>';

                }else if (val.status == 'Order Rejected') {
                    html = html + '<td class="order_rejected"><span>' + val.status + '</span></td>';

                }else if (val.status == 'Order Completed') {
                    html = html + '<td class="order_completed"><span>' + val.status + '</span></td>';

                }
                else if (val.status == 'Order Cancelled') {
                    html = html + '<td class="order_rejected"><span>' + val.status + '</span></td>';
                }else{
                    html = html + '<td class="order_completed"><span>' + val.status + '</span></td>';
        
                }
  
            html = html + '</a></tr>';
            count++;
        });
        return html;
    }


    function renderChart(chartNode, data, labels) {
        var ticksStyle = {
            fontColor: '#495057',
            fontStyle: 'bold'
        };

        var mode = 'index';
        var intersect = true;
        return new Chart(chartNode, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        backgroundColor: '#2EC7D9',
                        borderColor: '#2EC7D9',
                        data: data
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    mode: mode,
                    intersect: intersect
                },
                hover: {
                    mode: mode,
                    intersect: intersect
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: true,
                            lineWidth: '4px',
                            color: 'rgba(0, 0, 0, .2)',
                            zeroLineColor: 'transparent'
                        },
                        ticks: $.extend({
                            beginAtZero: true,
                            callback: function (value, index, values) {
                            	return currentCurrency + value.toFixed(decimal_degits);
                            }


                        }, ticksStyle)
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: false
                        },
                        ticks: ticksStyle
                    }]
                }
            }
        })
    }

    $(document).ready(function () {
        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });
    });


    function buildHTMLProductstotal(snapshotsProducts) {

        var adminCommission = snapshotsProducts.adminCommission;
        var discount = snapshotsProducts.discount;
        var couponCode = snapshotsProducts.couponCode;
        var extras = snapshotsProducts.extras;
        var extras_price = snapshotsProducts.extraCharges;
        var status = snapshotsProducts.status;
      
        var products = snapshotsProducts;
        var totalProductPrice = 0;
        var total_price = 0;


        var intRegex = /^\d+$/;
        var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
        var val = products;
        var sub_total = parseFloat(val.provider.price);

            if (val.provider.disPrice != null && val.provider.disPrice != undefined && val.provider.disPrice != '' && val.provider.disPrice != '0') {
                sub_total = parseFloat(val.provider.disPrice)
            }
            var price = sub_total;
            
                sub_total=parseFloat(val.quantity)*sub_total;             
            /*}else{
                sub_total=parseFloat(totalHours)*sub_total;            
            }*/
            total_price += parseFloat(sub_total);

            if (intRegex.test(discount) || floatRegex.test(discount)) {

                discount = parseFloat(discount).toFixed(decimal_degits);
                total_price -= parseFloat(discount);

                if (currencyAtRight) {
                    discount_val = discount + "" + currentCurrency;
                } else {
                    discount_val = currentCurrency + "" + discount;
                }

            }

            var tax = 0;
            taxlabel = '';
            taxlabeltype = '';

            if (snapshotsProducts.hasOwnProperty('taxSetting')) {
                var total_tax_amount = 0;
                for (var i = 0; i < snapshotsProducts.taxSetting.length; i++) {
                    var data = snapshotsProducts.taxSetting[i];

                    if (data.type && data.tax) {
                        if (data.type == "percentage") {
                            tax = (data.tax * total_price) / 100;
                            taxlabeltype = "%";
                        } else {
                            tax = data.tax;
                            taxlabeltype = "fix";
                        }
                        taxlabel = data.title;
                    }
                    total_tax_amount += parseFloat(tax);
                }
                total_price = parseFloat(total_price) + parseFloat(total_tax_amount);
            }

            if (currencyAtRight) {
                var total_price_val = parseFloat(total_price).toFixed(decimal_degits) + "" + currentCurrency;
            } else {
                var total_price_val = currentCurrency + "" + parseFloat(total_price).toFixed(decimal_degits);
            }

            return total_price_val;
    }

    function setVisitors(){

    	const data = {
		  labels: [
		    "{{trans('lang.dashboard_total_bookings')}}",
		    "{{trans('lang.dashboard_total_service')}}",
            "{{trans('lang.dashboard_total_worker')}}",
		  ],
		  datasets: [{
		    data: [jQuery("#booking_count").text(), jQuery("#service_count").text(),jQuery("#worker_count").text()],
		    backgroundColor: [
		      '#B1DB6F',
		      '#7360ed',
              '#fe95d3'
		    ],
		    hoverOffset: 4
		  }]
		};

        return new Chart('visitors',{
            type: 'doughnut',
            data: data,
            options: {
            	maintainAspectRatio: false,
            }
        })
    }

    function setCommision(){

    	const data = {
		  labels: [
		    "{{trans('lang.dashboard_total_earnings')}}",
            "{{trans('lang.dashboard_total_admin_commission')}}"
            
		  ],
		  datasets: [{
		    data: [jQuery("#earnings_count").text().replace(currentCurrency,""),jQuery("#commission_count").text().replace(currentCurrency,"")],
		    backgroundColor: [
		      '#feb84d',
		      '#9b77f8',
		      '#fe95d3'
		    ],
		    hoverOffset: 4
		  }]
		};
        return new Chart('commissions',{
            type: 'doughnut',
            data: data,
            options: {
            	maintainAspectRatio: false,
        		tooltips: {
		            callbacks: {
		                label: function(tooltipItems, data) {
		                	return data.labels[tooltipItems.index] +': '+ currentCurrency + data.datasets[0].data[tooltipItems.index];
		                }
		           }
			   }
			}
        })
    }


</script>

@endsection
