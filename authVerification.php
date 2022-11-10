<?php
session_start();

error_reporting( E_ERROR | E_PARSE );

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;
use \PHPMailer\PHPMailer\SMTP;

use Twilio\Rest\Client;

include './vendor/autoload.php';
//include './vendor/twilio/sdk/src/Twilio/autoload.php';

include './authDatabase.php';

$verifyCode = $_POST['verifyCode'];


$sendEmail = $_POST['sendEmail'];
$sendSMS = $_POST['sendSMS'];
$submitCode = $_POST['submitCode'];
$textNumber = $_POST['textNumber'];

$sid = 'ACc346ab0e30aca7aa3835d209e9b26779';
$token = '62d932db13e54b3e4588512cc8f1aaa7';

$client = new Client($sid, $token);


function sendEmail()
{
    global $db, $sendEmail;
    
    if (isset($sendEmail))
    {
        if (isset($_SESSION['email']) && $_SESSION['authenticate'] == true)
        {
            $getEmail = $_SESSION['email'];

            $queryForEmail = "SELECT verification_code FROM `user` WHERE email='$getEmail'";

            $sendEmailQuery = $db->query($queryForEmail) or die($db->error);
            $fetchCode = $sendEmailQuery->fetch_assoc();

            $emailMailer = new PHPMailer(true);

            try
            {
                $emailMailer->isSMTP();
                $emailMailer->Host = 'smtp.gmail.com';
                $emailMailer->SMTPAuth = true;
                $emailMailer->Username = 'kamronbrad20@gmail.com';
                $emailMailer->Password = 'ghebapsdoctfutpk';
                $emailMailer->SMTPSecure = 'ssl';
                $emailMailer->Port = 465;

                $emailMailer->setFrom("kamronbrad20@gmail.com");

                $emailMailer->addAddress($getEmail);

                $emailMailer->Subject = "Verify your email address";
                $emailMailer->isHTML(true);
                $emailMailer->Body = "<h1>Verification Code</h1><br>
                                    <p>{$fetchCode['verification_code']}</p>";

                $emailMailer->send();

            } catch(Exception $e)
            {
                echo $e->getMessage();
            }


            
        } else
        {
            return false;
        }
    }
}

function sendSMS()
{
    global $db, $sendSMS, $textNumber, $sid, $token, $client;

    if (isset($sendSMS))
    {
        $getEmail = $_SESSION['email'];

        $query = "SELECT * FROM `user` WHERE `email`=?";

        $queryForSMS = $db->prepare($query) or die("error");

        $queryForSMS->bind_param('s', $getEmail);

        $queryForSMS->execute();

        $getCodeFromSMS = $queryForSMS->get_result();

        $fetchCodeFromSMS = $getCodeFromSMS->fetch_assoc();

        try
        {
            $msg = $client->messages->create(
                "+1{$textNumber}", [
                    'from' => '+18318511767',
                    'body' => "Hey {$fetchCodeFromSMS['first_name']}! Verify your code => {$fetchCodeFromSMS['verification_code']}"
                ]
                );
            
            if ($msg)
            {
                echo "Message sent";
            } else
            {
                $error = new Exception();
                echo "Message not sent => " . $error->getMessage();
            }

        } catch (Exception $e)
        {
            echo $e->getMessage();
        }

    }
}

function submitCode()
{
    global $db, $submitCode, $verifyCode;

    if (isset($submitCode))
    {
        $getEmail = $_SESSION['email'];
        $queryForCode = "SELECT verification_code FROM `user` WHERE email = '$getEmail'";

        $codeQuery = $db->query($queryForCode) or die($db->error);
        $fetchCode = $codeQuery->fetch_assoc();

        if ($fetchCode['verification_code'] == $verifyCode)
        {
            $randCode = substr(number_format(time() * rand(), 0, '', ''), 0, 4);

            $intVal = 1;
            
            $updateQuery = "UPDATE `user` SET `verification_code`= ?, `time_verified` = NOW(), `is_verified`= ? WHERE `email` = ?";

            $updatePreparedQuery = $db->prepare($updateQuery) or die("Error: " . $db->error);

            $updatePreparedQuery->bind_param('iis', $randCode, $intVal, $getEmail);

            $updatePreparedQuery->execute();

            if ($updatePreparedQuery->store_result() == true)
            {
                header("location: authHome.php?verification=true");
            } else
            {
                echo "Email address not verified";
            }
        
        } else
        {
            echo "Email is not verified";
        }
    }
}

sendEmail();
submitCode();
sendSMS();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800;900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        .verify-container
        {
            border-left: none;
            border-right: none;
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            margin: auto;
            width: 700px;
            height: 60vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transform: translateY(100px);
        }
        .verify-container h1
        {
            font-size: 40px;
            text-transform: uppercase;
            margin: 30px 0px;
            letter-spacing: 1px;
        }
        .verify-container form
        {
            text-align: center;
            height: 30vh;
            padding: 40px 0px;
            display: flex;
        }
        input[type='text']
        {
            width: 300px;
            padding: 10px;
            border: 1px solid grey;
            border-radius: 5px;
            transform: translateY(-20px);
            text-align: center;
        }

        input[type='submit']
        {
            padding: 10px;
            width: 300px;
            background: black;
            color: white;
            border: 1px solid black;
            cursor: pointer;
            text-transform: uppercase;
            font-weight: 700;
        }

        #submitCode
        {
            background-color: rgb(2, 122, 197);
            border: none;
            border-radius: 10px;
            position: relative;
            top: 5px;
        }
        #textNumber
        {
            transform: translateY(-45px);
        }

    </style>
</head>
<body>
    <div class="verify-container">
        <h1>Verify your email address</h1>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="<?php echo strtoupper("post") ?>">
            <table>
                <tr>
                    <td>
                        <input type="text" name='textNumber' id='textNumber' placeholder='Enter phone number *'/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="text" name='verifyCode' id="verifyCode" placeholder="Enter code" pattern="[0-9]+"/>
                    </td>
                </tr>


                <tr>
                    <td>
                        <input type='submit' name='sendEmail' id='sendEmail' value='Send email address'/>
                    </td>
                </tr>

                <tr>
                    <td>
                        <input type='submit' name ='sendSMS' id='sendSMS' value='Text SMS'/>
                    </td>
                </tr>

                <tr>
                    <td>
                        <input type='submit' name='submitCode' id = 'submitCode' value='Submit'/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>