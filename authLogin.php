<?php
session_status() == PHP_SESSION_ACTIVE ? session_start() : session_start();

error_reporting( E_ERROR | E_PARSE );

include 'authDatabase.php';

$loginEmail = $_POST['loginEmail'];
$loginPassword = $_POST['loginPassword'];



$submitBtn = $_POST['submitBtn'];

class authLogin
{

    public $errorsMsg = [];

    public function __construct()
    {
        // leave empty
    }

    public function displayErrorMsg(): array
    {
        return $this->errorsMsg;
    }

    public function checkEmailError(string $email): bool
    {
        $validEmail = true;

        if (empty($email))
        {
            $validEmail = false;
        }

        return $validEmail;
    }

    public function checkEmptyPassword(string $password): bool
    {
        $validPassword = true;

        if (empty($password))
        {
            $validPassword = false;
        }

        return $validPassword;
    }

    public function displayEmailError(string $email): array
    {
        $emailError = $this->displayErrorMsg();

        if ($this->checkEmailError($email) == false)
        {
            array_push($emailError, "<p class='error'>Email field is required.</p>");
        }

        return $emailError;
    }

    public function displayPasswordError(string $password): array
    {
        $passwordError = $this->displayErrorMsg();

        if ($this->checkEmptyPassword($password) == false)
        {
            array_push($passwordError, "<p class='error'>Password is required.</p>");
        }

        return $passwordError;
    }

    public function authLogin(string $email, string $password)
    {
        global $db;

        if (count($this->displayEmailError($email)) == 0 && count($this->displayPasswordError($password)) == 0)
        {
            $loginQuery = "SELECT `email`,`password` FROM `user` WHERE `email` = ? OR `password` = ?";

            $prepareLoginQuery = $db->prepare($loginQuery) or die("Error query: " . $db->error);

            $prepareLoginQuery->bind_param('ss', $email, $password);

            $prepareLoginQuery->execute();

            $getLoginQuery = $prepareLoginQuery->get_result();

            $fetchLoginQueryRow = $getLoginQuery->fetch_assoc();

            if (is_array($fetchLoginQueryRow) && password_verify($password, $fetchLoginQueryRow['password'])){
                $_SESSION['email'] = $fetchLoginQueryRow['email'];
                $_SESSION['authenticate'] = true;

                header("Location: authVerification.php");

            }  else
            {
             header("Location: authLogin.php?authentication=false");   
            }
        } else
        {
            echo "false";
        }
    }
}

function login()
{
    global $loginEmail, $loginPassword, $submitBtn;

    if (isset($submitBtn))
    {
        $authLogin = new authLogin();

        $authLogin->authLogin($loginEmail, $loginPassword);
    }
}



login();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Login Page</title>
    <style>
        <?php
        $fileName = './lib/styles/authLogin.txt';

        $file = fopen($fileName, "r");

        while (!feof($file))
        {
            echo fgets($file);
        }

        fclose($file);
        ?>
    </style>
</head>
<body>
    <section>
        <nav>
            <div class="logo">
                <h2>KB</h2>
                <h4>Kamron Bradford</h4>
            </div>

            <div class="option">
                <p>Don't have an account</p>
                <input type="button" name="signupNav" class ="signupNav" value="Sign Up">
            </div>
        </nav>
        <form action = '<?php echo $_SERVER['PHP_SELF'] ?>' method='POST'>
        <div class="container">
                <h2>Log in</h2>

                <div class="container-input">
                    <div class="container-input-field">
                        <label for="email">Email</label>
                        <input type="email" name="loginEmail" class="loginEmail" placeholder = "example@kam.com" size=100/>
                        <p class='error' style='transform: translateY(20px);' hidden>Email is required</p>

                        <?php ?>

                        <div class="container-input-show">
                            <label for="password">Password</label>
                            <input type="password" name="loginPassword" class="loginPassword" placeholder="Kam2000" size=100/>
                            <p class='error' style="transform: translateY(8px);" hidden>Password is required</p>
                        </div>
                    </div>

                    <div class="container-btn">
                        <button type="button" name="nextBtn" class="nextBtn" >Next</button>
                    </div>

                    <div class="container-login-btn">
                        <input type="submit" name="submitBtn" class="submitBtn" value="Log in"/>
                    </div>
                </div>
            </div>
        </form>
    </section>

</body>
<script type='text/javascript' src='./lib/js/authLogin.js'></script>
</html>