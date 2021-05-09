<?php
    include("security.php");
    include("config.php");
    
    if ($_POST) {
        $firstName = $_POST["firstName"];
        $lastName = $_POST["lastName"];
        $userName = $_POST["userName"];
        $emailAddress = $_POST["emailAddress"];
        $userLevel = $_POST["userLevel"];
        $userType = $_POST["userType"];
        
        function randomPassword(){
            $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
            $newpass = array(); //remember to declare $pass as an array
            $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
                for($i = 0; $i < 6; $i++){
                  $n = rand(0, $alphaLength);
                  $newpass[] = $alphabet[$n];
                }
            return implode($newpass); //turn the array into a string
        }
         
        $password = randomPassword();

        $query = mysqli_query($allConn, "SELECT
            u.uname
        FROM
            mis_reports.users AS u
        WHERE
            u.uname = '$userName'");

        if (mysqli_num_rows($query) > 0) {
            echo "1";
        } else {
            $query = mysqli_query($allConn, "SELECT
                u.email
            FROM
                mis_reports.users AS u
            WHERE
                u.email = '$emailAddress'");

            if (mysqli_num_rows($query) > 0) {
                echo "2";
            } else {
                if (mysqli_query($allConn, "INSERT INTO mis_reports.users(first_name,last_name,uname,email,password,user_level,ustatus,user_type,addedby) VALUES('$firstName','$lastName','$userName','$emailAddress','$password','$userLevel','1','$userType','$user')")) {

                    //SEND_EMAIL_START
                    //include_once("PHPMailer/PHPMailerAutoload.php");
                    include_once("email-config.php");

                    //Embed Image
                    $mail->AddEmbeddedImage("images/company_logo.png", "companyLogo");

                    // Add a sender
                    $mail->setFrom('vtech.admin@vtechsolution.us','vTech Reports');

                    // Email subject
                    $mail->Subject = 'NEW User Access!';

                    $mailContent = '<html><body><center><table>';
                    $mailContent .= '<tr><td colspan="6" style="text-align: center;"><img src="cid:companyLogo" alt="Vtech Solutions"></td></tr>';
                    $mailContent .= '<tr><td colspan="6"><p style="text-align: center;background-color: #999;margin-top: 15px;margin-bottom: 15px;padding: 10px 0px;font-weight: bold;font-size: 22px;color: #000;letter-spacing: 1px;">vTech Reports</p></td></tr>';
                    $mailContent .= '<tr><td colspan="3"><p style="font-weight: bold;">Visit : </p></td><td colspan="3"><p style="text-align: right;font-weight: bold;"><a href="report.vtechsolution.com" target="_blanck">report.vtechsolution.com</a><p></td></tr>';
                    $mailContent .= '<tr><td colspan="6"></td></tr>';
                    $mailContent .= '<tr><td colspan="3"><p style="font-weight: bold;">Username : </p></td><td colspan="3"><p style="text-align: right;font-weight: bold;">'.$userName.'<p></td></tr>';
                    $mailContent .= '<tr><td colspan="6"></td></tr>';
                    $mailContent .= '<tr><td colspan="3"><p style="font-weight: bold;">Password : </p></td><td colspan="3"><p style="text-align: right;font-weight: bold;">'.$password.'</p></td></tr>';
                    $mailContent .= '<tr><td colspan="6"></td></tr><tr><td colspan="6"></td></tr><tr><td colspan="6"></td></tr><tr><td colspan="6"></td></tr><tr><td colspan="6"></td></tr>';
                    $mailContent .= '</table></center></body></html>';

                    // Add a recipient
                    $mail->addAddress($emailAddress);
                    $mail->addCc('haresh@vtechsolution.com');
                    $mail->addBcc('ravip@vtechsolution.us');

                    include("functions/email-send-config.php");
                    //SEND_EMAIL_END

                    echo "3";
                } else {
                    echo "4";
                }
            }
        }
    }
?>