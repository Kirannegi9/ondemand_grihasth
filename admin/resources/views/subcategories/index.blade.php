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
                <li class="breadcrumb-item active">{{trans('lang.subcategory_table')}}</li>
            </ol>
        </div>
        <div></div>
    </div>

    <div class="container-fluid">
       <div class="admin-top-section"> 
        <div class="row">
            <div class="col-12">
                <div class="d-flex top-title-section pb-4 justify-content-between">
                    <div class="d-flex top-title-left align-self-center">
                        <span class="icon mr-3"><img src="{{ asset('images/category.png') }}"></span>
                        <h3 class="mb-0">{{trans('lang.subcategory_plural')}}</h3>
                        <span class="counter ml-3 total_count"></span>
                    </div>  
                    <!--<div class="d-flex top-title-right align-self-center"> -->
                    <!--    <div class="select-box pl-3">-->
                    <!--        <select class="form-control filteredRecords sections" id="section_id" onchange="clickLink(this.value)">-->
                    <!--        <option value="" disabled selected>{{trans('lang.select')}} {{trans('lang.section_plural')}}-->
                    <!--        </select>-->
                    <!--    </div>    -->
                    <!--</div>                   -->
                </div>
            </div>
        </div> 
       </div>

       <div class="table-list">
       <div class="row">
           <div class="col-12">
               <div class="card border">
                 <div class="card-header d-flex justify-content-between align-items-center border-0">
                   <div class="card-header-title">
                    <h3 class="text-dark-2 mb-2 h4">{{trans('lang.subcategory_table')}}</h3>
                    <p class="mb-0 text-dark-2">{{trans('lang.subcategory_table_text')}}</p>
                   </div>
                   <div class="card-header-right d-flex align-items-center">
                    <div class="card-header-btn mr-3">                     
                        <a class="btn-primary btn rounded-full" href="{!! route('subcategories.create') !!}"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.subcategory_create')}}</a>
                     </div>
                   </div>                
                 </div>
                 <div class="card-body">
                         <div class="table-responsive m-t-10">
                            <table id="subcategoryTable"
                                   class="display nowrap table table-hover table-striped table-bordered"
                                   cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <?php if (in_array('subcategories.delete', json_decode(@session('user_permissions'),true))) { ?>
                                    <th class="delete-all"><input type="checkbox" id="is_active"><label for="is_active"><a id="deleteAll"
                                    class="do_not_delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i> {{trans('lang.all')}}</a></label></th>
                                    <?php } ?>
                                    <th>{{trans('lang.subcategory_info')}}</th>
                                    <th>{{trans('lang.category')}}</th>
                                    <!--<th>{{trans('lang.section')}}</th>-->
                                    <th>{{trans('lang.item')}}</th>
                                    <th>{{trans('lang.item_publish')}}</th>
                                    <th>{{trans('lang.actions')}}</th>
                                </tr>
                                </thead>  
                                <tbody id="append_list1"></tbody>                             
                            </table>
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

    var user_permissions = '<?php echo @session('user_permissions') ?>';
    user_permissions = JSON.parse(user_permissions);

    var checkDeletePermission = false;
    if ($.inArray('subcategories.delete', user_permissions) >= 0) {
        checkDeletePermission = true;
    }

    var database = firebase.firestore();
    var section_id = getCookie('section_id');
    var ref = (section_id != '') ? database.collection('vendor_subcategories').where('section_id', '==', section_id) : database.collection('vendor_subcategories');

    var placeholderImage = '';
    var ref_sections = database.collection('sections'); 

    $(document).ready(function () {
        var placeholder = database.collection('settings').doc('placeHolderImage');
        placeholder.get().then(function (snap) {
            placeholderImage = snap.data().image;
        });

        // Init select2
        $('.sections').select2({
            placeholder: "{{trans('lang.select')}} {{trans('lang.section_plural')}}",  
            minimumResultsForSearch: Infinity,
            allowClear: true 
        });

        // Datatable setup
        const table = $('#subcategoryTable').DataTable({
            pageLength: 10,
            serverSide: true,
            responsive: true,
            ajax: async function (data, callback) {
                const searchValue = data.search.value.toLowerCase();
                await ref.get().then(async function (snapshots) {
                    let records = [];
                    let filteredRecords = [];

                    await Promise.all(snapshots.docs.map(async (doc) => {
                        let childData = doc.data();
                        childData.id = doc.id;

                        // Get category name
                        let categoryName = '';
                        if(childData.category_id){
                            let catSnap = await database.collection('vendor_categories').doc(childData.category_id).get();
                            if(catSnap.exists) categoryName = catSnap.data().title;
                        }

                        // // Get section name
                        // let sectionName = '';
                        // if(childData.section_id){
                        //     let secSnap = await database.collection('sections').doc(childData.section_id).get();
                        //     if(secSnap.exists) sectionName = secSnap.data().name;
                        // }

                        // Get total products
                        let totalProducts = 0;
                        if(childData.section_id){
                            let prodSnap = await database.collection('vendor_products').where('subcategoryID', '==', childData.id).get();
                            totalProducts = prodSnap.docs.length;
                        }

                        childData.categoryName = categoryName;
                        // childData.sectionName = sectionName;
                        childData.totalProducts = totalProducts;

                        if (searchValue) {
                            if (
                                (childData.title && childData.title.toLowerCase().includes(searchValue)) ||
                                (categoryName && categoryName.toLowerCase().includes(searchValue)) 
                            ) {
                                filteredRecords.push(childData);
                            }
                        } else {
                            filteredRecords.push(childData);
                        }
                    }));

                    // Sorting + Pagination
                    const totalRecords = filteredRecords.length;
                    $('.total_count').text(totalRecords); 
                    const paginated = filteredRecords.slice(data.start, data.start + data.length);

                    await Promise.all(paginated.map(async (childData) => {
                        let row = [];
                        let id = childData.id;

                        let routeEdit = '{{route("subcategories.edit",":id")}}'.replace(':id', id);

                        if (checkDeletePermission) {
                            row.push('<td><input type="checkbox" class="is_open" dataId="'+id+'"></td>');
                        }

                        let img = childData.photo ? childData.photo : placeholderImage;
                        row.push('<td><img class="rounded" style="width:50px" src="'+img+'"> <a href="'+routeEdit+'">'+childData.title+'</a></td>');
                        row.push('<td>'+ (childData.categoryName ?? '') +'</td>');
                        // row.push('<td>'+ (childData.sectionName ?? '') +'</td>');
                        row.push('<td>'+ childData.totalProducts +'</td>');
                        row.push('<td><label class="switch"><input type="checkbox" '+(childData.publish ? "checked":"")+' id="'+id+'" name="isSwitch"><span class="slider round"></span></label></td>');

                        let actions = '<span class="action-btn"><a href="'+routeEdit+'"><i class="mdi mdi-lead-pencil"></i></a>';
                        if (checkDeletePermission) {
                            actions += '<a id="'+id+'" name="subcategory-delete" class="delete-btn" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';
                        }
                        actions += '</span>';
                        row.push(actions);

                        records.push(row);
                    }));

                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords,
                        recordsFiltered: totalRecords,
                        data: records
                    });
                });
            }
        });

        // Toggle publish
        $(document).on("click","input[name='isSwitch']",function(){
            var id = this.id;
            var ischeck = $(this).is(':checked');
            database.collection('vendor_subcategories').doc(id).update({ publish:ischeck });
        });

        // Delete subcategory
        $(document).on("click","a[name='subcategory-delete']", async function(){
            var id = this.id;
            await deleteDocumentWithImage('vendor_subcategories',id,'photo');
            window.location.href = '{{ route("subcategories")}}';
        });

        // Sections dropdown
        ref_sections.get().then(function(snapshots){
            snapshots.docs.forEach((doc)=>{
                var data = doc.data();
                if (data.serviceTypeFlag == "delivery-service" || data.serviceTypeFlag == "ecommerce-service") {
                    $('#section_id').append($("<option></option>").attr("value", data.id).text(data.name));
                }
            })
            $('#section_id').val(section_id);
        });

        // Select/Deselect all
        $("#is_active").click(function(){
            $("#subcategoryTable .is_open").prop('checked', $(this).prop('checked'));
        });

        // Delete all
        $("#deleteAll").click(function(){
            if ($('#subcategoryTable .is_open:checked').length) {
                if(confirm("{{trans('lang.selected_delete_alert')}}")){
                    $('#subcategoryTable .is_open:checked').each(async function(){
                        var dataId = $(this).attr('dataId');
                        await deleteDocumentWithImage('vendor_subcategories',dataId,'photo');
                        window.location.reload();
                    });
                }
            } else {
                alert("{{trans('lang.select_delete_alert')}}");
            }
        });

    });
</script>
@endsection
