<?php
	include("security.php");
    include("config.php");
	include("./lib/redis.php");
	
    if ($_POST) {
        $insertQueryArray = array();

        $selectedCategoryList = explode(",", $_POST["searchCategory2"]);
        $selectedCategory = $selectedCategoryList[0];
        $selectedUser = $selectedCategoryList[1];

        if (isset($_POST["selectReports"])) {
            foreach ($_POST["selectReports"] as $selectedReportsKey => $selectedReportsValue) {
                $insertQueryArray[] = "INSERT INTO mis_reports.mapping(uid,rid,addedby,catid) VALUES('$selectedUser','$selectedReportsValue','$user','$selectedCategory')";
            }
        }

        if (mysqli_query($allConn, "DELETE FROM mis_reports.mapping WHERE uid = '$selectedUser' AND catid = '$selectedCategory'")) {
            if (isset($_POST["selectReports"])) {
                $insertQueryGroup = implode(";", $insertQueryArray);
                if (mysqli_multi_query($allConn, $insertQueryGroup)) {
					Redis::delete("report:user_id:$selectedUser:report_category_list,report:user_id:$selectedUser:report_list");
                    echo "success";
                } else {
                    echo "error";
                }
            } else {
                echo "success";
            }
        } else {
            echo "error";
        }
	}
?>
