<?php
    $err="false";
    if($_SERVER["REQUEST_METHOD"]== "POST")
    {
        include '_dbconnect.php';

        $usrName = $_POST['name'];
        $usrEmail = $_POST['uemail'];
        $usrPwd = $_POST['upwd'];
        $usrCpwd = $_POST['ucpwd'];

        if($usrPwd == $usrCpwd)
        {
            $usrPassword = password_hash($usrPwd, PASSWORD_DEFAULT);

            $otpRandom = rand(1000,9999);

            $sql = "INSERT INTO `user` (`usr_name`, `usr_email`, `usr_pwd`, `usr_stamp`, `usr_start_otp`) VALUES ('$usrName', '$usrEmail', '$usrPassword', current_timestamp(), '$otpRandom');";

            $res = mysqli_query($con,$sql);

            if($res)
                {
                    $sql1 = "SELECT * FROM `user` WHERE `usr_email` = '$usrEmail'";
                    $res1 = mysqli_query($con,$sql1);
                    $numRows = mysqli_num_rows($res1);

                    if($numRows==1)
                        {
                            $row = mysqli_fetch_assoc($res1);
                            $otpPresent = $row['usr_start_otp'];
                            $usrId = $row['usr_id'];

                            $otpNow = (string) $otpPresent;
                            $id = (string) $usrId;

                            $otp = (int)($otpNow.$id);

                            $sqlMain = "UPDATE `user` SET `usr_start_otp`='$otp' WHERE usr_id ='$usrId';";
                            $result = mysqli_query($con,$sqlMain);

                            if($result)
                            {
                                $showAlert = true;

                                $subject = "OTP Validation From EduVantu";

                                $message = "Thanking for registering with us.
                                So as to complete the registration kindly enter the following otp:'.$otp.'";

                                // Always set content-type when sending HTML email
                                $headers = "MIME-Version: 1.0" . "\r\n";
                                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                                // More headers : Sender's Email Address
                                $headers .= 'From: <>' . "\r\n"; 

                                //Mail
                                
                                mail($usrEmail,$subject,$message,$headers);
                                header("Location: /eClass/partials/src/_signUp.html?userId=true?$usrEmail");
                                exit();
                            }
                            else
                            {
                                $err = "OTP ERROR 1";
                            }        
                        }
                    else
                        {
                            $err="NOT FOUND";
                        }
                }
            else
                {
                    $err="Details not added!!";
                }
        }
        header("Location: /eClass/partials/src/_signUp.html?errCL=$err"); 
    }
?>