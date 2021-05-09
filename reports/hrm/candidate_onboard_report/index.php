
<?php
  include_once("../../../security.php");
    if(isset($_SESSION['user'])){
    error_reporting(0);
    include_once('../../../config.php');

    $childUser = $_SESSION['userMember'];
    $reportID = '58';
    $sessionQuery = "SELECT * FROM mapping JOIN users ON users.uid = mapping.uid JOIN reports ON reports.rid = mapping.rid WHERE mapping.uid = '$user' AND mapping.rid = '$reportID' AND users.ustatus = '1' AND reports.rstatus = '1'";
    $sessionResult = mysqli_query($misReportsConn, $sessionQuery);
    if(mysqli_num_rows($sessionResult) > 0){
        include_once('query.php');
?>
<!DOCTYPE html>
<html>
<head>
  <title>HRM Onboarding Status Report</title>

  <?php
$scheme = isset($_SERVER["HTTPS"]) ? 'https:' : 'http:';
?>

<!--Titlebar LOGO-->
<link rel="icon" href="<?php echo IMAGE_PATH; ?>/logo.png">

<!--Bootstrap + jQuery CDN-->
<link rel="stylesheet" type="text/css" href="<?php echo $scheme; ?>//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="<?php echo $scheme; ?>//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="<?php echo $scheme; ?>//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!--Datatables CDN-->

<link rel="stylesheet" type="text/css" href="<?php echo $scheme; ?>//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $scheme; ?>//cdn.datatables.net/fixedcolumns/3.2.6/css/fixedColumns.dataTables.min.css">
<style>

  table.dataTable thead th,
    table.dataTable tfoot td{
      padding: 4px 2px;
    }
    table.dataTable tbody td{
      padding: 2px;
    }
    .btnx,
    .btnx:focus{
      background-color: #2266AA;
      color: #fff;
      outline: none;
      border-color: #2266AA;
      border-radius: 0px;
    }
    .btny,
    .btny:focus{
      background-color: #fff;
      color: #2266AA;
      font-weight: bold;
      outline: none;
      border-color: #2266AA;
      border-radius: 0px;
    }
    table.EXPdata {
    background: #ccc none repeat scroll 0 0;
    border: 3px solid #666;
    margin: 5px;
    padding: 5px;
    position: relative;
    width: 200px;
    height: 100px;
    /*overflow: auto;*/
  }

  div.dataTables_wrapper {
        width: 1200px;
        margin: 0 auto;
    }

    #example {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

#example td, #example th {
    border: 1px solid #ddd;
    padding: 8px;
}
  </style>
  <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
  <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/fixedcolumns/3.2.6/js/dataTables.fixedColumns.min.js"></script>
  
  <script>
      $(document).ready(function(){
        $("#LoadingImage").hide();
        $('#MainSection').removeClass("hidden");
        $('#EXPdatatable').removeClass("hidden");

        var tableX = $('#EXPdata').DataTable({
          "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            dom: 'Bfrtip',
            "aaSorting": [[0,'asc']],
              buttons:[
                  'excel','pageLength'
              ],
              initComplete: function(){
                $('div.dataTables_filter input').css("width","250")
          }
        });
        tableX.button(0).nodes().css('background', '#2266AA');
        tableX.button(0).nodes().css('border', '#2266AA');
        tableX.button(0).nodes().css('color', '#fff');
        tableX.button(0).nodes().html('Download Report');
        tableX.button(1).nodes().css('background', '#449D44');
        tableX.button(1).nodes().css('border', '#449D44');
        tableX.button(1).nodes().css('color', '#fff');

      });
  </script>
  <script>
$(document).ready(function() {
    var table = $('#example').DataTable( {
         scrollY: "300px",
        scrollX:        true,
        scrollCollapse: true,
        
        fixedColumns:   {
            leftColumns: 6
            
        }
    } );
} );
</script>
</head>
<body>

  <section>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12 text-center" style="font-size: 27px;background-color: #aaa;color: #111;padding: 10px;">HRM Onboarding Status Report</div>
        <div class="col-md-12" id="LoadingImage" style="margin-top: 10px;">
          <img src="<?php echo IMAGE_PATH; ?>/plw.gif" style="display: block;margin: 0 auto;width: 250px;">
        </div>
      </div>
    </div>
  </section>

  <section id="MainSection" class="hidden" style="margin-top: 30px;margin-bottom: 40px;">
    <div class="container">
      <div class="row">
        <div class="col-md-2">
          <button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="btnx form-control"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to Home</button>
        </div>

         <div class="col-md-3 col-md-offset-4">

          <h6 > <span style="color: green"><b>YES</span> - <span style="font-weight: bold;"> &nbsp Document has been Submitted</span></b></h6>
          <h6 ><span style="color: red"><b>NO</span> &nbsp- &nbsp  Document is under Process</b></h6>
          <h6 ><span style="color: green; text-align: center;" ><b>---</span>&nbsp &nbsp -  &nbsp Document is Not Applicable</b></h6>
          


        </div>

        <div class="col-md-2 ">
          <a href="../../../logout.php" class="btn btnx pull-right" style="color: #fff;"><i class="fa fa-fw fa-power-off"></i> Logout</a>
        </div>
      </div>
    </div>
  </section>

  <section id="EXPdatatable" class="hidden">
    <div class="container-fluid">
      <div class="row" style="margin-bottom: 50px;">
        <div class="col-md-12">
          <table id="example" class="stripe row-border order-column"  style="width:100%" >
            <thead>
              <tr style="background-color: #ccc;color: #000;font-size: 13px;">
                <th style="text-align: center;vertical-align: middle;" rowspan="2">Candidate</th>
                <th style="text-align: center;vertical-align: middle;" rowspan="2">Client</th>
                <th style="text-align: center;vertical-align: middle;" rowspan="2">Job&nbspOrder</th>
                <th style="text-align: left;vertical-align: middle;" rowspan="2">Employment&nbspType</th>
                <th style="text-align: center;vertical-align: middle;" rowspan="2"">Offer&nbspDate</th>
                <th style="text-align: center;vertical-align: middle;" rowspan="2"">End&nbspDate&nbsp&nbsp</th>
                <th style="text-align: left;vertical-align: middle;" rowspan="2">Required Compliance</th>
                <th style="text-align: center;vertical-align: middle;" rowspan="2"">Submitted Compliance</th>
                <th style="text-align: center;vertical-align: middle;" rowspan="2"">Pending Compliance</th>
                <th style="text-align: center;vertical-align: middle;" colspan=<?php 

                 $query = "SELECT  COUNT(name) as count FROM compliance_list";

                $dailyRESULT = mysqli_query($vtechTools, $query);

                  while($mainROW = mysqli_fetch_array($dailyRESULT)){
                  $complianceList[] = $mainROW;
              }

              foreach ($complianceList as $key => $totalComliance) {
              echo  $totalComliance['count'];
              }
             ?>>Compliances</th>
                 
              </tr>
              <tr style="background-color: #ccc;color: #000;font-size: 13px;">
                
                <?php
                foreach ($complianceArray as $key => $value) {
                ?>
                
                
                <th  style="text-align: center;vertical-align: middle;"><?php echo $value['name']; ?></th>
                <?php

                  }
                ?>
                 
                
              </tr>
            </thead>
           <tbody>
              <?php
                $managerList = array();
                $mainQUERY = "SELECT
                  e.id AS id,
                  CONCAT(e.first_name, ' ', e.last_name) AS ename,
                  es.name AS type,
                  e.custom1 AS benefit,
                  comp.company_id AS cid,
                  comp.name AS cname,
                  si.c_candidate_id AS canid,
                  si.c_joborder_id AS jobid,
                  cbs.onboarding_date,
                  date_format(e.joined_date, '%m-%d-%Y') AS offerDate,
                  date_format(e.joined_date, '%Y-%m-%d') AS offerDateX,
                  COUNT(CASE WHEN cbs.CCTID = cst.CCTID AND cbs.OVID != 6 AND cbs.client_id = cst.client_id  THEN cst.description END) AS totalCompliance,
                  COUNT(CASE WHEN cbs.OVID = 4 THEN cst.description AND cbs.client_id = cst.client_id END) AS submittedCompliance,
                  COUNT(CASE WHEN cbs.OVID != 4 AND cbs.OVID != 6 AND cbs.client_id = cst.client_id THEN cst.description END) AS pendingCompliance
                FROM
                  employees AS e
                  LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
                  LEFT JOIN cats.company AS comp ON comp.company_id = si.c_company_id
                  LEFT JOIN vtech_candidate_onboarding.candidate_bg_status AS cbs ON cbs.can_id = e.id
                  LEFT JOIN vtech_candidate_onboarding.compliance_client_template AS cst ON cbs.CCTID = cst.CCTID
                  INNER JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status

                WHERE
                  e.status = 'OnBoarding'
                GROUP BY e.id";
                    $dailyRESULT = mysqli_query($vtechhrmConn, $mainQUERY);
                          while($mainROW = mysqli_fetch_array($dailyRESULT)){
                          $managerList[$mainROW['id']] = $mainROW['ename'];
                          $result[$mainROW['id']] = $mainROW;
                        }
                        ?>
                <?php
                foreach ($result as $key => $value) {
                ?>
                <?php 
                ?>
              <tr style="font-size: 14px;">
                <td style="text-align: left;vertical-align: middle;"><a href="https://ats.vtechsolution.com/index.php?m=candidates&a=show&candidateID=<?php echo $value['canid']; ?>" target="_blank"><?php echo $value['ename']; ?></a></td>
                <td style="text-align: left;vertical-align: middle;"><a href="https://ats.vtechsolution.com/index.php?m=companies&a=show&companyID=<?php echo $value['cid']; ?>" target="_blank"><?php echo $value['cname']; ?></a></td>
                <td style="text-align: left;vertical-align: middle;"><a href="https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID=<?php echo $value['jobid']; ?>" target="_blank"><?php echo $value['jobid']; ?></a></td>
                <td style="text-align: left;vertical-align: middle;"><?php echo $value['type'] ."-". $value['benefit'] ; ?></td>
                <td style="text-align: center;vertical-align: middle;"><?php echo $offerDate = $value['offerDate']; ?></td>
                <td style="text-align: center;vertical-align: middle;"><?php 
                $query = mysqli_query($vtechMappingdbConn,"SELECT days 
                                                            FROM onboarding_days");

                $row=mysqli_fetch_assoc($query);
                 $days=$row['days'];
                  $date = $offerDate;
                $finalDate = str_replace('-', '/', $date);
                echo $days = date('m-d-Y',strtotime($finalDate . $days . 'days')); ?></td>
                <td style="text-align: center;vertical-align: middle; color: #673AB7" ><?php

                foreach ($countCompliance[$value['id']] as $key => $valueCount) {

                    echo $valueCount['required'];
                   
                }


                 ?></td>
                <td style="text-align: center;vertical-align: middle; color: #449D44"><?php 

                 foreach ($countCompliance[$value['id']] as $key => $valueCount) {

        echo $valueCount['completed'];
    }
                           
                 ?></td>
                <td style="text-align: center;vertical-align: middle; color: #fc2028;"><?php 

                foreach ($countCompliance[$value['id']] as $key => $valueCount) {

        echo $valueCount['required'] - $valueCount['completed'];
    }

                 ?></td>
                <?php
                foreach ($complianceArray as $complianceKey => $complianceValue) {
                 if (isset($documentCandidateStatus[$value['id']][$complianceValue["complianceId"]])) {
                    ?>


                  <td  style="text-align: center;vertical-align: middle; color: green"><?php  if($documentCandidateStatus[$value['id']][$complianceValue["complianceId"]] == 2) {
                    echo "YES";
                  }

                  else if ($documentCandidateStatus[$value['id']][$complianceValue["complianceId"]] == 1) {
                    echo "<p style = 'color:red;'>NO</p>";
                     }
                      else if ($documentCandidateStatus[$value['id']][$complianceValue["complianceId"]] == 6 ) {
                    echo "<p style = 'color:green;'>---</p>";
                     }
                    ?></td>
                  <?php
                 } else {
                  ?>
                  <td  style="text-align: center;vertical-align: middle; color: red">NO</td>
                  <?php

                 }
                  }
                  
                ?>
                <?php
                foreach ($complianceOthersArray as $complianceKey => $complianceValue) {
                 if (isset($documentsFileForOthers[$value['id']][$complianceValue["complianceId"]])) {



                  ?>
                  <td  style="text-align: center;vertical-align: middle; color: green"><?php  if($documentsFileForOthers[$value['id']][$complianceValue["complianceId"]] == 4 || $documentsFileForOthers[$value['id']][$complianceValue["complianceId"]] == 1) {
                    echo "YES";
                  }

                  else if ($documentsFileForOthers[$value['id']][$complianceValue["complianceId"]] == 2 || $documentsFileForOthers[$value['id']][$complianceValue["complianceId"]] == 5 || $documentsFileForOthers[$value['id']][$complianceValue["complianceId"]] == 3) {
                    echo "<p style = 'color:red;'>NO</p>";
                     }
                    ?></td>
                  <?php
                 } else {
                  ?>
                  <td  style="text-align: center;vertical-align: middle; color: green">---</td>
                  <?php

                 }
                  }
                  
                ?>
                <?php
                 }
                 ?>
             </tbody>
          </table>
      </div>
      </div>
    </div>
  </section>
</body>
</html>

<?php
        }else{
            if($childUser == 'Admin'){
                header("Location:../../../admin.php");
            }elseif($childUser == 'User'){
                header("Location:../../../user.php");
            }else{
                header("Location:../../../index.php");
            }
        }
    }else{
        header("Location:../../../index.php");
    }
?>

