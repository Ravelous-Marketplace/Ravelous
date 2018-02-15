var url_string = window.location.href;
var url = new URL(url_string);
var shop = url.searchParams.get("shop");
$(document).ready(function() {
    $('.menu > a.item').click(function(){
        $(this).parent().find('.active').removeClass('active');
        $(this).addClass('active');
    });
    
    reloadproducts();
});
function removeproduct(name) {
    id = btoa(name);
    console.log(url.origin + '/API/func/?removeproduct='+id)
    $.ajax({
        url: '/API/func/?removeproduct='+id,
        dataType: "JSON",
        success: function (resdata) {
            alert(resdata);
            if (resdata == 'ERR_OK') {
                reloadproducts();
            }
        },
    });
}
function reloadproducts() {
    if (!$('#search').val()) {
        var q = '';
    } else {
        var q = '&q=' + $('#search').val();
    }
    if (!$('#hi').val()) {
        var hi = '';
    } else {
        var hi = '&hi=' + $('#hi').val();
    }
    if (!$('#lo').val()) {
        var lo = '';
    } else {
        var lo = '&lo=' + $('#lo').val();
    }
    $.ajax({
        url: "/API/shop_listings.php?simple&shop=" + shop + q + hi + lo,
        type: "GET",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        cache: false,
    }).done(function (data) {
        $(".product").empty();
        $.each(data, function (i, item) {
            $(".product").append(item);
        });
    });
}