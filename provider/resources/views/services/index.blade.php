@extends('layouts.app')

@section('content')
<div class="page-wrapper">


    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">{{trans('lang.service_plural')}}</h3>

        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.service_plural')}}</li>
            </ol>
        </div>

        <div>

        </div>

    </div>


    <div class="container-fluid">
        <div id="data-table_processing" class="dataTables_processing panel panel-default"
              style="display: none;">{{ trans('lang.processing')}}
        </div>
        <div class="row">

            <div class="col-12">

                <div class="card">

                    <div class="card-body">
                        
                        <div class="card-header">
                            <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                                <li class="nav-item">
                                    <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.service_table')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{!! route('services.create') !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.service_create')}}</a>
                                </li>
                            </ul>
                        </div>

                        <div class="table-responsive m-t-10">

                            <table id="serviceTable"
                                   class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                   cellspacing="0" width="100%">
                                <thead>

                                <tr>
                                    <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                class="col-3 control-label" for="is_active"
                                        ><a id="deleteAll" class="do_not_delete"
                                            href="javascript:void(0)"><i
                                                        class="fa fa-trash"></i> {{trans('lang.all')}}</a></label></th>
                                    <th>{{trans('lang.name')}}</th>
                                    <th>{{trans('lang.category')}}</th>
                                    <th>{{trans('lang.section')}}</th>
                                    <th>{{trans('lang.price')}}</th>
                                    <th>{{trans('lang.publish')}}</th>
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
<script type="text/javascript">


    var database = firebase.firestore();
    var offest = 1;
    var pagesize = 10;
    var end = null;
    var endarray = [];
    var start = null;
    var user_id = "<?php echo $id; ?>";
    var append_list = '';
    var user_number = [];
    var refData = database.collection('providers_services').where('author', "==", user_id);
    var ref = database.collection('providers_services').orderBy('createdAt', 'desc').where('author', "==", user_id);

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
        var order_status = jQuery('#order_status').val();
        var search = jQuery("#search").val();


        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });
        jQuery('#search').hide();

        $(document.body).on('change', '#selected_search', function () {

            if (jQuery(this).val() == 'status') {
                jQuery('#order_status').show();
                jQuery('#search').hide();
            } else {

                jQuery('#order_status').hide();
                jQuery('#search').show();

            }
        });


        jQuery("#data-table_processing").show();
        append_list = document.getElementById('append_list1');
        append_list.innerHTML = '';
        ref.limit(pagesize).get().then(async function (snapshots) {
            html = '';
            html = await buildHTML(snapshots);
            jQuery("#data-table_processing").hide();
            if (html != '') {
                append_list.innerHTML = html;
                start = snapshots.docs[snapshots.docs.length - 1];
                endarray.push(snapshots.docs[0]);
            }
            if (snapshots.docs.length < pagesize) {

                jQuery("#data-table_paginate").hide();
            } else {

                jQuery("#data-table_paginate").show();
            }

             $('#serviceTable').DataTable({
                    columnDefs: [ 
                        
                        {orderable: false, targets: [0,5,6]},
                        {targets: 4, type: "html-num-fmt"},
                    ],
                    order: [['4', 'asc']],
                    "language": {
                        "zeroRecords": "{{trans('lang.no_record_found')}}",
                        "emptyTable": "{{trans('lang.no_record_found')}}"
                    },
                    responsive: true
                });

        });

    });

    async function buildHTML(snapshots) {
        var html='';
        await Promise.all(snapshots.docs.map(async (listval) => {
            var val = listval.data();
            var getData = await getListData(val);
            html += getData;
        }));
        return html;
    }

    async function getListData(val) {
        var html='';
        html = html + '<tr>';
        newdate = '';
        var id = val.id;
        var route1 = '{{route("services.edit",":id")}}';
        route1 = route1.replace(':id', id);
        var catName=await getCategoryName(val.categoryId);
        if(catName==''){
            catName='{{trans("lang.unknown")}}';
        }
        html = html + '<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label"\n' +
            'for="is_open_' + id + '" ></label></td>';

        html = html + '<td><a href="'+route1+'">' + val.title + '</a></td>';

        html += '<td>'+catName+'</td>';
        
        if (val.hasOwnProperty("sectionId")) {
            var sectionName = await getSectionName(val.sectionId);
            if(sectionName==''){
            sectionName='{{trans("lang.unknown")}}';
            }
            html = html + '<td>' + sectionName + '</td>';
        } else {
            html = html + '<td></td>';
        }

        if (val.disPrice == "0"){
            if (val.priceUnit == "Hourly") {
                if (currencyAtRight) {
                    html = html + '<td data-html="true" data-order="' + val.price + '">' + parseFloat(val.price).toFixed(decimal_degits) + '' + currentCurrency + '/hr</td>';
                }else {
                    html = html + '<td data-html="true" data-order="' + val.price + '">' + currentCurrency + parseFloat(val.price).toFixed(decimal_degits) + '/hr</td>';
                }
            } else {
                if (currencyAtRight) {
                    html = html + '<td data-html="true" data-order="' + val.price + '">' + parseFloat(val.price).toFixed(decimal_degits) +  '' + currentCurrency + '</td>';
                }else {
                    html = html + '<td data-html="true" data-order="' + val.price + '">' + currentCurrency + parseFloat(val.price).toFixed(decimal_degits) + '</td>';
                }
            }
        }else {
            if (val.priceUnit == "Hourly") {
                if (currencyAtRight) {
                    html = html + '<td data-html="true" data-order="' + val.disPrice + '">' + parseFloat(val.disPrice).toFixed(decimal_degits) + '' + currentCurrency + '/hr  <s>' + parseFloat(val.price).toFixed(decimal_degits) + '' + currentCurrency + '/hr</s></td>';
                } else {
                    html = html + '<td data-html="true" data-order="' + val.disPrice + '">' + '' + currentCurrency + parseFloat(val.disPrice).toFixed(decimal_degits) + '/hr  <s>' + currentCurrency + '' + parseFloat(val.price).toFixed(decimal_degits) + '/hr</s> </td>';
                }
            } else {
                if (currencyAtRight) {
                    html = html + '<td data-html="true" data-order="' + val.disPrice + '">' + parseFloat(val.disPrice).toFixed(decimal_degits) + '' + currentCurrency + '  <s>' + parseFloat(val.price).toFixed(decimal_degits) + '' + currentCurrency + '</s></td>';
                } else {
                    html = html + '<td data-html="true" data-order="' + val.disPrice + '">' + '' + currentCurrency + parseFloat(val.disPrice).toFixed(decimal_degits) + ' <s>' + currentCurrency + '' + parseFloat(val.price).toFixed(decimal_degits) + '</s> </td>';
                }
            }
        }

        if (val.publish) {
            html = html + '<td><label class="switch"><input type="checkbox" checked id="' + val.id + '" name="publish"><span class="slider round"></span></label></td>';
        } else {
            html = html + '<td><label class="switch"><input type="checkbox" id="' + val.id + '" name="publish"><span class="slider round"></span></label></td>';
        }

        html = html + '<td class="action-btn"><a href="' + route1 + '"><i class="fa fa-edit"></i></a><a id="' + val.id + '" name="service-delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a></td>';


        html = html + '</tr>';
        return html;

    }

    
    /* toggal publish action code start*/
    $(document).on("click", "input[name='publish']", function(e) {
        var ischeck = $(this).is(':checked');
        var id = this.id;
        if (ischeck) {
            database.collection('providers_services').doc(id).update({
                'publish': true
            }).then(function(result) {

            });
        } else {
            database.collection('providers_services').doc(id).update({
                'publish': false
            }).then(function(result) {

            });
        }
    });

    async function getSectionName(sectionId) {
        let sectionName = '';
        if (sectionId != '' && sectionId != null) {
            let sectionDoc = await database.collection('sections').doc(sectionId).get();
            if (sectionDoc.exists) {
                let sectionData = sectionDoc.data();
                sectionName = sectionData.name;
            }
        }
        return sectionName;
    }

    $(document).on("click", "a[name='service-delete']", function (e) {
        var id = this.id;
        database.collection('providers_services').doc(id).delete().then(function (result) {
            deleteServiceData(id);
            setTimeout(function () {
                window.location.reload();
            }, 3000);
        });
    });

    $("#is_active").click(function () {
        $("#serviceTable .is_open").prop('checked', $(this).prop('checked'));
    });

    $("#deleteAll").click(function () {
        if ($('#serviceTable .is_open:checked').length) {
            if (confirm("{{trans('lang.selected_delete_alert')}}")) {
                jQuery("#data-table_processing").show();
                $('#serviceTable .is_open:checked').each(function () {
                    var dataId = $(this).attr('dataId');
                    database.collection('providers_services').doc(dataId).delete().then(function () {
                        deleteServiceData(dataId);
                        setTimeout(function () {
                            window.location.reload();
                        }, 5000);

                    });
                });
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });
 async function getCategoryName(categoryId) {
       var catName='';
        await database.collection('provider_categories').where('id', '==', categoryId).get().then(async function (snapshots) {
            if(snapshots.docs.length>0) {
                var data = snapshots.docs[0].data();
                catName=data.title;                
            }
        });
        return catName;
        
    }
 async function deleteServiceData(serviceId){
            await database.collection('favorite_service').where('service_id', '==', serviceId).get().then(async function(snapshotsItem) {

            if (snapshotsItem.docs.length > 0) {
                snapshotsItem.docs.forEach((temData) => {
                    var item_data = temData.data();

                    database.collection('favorite_service').doc(item_data.id).delete().then(function() {

                    });
                });
            }

        });
        }

</script>

@endsection
