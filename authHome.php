<?php
session_start();

include './authDatabase.php';

$query = "SELECT * FROM `user` WHERE `email` = ?";

$emailSession = $_SESSION['email'];

$prepareQuery = $db->prepare($query) or die("error");

$prepareQuery->bind_param('s', $emailSession);

$prepareQuery->execute();

$getName = $prepareQuery->get_result();


$fetchName = $getName->fetch_assoc();



?>
<html>
    <head>
        <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800;900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body
        {
            overflow: hidden;
        }
        h1
        {
            position: absolute;
            width: 100%;
            text-align: center;
            color: green;
            margin: 20px 0px;
        }
        </style>
    </head>
    <?php 
        if (is_array($fetchName))
        {
            echo "<h1>Congratulations {$fetchName['first_name']}! Your account is verified</h1>";
        }
    
    
    ?>
</html>