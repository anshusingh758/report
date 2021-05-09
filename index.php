<?php
    include("security.php");
    include("config.php");
    if (!isset($_SESSION["user"])) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>Login to vTech Reports</title>

    <?php
        include("cdn.php");
    ?>

    <!--CSS + JS-->
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH;?>/style.css">
    <script src="<?php echo JS_PATH;?>/main.js"></script>

    <style>
        body {
            background-color: #2266AA;
        }
        section.reportLoginSection {
            margin-top: 100px;
        }
        .reportLoginPanel .panel-heading,
        .reportForgotPasswordPanel .panel-heading {
            border: none;
            padding-top: 20px;
            background-color: #fff;
        }
        .reportLoginPanel .panel-heading a,
        .reportForgotPasswordPanel .panel-heading a {
            outline: none;
        }
        .reportLoginPanel .panel-heading p,
        .reportForgotPasswordPanel .panel-heading p {
            text-align: center;
            background-color: #999;
            margin-top: 22px;
            margin-bottom: 0px;
            padding: 7px 0px 4px 0px;
            font-family: 'Fjalla One', sans-serif;
            font-size: 20px;
            color: #000;
            letter-spacing: 1px;
        }
        .reportLoginPanelBody,
        .reportForgotPasswordPanel .panel-body {
            padding: 15px 25px 15px 25px;
        }
        .forgotPasswordLink {
            font-size: 15px;
            cursor: pointer;
        }
        .top-margin-div {
            margin-top: 10px;
        }
    </style>

</head>
<body>

<section class="reportLoginSection">
	<div class="container">
		<div class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">
			<div class="panel panel-login reportLoginPanel">
			  	<div class="panel-heading">
					<a href="index.php"><img src="<?php echo IMAGE_PATH; ?>/company_logo.png" class="img-responsive center-block"></a>
					<p>vTech Reports</p>
			  	</div>
			  	<div class="panel-body">
			  		<form action="login.php" method="POST">
                        <div class="row">
                            <div class="form-group col-md-12 col-sm-12">
                                <div class="input-group">
                                    <div class="input-group-addon brdr">
                                        <i class="glyphicon glyphicon-user"></i>
                                    </div>
									<input type="text" name="username" id="lUserName" class="form-control brdr" placeholder="Username" autocomplete="off" required>
								</div>
							</div>
                            <div class="form-group col-md-12 col-sm-12">
                                <div class="input-group">
                                    <div class="input-group-addon brdr">
                                        <i class="glyphicon glyphicon-lock"></i>
                                    </div>
									<input type="password" name="password" id="lPassword" class="form-control brdr" placeholder="Password" required>
								</div>
							</div>
                            <div class="form-group col-md-12 col-sm-12">
                                <div class="pull-right">
                                    <a class="forgotPasswordLink">forgot password?</a>
                                </div>
                            </div>
                            <div class="form-group col-md-12 col-sm-12">
                                <button type="submit" name="reportLoginButton" class="form-control btn btn-login">Log In</button>
                            </div>
                        </div>
                    </form>
			  	</div>
			</div>
			<div class="panel panel-login reportForgotPasswordPanel hidden">
			  	<div class="panel-heading">
					<a href="index.php"><img src="<?php echo IMAGE_PATH; ?>/company_logo.png" class="img-responsive center-block"></a>
					<p>vTech Reports</p>
			  	</div>
                <div class="panel-body">
                    <form action="login.php" method="POST">
                        <div class="row">
                            <div class="form-group col-md-12 col-sm-12">
                                <div class="input-group">
                                    <div class="input-group-addon brdr">
                                        <i class="glyphicon glyphicon-user"></i>
                                    </div>
                                    <input type="text" name="username" class="form-control brdr" placeholder="Enter Username" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="form-group col-md-6 col-sm-6 top-margin-div">
                                <button type="submit" name="reportForgotPasswordButton" class="btn btn-login form-control">Send</button>
                            </div>
                            <div class="form-group col-md-6 col-sm-6 top-margin-div">
                                <button type="button" class="btn btn-normal form-control forgotPasswordBackButton">Back</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
		</div>
	</div>
</section>

<script>
    $(document).on("click", ".forgotPasswordLink", function(e){
        e.preventDefault();
        $(".reportLoginPanel").addClass("hidden");
        $(".reportForgotPasswordPanel").removeClass("hidden");
    });

    $(document).on("click", ".forgotPasswordBackButton", function(e){
        e.preventDefault();
        $(".reportForgotPasswordPanel").addClass("hidden");
        $(".reportLoginPanel").removeClass("hidden");
    });
</script>
</body>
</html>
<?php
    } else {
        if (isset($_SESSION["userMember"]) == "Admin") {
            header("Location:admin.php");
        } elseif (isset($_SESSION["userMember"]) == "User") {
            header("Location:user.php");
        }
    }
?>