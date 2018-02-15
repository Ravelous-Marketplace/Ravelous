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
            if (i < 5) {
                $("#featured").append(item);
            } else if (i < 10) {
                $("#popular").append(item);
            } else {
                $("#new").append(item);
            }
        });
    });
}
