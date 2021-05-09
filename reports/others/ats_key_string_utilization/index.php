<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "73";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
			$sessionROW = mysqli_fetch_array($sessionQUERY);

			$personnelList = $recruiterList = array();

			$personnelFullName = $sessionROW["first_name"]." ".$sessionROW["last_name"];
			
			if ($sessionROW["user_type"] == "CS Manager") {
				$personnelList[] = $personnelFullName;
				$recruiterList = catsRecruiterList($catsConn,$personnelFullName);
				$defaultManagerList = $personnelFullName;
			} elseif ($sessionROW["user_type"] == "Management") {
				$personnelList = catsExtraFieldPersonnelListByDirector($allConn,$personnelFullName);
				$recruiterList = catsRecruiterList($catsConn,$personnelList);
				$defaultManagerList = $personnelList;
			} else {
				$nullVariable = "";
				$personnelList = catsExtraFieldPersonnelList($catsConn,"Manager - Client Service");
				$recruiterList = catsRecruiterList($catsConn,$nullVariable);
				$defaultManagerList = $nullVariable;
			}
?>
<!DOCTYPE html>
<html>
<head>
	<title>ATS Key String Utilization Report</title>
	<?php include_once "../../../cdn.php";?>
	<style>
		table.dataTable thead th,
		table.dataTable tfoot th {
			padding: 3px 0px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable tbody td {
			padding: 2px 1px;
			text-align: center;
			vertical-align: middle;
		}
		table#keystringtTable {
	
	    left: 2%;
	    margin-left: 2%;
		}
		#manageKeyStringDataLoading {
		text-align: center;margin-top: 0px;font-size: 17px;
		}
		table.dataTable tbody td:nth-child(1) {
			text-align: left;
		}
		table.dataTable thead tr:nth-child(2) th:last-child {
			border-right: 1px solid #ddd;
		}
		table.dataTable tbody td a {
			cursor: pointer;
			font-weight: bold;
		}
		table#keystringtTable thead tr {
		    background-color: #ccc;
		}
		table#keystringtTable thead tr th {
		    text-align: left;vertical-align: middle;
		}
		table#keystringtTable tbody tr td {
		    text-align: center;vertical-align: middle;
		    border-bottom: 1px solid #ccc;
		}
		table#keystringtTable tbody tr td:nth-child(4) {
    		text-align: left;
		}
		table#keystringtTable thead tr th:nth-child(5) {
    		width: 131px !important;
		}
		.dataTables_length {
			margin-left: 4%;
		}
		#keystringtTable_filter {
			margin-right: 4%;
		}
		.dataTables_info {
			margin-left: 7%;
		}
		#keystringtTable_paginate {
			margin-right: 22px;
		}
		.dark-button,
		.dark-button:focus {
			outline: none;
			color: #fff;
			background-color: #2266AA;
			border: 1px solid #2266AA;
			border-radius: 0px;
		}
		.smooth-button,
		.smooth-button:focus {
			outline: none;
			background-color: #fff;
			color: #2266AA;
			border: 1px solid #2266AA;
			border-radius: 0px;
			font-weight: bold;
		}
		.logout-button {
			outline: none;
			color: #fff;
			background-color: #2266AA;
			border: 1px solid #2266AA;
			border-radius: 0px;
			padding: 5px 12px;
			float: right;
		}
		.report-title {
			color: #000;
			font-size: 27px;
			background-color: #aaa;
			padding: 10px;
			text-align: center;
		}
		.loading-image-style {
			display: block;
			margin: 0 auto;
			width: 250px;
		}
		.main-section {
			margin-top: 20px;
			/*margin-bottom: 80px;*/
		}
		.main-section-row {
			margin-top: 15px;
		}
		.main-section-submit-row {
			margin-top: 30px;
		}
		.input-group-addon {
			background-color: #2266AA;
			border-color: #2266AA;
			color: #fff;
		}
		.form-submit-button {
			background-color: #449D44;
			border-radius: 0px;
			border: #449D44;
			outline: none;
			color: #fff;
		}
		.report-bottom-style {
			margin-bottom: 50px;
		}
		.thead-tr-style th {
			background-color: #ccc;
			color: #000;
			font-size: 11px;
		}
		.tbody-tr-style td {
			color: #333;
			font-size: 12px;
		}
		.tfoot-tr-style th {
			background-color: #ccc;
			color: #000;
			font-size: 13px;
		}
		.wonDivision {
			font-size: 10px;
			color: #333;
			text-align: right;
			color: #2266AA;
			font-weight: bold;
		}
		.loader .ring {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%,-50%);
			width: 150px;
			height: 150px;
			background: transparent;
			border: 3px solid #3c3c3c;
			border-radius: 50%;
			text-align: center;
			line-height: 150px;
			font-family: sans-serif;
			font-size: 20px;
			color: #fff000;
			letter-spacing:4px;
			text-transform: uppercase;
			text-shadow:0 0 10px #fff000;
			box-shadow: 0 0 20px rgba(0,0,0,.5);
			z-index: 1
		}
		.loader .ring:before {
			content: '';
			position: absolute;
			top: -3px;
			left: -3px;
			width: 100%;
			height: 100%;
			border: 3px solid transparent;
			border-top: 3px solid #fff000;
			border-right: 3px solid #fff000;
			border-radius: 50%;
			animation: animateCircle 2s linear infinite;
		}
		.loader .ring span {
			display: block;
			position: absolute;
			top: calc(50% - 2px);
			left: 50%;
			width: 50%;
			height: 4px;
			background: transparent;
			transform-origin:left;
			animation: animate 2s linear infinite;
		}
		.loader .ring span:before {
			content:'';
			position: absolute;
			width: 16px;
			height: 16px;
			border-radius: 50%;
			background-color: #fff000;
			top: -6px;
			right: -8px;
			box-shadow: 0 0 20px #fff000;
		}
		
		.hiddenclass {
    		visibility:hidden;
		}

		 @keyframes animateCircle
		{
			0%
			{
				transform: rotate(0deg);
			}
			100%
			{
				transform: rotate(360deg);
			}
		}
		@keyframes animate
		{
			0%
			{
				transform: rotate(45deg);
			}
			100%
			{
				transform: rotate(405deg);
			}
		}
		.table-bordered {
			font-size: 12px;
			margin: 0;
		}
		.table-bordered thead th, .table-bordered td {
			line-height: 11px;
		}
		.table-bordered th, .table-bordered td {
			text-transform: capitalize;
			padding: 5px !important;
		}

		.table-bordered td {
			vertical-align: middle !important;
		}

		.table td.red {
			background-color: #ff0000;
			color: #fff;
		}
		.p-0 {
			padding: 0;
		}
	</style>
</head>
<body>
<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">ATS Key String Utilization Report</div>
				<div class="col-md-12 loading-image">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" class="loading-image-style">
				</div>
			</div>
		</div>
	</section>
	<section class="main-section hidden">
		<div class="container">
			<div class="row">
				<div class="col-md-4 col-md-offset-4">
					<div class="row">
						<div class="col-md-4">
							<button type="button" class="form-control months-button dark-button">Months</button>
						</div>
						<div class="col-md-4">
							<button type="button" class="form-control quarter-button smooth-button">Quarters</button>
						</div>
						<div class="col-md-4">
							<button type="button" class="form-control date-range-button smooth-button p-0">Date Range</button>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<button type="button" onclick="location.href='<?php echo DIR_PATH; ?>logout.php'" class="logout-button"><i class="fa fa-fw fa-power-off"></i> Logout</button>
				</div>
			</div>

			<form id="searchKeyStringForm">
				<div class="row main-section-row multiple-month-input">
					<div class="col-md-4 col-md-offset-4">
						<label>Select Months:</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>

							<input type="text" name="customized-multiple-month" class="form-control customized-multiple-month active-input-field" value="<?php if (isset($_REQUEST['customized-multiple-month'])) {echo $_REQUEST['customized-multiple-month'];}?>" placeholder="MM/YYYY" autocomplete="off" required>

							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
						</div>
					</div>
				</div>
				<div class="row main-section-row multiple-quarter-input hidden">
					<div class="col-md-4 col-md-offset-4">
						<label>Select Quarters:</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>

							<input type="text" name="customized-multiple-quarter" class="form-control customized-multiple-quarter" value="<?php if (isset($_REQUEST['customized-multiple-quarter'])) {echo $_REQUEST['customized-multiple-quarter'];}?>" placeholder="Quarters/YYYY" autocomplete="off" required>

							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
						</div>
					</div>
				</div>
				<div class="row main-section-row date-range-input hidden">
					<div class="col-md-2 col-md-offset-4">
						<label>Date From :</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>

							<input type="text" name="customized-from-date" class="form-control customized-date-picker customized-from-date" value="<?php if (isset($_REQUEST['customized-from-date'])) {echo $_REQUEST['customized-from-date'];}?>" placeholder="MM/DD/YYYY" autocomplete="off" disabled>
						</div>
					</div>
					<div class="col-md-2">
						<label>Date To :</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>

							<input type="text" name="customized-to-date" class="form-control customized-date-picker customized-to-date" value="<?php if (isset($_REQUEST['customized-to-date'])) {echo $_REQUEST['customized-to-date'];}?>" placeholder="MM/DD/YYYY" autocomplete="off" disabled>
						</div>
					</div>
				</div>
				<div class="row main-section-row">
					<div class="col-md-2 col-md-offset-4">
						<label>Select Manager :</label>
						<select id="manager-list" class="customized-selectbox-without-all customized-manager" name="manager-list">
							<option value="">Select Option</option>
							<?php
								sort($personnelList);
								foreach ($personnelList as $personnelKey => $personnelValue) {
									echo "<option value='".$personnelValue."'>".ucwords($personnelValue)."</option>";

								}
							?>
						</select>
					</div>
					<div class="col-md-2">
						<label>Select Recruiter :</label>
						<select id="recruiter-list" class="customized-selectbox-with-all customized-recruiter" name="recruiter-list[]" multiple required>
							<?php
								foreach ($recruiterList as $recruiterKey => $recruiterValue) {
									echo "<option value='".$recruiterValue['id']."'>".$recruiterValue['name']."</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class="row main-section-submit-row">
					<div class="col-md-2 col-md-offset-4">
						<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="dark-button form-control"><i class="fa fa-arrow-left"></i> Back to Home</button>
					</div>
					<div class="col-md-2">
						<button type="submit" name="form-submit-button" class="form-control form-submit-button"><i class="fa fa-search"></i> Search</button>
					</div>
				</div>
			</form>
		</div>
		<div id="manageKeyStringDataLoading" class="hidden">Too much Data to Compute. Give us a moment, we will be back with the Result ....</div> <br>
	</section>

	<section id="manageKeyStringData" class="customized-datatable-section hiddenclass">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table id="keystringtTable" class="table table-striped table-bordered customized-datatable" cellspacing="0" width="96%">
            <thead>
            <tr>
            	<th class="no-sort">No</th>
                <th class="no-sort" style="width: 81px;text-align: center;">Recruiter</th>
                <th class="no-sort" style="width: 107px;text-align: center;">CS Manager</th>
                <th class="no-sort" style="text-align: center;">Key String</th>
                <th style="text-align: center;">Date Time</th>
                <th class="no-sort" style="text-align: center;">Job Portal Name</th>
                <th class="no-sort" style="text-align: center;">Count</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
</div>
	</section>

<!--View view-margin-detail Modal-->
<div class="modal fade keystringBriefViewModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
	        <div class="modal-body keystringBriefViewModalBody">
	        </div>
        </div>
    </div>
</div>

</body>

<script>
	$(document).ready(function(){
		$(".loading-image").hide();
		$(".main-section").removeClass("hidden");
		$(".customized-datatable-section").removeClass("hidden");
		$.fn.datepicker.dates['qtrs'] = {
		  days: ["Sunday", "Moonday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
		  daysShort: ["Sun", "Moon", "Tue", "Wed", "Thu", "Fri", "Sat"],
		  daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
		  months: ["Q1", "Q2", "Q3", "Q4", "", "", "", "", "", "", "", ""],
		  monthsShort: ["Q1", "Q2", "Q3", "Q4", "", "", "", "", "", "", "", ""],
		  today: "Today",
		  clear: "Clear",
		  format: "mm/dd/yyyy",
		  titleFormat: "MM yyyy",
		  /* Leverages same syntax as 'format' */
		  weekStart: 0,

		};
        $(".customized-multiple-month").datepicker({
            format: "mm/yyyy",
            startView: 1,
            minViewMode: 1,
            maxViewMode: 2,
            clearBtn: true,
            multidate: true,
            orientation: "top",
            autoclose: false
        });

        $(".customized-date-picker").datepicker({
            todayHighlight: true,
            clearBtn: true,
            orientation: "top",
            autoclose: true
        });
        $(".customized-multiple-quarter").datepicker({
						format: "MM/yyyy",
						minViewMode: 1,
						language: "qtrs",
						forceParse: false,
						clearBtn: true,
						multidate: true,
						autoclose: false,
        }).on("show", function(event) {
				$(".month").each(function(index, element) {
			    if (index > 3) $(element).hide();
			});
		});

		$(".customized-selectbox-without-all").multiselect({
            nonSelectedText: "Select Option",
            numberDisplayed: 1,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: "100%",
            includeSelectAllOption: true,
            maxHeight: 200
        });

        $(".customized-selectbox-with-all").multiselect({
            nonSelectedText: "Select Option",
            numberDisplayed: 1,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: "100%",
            includeSelectAllOption: true,
            maxHeight: 200
        });
		$(".customized-selectbox-with-all").multiselect("selectAll", false);
		$(".customized-selectbox-with-all").multiselect("updateButtonText");

    });

	$(document).on("change", "#manager-list", function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "<?php echo DIR_PATH; ?>functions/search-recruiter-by-manager.php",
			data: "manager="+$("#manager-list").val()+"&type="+'<?php echo $sessionROW['user_type']; ?>'+"&default="+'<?php echo json_encode($defaultManagerList); ?>',
			success:function(response){
				$("#recruiter-list").html(response);
				$("#recruiter-list").multiselect("destroy");
				$("#recruiter-list").multiselect({
		            nonSelectedText: "Select Option",
		            numberDisplayed: 1,
		            enableFiltering: true,
		            enableCaseInsensitiveFiltering: true,
		            buttonWidth: "100%",
		            includeSelectAllOption: true,
		            maxHeight: 200
		        });
				$("#recruiter-list").multiselect("selectAll", false);
				$("#recruiter-list").multiselect("updateButtonText");
			}
		});
	});

	// Remove error on focus of input element
	$(document).on("focus",".active-input-field",function(){
		if ($(this).parent('.input-group').hasClass('has-error')){
				$(this).parent('.input-group').removeClass('has-error');
		}
	});

	$(document).on("click", ".months-button", function(e){
		e.preventDefault();
		$(".customized-multiple-month").prop("required", true);
		$(".customized-date-picker, .customized-multiple-quarter").prop("required", false);
		$(".customized-multiple-month").prop("disabled", false);
		$(".customized-date-picker, .customized-multiple-quarter").prop("disabled", true);
		$(".date-range-input, .multiple-quarter-input").addClass("hidden");
		$(".multiple-month-input").removeClass("hidden");
		$(".months-button").addClass("dark-button");
		$(".months-button").removeClass("smooth-button");
		$(".date-range-button, .quarter-button").addClass("smooth-button");
		$(".date-range-button, .quarter-button").removeClass("dark-button");
		$(".customized-date-picker, .customized-multiple-quarter").removeClass("active-input-field");
		$(".customized-multiple-month").addClass("active-input-field");
	});

	$(document).on("click", ".quarter-button", function(e){
		e.preventDefault();
		$(".customized-multiple-quarter").prop("required", true);
		$(".customized-date-picker, .customized-multiple-month").prop("required", false);
		$(".customized-multiple-quarter").prop("disabled", false);
		$(".customized-date-picker, .customized-multiple-month").prop("disabled", true);
		$(".date-range-input, .multiple-month-input").addClass("hidden");
		$(".multiple-quarter-input").removeClass("hidden");
		$(".quarter-button").addClass("dark-button");
		$(".quarter-button").removeClass("smooth-button");
		$(".date-range-button, .months-button").addClass("smooth-button");
		$(".date-range-button, .months-button").removeClass("dark-button");
		$(".customized-date-picker, .customized-multiple-month").removeClass("active-input-field");
		$(".customized-multiple-quarter").addClass("active-input-field");
	});

	$(document).on("click", ".date-range-button", function(e){
		e.preventDefault();
		$(".customized-date-picker").prop("required", true);
		$(".customized-multiple-month, .customized-multiple-quarter").prop("required", false);
		$(".customized-date-picker").prop("disabled", false);
		$(".customized-multiple-month, .customized-multiple-quarter").prop("disabled", true);
		$(".date-range-input").removeClass("hidden");
		$(".multiple-month-input, .multiple-quarter-input").addClass("hidden");
		$(".date-range-button").addClass("dark-button");
		$(".date-range-button").removeClass("smooth-button");
		$(".months-button, .quarter-button").addClass("smooth-button");
		$(".months-button, .quarter-button").removeClass("dark-button");
		$(".customized-multiple-month, .customized-multiple-quarter").removeClass("active-input-field");
		$(".customized-date-picker").addClass("active-input-field");
	});

	$(document).on("click", ".form-submit-button", function(e){
		e.preventDefault();
		let data = null;
		let multipleMonth = $('.customized-multiple-month').val();
		let fromDate = $('.customized-from-date').val();
		let toDate = $('.customized-to-date').val();
		let multipleQuarter = $('.customized-multiple-quarter').val();
		let clientName = $('.customized-manager').val();
		let recruiterName = $('.customized-recruiter').val();
		if ($('.multiple-month-input').hasClass('hidden') === true) {
			multipleMonth = '';
		}

		if ($('.multiple-quarter-input').hasClass('hidden') === true) {
			multipleQuarter = '';
		}

		if ($('.date-range-input').hasClass('hidden') === true) {
			fromDate = '';
			toDate = '';
		}

		if (multipleMonth == '' && fromDate == '' && toDate == '' && multipleQuarter == '') {
			$(".active-input-field").parent('.input-group').addClass('has-error'); // To display an error if left it blank
			return false;
		}

		data = {
			multipleMonth: multipleMonth,
			fromDate: fromDate,
			toDate: toDate,
			multipleQuarter: multipleQuarter,
			clientName: clientName,
			recruiterName: recruiterName

		}
		$("#manageKeyStringDataLoading").removeClass("hidden");
		var dataTable=$('#keystringtTable').DataTable({
                "processing": true,
                "serverSide":true,
                "bDestroy": true,
                "aaSorting": [[4,"asc"]],
                "columnDefs":[{
					"targets" : "no-sort",
					"orderable": false,
				}],
                "ajax":{
                    url:"<?php echo SAAS_PATH_HTTPS .'/api/v1/keystring/keystringreport'; ?>",
                    type:"post",
                    data: data,
                    dataFilter: function(reps) {
                    $("#manageKeyStringDataLoading").addClass("hidden");
	                $("#manageKeyStringData").removeClass("hiddenclass");
	                return reps;
            	},
                },
                "columns": [
                	{ "data" : "slno"},
                	{ "data" : "recruiterName"},
                	{ "data" : "managerName"},
                	{ "data" : "keyword",
                	"render": function(data, type, row, meta){
			            if(type === 'display'){
			                data = '<a href="javascript:void(0)" class="keystringBriefView" data-output =\''+row.keyString+'\' data-keyword =\''+row.keyword+'\'>' + data + '</a>';
			            }
			            
			            return data;
			        }
                	},
                	{ "data" : "date"},
                	{ "data" : "source"},
                	{ "data" : "count"}
                ]
            });
			//$("#manageKeyStringData").removeClass("hiddenclass");
        });

	$(document).on("click", ".keystringBriefView", function(e){
		e.preventDefault();
		$(".keystringBriefViewModal").modal("show");
		$(".keystringBriefViewModalBody").html("");
		var outputData = $(this).data("output");
		var outputkeyword = $(this).data("keyword");
		console.log(outputkeyword);
		var zip = outputData.zip;
		if (typeof outputData.zip === 'undefined') {

			var zip = outputData.location;
		}
		if (typeof outputData.experience === 'undefined') {

			var experience = null;
		} else {
			var experience = outputData.experience;
		}
		if (typeof outputData.resume_post_to === 'undefined') {

			var resume_post_to = null;
		} else {
			var resume_post_to = outputData.resume_post_to;
		}
		if (typeof outputData.relocation === 'undefined') {

			var relocation = null;
		} else {
			var relocation = outputData.relocation;
		}
		if (typeof outputData.work_authority === 'undefined') {

			var work_authority = null;
		} else {
			var work_authority = outputData.work_authority;
		}
		if (typeof outputData.career_level === 'undefined') {

			var career_level = null;
		} else {
			var career_level = outputData.career_level;
		}
		if (typeof outputData.sort === 'undefined') {

			var sort = null;
		} else {
			var sort = outputData.sort;
		}
		if (typeof outputData.country === 'undefined') {

			var country = null;
		} else {
			var country = outputData.country;
		}
		var output = '<div class="details">\
					<table class="table table-bordered">\
				  		<tbody>\
					    	<tr>\
					        	<th>keyword</th>\
					        	<td>'+outputkeyword+'</td>\
					    	</tr>\
					    	<tr>\
					        	<th>radius</th>\
					        	<td>'+outputData.radius+'</td>\
					    	</tr>\
					    	<tr>\
					        	<th>zip</th>\
					        	<td>'+zip+'</td>\
					    	</tr>\
					    	<tr>\
					        	<th>experience</th>\
					        	<td>'+experience+'</td>\
					    	</tr>\
					    	<tr>\
					        	<th>education</th>\
					        	<td>'+outputData.education+'</td>\
					    	</tr>\
					    	<tr>\
					        	<th>minimum_salary</th>\
					        	<td>'+outputData.minimum_salary+'</td>\
					    	</tr>\
					    	<tr>\
					        	<th>maximum_salary</th>\
					        	<td>'+outputData.maximum_salary+'</td>\
					    	</tr>\
					    	<tr>\
					        	<th>resume_post_to</th>\
					        	<td>'+resume_post_to+'</td>\
					    	</tr>\
					    	<tr>\
					        	<th>Target_Job</th>\
					        	<td>'+outputData.industry_type+'</td>\
					    	</tr>\
					    	<tr>\
					        	<th>industry_type</th>\
					        	<td>'+outputData.industry_type+'</td>\
					    	</tr>\
					    	<tr>\
					        	<th>relocation</th>\
					        	<td>'+relocation+'</td>\
					    	</tr>\
					    	<tr>\
					        	<th>work_authority</th>\
					        	<td>'+work_authority+'</td>\
					    	</tr>\
					    	<tr>\
					        	<th>career_level</th>\
					        	<td>'+career_level+'</td>\
					    	</tr>\
					    	<tr>\
					        	<th>sort</th>\
					        	<td>'+sort+'</td>\
					    	</tr>\
					    	<tr>\
					        	<th>country</th>\
					        	<td>'+country+'</td>\
					    	</tr>\
					    <tbody>\
					</table>\
				</div>';
		$(".keystringBriefViewModalBody").html(output);
	});
</script>
</html>
<?php
		} else {
			if ($userMember == "Admin") {
				header("Location:../../../admin.php");
			} elseif ($userMember == "User") {
				header("Location:../../../user.php");
			} else {
				header("Location:../../../index.php");
			}
		}
    } else {
        header("Location:../../../index.php");
    }
?>