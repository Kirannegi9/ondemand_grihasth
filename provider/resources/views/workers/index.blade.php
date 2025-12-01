@extends('layouts.app')

@section('content')

<div class="page-wrapper">

    <div class="row page-titles">

        <div class="col-md-5 align-self-center">

            <h3 class="text-themecolor">{{trans('lang.worker_plural')}}</h3>

        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.worker_plural')}}</li>
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
                                        class="fa fa-list mr-2"></i>{{trans('lang.worker_table')}}</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{!! route('workers.create') !!}"><i
                                        class="fa fa-plus mr-2"></i>{{trans('lang.worker_create')}}</a>

                            </li>

                        </ul>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive m-t-10">

                            <table id="workerTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">

                                <thead>
                                    <tr>
                                        <th class="delete-all"><input type="checkbox" id="is_active">
                                            <label class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                    class="do_not_delete" href="javascript:void(0)"><i
                                                        class="fa fa-trash"></i> {{trans('lang.all')}}</a></label>
                                        </th>
                                        <th>{{trans('lang.image')}}</th>
                                        <th>{{trans('lang.name')}}</th>
                                        <th>{{trans('lang.email')}}</th>
                                        <th>{{trans('lang.salary')}}</th>
                                        <th>{{trans('lang.status')}}</th>
                                        <th>{{trans('lang.onoff')}}</th>
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
    var provideUserId = "<?php echo $id; ?>";

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

    var placeholder = database.collection('settings').doc('placeHolderImage');
    placeholder.get().then(async function (snapshotsimage) {
        var placeholderImageData = snapshotsimage.data();
        placeholderImage = placeholderImageData.image;
    })
    var append_list = '';

    var refData = ''

    var providerId = provideUserId;

    ref = database.collection('providers_workers').where('providerId', '==', providerId);


    $(document).ready(function () {

        $(document.body).on('click', '.redirecttopage', function () {
            var url = $(this).attr('data-url');
            window.location.href = url;
        });


        jQuery("#data-table_processing").show();

        append_list = document.getElementById('append_list1');
        append_list.innerHTML = '';
        ref.get().then(async function (snapshots) {
            html = '';
            html = await buildHTML(snapshots);

            if (html != '') {
                append_list.innerHTML = html;
                start = snapshots.docs[snapshots.docs.length - 1];
                endarray.push(snapshots.docs[0]);

            }

            $('#workerTable').DataTable({
                order: [],
                columnDefs: [

                    { orderable: false, targets: [0, 1, 5, 6,7] },
                ],
                order: [['2', 'asc']],

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
        html = '';
        id = val.id;
        var route1 = '{{route("workers.edit",":id")}}';
        route1 = route1.replace(':id', val.id);

        var salary = 0;
        html = html + '<tr>';
        html = html + '<td class="delete-all"><input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label"\n' +
            'for="is_open_' + id + '" ></label></td>';
        if (val.profilePictureURL == '') {

            html = html + '<td><img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image"></td>';
        } else {
            html = html + '<td><img class="rounded" style="width:50px" src="' + val.profilePictureURL + '" alt="image"></td>';
        }

        html = html + '<td>' + val.firstName + ' ' + val.lastName + '</td>';
        html = html + '<td>' + val.email + '</td>';
        if (currencyAtRight) {
            salary = parseFloat(val.salary).toFixed(decimal_degits) + "" + currentCurrency;
        } else {
            salary = currentCurrency + "" + parseFloat(val.salary).toFixed(decimal_degits);
        }
        html = html + '<td>' + salary + '</td>';
        if (val.active) {
            html = html + '<td><label class="switch"><input type="checkbox" checked id="' + val.id + '" name="isActive"><span class="slider round"></span></label></td>';
        } else {
            html = html + '<td><label class="switch"><input type="checkbox" id="' + val.id + '" name="isActive"><span class="slider round"></span></label></td>';
        }

        var stus = '';
        if(val.online == true){
            stus = "Online";
        }
        else
        {
            stus = "Offline";
        }

        html = html + '<td>'+stus+'</td>';

        html = html + '<td class="action-btn"><a href="' + route1 + '"><i class="fa fa-edit"></i></a>';
        html = html + '<a id="' + val.id + '" class="do_not_delete" name="user-delete" href="javascript:void(0)"><i class="fa fa-trash"></i></a>';
        html = html + '</td>';
        html = html + '</tr>';
        return html;

    }

    $("#is_active").click(function () {
        $("#workerTable .is_open").prop('checked', $(this).prop('checked'));
    });

    $("#deleteAll").click(function () {
        if ($('#workerTable .is_open:checked').length) {
            if (confirm("{{trans('lang.selected_delete_alert')}}")) {
                jQuery("#data-table_processing").show();
                $('#workerTable .is_open:checked').each(function () {
                    var dataId = $(this).attr('dataId');
                    database.collection('providers_workers').doc(dataId).delete().then(function () {
                        var deleteUser = deleteUserData(dataId);
                        setTimeout(function () {
                            window.location.reload();
                        }, 7000);
                    });

                });
            }
        } else {
            alert("{{trans('lang.select_delete_alert')}}");
        }
    });
    $(document).on("click", "a[name='user-delete']", function (e) {
        var id = this.id;
        jQuery("#data-table_processing").show();
        database.collection('providers_workers').doc(id).delete().then(function (result) {
            var deleteUser = deleteUserData(id);
            setTimeout(function () {
                window.location.reload();
            }, 7000);
        });
    });

    $(document).on("click", "input[name='isActive']", function (e) {
        var ischeck = $(this).is(':checked');
        var id = this.id;
        if (ischeck) {
            database.collection('providers_workers').doc(id).update({
                'active': true
            }).then(function (result) { });
        } else {
            database.collection('providers_workers').doc(id).update({
                'active': false
            }).then(function (result) { });
        } 
    });

    async function deleteUserData(userId) {

        var dataObject = {
            "data": {
                "uid": userId
            }
        };
        var projectId = '<?php echo env('FIREBASE_PROJECT_ID') ?>';
        jQuery.ajax({
            url: 'https://us-central1-' + projectId + '.cloudfunctions.net/deleteUser',
            method: 'POST',
            contentType: "application/json; charset=utf-8",
            data: JSON.stringify(dataObject),
            success: function (data) {
                console.log('Delete user success:', data.result);
            },
            error: function (xhr, status, error) {
                var responseText = JSON.parse(xhr.responseText);
                console.log('Delete user error:', responseText.error);
            }
        });
    }


</script>

@endsection