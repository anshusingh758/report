<?php
    include("security.php");
    include("config.php");
    include('./lib/redis.php');
    if (isset($_SESSION['user'])) {
        $childUser = $_SESSION['userMember'];
        if ($childUser=='User') {
            $redisReportPath["user_name"] = 'report:user_id:'.$user.':user_name';
            $redisReportPath["report_category_list"] = 'report:user_id:'.$user.':report_category_list';
            $redisReportPath["report_list"] = 'report:user_id:'.$user.':report_list';

            $redisReportDeleteUserDataPath = 'report:user_id:'.$user.':user_name,report:user_id:'.$user.':report_category_list,report:user_id:'.$user.':report_list';

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
    </style>

    <script>
        $(document).ready(function(){
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

            // Password Metch Script
            var password = document.getElementById("newpassword");
            var confirm_password = document.getElementById("confirmpassword");

            function validatePassword(){
              if(password.value != confirm_password.value){
                confirm_password.setCustomValidity("Passwords Don't Match");
              }else{
                confirm_password.setCustomValidity('');
              }
            }

            password.onchange = validatePassword;
            confirm_password.onkeyup = validatePassword;
        });
        
        // Select Reports of Particular Category
        $(document).on("change", "#searchCategory", function(e){
            e.preventDefault();
            
            $.ajax({
                url:"searchCategReports.php",
                type:"POST",
                data:"categoryId="+$(this).val(),
                success:function(response){
                    $("#responseReports").html("");
                    $("#responseReports").html(response);
                }
            });
        });
    </script>

</head>
<body>

<!--Change Password Modal-->
<div class="modal fade" id="change_password_modal" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form action="login.php" method="POST">
                <div class="modal-header" style="background-color: #2266AA;color: #fff;border-bottom: 5px solid #aaa;">
                    <button type="button" class="close" data-dismiss="modal" style="color: #fff;">&times;</button>
                    <center><h4 class="modal-title"><i class="fa fa-fw fa-key"></i> Change Password!</h4></center>
                </div>
                <div class="modal-body" style="padding: 30px 1px;">
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <input type="hidden" name="userid" value="<?php echo $user; ?>">
                            <input type="password" name="newpassword" id="newpassword" class="form-control brdr" placeholder="New Password" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 15px;">
                        <div class="col-md-10 col-md-offset-1">
                            <input type="password" name="confirmpassword" id="confirmpassword" class="form-control brdr" placeholder="Confirm Password" autocomplete="off" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="reportChangePasswordButton" class="btn" style="background-color: #2266AA;color: #fff;border: 1px solid #2266AA;border-radius: 0px;"><i class="fa fa-pencil"></i> Change</button>
                </div>
            </form>
        </div>
    </div>
</div>

<nav class="navbar navbar-inverse navbar-fixed-top" style="background-color: #2266AA;border: none;color: #fff;">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle pull-right" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a href="index.php" class="navbar-brand" style="color: #fff;margin: -1px auto;font-size: 20px;"><i class="fa fa-fw fa-file-text"></i> vTech Reports</a>
    </div>
    <!--Top Menu-->
    <ul class="nav navbar-right top-nav">
        <?php
            if (isset($_SESSION['user'])) {
                if (isset($cacheRedisReportDetail[$redisReportPath["user_name"]]) && $cacheRedisReportDetail[$redisReportPath["user_name"]] != NULL) {
                    $result['name'] = json_decode($cacheRedisReportDetail[$redisReportPath["user_name"]], 'ARRAY');
                } else {
                    $query = mysqli_query($misReportsConn, "SELECT
                        CONCAT(u.first_name,' ',u.last_name) AS name
                    FROM
                        mis_reports.users AS u
                    WHERE
                        u.uid = '$user'");
                    if (mysqli_num_rows($query) > 0) {
                        $result = mysqli_fetch_array($query);
                        
                        Redis::set($redisReportPath["user_name"], $result['name']);
                    }
                }
            }
            if (isset($_SESSION['user'])) {
        ?>
        <li><a href="helpdesk.php"><i class="fa fa-fw fa-rss-square"></i> HelpDesk</a></li>
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" style="cursor: pointer;"><i class="fa fa-fw fa-user"></i> <?php echo $result['name']; ?> <b class="fa fa-fw fa-angle-down"></b></a>
            <ul class="dropdown-menu">
                <li><a href="lib/redis_delete.php?keys=<?php echo $redisReportDeleteUserDataPath; ?>"><i class="fa fa-fw fa-refresh"></i> Refresh Page</a></li>
                <hr style="border: 0.5px #ccc solid;margin: 5px 0px;">
                <li><a data-toggle="modal" data-target="#change_password_modal" style="cursor: pointer;"><i class="fa fa-fw fa-key"></i> Change Password</a></li>
                <hr style="border: 0.5px #ccc solid;margin: 5px 0px;">
                <li><a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Logout</a></li>
            </ul>
        </li>
        <?php
            }
        ?>
    </ul>
</nav>

<section style="margin-top: 75px;margin-bottom: 75px;">
    <div class="container">
        <div class="dashboard-section">
            <div class="row">
                <div class="col-md-4" style="margin-bottom: 30px;">
                    <select id="searchCategory">
                        <?php
                            $allOptionsArray = $givenOptionsArray = array();
                            
                            if (isset($cacheRedisReportDetail[$redisReportPath["report_category_list"]]) && $cacheRedisReportDetail[$redisReportPath["report_category_list"]] != NULL) {
                                $categoryArray = json_decode($cacheRedisReportDetail[$redisReportPath["report_category_list"]], 'ARRAY');

                                $allOptionsArray = $categoryArray["all_options"];
                                $givenOptionsArray = $categoryArray["given_options"];
                            } else {
                                $query = mysqli_query($misReportsConn, "SELECT
                                        c.catid,
                                        c.catname
                                    FROM
                                        category AS c
                                        JOIN mapping AS m ON m.catid = c.catid
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
                <div class="col-md-4 col-md-offset-4" style="margin-bottom: 15px;">
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
                    $reportList = json_decode($cacheRedisReportDetail[$redisReportPath["report_list"]], 'ARRAY');
                } else {
                    $query = mysqli_query($misReportsConn, "SELECT
                        r.rname AS report_name,
                        r.location AS report_location
                    FROM
                        reports AS r
                        JOIN mapping AS m ON m.rid = r.rid
                        JOIN users AS u ON u.uid = m.uid
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
        </div>
    </div>
</section>

<div class="footer">&copy; vTech Solution, Inc. All rights reserved.</div>

<script>
    // Seach Home ICONS
    $("#search_field").on("keyup", function(){
        var matcher = new RegExp($(this).val(), "i");
        $(".connect_cat").show().not(function(){
            return matcher.test($(this).find(".name_cat").text())
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
        } elseif ($childUser == "Admin") {
            header("Location:admin.php");
        } else {
			header("Location:logout.php");
		}
    } else {
        header("Location:logout.php");
    }
?>