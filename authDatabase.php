<?php

    DEFINE("LOCALHOST", "localhost");
    DEFINE("USERNAME", "userKam");
    DEFINE("PASSWORD", "userKam525");
    DEFINE("DATABASE", "container");

    $db = new mysqli(LOCALHOST, USERNAME, PASSWORD, DATABASE);

    if ($db->connect_error):
        die("Connect Failed: " . $db->error);
    else:
        // $tableQuery = "CREATE TABLE IF NOT EXISTS `user` (
        //     `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        //     `first_name` varchar(255) NOT NULL,
        //     `last_name` varchar(255) NOT NULL,
        //     `email` varchar(255) NOT NULL UNIQUE,
        //     `password` varchar(255) NOT NULL,
        //     `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        //     `verification_code` int(5) NOT NULL,
        //     `time_verified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        //     `is_verified` tinyint(1) NOT NULL DEFAULT 0
        //     )ENGINE=InnoDB DEFAULT CHARSET=UTF8";

        $modifyQuery = "ALTER TABLE container.user ALTER `time_verified` SET DEFAULT NONE";


            try
            {
                // $res = $db->query($modifyQuery) or die("Error with query -> " . $db->error);
                // echo "Successful table query";
            } catch (Exception $e)
            {
                echo $e->getMessage();
            }
    endif;





?>

