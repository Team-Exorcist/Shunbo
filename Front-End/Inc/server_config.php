<?php

$host = 'localhost';
$owner = 'root';
$password = '';
$database_name = 'test';

$connect = mysqli_connect($host, $owner, $password, $database_name);
if (!$connect) {
    die("connection failed: " .  mysqli_connect_error());
}