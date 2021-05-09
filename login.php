<?php
	error_reporting(0);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Login to vTech Reports</title>

    <?php
        include("cdn.php");
    ?>
</head>
</html>
<?php
	include("config.php");
	
	if (isset($_POST["reportLoginButton"])) {
		$username = $_POST["username"];
		$password = $_POST["password"];

		$query = mysqli_query($allConn, "SELECT
			u.uid,
			u.user_level
		FROM
			mis_reports.users AS u
		WHERE
			u.uname = '$username'
		AND
			u.password = '$password'
		AND
			u.ustatus = '1'");

		if (mysqli_num_rows($query) > 0) {
			$row = mysqli_fetch_array($query);
			
			session_start();
			$_SESSION["user"] = $row["uid"];
			$_SESSION["userMember"] = $row["user_level"];

			if ($row["user_level"] == "Admin") {
				header("Location:admin.php");
			} elseif ($row["user_level"] == "User") {
				header("Location:user.php");
			} else {
				header("Location:logout.php");
			}
		} else {
			$query = mysqli_query($allConn, "SELECT
				u.uid
			FROM
				mis_reports.users AS u
			WHERE
				u.uname = '$username'
			AND
				u.password = '$password'
			AND
				u.ustatus = '0'");

			if (mysqli_num_rows($query) > 0) {
				echo "<script>
	                swal({
	                    title: 'Your Account is inactive!',
	                    type: 'info',
	                    button: 'OK!'
	                },function(isConfirm){
	                    alert('ok');
	                });
	                $('.swal2-modal').css('background-color', '#fff');
	                $('.swal2-container').css('background-color', '#2266AA');
	                $('.swal2-confirm').click(function(){
	                    window.location.href = 'index.php';
	                });
	            </script>";
			} else {
				echo "<script>
	                swal({
	                    title: 'Invaild Username or Password!',
	                    type: 'error',
	                    button: 'OK!'
	                },function(isConfirm){
	                    alert('ok');
	                });
	                $('.swal2-modal').css('background-color', '#fff');
	                $('.swal2-container').css('background-color', '#2266AA');
	                $('.swal2-confirm').click(function(){
	                    window.location.href = 'index.php';
	                });
	            </script>";
			}
		}
	}

	if (isset($_POST["reportForgotPasswordButton"])) {
	    $username = $_POST["username"];
	    
	    $query = mysqli_query($allConn, "SELECT
	    	u.email
	    FROM
	    	mis_reports.users AS u
	    WHERE
	    	u.uname = '$username'");

	    if (mysqli_num_rows($query) > 0) {
			$row = mysqli_fetch_array($query);
	    	$emailAddress = $row["email"];

	        function randomPassword(){
	            $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	            $newpass = array(); //remember to declare $pass as an array
	            $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	                for ($i = 0; $i < 6; $i++) {
	                  $n = rand(0, $alphaLength);
	                  $newpass[] = $alphabet[$n];
	                }
	            return implode($newpass); //turn the array into a string
	        }
	         
	        $password = randomPassword();

	        //SEND_EMAIL_START
			//include_once("PHPMailer/PHPMailerAutoload.php");
			include_once("email-config.php");

			//Embed Image
			$mail->AddEmbeddedImage("images/company_logo.png", "companyLogo");

			// Add a sender
			$mail->setFrom('vtech.admin@vtechsolution.us','vTech Reports');

			// Email subject
			$mail->Subject = 'NEW Password!';

			$mailContent = '<html><body><center><table>';
			$mailContent .= '<tr><td colspan="2" style="text-align: center;"><img src="cid:companyLogo" alt="Vtech Solutions"></td></tr>';
			$mailContent .= '<tr><td colspan="2"><p style="text-align: center;background-color: #999;margin-top: 15px;margin-bottom: 20px;padding: 10px 0px;font-weight: bold;font-size: 22px;color: #000;letter-spacing: 1px;">vTech Reports</p></td></tr>';
			$mailContent .= '<tr><td><p style="font-weight: bold;">New Password : </p></td><td><p style="text-align: right;font-weight: bold;">'.$password.'<p></td></tr>';
			$mailContent .= '<tr><td colspan="2"></td></tr>';
			$mailContent .= '<tr><td><p style="font-weight: bold;">For User : </p></td><td><p style="text-align: right;font-weight: bold;">'.$username.'</p></td></tr>';
			$mailContent .= '</table></center></body></html>';

			// Add a recipient
			$mail->addAddress($emailAddress);
			$mail->addBcc('ravip@vtechsolution.us');

			include("functions/email-send-config.php");
			//SEND_EMAIL_END

	        if (mysqli_query($allConn, "UPDATE mis_reports.users SET password = '$password' WHERE uname = '$username'")) {
				echo "<script>
				    swal({
				        title: 'New password has been sent!',
				        type: 'success',
				        button: 'OK!'
				    },function(isConfirm){
				        alert('ok');
				    });
				    $('.swal2-modal').css('background-color', '#fff');
				    $('.swal2-container').css('background-color', '#2266AA');
				    $('.swal2-confirm').click(function(){
				        window.location.href = 'index.php';
				    });
				</script>";
	        }
	    } else {
			echo "<script>
			    swal({
			        title: 'User is not Registered!',
			        type: 'error',
			        button: 'OK!'
			    },function(isConfirm){
			        alert('ok');
			    });
			    $('.swal2-modal').css('background-color', '#fff');
			    $('.swal2-container').css('background-color', '#2266AA');
			    $('.swal2-confirm').click(function(){
			        window.location.href = 'index.php';
			    });
			</script>";
	    }
	}


	if (isset($_POST["reportChangePasswordButton"])) {
	    $userid = $_POST["userid"];
	    $confirmpassword = mysqli_real_escape_string($allConn, $_POST["confirmpassword"]);

	    if (mysqli_query($allConn, "UPDATE mis_reports.users SET password = '$confirmpassword' WHERE uid = '$userid'")) {
	        echo "<script>
			    swal({
			        title: 'Password Successfully Changed!',
			        type: 'success',
			        button: 'OK!'
			    },function(isConfirm){
			        alert('ok');
			    });
			    $('.swal2-modal').css('background-color', '#fff');
			    $('.swal2-container').css('background-color', '#2266AA');
			    $('.swal2-confirm').click(function(){
			        window.location.href = 'index.php';
			    });
			</script>";
	    }
	}
?>