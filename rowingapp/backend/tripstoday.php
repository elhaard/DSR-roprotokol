<?php
include("inc/common.php");
include("inc/utils.php");


$s="SELECT Trip.id, TripType.Name AS triptype, Boat.Name AS boat, Trip.Destination as destination, Trip.InTime as intime,Trip.OutTime as outtime, Trip.ExpectedIn as expectedintime,GROUP_CONCAT(Member.MemberID,':§§:', MemberName SEPARATOR '££') AS rowers " .
  " FROM Trip, Boat, TripType, TripMember LEFT JOIN Member ON Member.id = TripMember.member_id  " .
  " WHERE Boat.id=Trip.BoatID AND Trip.id=TripMember.TripID AND Trip.InTime Is NOT Null AND TripType.id = Trip.TripTypeID  AND Trip.InTime  >= CURDATE() ".
  " GROUP BY id ".
  " ORDER BY Trip.id DESC, TripMember.Seat ";


// echo $s."<br>";
if ($stmt = $rodb->prepare($s)) {
     $stmt->execute(); 
     $result= $stmt->get_result() or die("Error in stat query: " . mysqli_error($rodb));

     echo '[';
     $first=1;
     while ($row = $result->fetch_assoc()) {
       if ($first) $first=0; else echo ',';
       $row['rowers']=multifield($row['rowers']);
       echo json_encode($row,JSON_PRETTY_PRINT|JSON_FORCE_OBJECT);
     }
     echo ']';
}
$rodb->close();
?> 
