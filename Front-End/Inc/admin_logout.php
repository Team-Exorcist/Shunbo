<?php
session_start();
if (isset($_SESSION['aID'])) {
    unset($_SESSION['aID']);
}

header("location: ../HTML SCRIPTS/Admin_panel/adminlogin.php");
die;