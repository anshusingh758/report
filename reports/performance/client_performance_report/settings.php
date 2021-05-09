<?php
	include_once ("../../../config.php");
	include_once ("../../../security.php");
	include_once ("../../../functions/reporting-service.php");
	include_once ("../../../cdn.php");

	$selectQuery = mysqli_query($allConn, "SELECT
		com.id,
		com.title,
		com.resource_from,
		com.resource_to,
		com.margin_min,
		com.margin_max,
		com.color
	FROM
		vtech_mappingdb.client_optimization_matrix AS com
	GROUP BY com.id");

	$averageMarginColumnList = $resourceWorkingRowList = $manageMatrixList = array();

	while ($selectRow = mysqli_fetch_array($selectQuery)) {
	    $columnKey = $selectRow['margin_min'] . '-' . $selectRow['margin_max'];
	    
	    $rowKey = $selectRow['resource_from'] . '-' . $selectRow['resource_to'];

	    if (!isset($averageMarginColumnList[$columnKey])) {
	        $averageMarginColumnList[$columnKey] = array(
	        	"margin_min" => $selectRow['margin_min'],
	        	"margin_max" => $selectRow['margin_max']
	        );
	    }

	    if (!isset($resourceWorkingRowList[$rowKey])) {
	        $resourceWorkingRowList[$rowKey] = array(
	        	"resource_from" => $selectRow['resource_from'],
	        	"resource_to" => $selectRow['resource_to']
	        );
	    }

	    $manageMatrixList[] = $selectRow;
	}

	$optimizationMatrix = $optimizationMatrixArray = array();

	foreach ($resourceWorkingRowList as $resourceKey => $resourceValue) {
	    foreach ($averageMarginColumnList as $marginKey => $marginValue) {
	        $rangeMatrixDetail = geRangeMatrixDeatil($manageMatrixList, $marginValue, $resourceValue);
	        
	        if ($rangeMatrixDetail != '') {
	            $optimizationMatrix[] = array(
	            	"title" => $rangeMatrixDetail['title'],
	            	"color" => $rangeMatrixDetail['color'],
	            	"client_list" => array()
	            );
	            $optimizationMatrixArray[$resourceKey]['title'][] = $rangeMatrixDetail['title'];
	            $optimizationMatrixArray[$resourceKey]['color'][] = $rangeMatrixDetail['color'];
	        }
	    }
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Manage Matrix Settings</title>

	<?php include_once ("../../../cdn.php"); ?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot td{
			padding: 5px 1px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable tbody td{
			padding: 2px 1px;
			text-align: center;
			vertical-align: middle;
		}
		.titleStyle {
			text-align: center;
			font-size: 30px;
			background-color: #aaa;
			color: #111;
			padding: 10px;
		}
        .vertical-text {
			writing-mode: tb-rl;
			font-weight: bold;
			transform: rotate(-180deg);
		}
	</style>
</head>
<body>
	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 titleStyle">Manage Matrix Settings</div>
			</div>
		</div>
	</section>

	<div class="row navPanel" style="margin-top: 20px;">
		<div class="col-md-2 col-md-offset-8">
			<button type="button" class="myModal form-control btn-primary">Add Matrix</button>
		</div>
	</div> <br>
	<section class="customized-datatable-section">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-6 col-md-offset-2">
					<table class="table table-striped table-bordered customized-datatable">
						<tr>
							<th colspan="6" style="text-align: center;">Optimization Matrix</th>
						</tr>
						<tr>
							<th style="text-align: center;" rowspan="<?php echo (count($resourceWorkingRowList) * 2); ?>"><center><span class="vertical-text">Resources Working</span></center></th>
						</tr>
						<?php foreach (array_reverse($resourceWorkingRowList) as $key => $value) {
						?>
						 	<tr>
						    	<th style="text-align: center;" scope="row"><?php echo $key; ?></th>
						    <?php
	    						for ($i = 0; $i < count($resourceWorkingRowList);$i++) {
							?>
					    		<th style="text-align: center;background-color: <?php echo $optimizationMatrixArray[$key]['color'][$i]; ?>"><a class="myModal-edit" data-title = "<?php echo $optimizationMatrixArray[$key]['title'][$i]; ?>" data-toggle="modal" data-target="#add_data_Modal"><?php echo $optimizationMatrixArray[$key]['title'][$i]; ?></a></th>
						    		<?php
	   							}
							?>
						  	</tr>
						  	<tr>
						 	<?php
								}
							?>
						 	</tr>
						 	<tr>
						  		<th colspan="2"></th>
						  	<?php foreach ($averageMarginColumnList as $key => $value) { ?>
						  		<th style="text-align: center;" scope="col"><?php echo $key; ?></th>
						  	<?php
								}
							?>
						</tr>
						<tr>
							<th colspan="6" style="text-align: center;">Average Margin(USD)</th>
						</tr>
					</table>
					<div class="col-md-4">
						<button type="button" onclick="location.href='<?php echo REPORT_PATH ?>/performance/client_performance_report/index.php'" class="form-control btn-primary">Back to Report</button>
					</div>
				</div>
			</div>
		</div>
	</section>

	<div class="modal fade" id="myModal-edit" role="dialog">
	    <div class="modal-dialog">
	      	<div class="modal-content">
		        <div class="modal-header">
		          	<button type="button" class="close" data-dismiss="modal">&times;</button>
		          	<center><h4 class="modal-title">Update Matrix</h4></center>
		        </div>
		        <div class="modal-body">
		          	<form method="post">
						<div class="modal-body">				
							<div class="form-group">
								<label for="title">Title</label>
								<input type="text" name="title" id="title" class="form-control">
							</div>
							<div class="form-group">
								<label for="resouces">Resouces Working</label>
		                		<div class="row">
		                  			<label class="col-sm-2 control-label">From</label>
		                  			<div class = "col-sm-6">
		                        		<input class="form-control" id="resource_from" type="text" name="from">
		                  			</div>
		                		</div>
		                		<br>
				                <div class="row">
				                  	<label class="col-sm-2 control-label">To</label>
				                  	<div class = "col-sm-6">
				                        <input class="form-control" id="resource_to" type="text" name="to">
				                  	</div>
				              	</div>
							</div>
							<div class="form-group">
									<label for="resouces">Avergae Margin</label>
		                		<div class="row">
		                  			<label class="col-sm-2 control-label">Min</label>
		                  			<div class = "col-sm-6">
		                        		<input class="form-control" id="margin_min" type="text" name="min">
		                  			</div>
		                		</div>
		                		<br>
		                		<div class="row">
		                  			<label class="col-sm-2 control-label">Max</label>
		                  			<div class = "col-sm-6">
		                        		<input class="form-control" id="margin_max" type="text" name="max">
		                 			</div>
		                		</div>
							</div>
							<div class="form-group">
								<label for="color">Color</label>
								<select class="form-control" name="color" id="color" required>
									<option value="">None</option>
									<option value="#dd7c7c">Red</option>
									<option value="#e8ac43">Yellow</option>
									<option value="#83c48a">Green</option>
								</select>
							</div>					
						</div>
						<div class="modal-footer">					
							<input type="submit" class="btn btn-success" name="form-submit-button">
						</div>
					</form>
		        </div>
	     	 </div>
	  	</div>
  	</div>

  	<div class="modal fade" id="myModal" role="dialog">
	    <div class="modal-dialog">
	      	<div class="modal-content">
		        <div class="modal-header">
			          <button type="button" class="close" data-dismiss="modal">&times;</button>
			          <center><h4 class="modal-title">Add Matrix</h4></center>
			    </div>
		        <div class="modal-body">
		          	<form method="post">
						<div class="modal-body">				
							<div class="form-group">
								<label for="title">Title</label>
								<input type="text" name="title" class="form-control">
							</div>
							<div class="form-group">
								<label for="resouces">Resouces Working</label>
			                	<div class="row">
			                  		<label class="col-sm-2 control-label">From</label>
			                  		<div class = "col-sm-6">
			                        	<input class="form-control" type="text" name="from">
			                  		</div>
			                	</div>
			                	<br>
			                	<div class="row">
			                  		<label class="col-sm-2 control-label">To</label>
			                  		<div class = "col-sm-6">
			                        	<input class="form-control" type="text" name="to">
			                  		</div>
			              		</div>
							</div>
							<div class="form-group">
								<label for="resouces">Avergae Margin</label>
		                		<div class="row">
		                  			<label class="col-sm-2 control-label">Min</label>
		                  			<div class = "col-sm-6">
		                        		<input class="form-control" type="text" name="min">
		                  			</div>
		                		</div>
		                		<br>
		                		<div class="row">
		                  				<label class="col-sm-2 control-label">Max</label>
		                  			<div class = "col-sm-6">
		                        		<input class="form-control" type="text" name="max">
		                 			</div>
		                		</div>
							</div>
							<div class="form-group">
								<label for="color">Color</label>
								<select class="form-control" name="color" required>
									<option value="">None</option>
									<option value="#dd7c7c">Red</option>
									<option value="#e8ac43">Yellow</option>
									<option value="#83c48a">Green</option>
								</select>
							</div>					
						</div>
						<div class="modal-footer">					
							<input type="submit" class="btn btn-success" name="form-submit-button">
						</div>
					</form>
		        </div>
	      	</div>
	  	</div>
  	</div>
  	<?php
	if (isset($_REQUEST["form-submit-button"])) {

	    $title = $_POST['title'];
	    $resourceFrom = $_POST['from'];
	    $resourceTo = $_POST['to'];
	    $marginMin = $_POST['min'];
	    $marginMax = $_POST['max'];
	    $colorCombination = $_POST['color'];
	    $created_at = date('Y-m-d');

	    $query = "SELECT * FROM vtech_mappingdb.client_optimization_matrix WHERE title = '" . $_POST["title"] . "'";

	    $result = mysqli_query($allConn, $query);
	    $numberOfRows = mysqli_num_rows($result);

	    if ($numberOfRows > 0) {
	        echo $query = "UPDATE vtech_mappingdb.client_optimization_matrix SET `title`='$title',`resource_from`='$resourceFrom',`resource_to`='$resourceTo',`margin_min`='$marginMin',`margin_max`='$marginMax',`color`='$colorCombination' WHERE title = '" . $_POST["title"] . "'";
	        if (mysqli_query($allConn, $query)) {
	            $result = "Record updated successfully";
	        }
	    } else {
	        mysqli_query($allConn, "INSERT INTO vtech_mappingdb.client_optimization_matrix(`id`, `title`, `resource_from`, `resource_to`, `margin_min`, `margin_max`, `color`, `loginId`, `created_at`) VALUES ('','$title','$resourceFrom','$resourceTo','$marginMin','$marginMax','$colorCombination','$user','$created_at')");
	    }
	}
?>

<script>
	$(document).on("click",".myModal",function(){
		$("#myModal").modal();
	});
	$(document).on("click",".myModal-edit",function(){
		var title = $(this).data("title");
		$.ajax({  
            url:"fetch.php",  
            method:"POST",  
            data:{title:title},  
            dataType:"json",  
            success:function(data){
				$('#title').val(data.title);  
				$('#resource_from').val(data.resource_from);  
				$('#resource_to').val(data.resource_to);  
				$('#margin_min').val(data.margin_min);  
				$('#margin_max').val(data.margin_max);  
				$('#color').val(data.color);  
				$("#myModal-edit").modal("show");
            }
        });
	});
</script>
</body>
</html>
