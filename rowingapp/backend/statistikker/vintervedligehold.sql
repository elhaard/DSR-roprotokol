select TripMember.member_id as medlemsId,
       Member.MemberID as medlemsnr,
       tblMembers.E_mail as medlemsmail,
       TripMember.MemberName as navn,
       SUM(Trip.Meter)/1000  as kilometer
FROM Trip 
JOIN TripMember ON (TripMember.TripID = Trip.TripID)
INNER JOIN Boat ON (Boat.id = Trip.BoatID)
INNER JOIN BoatType ON (Boat.BoatType = BoatType.id)
LEFT JOIN Member ON (TripMember.member_id = Member.id)
LEFT JOIN tblMembers ON (Member.MemberID = tblMembers.MemberID)
where Trip.season='2015'
  AND BoatType.Category = 2
GROUP BY TripMember.member_id
HAVING kilometer > 100
  order by kilometer desc, navn asc;
