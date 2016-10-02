<?php
require __DIR__ . '/../vendor/autoload.php';

$swagger = \Swagger\scan(__DIR__ . '/../src');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type,X-Amz-Date,Authorization,X-Api-Key,X-Amz-Security-Token");

header('Content-Type: application/json');

echo $swagger;
