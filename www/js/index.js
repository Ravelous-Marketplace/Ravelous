$(document).ready(function(){
    $.ajax({
        url: "/API/shops.php?i=3",
        type: "GET",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        cache: false,
    }).done(function (data) {
        $.each(data, function (i, item) {
            html = '<div id="shop" style="background-image: url(\'data:image/gif;base64,' + item[2] + '\')"><div id="inner"><h3>' + item[0] + '</h3><p>' + item[1] + '</p><a class="inverted button" href="/shop/?shop=' + item[3] + '">Visit Shop</a><div></div>';
            $('#shopslider').append(html);
        });
        $('#shopslider').slick();
    });
    $.ajax({
        url: "/API/listings.php?simple",
        type: "GET",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        cache: false,
    }).done(function (data) {
        $.each(data, function (i, item) {
            $("#products").append(item);
        });
    });
});