<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/css/style.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
<script>
<?php 
if (isset($result)) {
    echo "alert('".$result."')"."\n";
    echo "window.location.href = '/account/'";
}
?>

function openImage (data) {
        var image = new Image();
        image.src = "data:image/jpg;base64," + data;

        var w = window.open("");
        w.document.write(image.outerHTML);
}
</script>