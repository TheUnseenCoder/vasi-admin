<?php 
    session_start();
    session_unset();

    $_SESSION["loggedinasadmin"] = false;
    header('Location: ../../index.php');
    exit;
?>
