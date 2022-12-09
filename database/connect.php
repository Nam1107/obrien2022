<?php

global $conn;
try {
   $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_TABLE . ";charset=" . DB_CHARSET,  DB_USER, DB_PASS);
   // set the PDO error mode to exception
   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   // $res['errors'] =  "Connection failed: " . $e->getMessage();
   $errors = array();
   array_push($errors, "Connection failed: " . $e->getMessage());
   http_response_code(400);
   $res['status'] = 0;
   $res['errors'] = $errors;
   echo json_encode($res);
   exit();
}

// $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_TABLE);
// mysqli_set_charset($conn, 'UTF8');
// // Check connection
// if ($conn->connect_error) {
//    die("Connection failed: " . $conn->connect_error);
// }