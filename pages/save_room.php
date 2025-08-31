<?php
include('../connect.php');
session_start();

// Extract POST values safely
$floorno = $_POST['floorno'] ?? '';
$roomname = $_POST['roomname'] ?? '';
$per_adult_price = $_POST['per_adult_price'] ?? 0;
$per_kid_price = $_POST['per_kid_price'] ?? 0;
$amenities = $_POST['amenities'] ?? '';

// Basic validation
if($floorno && $roomname && $per_adult_price >=0 && $per_kid_price >=0) {

    $sql = "INSERT INTO tbl_rooms(floorno, roomname, per_adult_price, per_kid_price, amenities) 
            VALUES ('$floorno', '$roomname', '$per_adult_price', '$per_kid_price', '$amenities')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = 'Record Successfully Added';
    } else {
        $_SESSION['error'] = 'Something Went Wrong: '.$conn->error;
    }
} else {
    $_SESSION['error'] = 'Invalid Input';
}

// Redirect back
header("Location: ../view_room.php");
exit();
?>
