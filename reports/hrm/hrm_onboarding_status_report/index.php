
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
    include_once('../../../cdn.php');
  ?>

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
  </style>
  
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
        <div class="col-md-2 col-md-offset-8">
          <a href="../../../logout.php" class="btn btnx pull-right" style="color: #fff;"><i class="fa fa-fw fa-power-off"></i> Logout</a>
        </div>
      </div>
    </div>
  </section>

  <section id="EXPdatatable" class="hidden">
    <div class="container-fluid">
      <div class="row" style="margin-bottom: 50px;">
        <div class="col-md-10 col-md-offset-1">
          <table id="EXPdata" class="table table-striped table-bordered">
            <thead>
              <tr style="background-color: #ccc;color: #000;font-size: 13px;">
                <th style="text-align: center;vertical-align: middle;" rowspan="2">Candidate</th>
                <th style="text-align: center;vertical-align: middle;" rowspan="2">Client</th>
                <th style="text-align: center;vertical-align: middle;" rowspan="2">Job Order</th>
                <th style="text-align: left;vertical-align: middle;" rowspan="2">Employment Type</th>
                <th style="text-align: center;vertical-align: middle;" colspan="6">Compliances</th>
              </tr>
              <tr style="background-color: #ccc;color: #000;font-size: 13px;">
                <th style="text-align: center;vertical-align: middle;">Offer Date</th>
                <th style="text-align: center;vertical-align: middle;">End Date</th>

                
                
                <th style="text-align: center;vertical-align: middle;">Total</th>
                <th style="text-align: center;vertical-align: middle;">Submitted</th>
                <th style="text-align: center;vertical-align: middle;">Pending</th>

                <th style="text-align: center;vertical-align: middle;">Status</th>
              </tr>
            </thead>
           <tbody>
              <?php
                $managerList = array();
                $mainQUERY = "SELECT
                  e.id AS id,
                  CONCAT(e.first_name, ' ', e.last_name) AS ename,
                  es.name AS type,
                  comp.company_id AS cid,
                  comp.name AS cname,
                  si.c_candidate_id AS canid,
                  si.c_joborder_id AS jobid,
                  cbs.onboarding_date,
                  date_format(e.joined_date, '%m-%d-%Y') AS offerDate,
                  date_format(e.joined_date, '%Y-%m-%d') AS offerDateX,
                  COUNT(CASE WHEN cbs.CCTID = cst.CCTID AND cbs.OVID != 6 AND cbs.client_id = cst.client_id THEN cst.description END) AS totalCompliance,
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
                <td style="text-align: left;vertical-align: middle;"><?php echo $value['type']; ?></td>
                <td style="text-align: center;vertical-align: middle;"><?php echo $value['offerDate']; ?></td>
                <td style="text-align: center;vertical-align: middle;"><?php echo date('m-d-Y', strtotime($value['offerDateX'].'9 weekdays')); ?></td>


                <?php
                    if ($value['totalCompliance'] < 1) {
                      
                      ?>
                      <td style="text-align: center;vertical-align: middle;"><a data-toggle="modal" data-target="<?php echo $value['id']; ?>" style="text-decoration: none;color: #673AB7;"><?php echo $value['totalCompliance']; ?></a></td>
                      <?php
                    }
                    else {
                      ?>
                      

                      <td style="text-align: center;vertical-align: middle;"><a data-toggle="modal" data-target="#totalCompliance<?php echo $value['id']; ?>" style="cursor: pointer;text-decoration: none;color: #673AB7;"><?php echo $value['totalCompliance']; ?></a></td>
                      <?php
                    }
                ?>
                
                <?php
                    if ($value['submittedCompliance'] < 1) {
                      
                      ?>
                      <td style="text-align: center;vertical-align: middle;"><a data-toggle="modal" data-target="<?php echo $value['id']; ?>" style="text-decoration: none;color: #449D44;"><?php echo $value['submittedCompliance']; ?></a></td>
                      <?php
                    }
                    else {
                      ?>
                      

                      <td style="text-align: center;vertical-align: middle;"><a data-toggle="modal" data-target="#submittedCompliance<?php echo $value['id']; ?>" style="cursor: pointer;text-decoration: none;color: #449D44;"><?php echo $value['submittedCompliance']; ?></a></td>
                      <?php
                    }
                ?>

                <?php
                    if ($value['pendingCompliance'] < 1) {
                      
                      ?>
                      <td style="text-align: center;vertical-align: middle;"><a data-toggle="modal" data-target="<?php echo $value['id']; ?>" style="text-decoration: none;color: #fc2028;"><?php echo $value['pendingCompliance']; ?></a></td>
                      <?php
                    }
                    else {
                      ?>
                      

                      <td style="text-align: center;vertical-align: middle;"><a data-toggle="modal" data-target="#pendingCompliance<?php echo $value['id']; ?>" style="cursor: pointer;text-decoration: none;color: #fc2028;"><?php echo $value['pendingCompliance']; ?></a></td>
                      <?php
                    }
                ?>

                

                <td style="text-align: center;vertical-align: middle;"><a data-toggle="modal" data-target="#statusCompliance<?php echo $value['id']; ?>" style="cursor: pointer;text-decoration: none;color: #333;font-size: 15px;"><i class="fa fa-eye"></i></a></td>
              </tr>
              <?php
            }

              ?>

              </tbody>
          </table>
              
              <!--submittedCompliance Modal -->
              <?php
                foreach ($result as $key => $value) {
                ?>
              <div id="submittedCompliance<?php echo $value['id']; ?>" class="modal fade" role="dialog">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header" style="background-color: #449D44;color: #fff;">
                      <button type="button" class="close" data-dismiss="modal" style="color: #fff;">&times;</button>
                      <center><h4 class="modal-title">Submitted Compliances of <?php echo $value['ename']; ?></h4></center>
                    </div>
                    <div class="modal-body">
                      <?php
                        if (isset($array[$value['id']])) {
                          $count = 1;
                          foreach($array[$value['id']] as $innerRow => $value) {
                            if ($value['ovid'] != '4' || $value['submit'] == '') {
                              continue;
                            }
                            ?>
                            <div class="row" style="padding: 2px;">
                            <div class="col-md-1 col-md-offset-1 text-center" style="border-bottom: 1px dotted #ccc;"><?php echo $count; ?></div>
                            <div class="col-md-9" style="border-bottom: 1px dotted #ccc;"><?php echo $value['submit']; ?></div>
                          </div>
                            <?php 
                            $count++;
                          }
                        }
                        ?>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
              <?php
            }
              ?>
              <?php
                foreach ($result as $key => $value) {
                ?>
              <div id="pendingCompliance<?php echo $value['id']; ?>" class="modal fade" role="dialog">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header" style="background-color: #449D44;color: #fff;">
                      <button type="button" class="close" data-dismiss="modal" style="color: #fff;">&times;</button>
                      <center><h4 class="modal-title">Pending Compliances of <?php echo $value['ename']; ?></h4></center>
                    </div>
                    <div class="modal-body">
                      <?php
                        if (isset($array[$value['id']])) {
                          $count = 1;
                          foreach($array[$value['id']] as $innerRow => $value) {
                            if ($value['ovid'] == '4' || $value['pending'] == '') {
                              continue;
                            }
                            ?>
                            <div class="row" style="padding: 2px;">
                            <div class="col-md-1 col-md-offset-1 text-center" style="border-bottom: 1px dotted #ccc;"><?php echo $count; ?></div>
                            <div class="col-md-9" style="border-bottom: 1px dotted #ccc;"><?php echo $value['pending']; ?></div>
                          </div>
                            <?php 
                            $count++;
                          }
                        }
                        ?>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
              <?php
            }
              ?>
              <?php
                foreach ($result as $key => $value) {
                ?>
              <div id="totalCompliance<?php echo $value['id']; ?>" class="modal fade" role="dialog">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header" style="background-color: #449D44;color: #fff;">
                      <button type="button" class="close" data-dismiss="modal" style="color: #fff;">&times;</button>
                      <center><h4 class="modal-title">Total Compliances of <?php echo $value['ename']; ?></h4></center>
                    </div>
                    <div class="modal-body">
                      <?php
                        if (isset($array[$value['id']])) {
                          $count = 1;
                          foreach($array[$value['id']] as $innerRow => $value) {
                            if ($value['ovid'] == '6' || $value['required'] == '') {
                              continue;
                            }
                            ?>
                            <div class="row" style="padding: 2px;">
                            <div class="col-md-1 col-md-offset-1 text-center" style="border-bottom: 1px dotted #ccc;"><?php echo $count; ?></div>
                            <div class="col-md-9" style="border-bottom: 1px dotted #ccc;"><?php echo $value['required']; ?></div>
                          </div>
                            <?php 
                            $count++;
                          }
                        }
                        ?>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
              <?php
            }
              ?>
              <?php
                foreach ($result as $key => $value) {
                ?>
              <div id="statusCompliance<?php echo $id = $value['id']; ?>" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header" style="background-color: #2266AA;color: #fff;">
                      <button type="button" class="close" data-dismiss="modal" style="color: #fff;">&times;</button>
                      <center><h4 class="modal-title">Status of <?php echo $value['ename']; ?></h4></center>

                    </div>
                    <div class="modal-body">

                      <div class="col-md-12">
                        <div class="row" style="padding: 3px;color: #fff;background-color: #777;color: #fff;">
                          <div class="col-md-1 text-center">No.</div>
                          <div class="col-md-5">Description</div>
                          <div class="col-md-2">Status</div>
                          <div class="col-md-2">In Process On</div>
                          <div class="col-md-2">Completed On</div>
                        </div>
                        <?php
                         if (isset($array[$value['id']])) {
                          $count = 1;
                          foreach($array[$value['id']] as $innerRow => $value) {
                            if ( $value['status'] == '') {

                                continue;
                              
                            }
                            ?>
                                    <div class="row" style="padding: 2px;">
                                    <div class="col-md-1 text-center" style="border-bottom: 1px dotted #ccc;"> <?php echo $count;?></div>
                                   <div class="col-md-5" style="border-bottom: 1px dotted #ccc;"><?php echo $value['status'];?></div>
                                     <?php
                                          if(($value['ovid']==5 ) || ($value['ovid']==2 )){

                                      ?>
                                     <div class="col-md-2" style="border-bottom: 1px dotted #ccc; color: #fb1111"><?php echo $value['value']; ?></div>
                                      <?php
                                          
                                       }
                                      elseif (($value['ovid']==5) || ($value['ovid']==2)){
                                       ?>
                                        <div class="col-md-2" style="border-bottom: 1px dotted #ccc; color:#FF0000"><?php echo $value['value'];?></div>
                                          <?php
                                      }
                                      elseif($value['ovid']==3){
                                      ?>
                                    <div class="col-md-2" style="border-bottom: 1px dotted #ccc; color:orange"><?php echo $value['value'];?></div>
                                          <?php
                                        }
                                        elseif($value['ovid']==1 || $value['ovid']==4){
                                        ?>
                                      <div class="col-md-2" style="border-bottom: 1px dotted #ccc; color:green"><?php echo $value['value'];?></div>
                                      <?php
                                       }
                                    else{
                                    ?>
                                    <div class="col-md-2" style="border-bottom: 1px dotted #ccc"><?php echo $value['value'];?></div>
                                    <?php
                                      }?>
                                    <?php
                                    if ($value['inprocess_on'] == '0000-00-00 00:00:00') {
                                                        $d_time_in = "-";
                                                    }else{
                                                     $d_time_in = date("m-d-Y", strtotime($value['inprocess_on']));
                                                    }
                                                    if ($value['completed_on'] == '0000-00-00 00:00:00') {
                                                        $d_time_co = "-";
                                                    }else{
                                                     $d_time_co = date("m-d-Y", strtotime($value['completed_on']));
                                                    }
                                        ?>
                                    <div class="col-md-2" style="border-bottom: 1px dotted #ccc; text-align: center;"><?php echo $d_time_in?></div>
                                    <div class="col-md-2" style="border-bottom: 1px dotted #ccc; text-align: center;"><?php echo $d_time_co?></div>
                                  </div>
                                  <?php 
                                      $count++;
                                    }
                                  }
                                  ?>
                               </div>
                             </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>
                <?php
                  }
                ?>
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

