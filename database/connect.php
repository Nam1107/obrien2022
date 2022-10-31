<?php

global $conn;
try {
   $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_TABLE,  DB_USER, DB_PASS);
   // set the PDO error mode to exception
   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   echo "Connection failed: " . $e->getMessage();
}

// $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_TABLE);

// // Check connection
// if ($conn->connect_error) {
//    die("Connection failed: " . $conn->connect_error);
// }