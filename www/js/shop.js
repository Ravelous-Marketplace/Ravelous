$(document).ready(function() {
    loadlistings();
});

function loadlistings() {
    $shop = $('#user').text();
    $.ajax({
        url: "/API/shop_listings.php?simple&shop=" + $shop,
        type: "GET",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        cache: false,
    }).done(function (data) {
        $.each(data, function (i, item) {
            $("#products").append(item);
        });
    });
}
