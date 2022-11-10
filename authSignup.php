<?php
session_status() !== PHP_SESSION_ACTIVE ? session_start() : session_start();

error_reporting( E_ERROR | E_PARSE );

include './authDatabase.php';

$firstName = $_POST['first_name'];
$lastName = $_POST['last_name'];
$postEmail = $_POST['email'];
$postPassword = $_POST['password'];

$submitBtn = $_POST['submit'];

$path = $_SERVER['PHP_SELF'];

class authSignup
{
    public $errorsMsg = [];

    public function __construct()
    {
       // leave empty
    }

    public function getErrorsMsg(): array
    {
        return $this->errorsMsg;
    }

    public function checkValidName(string $first_name, string $last_name): bool
    {
        global $path;

        $validFullName = true;

        if (empty($first_name) or empty($last_name)):
            $validFullName = false;

            header("location: $path");
        endif;

        return $validFullName;
    }

    public function checkValidEmail(string $email): bool
    {
        global $path;

        $validEmail = true;

        if (empty($validEmail)):
            $validEmail = false;

            header("location: $path");
        endif;

        if (!filter_var($email) or !isset($email)):
            $validEmail = false;

            header("location: $path");
        endif;

        return $validEmail;
    }

    public function checkValidPassword(string $password): bool
    {
        global $path;

        $validPassword = true;

        if (empty($password)):
            $validPassword = false;

            header("location: $path");
        endif;

        if (mb_strlen($password) > 8)
        {
            $validPassword = false;

            header("location: $path");
        }

        return $validPassword;
    }

    public function checkIfEmailAlreadyExists(string $email)
    {
        global $db, $path;

        $doesEmailExists = true;

        $queryForEmail = "SELECT id FROM `user` WHERE `email`=?";

        $prepareEmail = $db->prepare($queryForEmail) or die("Query error: " . $db->error);

        $prepareEmail->bind_param('s', $email);

        $prepareEmail->execute();

        $prepareEmail->store_result();

        if ($prepareEmail->affected_rows == 1):
            $doesEmailExists = false;
        else:
            $doesEmailExists = true;
        endif;

        return $doesEmailExists;

    
    }

    public function displayErrors(string $first_name, string $last_name, string $email, string $password): array
    {
        $displayError = $this->getErrorsMsg();

        if ($this->checkValidName($first_name, $last_name) == false):
            array_push($displayError, "Fill in name fields.");
        endif;

        if ($this->checkValidEmail($email) == false):
            array_push($displayError, "Either the email field is empty or email is invalid.");
        endif;

        if ($this->checkValidPassword($password) == false):
            array_push($displayError, "Either the password is empty or password exceeds maximum characters.");
        endif;

        if ($this->checkIfEmailAlreadyExists($email) == false):
            array_push($displayError, "Email already exist");
        endif;

        return $displayError;
    }

    // get id from name;

    public function authSignup(string $first_name, string $last_name, string $email, string $password): int
    {
        global $db, $path;

        $validID = NULL;


        if (count($this->displayErrors($first_name, $last_name, $email, $password)) == 0)
        {
            $setVerificationCode = substr(number_format(time() * rand(), 0, '', ''), 0, 4);
            
            $signupQuery = "INSERT INTO `user` (`first_name`, `last_name`, `email`, `password`, `verification_code`) VALUES (?,?,?,?,?)";

            $prepareSignupQuery = $db->prepare($signupQuery);

            $hashThePassword = password_hash($password, PASSWORD_DEFAULT);

            $prepareSignupQuery->bind_param('ssssi', $first_name, $last_name, $email, $hashThePassword, $setVerificationCode);

            $prepareSignupQuery->execute();

            $validID = $prepareSignupQuery->insert_id;

            header("location: authLogin.php?signup=true");

        } else
        {
            echo "Error occurred in the required fields";
        }

        return $validID;
    }

}


function signupField ()
{
    global $firstName, $lastName, $postEmail, $postPassword, $submitBtn, $path;

    if (isset($submitBtn)){

        $signup = new authSignup();

        $signup->displayErrors($firstName, $lastName, $postEmail, $postPassword);
        
        if ($signup->checkIfEmailAlreadyExists($postEmail) == false):
            header("location: $path?error=true");
        else:
            $signup->authSignup($firstName,$lastName,$postEmail,$postPassword);
        endif;

    }

    


}

signupField();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset='utf8'>
        <meta name='viewport' content='width=width-device, initial-scale=1.0'/>
        <meta http-equiv='X-UA-Compatible' content='ie=edge'/>
        <title>Authention Signup Page</title>
        <style>
            <?php
            $fileName = './lib/styles/authSignup.txt';

            $file = fopen($fileName, "r") or die("File does not exists");

            $file == false ? '': $file;

            while (!feof($file)):
                echo fgets($file);
            endwhile;

            fclose($file);

            ?>

       ::placeholder
       {
            color: white;
            font-size: 15px;
       }
       /* ul li:before
       {
         content: '✓';
         margin-right: 5px;
       } */
        </style>
    </head>
    <body>
        <section id="bg-wrapper">
            <section id = "bg-form">
            <nav>
                <div class="auth-options">
                    <p>Already have an account</p>
                    <input type='button' name="loginNavigation" id="loginNavigation" value="Log In">
                </div>
            </nav>
            <div class="form-container">
                <div class="logo">
                    <h1>KB</h1>
                </div>
                
                <div class="sub-title">
                    <h3>Get started with a free KB account.</h3>
                    <h3>No credit card required.</h3>
                </div>

                <div class="form-holder">
                    <div class="form-build">
                        <div class="form-build-wrapper">
                            <div class="form-build-title">
                                <h4>With KB You Can Build:</h4>
                            </div>
                            <div class="form-build-list">
                                <ul>
                                    <li>Email Verification</li>
                                    <li>SMS Verification</li> 
                                    <li>Push and Set Cookies</li>
                                    <li>Enable Sessions</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-information">
                        <div class="form-information-wrapper">
                            <form action = <?php echo $_SERVER['PHP_SELF'] ?> method= <?php echo strtoupper("post") ?>>
                                <div>
                                    <input type="text" name="first_name" id = "firstName" placeholder="First Name *"/>
                                </div>
                                <div>
                                    <input type="text" name="last_name" id="lastName" placeholder="Last Name *"/>
                                </div>
                                <div>
                                    <input type="email" name="email" id = "Email" placeholder="Email *"/>
                                </div>
                                <div>
                                    <input type="password" name="password" id="Password" placeholder="Password (8+ Characters) *"/>
                                </div>
                                <div>
                                    <input type="checkbox" name="checkbox" checked>
                                    <p>I accept the <a href="#">KB Terms of Service</a> and have read the <a href="#">KB Privacy Notice.</a> If I am a micro- or small enterprise or a not-for-profit organization in the EEA or UK,
                                    I agree to the <a href="#">United States Electronic Communications Code Rights Waiver.</a></p>
                                </div>
                                <div>
                                    <input type="submit" name="submit" id = "submit" value="Start your free trial"/>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            </section>
        </section>
    </body>
    <script type='text/javascript'>

        
        let TextFields = document.querySelectorAll("input[type='text']");
        let EmailField = document.querySelector("input[type='email']");
        let PasswordField = document.querySelector("input[type='password']");

        let CheckBox = null; // Optional // Submit box whether it's checked or not.

        console.log(TextFields);

        let Submit = document.querySelector("#submit");

        function Styles(target, attr, val)
        {
            return target['style'][attr] = val;
        }

        // Check box is optional from the Signup form.

        function SubmitForm()
        {
            Submit.addEventListener('click', (e) => {
                if (TextFields[0].value == '' && TextFields[1].value == '' && EmailField.value == '' && PasswordField.value == ''){
                    var targetArray = [TextFields[0],TextFields[1],EmailField,PasswordField];

                    targetArray.forEach(target => {
                        Styles(target, "border", "2px solid red");
                    })
                    e.preventDefault();

                    return false;
                }
                return true;
            })
    }

    SubmitForm();

    </script>
</html>