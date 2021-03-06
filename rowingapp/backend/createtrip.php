<?php
include("inc/common.php");

$season=date('Y');
$res=array ("status" => "ok");

$data = file_get_contents("php://input");
$newtrip=json_decode($data);
$message="createtrip  ".json_encode($newtrip);
$error=null;

$rodb->begin_transaction();
if ($stmt = $rodb->prepare("SELECT 'x' FROM  Trip WHERE BoatID=? AND InTime IS NULL")) { 
  $stmt->bind_param('i', $newtrip->boat->id);
  $stmt->execute();
  $result= $stmt->get_result();
  if ($result->fetch_assoc()) {
      $res["status"]="error";
      $error="already on water";
#      $message='create trip failed, already on water: '. json_encode($newtrip,true);
      error_log($error);
  }
}
error_log("udskriv ".$newtrip->boat->name);

if (!$error) {
    if ($stmt = $rodb->prepare("INSERT INTO Trip(Season,BoatID,Destination,TripTypeID,CreatedDate,EditDate,OutTime,ExpectedIn,Meter) VALUES(?,?,?,?,NOW(),NOW(),CONVERT_TZ(?,'+00:00','SYSTEM'),CONVERT_TZ(?,'+00:00','SYSTEM'),?)")) { 
        $stmt->bind_param('iisissi', $season, $newtrip->boat->id , $newtrip->destination->name, $newtrip->triptype->id, $newtrip->starttime, $newtrip->expectedtime,$newtrip->distance);
        error_log('now EXE '. json_encode($newtrip));
        if (!$stmt->execute()) {
            $error=mysqli_error($rodb);
            $message=$message."\n"."create trip insert error";
        }
    } 
    error_log("\n\nnow all rowers ".json_encode($newtrip->rowers));
    
    if ($stmt = $rodb->prepare("INSERT INTO TripMember(TripID,Season,Seat,member_id,MemberName,CreatedDate,EditDate) ".
    "SELECT LAST_INSERT_ID(),?,?,Member.id,?,NOW(),NOW() FROM Member Where MemberID=?"
    )) {
        $seat=1;
        error_log("ROWERS");
        foreach ($newtrip->rowers as $rower) {
            error_log("SEAT".$seat);
            error_log("DO trip mb ".$rower->name);
            $stmt->bind_param('iisi',$season,$seat,$rower->name,$rower->id);
            $stmt->execute();
            $seat+=1;
        }
    } else {
        error_log("OOOPs2".$rodb->error);
        $error="trim member DB error".mysqli_error($rodb);
    }
}



if (isset($newtrip->event)) {
    if ($stmt = $rodb->prepare("INSERT INTO event_log (event,event_time) VALUES(?,NOW())")) {
        $ev=$newtrip->triptype->name ." til ". $newtrip->destination->name." i ".$newtrip->boat->name .": ". $newtrip->event;
        $stmt->bind_param('s', $ev);
        $stmt->execute();
    }     
}


if ($error) {
    error_log('DB error ' . $error);
    $res['message']=$message.'\n'.$error;
    $res['status']='error';
    $res['error']=$error;
    $rodb->rollback();
} else {
$rodb->commit();
}

$res['message']=$message;
invalidate("trip");
$rodb->close();
echo json_encode($res);
?> 
