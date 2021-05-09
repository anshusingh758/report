<?php
    include('security.php');
    include('config.php');
    include('./lib/redis.php');

    if (isset($user) && isset($userMember)) {
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
    <title>HelpDesk</title>

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
    </script>

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

</head>
<body>
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
        <li><a href="helpdesk.php"><i class="fa fa-fw fa-rss-square"></i> HelpDesk</a></li>
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-fw fa-user"></i> <?php echo $loggedInUsername; ?> <b class="fa fa-fw fa-angle-down"></b></a>
            <ul class="dropdown-menu">
                <li><a data-toggle="modal" data-target="#change_password_modal" style="cursor: pointer;"><i class="fa fa-fw fa-key"></i> Change Password</a></li>
                <hr style="border: 0.5px #ccc solid;margin: 5px 0px;">
                <li><a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>

<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
            <?php
                $curl = curl_init();
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
                $enc = base64_encode($baseString);
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

                <iframe src="https://support.vtechsolution.com/helpdesk/WebObjects/Helpdesk.woa/?vT=<?php echo $enc ?>" style="height:650px;width:100%;border:2px solid white" id="iframe_tab"></iframe>
                
                <script>
                    timer = setTimeout(function(){
                        $('.no-password').hide();
                    }, 5000);
                </script>
            </div>
        </div>
    </div>
</section>

<div class="footer">&copy; vTech Solution, Inc. All rights reserved.</div>

</body>
</html>
<?php
    } else {
        header("Location:index.php");
    }
?>
