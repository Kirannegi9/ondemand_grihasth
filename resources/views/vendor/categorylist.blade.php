@include('layouts.app')
@include('layouts.header')
<style>
.list-card-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    margin: 0 auto;
    position: relative;
    background: #f0f0f0; /* Fallback background */
}

.list-card-image a {
    width: 100%;
    height: 100%;
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    padding: 0;
    margin: 0;
    border: none;
    outline: none;
}

.list-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    display: block;
    margin: 0;
    padding: 0;
    border: none;
    outline: none;
    position: absolute;
    top: 0;
    left: 0;
    min-width: 100%;
    min-height: 100%;
    max-width: none;
    max-height: none;
}

.brand-list .list-card-image {
    margin-bottom: 15px;
}

/* Force complete coverage */
.list-card-image img:hover {
    transform: scale(1.05);
    transition: transform 0.3s ease;
}

/* Ensure no gaps */
.list-card-image,
.list-card-image * {
    box-sizing: border-box;
}
</style>
<div class="st-cats-page pt-5 bg-white category-listing-page">
    <div class="container">
        <div class="d-flex align-items-center mb-3 page-title">
            <h3 class="font-weight-bold text-dark title">
                {{trans('lang.categories')}}
            </h3>
        </div>
        <div id="categorylist"></div>
        <div id="subcategorylist" style="display:none;"></div>
        <div id="productlist"></div>
    </div>
</div>
@include('layouts.footer')
<script type="text/javascript">
    var section_id = "<?php echo @$_COOKIE['section_id']; ?>";
    var catsRef = database.collection('vendor_categories').where('section_id', '==', section_id).where("publish", "==", true);
    var placeholderImageRef = database.collection('settings').doc('placeHolderImage');
    var placeholderImageSrc = '';
    placeholderImageRef.get().then(async function (placeholderImageSnapshots) {
        var placeHolderImageData = placeholderImageSnapshots.data();
        placeholderImageSrc = placeHolderImageData.image;
    })
    $(document).ready(function () {
        jQuery("#overlay").show();
        catsRef.get().then(async function (snapshots) {
            if (snapshots != undefined) {
                var html = '';
                html = buildHTML(snapshots);
                if (html != '') {
                    var append_list = document.getElementById('categorylist');
                    append_list.innerHTML = html;
                    jQuery("#overlay").hide();
                } else {
                    var append_list = document.getElementById('categorylist');
                    append_list.innerHTML = '<p class="text-center font-weight-bold mt-2">{{ trans("lang.no_results") }}</p>';
                    jQuery("#overlay").hide();
                }
            }
        });
    })

    function buildHTML(nearestRestauantSnapshot) {
        var html = '';
        var alldata = [];
        nearestRestauantSnapshot.docs.forEach((listval) => {
            var datas = listval.data();
            datas.id = listval.id;
            alldata.push(datas);
        });
        var count = 0;
        var popularFoodCount = 0;
        html = html + '<div class="row">';
        alldata.forEach((listval) => {
            var val = listval;
            html = html + '<div class="col-md-2 pb-3 brand-list mb-3"><div class="list-card position-relative"><div class="list-card-image">';
            if (val.photo) {
                photo = val.photo;
            } else {
                photo = placeholderImageSrc;
            }
            html = html + '<a href="javascript:void(0)" onclick="getSubcategoriesPage(\'' + val.id + '\')"><img alt="#" src="' + photo + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" class="img-fluid item-img"></a></div><div class="p-2 position-relative brand-title"><div class="list-card-body"><h6 class="mb-1"><a href="javascript:void(0)" onclick="getSubcategoriesPage(\'' + val.id + '\')" class="text-black">' + val.title + '</a></h6>';
            html = html + '</div>';
            html = html + '</div></div></div>';
        });
        html = html + '</div>';
        return html;
    }

    // Function to show subcategories for a category on the listing page
    function getSubcategoriesPage(categoryId) {
        // Hide category list and show subcategory list
        $('#categorylist').hide();
        $('#subcategorylist').show();
        $('#productlist').hide();
        
        // Try fetching all subcategories first, then filter in JavaScript
        var subcatsRef = database.collection('vendor_subcategories')
            .where('publish', '==', true);
            
        subcatsRef.get().then(async function(snapshots) {
            var subcategoryHtml = '';
            
            // Debug logging
            console.log('Category ID:', categoryId);
            console.log('Section ID:', section_id);
            console.log('Total subcategories found:', snapshots.docs.length);
            
            // Filter subcategories by category_id
            var filteredSubcategories = snapshots.docs.filter(function(doc) {
                var data = doc.data();
                console.log('Subcategory category_id:', data.category_id, 'looking for:', categoryId);
                return data.category_id == categoryId;
            });
            
            console.log('Filtered subcategories:', filteredSubcategories.length);
            
            if (filteredSubcategories.length > 0) {
                subcategoryHtml = '<div class="d-flex align-items-center mb-3 page-title">';
                subcategoryHtml += '<button onclick="showCategories()" class="btn btn-sm btn-primary mr-3">← Back to Categories</button>';
                subcategoryHtml += '<h3 class="font-weight-bold text-dark title">Subcategories</h3>';
                subcategoryHtml += '</div>';
                subcategoryHtml += '<div class="row">';
                
                filteredSubcategories.forEach((subcat) => {
                    var subcatData = subcat.data();
                    var view_vendor_status = "{{ url('products/subcategory') }}/" + subcat.id;
                    
                    subcategoryHtml += '<div class="col-md-2 pb-3 brand-list mb-3">';
                    subcategoryHtml += '<div class="list-card position-relative">';
                    subcategoryHtml += '<div class="list-card-image">';
                    subcategoryHtml += '<a href="' + view_vendor_status + '">';
                    subcategoryHtml += '<img alt="#" src="' + (subcatData.photo || placeholderImageSrc) + '" onerror="this.onerror=null;this.src=\'' + placeholderImageSrc + '\'" class="img-fluid item-img">';
                    subcategoryHtml += '</a></div>';
                    subcategoryHtml += '<div class="p-2 position-relative brand-title">';
                    subcategoryHtml += '<div class="list-card-body">';
                    subcategoryHtml += '<h6 class="mb-1"><a href="' + view_vendor_status + '" class="text-black">' + subcatData.title + '</a></h6>';
                    
                    subcategoryHtml += '</div></div></div></div>';
                });
                
                subcategoryHtml += '</div>';
            } else {
                subcategoryHtml = '<div class="d-flex align-items-center mb-3 page-title">';
                subcategoryHtml += '<button onclick="showCategories()" class="btn btn-sm btn-primary mr-3">← Back to Categories</button>';
                subcategoryHtml += '<h3 class="font-weight-bold text-dark title">Subcategories</h3>';
                subcategoryHtml += '</div>';
                subcategoryHtml += '<p class="text-center font-weight-bold mt-2">{{ trans("lang.no_results") }}</p>';
            }
            
            $('#subcategorylist').html(subcategoryHtml);
        });
    }

    // Function to show categories (back button)
    function showCategories() {
        $('#categorylist').show();
        $('#subcategorylist').hide();
        $('#productlist').hide();
    }
</script>
@include('layouts.nav')