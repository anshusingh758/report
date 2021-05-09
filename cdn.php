<?php
$scheme = isset($_SERVER["HTTPS"]) ? 'https:' : 'http:';
?>

<!--Titlebar LOGO-->
<link rel="icon" href="<?php echo IMAGE_PATH; ?>/logo.png">

<!--Bootstrap + jQuery CDN-->
<link rel="stylesheet" type="text/css" href="<?php echo $scheme; ?>//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="<?php echo $scheme; ?>//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="<?php echo $scheme; ?>//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!--Include FontAwesome CDN-->
<link rel="stylesheet" href="<?php echo $scheme; ?>//formden.com/static/cdn/font-awesome/4.4.0/css/font-awesome.min.css">

<!--Datatables CDN-->
<link rel="stylesheet" type="text/css" href="<?php echo $scheme; ?>//cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css">
<script src="<?php echo $scheme; ?>//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="<?php echo $scheme; ?>//cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>

<!--Datatables Excel Buttons CDN-->
<link rel="stylesheet" type="text/css" href="<?php echo $scheme; ?>//cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
<script src="<?php echo $scheme; ?>//cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="<?php echo $scheme; ?>//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="<?php echo $scheme; ?>//cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>

<!-- Include DatePicker CDN-->
<link rel="stylesheet" href="<?php echo $scheme; ?>//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
<script type="text/javascript" src="<?php echo $scheme; ?>//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>

<!--MultiSelect CDN-->
<link rel="stylesheet" href="<?php echo $scheme; ?>//cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
<script src="<?php echo $scheme; ?>//cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>

<!--Sweet Alert CDN-->
<script src="<?php echo $scheme; ?>//unpkg.com/sweetalert2@7.12.15/dist/sweetalert2.all.js"></script>
