<?php
include("inc/common.php");


error_log('close trip');
$data = file_get_contents("php://input");
$closedtrip=json_decode($data);

$rodb->query("BEGIN TRANSACTION");
error_log("close trip ". $closedtrip->boat->trip);

if ($stmt = $rodb->prepare("SELECT 'x' FROM  Trip WHERE BoatID=? AND TripID IS NOT NULL")) { 
  $stmt->bind_param('i', $closedtrip->boat->trip);
  $stmt->execute();
  $result= $stmt->get_result();
  if ($result->fetch_assoc()) {
    echo '{"error":"already checked in this trip"}';
    error_log('trip already closed: '. print_r($closedtrip,true));
    $rodb->close();
    exit(0);
  }
} 

error_log("close trip ID". $closedtrip->boat->trip);
if ($stmt = $rodb->prepare(
  "UPDATE Trip SET InTime = NOW() WHERE id=?;"
)) { 
     $stmt->bind_param('i', $closedtrip->boat->trip);
     $stmt->execute(); 
}

$rodb->query("END TRANSACTION");
$rodb->close();
invalidate('trip')
?> 
