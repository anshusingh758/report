<?php
	include("security.php");
	include('config.php');

	if ($_POST) {
		$categoryId = $_POST["categoryId"];
	    
	    $max = 4;
	    $number = 0;
	    
	    $query = mysqli_query($allConn, "SELECT
            r.rname AS report_name,
            r.location AS report_location
        FROM
            mis_reports.reports AS r
            JOIN mis_reports.mapping AS m ON m.rid = r.rid
            JOIN mis_reports.users AS u ON u.uid = m.uid
        WHERE
            m.uid = '$user'
        AND
            r.rstatus = '1'
        AND
        	r.catid IN ($categoryId)
        ORDER BY r.rname ASC");

	    if (mysqli_num_rows($query) > 0) {
	        while ($row = mysqli_fetch_array($query)) {
	        	$reportName = strlen($row["report_name"]) > 40 ? substr($row["report_name"],0,40)."..." : $row["report_name"];
				$reportLocation = $row["report_location"];

	            if ($number == $max) {
	                $number = 0;
	            }
	            if ($number == 0) {
		            echo "<div class='col-md-4 connect_cat'>
		            <div class='panel' style='border:none;'>
		            <div class='panel-body div11 name_cat'>".$reportName."</div>
		            <a href='".REPORT_PATH.''.$reportLocation."' target='_blank' style='outline:none;text-decoration:none;'><div class='panel-footer div12'>View <i class='fa fa-arrow-circle-right'></i></div></a>
		            </div>
		            </div>
		            </div>";
	            }
	            if ($number == 1) {
		            echo "<div class='col-md-4 connect_cat'>
		            <div class='panel' style='border:none;'>
		            <div class='panel-body div21 name_cat'>".$reportName."</div>
		            <a href='".REPORT_PATH.''.$reportLocation."' target='_blank' style='outline:none;text-decoration:none;'><div class='panel-footer div22'>View <i class='fa fa-arrow-circle-right'></i></div></a>
		            </div>
		            </div>
		            </div>";
	            }
	            if ($number == 2) {
		            echo "<div class='col-md-4 connect_cat'>
		            <div class='panel' style='border:none;'>
		            <div class='panel-body div31 name_cat'>".$reportName."</div>
		            <a href='".REPORT_PATH.''.$reportLocation."' target='_blank' style='outline:none;text-decoration:none;'><div class='panel-footer div32'>View <i class='fa fa-arrow-circle-right'></i></div></a>
		            </div>
		            </div>
		            </div>";
	            }
	            if ($number == 3) {
		            echo "<div class='col-md-4 connect_cat'>
		            <div class='panel' style='border:none;'>
		            <div class='panel-body div41 name_cat'>".$reportName."</div>
		            <a href='".REPORT_PATH.''.$reportLocation."' target='_blank' style='outline:none;text-decoration:none;'><div class='panel-footer div42'>View <i class='fa fa-arrow-circle-right'></i></div></a>
		            </div>
		            </div>
		            </div>";
	            }
	            $number++;
	        }
	    } else {
        ?>
            <div class="col-md-8 col-md-offset-2" style="margin-top: 50px;">
                <img src="<?php echo IMAGE_PATH; ?>/found.jpg" class="center-block img-responsive">
            </div>
<?php
        }
	}
?>