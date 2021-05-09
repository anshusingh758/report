<?php
    include('security.php');
    include('config.php');
    include('./lib/redis.php');

    if (isset($_SESSION['user'])) {
        $childUser = $_SESSION['userMember'];
        if ($childUser == 'Admin') {
            $redisReportPath["user_name"] = 'report:user_id:'.$user.':user_name';
            $redisReportPath["first_name"] = 'report:user_id:'.$user.':first_name';
            $redisReportPath["last_name"] = 'report:user_id:'.$user.':last_name';
            $redisReportPath["email"] = 'report:user_id:'.$user.':email';
            $redisReportPath["report_category_list"] = 'report:user_id:'.$user.':report_category_list';
            $redisReportPath["report_list"] = 'report:user_id:'.$user.':report_list';

            $redisReportDetail = implode(",", $redisReportPath);
            $cacheRedisReportDetail = Redis::get($redisReportDetail);
?>
<!DOCTYPE html>
<html>
<head>
    <title>vTech Reports</title>

    <?php
        include("cdn.php");
    ?>

    <!--CSS + JS-->
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH;?>/style.css">
    <script src="<?php echo JS_PATH;?>/main.js"></script>

    <style>
        #viewUserAccessTable thead th,
        #viewUserAccessTable tbody td,
        #viewReportAccessTable thead th,
        #viewReportAccessTable tbody td {
            padding: 5px 2px;
        }
        .theme-color {
            background-color: #2266AA;
        }
        #divLoading {
        display : none;
        }
        #divLoading.show {
            display : block;
            position : fixed;
            z-index: 100;
            background-image : url('images/loadingLogo.gif');
            background-color:#666;
            opacity : 0.4;
            background-repeat : no-repeat;
            background-position : center;
            left : 0;
            bottom : 0;
            right : 0;
            top : 0;
        }
        .title-style {
            background-color: #ccc;
            color: #2266AA;
            padding: 7px;
            margin-bottom: 25px;
            font-size: 18px;
            font-weight: bold;
            border-bottom: 3px solid #2266AA;
        }
        .solid-button {
            background-color: #2266AA;
            color: #fff;
            border: 1px solid #2266AA;
            border-radius: 0px;
        }
        .addReportsForm {
            padding: 0px 40px 0px 20px;
        }
        .connect_cat a {
            outline: none;
            text-decoration: none;
        }
        .connect_cat .panel {
            border:none;
        }
        .notfondimage {
            margin-top: 50px;
        }
        .navbar-fixed-top {
            color: #fff;
            border: none;
            background-color: #2266AA;
        }
        .navbar-header a.navbar-brand {
            color: #fff;
            font-size: 20px;
            margin: -1px auto;
        }
        #change_password_modal .modal-header {
            color: #fff;
            background-color: #2266AA;
            border-bottom: 5px solid #aaa;
        }
        #change_password_modal .modal-header button {
            color: #fff;
        }
        #change_password_modal .modal-body {
            padding: 30px 1px;
        }
        #change_password_modal .modal-body div.row:nth-child(2) {
            margin-top: 15px;
        }
        li.dropdown a.dropdown-toggle {
            cursor: pointer;
        }
        li.dropdown ul.dropdown-menu hr {
            margin: 5px 0px;
            border: 0.5px #ccc solid;
        }
        li.dropdown ul.dropdown-menu a {
            cursor: pointer;
        }
    </style>
    
    <!--Datatable Calling-->
    <script>
        $(document).ready(function(){
            $('.customized-datatable').DataTable({
                "columnDefs":[{
                    "targets" : 'no-sort',
                    "orderable": false,
                }],
                "lengthMenu": [[5, 10, 30, 50, -1], [5, 10, 30, 50, "All"]]
            });

            // Select Reports
            $('#selectReports').multiselect({
                nonSelectedText: 'Select Reports',
                numberDisplayed: 1,
                enableFiltering:true,
                enableCaseInsensitiveFiltering:true,
                buttonWidth:'100%',
                includeSelectAllOption: true,
                maxHeight: 250
            });

            // Select Users
            $('#selectUsers').multiselect({
                nonSelectedText: 'Select User',
                numberDisplayed: 1,
                enableFiltering:true,
                enableCaseInsensitiveFiltering:true,
                buttonWidth:'100%',
                includeSelectAllOption: true,
                maxHeight: 250
            });

            // Search Category
            $('#searchCategory').multiselect({
                nonSelectedText: 'Select Category',
                numberDisplayed: 1,
                enableFiltering:true,
                enableCaseInsensitiveFiltering:true,
                buttonWidth:'100%',
                includeSelectAllOption: true,
                maxHeight: 250
            });

            // Select Users
            $('#selectUsers2').multiselect({
                nonSelectedText: 'Select User',
                numberDisplayed: 1,
                enableFiltering:true,
                enableCaseInsensitiveFiltering:true,
                buttonWidth:'100%',
                includeSelectAllOption: true,
                maxHeight: 250
            });

            // Select Report
            $('#selectReport2').multiselect({
                nonSelectedText: 'Select Report',
                numberDisplayed: 1,
                enableFiltering:true,
                enableCaseInsensitiveFiltering:true,
                buttonWidth:'100%',
                includeSelectAllOption: true,
                maxHeight: 250
            });

            // Search Category
            $('#searchCategory2').multiselect({
                nonSelectedText: 'Select Category',
                numberDisplayed: 1,
                enableFiltering:true,
                enableCaseInsensitiveFiltering:true,
                buttonWidth:'100%',
                includeSelectAllOption: true,
                maxHeight: 200
            });

            //Password Metch Script
            var password = document.getElementById("newpassword");
            var confirm_password = document.getElementById("confirmpassword");

            function validatePassword(){
              if (password.value != confirm_password.value) {
                confirm_password.setCustomValidity("Passwords Don't Match");
              } else {
                confirm_password.setCustomValidity('');
              }
            }

            password.onchange = validatePassword;
            confirm_password.onkeyup = validatePassword;
        });


        // Add Users
        $(document).on("submit", "#addUsersForm", function(e){
            $("#divLoading").addClass("show");
            $.ajax({
                type:"POST",
                url: "insertusers.php",
                data:$("#addUsersForm").serialize(),
                success:function(response){
                    $("#divLoading").removeClass("show");
                    if ($.trim(response) == "1") {
                        swal({
                            title: "Username Already Exists!",
                            type: "error",
                            button: "OK!"
                        });
                    }
                    if ($.trim(response) == "2") {
                        swal({
                            title: "Email Already Exists!",
                            type: "error",
                            button: "OK!"
                        });
                    }
                    if ($.trim(response) == "3") {
                        swal({
                            title: "User Successfully Created!",
                            type: "success",
                            button: "OK!"
                        });
                        $("#addUsersForm")[0].reset();
                    }
                    if ($.trim(response) == "4") {
                        swal({
                            title: "Something wrong!",
                            type: "error",
                            button: "OK!"
                        });
                    }
                }
            });
        });

        // Add Reports
        $(document).on("submit", "#addReportsForm", function(e){
            e.preventDefault();

            $.ajax({
                type:"POST",
                url: "insertreports.php",
                data:$("#addReportsForm").serialize(),
                success:function(response){
                    swal({
                        title: "Report Successfully Created!",
                        type: "success",
                        button: "OK!"
                    });
                    $("#addReportsForm")[0].reset();
                }
            });
        });

        // Add Report Access
        $(document).on("submit", "#reportAccessForm", function(e){
            e.preventDefault();

            $.ajax({
                type:"POST",
                url: "reportAccess.php",
                data:$("#reportAccessForm").serialize(),
                success:function(response){
                    if ($.trim(response) == "success") {
                        swal({
                            title: 'Report Permission Granted!',
                            type: 'success',
                            button: 'OK!'
                        },function(isConfirm){
                            alert('ok');
                        });
                    } else {
                        swal({
                        title: 'Something wrong!',
                            type: 'error',
                            button: 'OK!'
                        });
                    }
                }
            });
        });

        $(document).on("change", "#selectUsers2", function(e){
            e.preventDefault();

            $.ajax({
                url:"searchUserAccess.php",
                type:"POST",
                data:"givenId="+$(this).val(),
                success:function(response){
                    $("#view-user-access-div").html("");
                    $("#view-user-access-div").html(response);
                }
            });
        });

        $(document).on("change", "#selectReport2", function(e){
            e.preventDefault();

            $.ajax({
                url:"searchReportAccess.php",
                type:"POST",
                data:"givenId="+$(this).val(),
                success:function(response){
                    $("#view-report-access-div").html("");
                    $("#view-report-access-div").html(response);
                }
            });
        });

        $(document).on("change", "#selectUsers", function(e){
            e.preventDefault();

            var output = "";
            $.ajax({
                url:'searchCateg.php',
                type:'POST',
                data:'id='+$(this).val(),
                success:function(response){
                    $('#searchCategory2').html(response);
                    $('#searchCategory2').multiselect("destroy");
                    $('#searchCategory2').multiselect({
                        nonSelectedText: 'Select Category',
                        numberDisplayed: 1,
                        enableFiltering:true,
                        enableCaseInsensitiveFiltering:true,
                        buttonWidth:'100%',
                        includeSelectAllOption: true,
                        maxHeight: 200
                    });
                    $('#selectReports').html(output);
                    $('#selectReports').multiselect("destroy");
                    $('#selectReports').multiselect({
                        nonSelectedText: 'Select Reports',
                        numberDisplayed: 1,
                        enableFiltering:true,
                        enableCaseInsensitiveFiltering:true,
                        buttonWidth:'100%',
                        includeSelectAllOption: true,
                        maxHeight: 250
                    });
                }
            });
        });

        // Select Reports of Particular Client
        $(document).on("change", "#searchCategory2", function(e){
            e.preventDefault();

            $.ajax({
                url:"searchreports.php",
                type:"POST",
                data:"id="+$(this).val(),
                success:function(response){
                    $("#selectReports").html(response);
                    $("#selectReports").multiselect("destroy");
                    $("#selectReports").multiselect({
                        nonSelectedText: "Select Reports",
                        numberDisplayed: 1,
                        enableFiltering:true,
                        enableCaseInsensitiveFiltering:true,
                        buttonWidth:"100%",
                        includeSelectAllOption: true,
                        maxHeight: 250
                    });
                }
            });
        });

        // Select Reports of Particular Category
        $(document).on("change", "#searchCategory", function(e){
            e.preventDefault();
            
            $.ajax({
                url:'searchCategReports.php',
                type:'POST',
                data:'categoryId='+$(this).val(),
                success:function(response){
                    $('#responseReports').html("");
                    $('#responseReports').html(response);
                }
            });
        });
    </script>

</head>
<body>
<div id="divLoading"></div>

<?php
    if (isset($_POST['editUserDetail'])) {
        $uidModal = $_POST['uidModal'];
        $fname = $_POST['first_name'];
        $lname = $_POST['last_name'];
        $unamexs = $_POST['unamexs'];
        $emailid = $_POST['emailid'];
        $statusX = $_POST['statusX'];
        $statusXX = $_POST['statusXX'];
        $userTypeX = $_POST['userTypeX'];

        $query = "UPDATE
            mis_reports.users
        SET
            first_name = '$fname',
            last_name = '$lname',
            uname = '$unamexs',
            email = '$emailid',
            user_level = '$statusX',
            user_type = '$userTypeX',
            ustatus = '$statusXX'
        WHERE
            uid = '$uidModal'";

        if (mysqli_query($allConn, $query)) {
            echo "<script>
                swal({
                    title: 'User successfully updated!',
                    type: 'success',
                    button: 'OK!'
                },function(isConfirm){
                    alert('ok');
                });
                $('.swal2-confirm').click(function(){
                    window.location.href = 'admin.php';
                });
            </script>";
        }
    }

    if (isset($_POST['editReportDetail'])) {
        $ridModal = $_POST['ridModal'];
        $rname = $_POST['report_name'];
        $rurl = $_POST['report_url'];
        $rstatus = $_POST['statusXX'];
        $rcateg = $_POST['categXXX'];

        $query="UPDATE
            mis_reports.reports
        SET
            rname = '$rname',
            location = '$rurl',
            rstatus = '$rstatus',
            catid = '$rcateg'
        WHERE
            rid = '$ridModal'";
        
        if (mysqli_query($allConn, $query)) {
            echo "<script>
                swal({
                    title: 'Report successfully updated!',
                    type: 'success',
                    button: 'OK!'
                },function(isConfirm){
                    alert('ok');
                });
                $('.swal2-confirm').click(function(){
                    window.location.href = 'admin.php';
                });
            </script>";
        }
    }

?>

<!--Change Password Modal-->
<div class="modal fade" id="change_password_modal" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form action="login.php" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <center><h4 class="modal-title"><i class="fa fa-fw fa-key"></i> Change Password!</h4></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <input type="hidden" name="userid" value="<?php echo $user; ?>">
                            <input type="password" name="newpassword" id="newpassword" class="form-control brdr" placeholder="New Password" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <input type="password" name="confirmpassword" id="confirmpassword" class="form-control brdr" placeholder="Confirm Password" autocomplete="off" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="reportChangePasswordButton" class="btn solid-button"><i class="fa fa-pencil"></i> Change</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="wrapper">
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle pull-right" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="index.php" class="navbar-brand"><i class="fa fa-fw fa-file-text"></i> vTech Reports</a>
        </div>
        <!--Top Menu-->
        <ul class="nav navbar-right top-nav">
            <?php
                if (isset($cacheRedisReportDetail[$redisReportPath["user_name"]]) && $cacheRedisReportDetail[$redisReportPath["user_name"]] != NULL) {
                    $loggedInUsername = json_decode($cacheRedisReportDetail[$redisReportPath["user_name"]], "ARRAY");
                    $loggedInFirstName = json_decode($cacheRedisReportDetail[$redisReportPath["first_name"]], "ARRAY");
                    $loggedInLastName = json_decode($cacheRedisReportDetail[$redisReportPath["last_name"]], "ARRAY");
                    $loggedInEmail = json_decode($cacheRedisReportDetail[$redisReportPath["email"]], "ARRAY");
                } else {
                    $query = mysqli_query($allConn, "SELECT
                        CONCAT(u.first_name,' ',u.last_name) AS name,
                        u.first_name,
                        u.last_name,
                        u.email
                    FROM
                        mis_reports.users AS u
                    WHERE
                        u.uid = '$user'");
                    if (mysqli_num_rows($query) > 0) {
                        $result = mysqli_fetch_array($query);
                        $loggedInUsername = $result["name"];
                        $loggedInFirstName = $result["first_name"];
                        $loggedInLastName = $result["last_name"];
                        $loggedInEmail = $result["email"];

                        Redis::set($redisReportPath["user_name"], $loggedInUsername);
                        Redis::set($redisReportPath["first_name"], $loggedInFirstName);
                        Redis::set($redisReportPath["last_name"], $loggedInLastName);
                        Redis::set($redisReportPath["email"], $loggedInEmail);
                    }
                }
            ?>
            <li><a href="helpdesk.php"><i class="fa fa-fw fa-rss-square"></i> HelpDesk</a></li>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-fw fa-user"></i> <?php echo $loggedInUsername; ?> <b class="fa fa-fw fa-angle-down"></b></a>
                <ul class="dropdown-menu">
                    <!-- <li><a class="helpdesk-link"><i class="fa fa-fw fa-rss-square"></i> HelpDesk</a></li>
                    <hr> -->
                    <li><a data-toggle="modal" data-target="#change_password_modal"><i class="fa fa-fw fa-key"></i> Change Password</a></li>
                    <hr>
                    <li><a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
        <!--Sidebar-->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav side-nav">
                <li>
                    <a class="theme-color dashboard-link" style="color: #fff;cursor: pointer;"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                </li>
                <li>
                    <a class="add-users-link" style="color: #fff;cursor: pointer;"><i class="fa fa-fw fa-user-plus"></i> Add Users</a>
                </li>
                <li>
                    <a class="all-users-link" style="color: #fff;cursor: pointer;"><i class="fa fa-fw fa-user"></i> All Users</a>
                </li>
                <li>
                    <a class="add-reports-link" style="color: #fff;cursor: pointer;"><i class="fa fa-fw fa-plus-square"></i> Add Reports</a>
                </li>
                <li>
                    <a class="all-reports-link" style="color: #fff;cursor: pointer;"><i class="fa fa-fw fa-file-text"></i> All Reports</a>
                </li>
                <li>
                    <a class="give-report-access-link" style="color: #fff;cursor: pointer;"><i class="fa fa-fw fa-shield"></i> Report Access</a>
                </li>
                <li>
                    <a class="view-user-access-link" style="color: #fff;cursor: pointer;"><i class="fa fa-fw fa-eye"></i> View User Access</a>
                </li>
                <li>
                    <a class="view-report-access-link" style="color: #fff;cursor: pointer;"><i class="fa fa-fw fa-eye"></i> View Report Access</a>
                </li>
            </ul>
        </div>
    </nav>

    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="dashboard-section">
                <div class="row">
                    <div class="col-md-5" style="margin-bottom: 30px;">
                        <select id="searchCategory">
                            <?php
                                $allOptionsArray = $givenOptionsArray = array();
                                
                                if (isset($cacheRedisReportDetail[$redisReportPath["report_category_list"]]) && $cacheRedisReportDetail[$redisReportPath["report_category_list"]] != NULL) {
                                    $categoryArray = json_decode($cacheRedisReportDetail[$redisReportPath["report_category_list"]], "ARRAY");

                                    $allOptionsArray = $categoryArray["all_options"];
                                    $givenOptionsArray = $categoryArray["given_options"];
                                } else {
                                    $query = mysqli_query($allConn, "SELECT
                                        c.catid,
                                        c.catname
                                    FROM
                                        mis_reports.category AS c
                                        JOIN mis_reports.mapping AS m ON m.catid = c.catid
                                    WHERE
                                        m.uid = '$user'
                                    GROUP BY c.catid");
                                    
                                    while ($row = mysqli_fetch_array($query)) {
                                        $allOptionsArray[] = $row['catid'];
                                        $givenOptionsArray[$row['catid']] = $row['catname'];
                                    }

                                    $categoryArray = array(
                                        "all_options" => $allOptionsArray,
                                        "given_options" => $givenOptionsArray
                                    );

                                    Redis::set($redisReportPath["report_category_list"], $categoryArray);
                                }
                                
                                $allOptions = implode(",", $allOptionsArray);
                                
                                echo "<option value='".$allOptions."'>Select All</option>";
                                
                                foreach ($givenOptionsArray as $givenOptionsKey => $givenOptionsValue) {
                                    echo "<option value='".$givenOptionsKey."'>".$givenOptionsValue."</option>";;
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-5 col-md-offset-2" style="margin-bottom: 15px;">
                        <div class="form-group has-feedback">
                            <input type="text" id="search_field" class="form-control brdr" placeholder="Search Report..." onkeydown="onkeypressed(event, this);" required>
                            <span class="glyphicon glyphicon-search form-control-feedback" style="color: #777;"></span>
                        </div>
                    </div>
                </div>
                <div id="responseReports" class="row">
                <?php
                    $max = 4;
                    $number = 0;
            
                    if (isset($cacheRedisReportDetail[$redisReportPath["report_list"]]) && $cacheRedisReportDetail[$redisReportPath["report_list"]] != NULL) {
                        $reportList = json_decode($cacheRedisReportDetail[$redisReportPath["report_list"]], "ARRAY");
                    } else {
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
                        ORDER BY r.rname ASC");
                    
                        while ($row = mysqli_fetch_array($query)) {
                            $reportList[] = array(
                                "report_name" => $row["report_name"],
                                "report_location" => $row["report_location"]
                            );
                        }

                        Redis::set($redisReportPath["report_list"], $reportList);
                    }

                    if (count($reportList) > 0) {
                        foreach ($reportList as $reportListKey => $reportListValue) {
                            if ($number == $max) {
                                $number = 0;
                            }
                            if ($number == 0) {
                    ?>
                            <div class="col-md-4 connect_cat">
                                <div class="panel">
                                    <div class="panel-body div11 name_cat"><?php echo strlen($reportListValue['report_name']) > 40 ? substr($reportListValue['report_name'],0,40)."..." : $reportListValue['report_name']; ?></div>
                                    <a href="<?php echo REPORT_PATH.''.$reportListValue['report_location']; ?>" target="_blank"><div class="panel-footer div12">View <i class="fa fa-arrow-circle-right"></i></div></a>
                                </div>
                            </div>
                    <?php
                            }
                            if ($number == 1) {
                    ?>
                            <div class="col-md-4 connect_cat">
                                <div class="panel">
                                    <div class="panel-body div21 name_cat"><?php echo strlen($reportListValue['report_name']) > 40 ? substr($reportListValue['report_name'],0,40)."..." : $reportListValue['report_name']; ?></div>
                                    <a href="<?php echo REPORT_PATH.''.$reportListValue['report_location']; ?>" target="_blank"><div class="panel-footer div22">View <i class="fa fa-arrow-circle-right"></i></div></a>
                                </div>
                            </div>
                    <?php
                            }
                            if ($number == 2) {
                    ?>
                            <div class="col-md-4 connect_cat">
                                <div class="panel">
                                    <div class="panel-body div31 name_cat"><?php echo strlen($reportListValue['report_name']) > 40 ? substr($reportListValue['report_name'],0,40)."..." : $reportListValue['report_name']; ?></div>
                                    <a href="<?php echo REPORT_PATH.''.$reportListValue['report_location']; ?>" target="_blank"><div class="panel-footer div32">View <i class="fa fa-arrow-circle-right"></i></div></a>
                                </div>
                            </div>
                    <?php
                            }
                            if ($number == 3) {
                    ?>
                            <div class="col-md-4 connect_cat">
                                <div class="panel">
                                    <div class="panel-body div41 name_cat"><?php echo strlen($reportListValue['report_name']) > 40 ? substr($reportListValue['report_name'],0,40)."..." : $reportListValue['report_name']; ?></div>
                                    <a href="<?php echo REPORT_PATH.''.$reportListValue['report_location']; ?>" target="_blank"><div class="panel-footer div42">View <i class="fa fa-arrow-circle-right"></i></div></a>
                                </div>
                            </div>
                    <?php
                            }
                            $number++;
                        }
                    } else {
                ?>
                    <div class="col-md-8 col-md-offset-2 notfondimage">
                        <img src="<?php echo IMAGE_PATH; ?>/found.jpg" class="center-block img-responsive">
                    </div>
                <?php
                    }
                ?>
                </div>
            </div><!-- 
            <div class="helpdesk-section hidden">
                <div class="row">
                    <div class="col-md-12">
                    <?php
                        /*$curl = curl_init();
                        curl_setopt_array($curl, array(
                        CURLOPT_URL => SAAS_IP_URL."/api/v1/ticket-service/user-credentials?client_email=".$loggedInEmail."&client_firstname=".$loggedInFirstName."&client_lastname=".$loggedInLastName,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);
                        
                        $userData = json_decode($response,"ARRAY");
                        
                        if (!isset($userData['email']) && !isset($userData['password'])) {
                            echo "<center><div><p class='error'>Please contact application team and get your issue resolved.</p></div></center>";
                            return;
                        }

                        $username = $userData['email'];
                        $password = $userData['password'];
                        $baseString = "userName::".$username.":::password::".$password;
                        $enc = base64_encode($baseString);*/
                    ?>
                        <style>
                            .error {
                                font-size: 25px;
                                margin-top: 180px;
                                color: red;
                            }
                            .no-password {
                                font-size: 20px;
                                color: red;
                                font-weight: bold;
                            }
                        </style>

                        <iframe src="https://support.vtechsolution.com/helpdesk/WebObjects/Helpdesk.woa/?vT=<?php //echo $enc ?>" style="height:650px;width:100%;border:2px solid white" id="iframe_tab"></iframe>
                        
                        <script>
                            timer = setTimeout(function(){
                                $('.no-password').hide();
                            }, 5000);
                        </script>
                    </div>
                </div>
            </div> -->
            <div class="add-users-section hidden">
                <div class="row">
                    <div class="col-md-12">
                        <p class="title-style"><i class="fa fa-fw fa-user-plus"></i> Add Users</p>
                    </div>
                </div>
                <form id="addUsersForm" style="padding: 0px 40px 0px 20px;">
                    <div class="row form-group">
                        <div class="col-md-3">
                            <input type="text" name="firstName" class="form-control brdr" placeholder="First Name" autocomplete="off" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="lastName" class="form-control brdr" placeholder="Last Name" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <input type="text" name="userName" class="form-control brdr" placeholder="User Name" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <input type="email" name="emailAddress" class="form-control brdr" placeholder="Email Address" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <select name="userLevel" class="form-control brdr" required>
                                <option value="">--- Select User Level ---</option>
                                <option value="Admin">Admin</option>
                                <option value="User">User</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <select name="userType" class="form-control brdr" required>
                                <option value="">--- Select User Type ---</option>
                                <?php
                                    $query = mysqli_query($allConn, "SELECT
                                        ut.name
                                    FROM
                                        mis_reports.user_types AS ut");

                                    while ($row = mysqli_fetch_array($query)) {
                                        echo "<option value='".$row["name"]."'>".$row["name"]."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group" style="margin-top: 30px;">
                        <div class="col-md-3">
                            <button type="submit" class="form-control btn-login">Create</button>
                        </div>
                        <div class="col-md-3">
                            <button type="reset" class="form-control btn-normal">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="all-users-section row hidden" style="margin-bottom: 50px;">
                <div class="col-md-12">
                    <p class="title-style"><i class="fa fa-fw fa-user"></i> All Users</p>
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered table-striped customized-datatable">
                        <thead>
                            <tr style="background-color: #337AB7;color: #fff;">
                                <th style="text-align: center;vertical-align: middle;">ID</th>
                                <th style="text-align: center;vertical-align: middle;">First Name</th>
                                <th style="text-align: center;vertical-align: middle;">Last Name</th>
                                <th style="text-align: center;vertical-align: middle;">User Name</th>
                                <th style="text-align: center;vertical-align: middle;">Email</th>
                                <th style="text-align: center;vertical-align: middle;">Added By</th>
                                <th style="text-align: center;vertical-align: middle;">User Level</th>
                                <th style="text-align: center;vertical-align: middle;">User Type</th>
                                <th style="text-align: center;vertical-align: middle;">Status</th>
                                <th class="no-sort" style="text-align: center;vertical-align: middle;">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $query = mysqli_query($allConn, "SELECT
                                    u.*,
                                    u2.uname AS addedby_username
                                FROM
                                    mis_reports.users AS u
                                    LEFT JOIN mis_reports.users AS u2 ON u2.uid = u.addedby
                                ORDER BY uid ASC"); 
                                
                                while ($rowX = mysqli_fetch_array($query)) {
                                    $my_string = array();
                            ?>
                            <tr>
                                <td style="vertical-align: middle;"><?php echo $rowX['uid']; ?></td>
                                <td style="vertical-align: middle;"><?php echo $rowX['first_name']; ?></td>
                                <td style="vertical-align: middle;"><?php echo $rowX['last_name']; ?></td>
                                <td style="vertical-align: middle;"><?php echo $rowX['uname']; ?></td>
                                <td style="vertical-align: middle;"><?php echo $rowX['email']; ?></td>
                                <td style="vertical-align: middle;"><?php echo $rowX['addedby_username']; ?></td>
                                <td style="vertical-align: middle;"><?php echo $rowX['user_level']; ?></td>
                                <td style="vertical-align: middle;"><?php echo $rowX['user_type']; ?></td>
                                <td style="vertical-align: middle;">
                                <?php
                                    if ($rowX['ustatus'] == "1") {
                                        echo "Active";
                                    } elseif ($rowX['ustatus'] == "0") {
                                        echo "Inactive";
                                    }
                                ?>
                                </td>
                                <td style="text-align: center;vertical-align: middle;">
                                    <a data-toggle="modal" data-target="#editUser_modal<?php echo $rowX['uid']; ?>" style="cursor: pointer;text-decoration: none;outline: none;"><i class="fa fa-lg fa-edit"></i></a>
                                </td>
                            </tr>
                            <!--editUser_modal-->
                            <div class="modal fade" id="editUser_modal<?php echo $rowX['uid']; ?>" role="dialog">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <form action="admin.php" method="POST">
                                            <div class="modal-header" style="background-color: #2266AA;color: #fff;border-bottom: 5px solid #aaa;">
                                                <button type="button" class="close" data-dismiss="modal" style="color: #fff;">&times;</button>
                                                <center><h4 class="modal-title">Edit User Details</h4></center>
                                            </div>
                                            <div class="modal-body" style="padding: 25px 0px 50px 0px;">
                                                <div class="row">
                                                    <div class="col-sm-5 col-sm-offset-1">
                                                        <label>First Name: </label>
                                                        <input type="hidden" name="uidModal" value="<?php echo $rowX['uid']; ?>">
                                                        <input type="text" name="first_name" class="form-control brdr" value="<?php echo $rowX['first_name']; ?>" autocomplete="off" onchange="validateFname(this);" required>
                                                        <span id="fname_status" style="color: red;"></span>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <label>Last Name: </label>
                                                        <input type="text" name="last_name" class="form-control brdr" value="<?php echo $rowX['last_name']; ?>" autocomplete="off" onchange="validateLname(this);" required>
                                                        <span id="lname_status" style="color: red;"></span>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 15px;">
                                                    <div class="col-sm-10 col-sm-offset-1">
                                                        <label>User Name: </label>
                                                        <input type="text" name="unamexs" class="form-control brdr" value="<?php echo $rowX['uname']; ?>" autocomplete="off" readonly>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 15px;">
                                                    <div class="col-sm-10 col-sm-offset-1">
                                                        <label>Email: </label>
                                                        <input type="text" name="emailid" class="form-control brdr" value="<?php echo $rowX['email']; ?>" autocomplete="off" onchange="validateEmail(this);" readonly>
                                                        <span id="email_status" style="color: red;"></span>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 15px;">
                                                    <div class="col-sm-3 col-sm-offset-1">
                                                        <label>User Level: </label>
                                                        <select name="statusX" class="form-control brdr" required>
                                                        <?php
                                                            if($rowX['user_level'] == "Admin"){
                                                        ?>
                                                            <option value="Admin" selected>Admin</option>
                                                            <option value="User">User</option>
                                                        <?php }
                                                            if($rowX['user_level'] == "User"){
                                                        ?>
                                                            <option value="Admin">Admin</option>
                                                            <option value="User" selected>User</option>
                                                        <?php
                                                            }
                                                        ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label>User Type: </label>
                                                        <select name="userTypeX" class="form-control brdr" required>
                                                            <option value="">Select Option</option>
                                                            <?php
                                                                $userTypeQUERY = mysqli_query($allConn, "SELECT
                                                                    ut.name
                                                                FROM
                                                                    mis_reports.user_types AS ut");
                                                                
                                                                while ($userTypeROW = mysqli_fetch_array($userTypeQUERY)) {
                                                                    if ($rowX['user_type'] == $userTypeROW['name']) {
                                                                        $isSelected = ' selected';
                                                                    } else {
                                                                        $isSelected = '';
                                                                    }
                                                                    echo "<option value='".$userTypeROW['name']."'".$isSelected.">".$userTypeROW['name']."</option>";
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label>Status: </label>
                                                        <select name="statusXX" class="form-control brdr" required>
                                                        <?php
                                                            if ($rowX['ustatus'] == "1") {
                                                        ?>
                                                            <option value="1" selected>Active</option>
                                                            <option value="0">Inactive</option>
                                                        <?php }
                                                            if ($rowX['ustatus'] == "0") {
                                                        ?>
                                                            <option value="1">Active</option>
                                                            <option value="0" selected>Inactive</option>
                                                        <?php
                                                            }
                                                        ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="editUserDetail" class="btn solid-button"><i class="fa fa-pencil"></i> Change</button>
                                                <button type="button" class="btn btn-default" data-dismiss="modal" style="background-color: #fff;color: #2266AA;border: 1px solid #2266AA;border-radius: 0px;"><i class="fa fa-close"></i> Close</button>
                                            </div>
                                         </form>   
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="add-reports-section hidden">
                <div class="row">
                    <div class="col-md-12">
                        <p class="title-style"><i class="fa fa-fw fa-plus-square"></i> Add Reports</p>
                    </div>
                </div>
                <form id="addReportsForm">
                    <div class="row form-group">
                        <div class="col-md-6">
                            <input type="text" name="reportName" class="form-control brdr" placeholder="Enter Report Name" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <textarea name="reportURL" class="form-control brdr" placeholder="Enter URL..." rows="3" autocomplete="off" required></textarea>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <select name="reportCategory" class="form-control brdr" required>
                                <option value="">--- Select Category ---</option>
                                <?php
                                    $query = mysqli_query($allConn, "SELECT
                                        c.catid,
                                        c.catname
                                    FROM
                                        mis_reports.category AS c
                                    ORDER BY c.catid ASC");

                                    while ($row = mysqli_fetch_array($query)) {
                                        echo "<option value='".$row["catid"]."'>".$row["catname"]."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <select name="reportStatus" class="form-control brdr" required>
                                <option value="">--- Select Status ---</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group" style="margin-top: 30px;">
                        <div class="col-md-3">
                            <button type="submit" class="form-control btn-login">submit</button>
                        </div>
                        <div class="col-md-3">
                            <button type="reset" class="form-control btn-normal">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="all-reports-section row hidden" style="margin-bottom: 50px;">
                <div class="col-md-12">
                    <p class="title-style"><i class="fa fa-fw fa-file-text"></i> All Reports</p>
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered table-striped customized-datatable">
                        <thead>
                            <tr style="background-color: #337AB7;color: #fff;">
                                <th style="text-align: center;vertical-align: middle;">ID</th>
                                <th style="text-align: center;vertical-align: middle;">Report Name</th>
                                <th style="text-align: center;vertical-align: middle;">Location (URL)</th>
                                <th style="text-align: center;vertical-align: middle;">Added By</th>
                                <th style="text-align: center;vertical-align: middle;">Category</th>
                                <th style="text-align: center;vertical-align: middle;">Status</th>
                                <th class="no-sort" style="text-align: center;vertical-align: middle;">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $queryXX="SELECT * FROM mis_reports.reports ORDER BY rid ASC";
                                $resultXX=mysqli_query($allConn,$queryXX); 
                                while($rowXX=mysqli_fetch_array($resultXX)){
                                    $my_stringX=array();
                            ?>
                            <tr>
                                <td style="vertical-align: middle;"><?php echo $rowXX['rid']; ?></td>
                                <td style="vertical-align: middle;"><?php echo $rowXX['rname']; ?></td>
                                <td style="vertical-align: middle;"><div style="word-break:break-word;"><?php echo $rowXX['location']; ?></div></td>
                                <td style="vertical-align: middle;">
                                    <?php
                                        $uidXX2 = $rowXX['addedby'];
                                        $resultXX2 = mysqli_query($allConn, "SELECT
                                            u.first_name
                                        FROM
                                            mis_reports.users AS u
                                        WHERE
                                            u.uid = '$uidXX2'");
                                        $rowXX2 = mysqli_fetch_array($resultXX2);
                                        echo $rowXX2['first_name'];
                                    ?>
                                </td>
                                <td style="vertical-align: middle;">
                                    <?php
                                        $cidX = $rowXX['catid'];
                                        
                                        $resXX = mysqli_query($allConn, "SELECT
                                            c.catname
                                        FROM
                                            mis_reports.category AS c
                                        WHERE
                                            c.catid = '$cidX'");

                                        if (mysqli_num_rows($resXX)) {
                                            while ($rwXX = mysqli_fetch_array($resXX)) {
                                                echo $rwXX['catname'];
                                            }
                                        }
                                    ?>
                                </td>
                                <td style="vertical-align: middle;">
                                <?php
                                    if ($rowXX['rstatus'] == "1") {
                                        echo "Active";
                                    } elseif ($rowXX['rstatus'] == "0") {
                                        echo "Inactive";
                                    }
                                ?>
                                </td>
                                <td style="text-align: center;vertical-align: middle;">
                                    <a data-toggle="modal" data-target="#editReport_modal<?php echo $rowXX['rid']; ?>" style="cursor: pointer;text-decoration: none;outline: none;"><i class="fa fa-lg fa-edit"></i></a>
                                </td>
                            </tr>
                            <!--editReport_modal-->
                            <div class="modal fade" id="editReport_modal<?php echo $rowXX['rid']; ?>" role="dialog">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <form action="admin.php" method="POST">
                                            <div class="modal-header" style="background-color: #2266AA;color: #fff;border-bottom: 5px solid #aaa;">
                                                <button type="button" class="close" data-dismiss="modal" style="color: #fff;">&times;</button>
                                                <center><h4 class="modal-title">Edit Report Details</h4></center>
                                            </div>
                                            <div class="modal-body" style="padding: 25px 0px 50px 0px;">
                                                <div class="row">
                                                    <div class="col-sm-10 col-sm-offset-1">
                                                        <label>Report Name: </label>
                                                        <input type="hidden" name="ridModal" value="<?php echo $rowXX['rid']; ?>">
                                                        <input type="text" name="report_name" class="form-control brdr" value="<?php echo $rowXX['rname']; ?>" autocomplete="off" required>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 15px;">
                                                    <div class="col-sm-10 col-sm-offset-1">
                                                        <label>Location (URL): </label>
                                                        <textarea name="report_url" class="form-control brdr" rows="3" autocomplete="off" required><?php echo $rowXX['location']; ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 15px;">
                                                    <div class="col-sm-5 col-sm-offset-1">
                                                        <label>Category: </label>
                                                        <select name="categXXX" class="form-control brdr" required>
                                                            <?php
                                                                $resX = mysqli_query($allConn, "SELECT
                                                                    c.catid,
                                                                    c.catname
                                                                FROM
                                                                    mis_reports.category AS c
                                                                ORDER BY c.catid ASC");

                                                                while ($rowX = mysqli_fetch_array($resX)) {
                                                                    if ($rowXX['catid'] == $rowX['catid']) {
                                                                        echo "<option value='".$rowX['catid']."' selected>".$rowX['catname']."</option>";
                                                                    }else{
                                                                        echo "<option value='".$rowX['catid']."'>".$rowX['catname']."</option>";
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <label>Status: </label>
                                                        <select name="statusXX" class="form-control brdr" required>
                                                        <?php
                                                            if ($rowXX['rstatus'] == "1") {
                                                        ?>
                                                            <option value="1" selected>Active</option>
                                                            <option value="0">Inactive</option>
                                                        <?php }
                                                            if ($rowXX['rstatus'] == "0") {
                                                        ?>
                                                            <option value="1">Active</option>
                                                            <option value="0" selected>Inactive</option>
                                                        <?php
                                                            }
                                                        ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="editReportDetail" class="btn solid-button"><i class="fa fa-pencil"></i> Change</button>
                                                <button type="button" class="btn btn-default" data-dismiss="modal" style="background-color: #fff;color: #2266AA;border: 1px solid #2266AA;border-radius: 0px;"><i class="fa fa-close"></i> Close</button>
                                            </div>
                                         </form>   
                                    </div>
                                </div>
                            </div>
                            <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="give-report-access-section hidden">
                <div class="row">
                    <div class="col-md-12">
                        <p class="title-style"><i class="fa fa-fw fa-shield"></i> Report Access Level</p>
                    </div>
                </div>
                <form id="reportAccessForm">
                    <div class="row">
                        <div class="col-md-5">
                            <label>Select User :</label>
                            <select id="selectUsers" name="selectUsers" required>
                                <option value="">Select Users</option>
                                <?php
                                    $query = mysqli_query($allConn, "SELECT
                                        u.uid AS user_id,
                                        CONCAT(u.first_name,' ',u.last_name) AS user_name
                                    FROM
                                        mis_reports.users AS u
                                    WHERE
                                        u.ustatus != '0'
                                    GROUP BY user_id
                                    ORDER BY user_name ASC");

                                    while ($row = mysqli_fetch_array($query)) {
                                        echo "<option value='".$row["user_id"]."'>".$row["user_name"]."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 25px;">
                        <div class="col-md-5">
                            <label>Select Category :</label>
                            <select id="searchCategory2" name="searchCategory2" required>
                            </select>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 25px;">
                        <div class="col-md-5">
                            <label>Select Reports :</label>
                            <select id="selectReports" name="selectReports[]" multiple>
                            </select>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 35px;">
                        <div class="col-md-5">
                            <button type="submit" name="RAMsubmit" class="btn form-control solid-button"><i class="fa fa-shield"></i> Allow</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="view-user-access-section hidden">
                <div class="row">
                    <div class="col-md-12">
                        <p class="title-style"><i class="fa fa-fw fa-eye"></i> View User Access</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label>Select User :</label>
                        <select id="selectUsers2" data-type="user">
                            <option value="">Select User</option>
                            <?php
                                $query = mysqli_query($allConn, "SELECT
                                    u.uid AS user_id,
                                    CONCAT(u.first_name,' ',u.last_name) AS user_name
                                FROM
                                    mis_reports.users AS u
                                WHERE
                                    u.ustatus != '0'
                                GROUP BY user_id
                                ORDER BY user_name ASC");
                                
                                while ($row = mysqli_fetch_array($query)) {
                                    echo "<option value='".$row["user_id"]."''>".$row["user_name"]."</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div id="view-user-access-div"></div>
            </div>
            <div class="view-report-access-section hidden">
                <div class="row">
                    <div class="col-md-12">
                        <p class="title-style"><i class="fa fa-fw fa-eye"></i> View Report Access</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label>Select Report :</label>
                        <select id="selectReport2" data-type="report">
                            <option value="">Select Report</option>
                            <?php
                                $query = mysqli_query($allConn, "SELECT
                                    r.rid AS report_id,
                                    r.rname AS report_name
                                FROM
                                    mis_reports.reports AS r
                                WHERE
                                    r.rstatus = '1'
                                GROUP BY report_id
                                ORDER BY report_name ASC");
                                
                                while ($row = mysqli_fetch_array($query)) {
                                    echo "<option value='".$row["report_id"]."''>".$row["report_name"]."</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div id="view-report-access-div"></div>
            </div>
        </div>
    </div>
</div>

<div class="footer">&copy; vTech Solution, Inc. All rights reserved.</div>

<script>
    /*$(document).on("click", ".helpdesk-link", function(e){
        e.preventDefault();
        $(".dashboard-link, .add-users-link, .all-users-link, .add-reports-link, .all-reports-link, .give-report-access-link, .view-user-access-link, .view-report-access-link").removeClass("theme-color");
        $(".helpdesk-section").removeClass("hidden");
        $(".dashboard-section, .add-users-section, .all-users-section, .add-reports-section, .all-reports-section, .give-report-access-section, .view-user-access-section, .view-report-access-section").addClass("hidden");
    });*/

    $(document).on("click", ".dashboard-link", function(e){
        e.preventDefault();
        $(".dashboard-link").addClass("theme-color");
        $(".add-users-link, .all-users-link, .add-reports-link, .all-reports-link, .give-report-access-link, .view-user-access-link, .view-report-access-link").removeClass("theme-color");
        $(".dashboard-section").removeClass("hidden");
        $(".add-users-section, .all-users-section, .add-reports-section, .all-reports-section, .give-report-access-section, .view-user-access-section, .view-report-access-section").addClass("hidden");
    });

    $(document).on("click", ".add-users-link", function(e){
        e.preventDefault();
        $(".add-users-link").addClass("theme-color");
        $(".dashboard-link, .all-users-link, .add-reports-link, .all-reports-link, .give-report-access-link, .view-user-access-link, .view-report-access-link").removeClass("theme-color");
        $(".add-users-section").removeClass("hidden");
        $(".dashboard-section, .all-users-section, .add-reports-section, .all-reports-section, .give-report-access-section, .view-user-access-section, .view-report-access-section").addClass("hidden");
    });
    
    $(document).on("click", ".all-users-link", function(e){
        e.preventDefault();
        $(".all-users-link").addClass("theme-color");
        $(".dashboard-link, .add-users-link, .add-reports-link, .all-reports-link, .give-report-access-link, .view-user-access-link, .view-report-access-link").removeClass("theme-color");
        $(".all-users-section").removeClass("hidden");
        $(".dashboard-section, .add-users-section, .add-reports-section, .all-reports-section, .give-report-access-section, .view-user-access-section, .view-report-access-section").addClass("hidden");
    });
    
    $(document).on("click", ".add-reports-link", function(e){
        e.preventDefault();
        $(".add-reports-link").addClass("theme-color");
        $(".dashboard-link, .add-users-link, .all-users-link, .all-reports-link, .give-report-access-link, .view-user-access-link, .view-report-access-link").removeClass("theme-color");
        $(".add-reports-section").removeClass("hidden");
        $(".dashboard-section, .add-users-section, .all-users-section, .all-reports-section, .give-report-access-section, .view-user-access-section, .view-report-access-section").addClass("hidden");
    });

    $(document).on("click", ".all-reports-link", function(e){
        e.preventDefault();
        $(".all-reports-link").addClass("theme-color");
        $(".dashboard-link, .add-users-link, .all-users-link, .add-reports-link, .give-report-access-link, .view-user-access-link, .view-report-access-link").removeClass("theme-color");
        $(".all-reports-section").removeClass("hidden");
        $(".dashboard-section, .add-users-section, .all-users-section, .add-reports-section, .give-report-access-section, .view-user-access-section, .view-report-access-section").addClass("hidden");
    });

    $(document).on("click", ".give-report-access-link", function(e){
        e.preventDefault();
        $(".give-report-access-link").addClass("theme-color");
        $(".dashboard-link, .add-users-link, .all-users-link, .add-reports-link, .all-reports-link, .view-user-access-link, .view-report-access-link").removeClass("theme-color");
        $(".give-report-access-section").removeClass("hidden");
        $(".dashboard-section, .add-users-section, .all-users-section, .add-reports-section, .all-reports-section, .view-user-access-section, .view-report-access-section").addClass("hidden");
    });

    $(document).on("click", ".view-user-access-link", function(e){
        e.preventDefault();
        $(".view-user-access-link").addClass("theme-color");
        $(".dashboard-link, .add-users-link, .all-users-link, .add-reports-link, .all-reports-link, .give-report-access-link, .view-report-access-link").removeClass("theme-color");
        $(".view-user-access-section").removeClass("hidden");
        $(".dashboard-section, .add-users-section, .all-users-section, .add-reports-section, .all-reports-section, .give-report-access-section, .view-report-access-section").addClass("hidden");
    });

    $(document).on("click", ".view-report-access-link", function(e){
        e.preventDefault();
        $(".view-report-access-link").addClass("theme-color");
        $(".dashboard-link, .add-users-link, .all-users-link, .add-reports-link, .all-reports-link, .give-report-access-link, .view-user-access-link").removeClass("theme-color");
        $(".view-report-access-section").removeClass("hidden");
        $(".dashboard-section, .add-users-section, .all-users-section, .add-reports-section, .all-reports-section, .give-report-access-section, .view-user-access-section").addClass("hidden");
    });

    // Seach Home ICONS
    $("#search_field").on("keyup", function(){
        var matcher = new RegExp($(this).val(), "i");
        $(".connect_cat").show().not(function(){
            return matcher.test($(this).find(".name_cat").text());
        }).hide();
    });

    // Erase Search Field
    function onkeypressed(evt,input){
        var code = evt.charCode || evt.keyCode;
        if (code == 27) {
            input.value = "";
        }
    }
</script>

</body>
</html>
<?php
        } elseif ($childUser == "User") {
            header("Location:user.php");
        } else {
            header("Location:logout.php");
		}
    } else {
        header("Location:logout.php");
    }
?>