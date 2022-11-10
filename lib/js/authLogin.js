const { log } = console;

let loginEmail = document.getElementsByClassName("loginEmail")[0];
let loginPassword = document.getElementsByClassName("loginPassword")[0];
let nextBtn = document.getElementsByClassName("nextBtn")[0];
let submitBtn = document.getElementsByClassName("submitBtn")[0];

let signupNav = document.getElementsByClassName("signupNav")[0];

let Errors = document.getElementsByClassName('error');

let containerPassword = document.getElementsByClassName("container-input-show")[0];


function Input()
{
    nextBtn.addEventListener("click", function(e){


        if (loginEmail.value == ''){

            loginEmail.style.borderColor = 'red';

            Errors[0].hidden = false;

            return false;
        } else {

        loginEmail.type = "text";

        loginEmail.classList.add("displayEmail");
        containerPassword.classList.add("displayPassword");

        nextBtn.style.display = "none";
        submitBtn.style.display = 'flex';

        Errors[0].hidden = true;
        }


        // log (window.location.href.substring(27, 40));

        // window.location.href = `${window.location.href.substring(27, 40)}?nextBtn=true`;


    })
}

function submitInput()
{
    submitBtn.addEventListener('click', (e) => {
        if (loginPassword.value == '')
        {
            e.preventDefault();

            loginPassword.style.borderColor = 'red';
            loginPassword.style.transform = 'translateY(5px)';

            Errors[1].hidden = false;

            return false;
        } else
        {
            Errors[1].hidden = true;

            return true;
        }
    })
}


signupNav['addEventListener']('click', (e) => {
    window.location = window.location.href.replace(window.location.href, "/crud/auth/authSignup.php");
})



Input()
submitInput();