<?php
    include("security.php");
    include("config.php");
    
    if ($_POST) {
        $reportName = $_POST["reportName"];
        $reportURL = $_POST["reportURL"];
        $reportStatus = $_POST["reportStatus"];
        $reportCategory = $_POST["reportCategory"];

        if (mysqli_query($allConn, "INSERT INTO mis_reports.reports(rname,location,rstatus,addedby,catid) VALUES('$reportName','$reportURL','$reportStatus','$user','$reportCategory')")) {
        	echo "success";
        } else {
        	echo "error";
        }
    }
?>