<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// show error reporting for dev
ini_set('display_errors', 1);
error_reporting(E_ALL);

// loading all files as auto loader
$path = realpath('../src');

$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path),
    RecursiveIteratorIterator::SELF_FIRST);
foreach($objects as $fileName => $object){

    if (is_file($fileName)) {
        include $fileName;
    }
}

// environment variables
$envValues = file("../env");
foreach ($envValues as $value) {
    $values = explode('=', $value); 
    define(trim($values[0]), trim($values[1]));
} 

// routing process file
include 'routes.php';