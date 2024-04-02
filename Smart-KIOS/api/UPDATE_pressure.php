<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
include 'index.php' ;
// กำหนดค่าตัวแปร
$userID = $_POST['userID'];
$pressure = $_POST['pressure'];
echo json_encode(array('text' => $text));
echo json_encode(array('questionId' => $answerid));
try {
    

    // กำหนด query
    $sql = "UPDATE `measurement` SET `bloodpressure`= ? WHERE `userID` = ? AND DATE(`date`) = CURDATE()";
    $stmt = $dbh->prepare($sql);

    // Bind parameters
    $stmt->bind_param("si", $pressure, $userID); // "di" หมายถึง double, integer

    // Execute the query
    $stmt->execute();

    // ปิดการเชื่อมต่อ
    $dbh->close();
} catch (PDOException $e) {
    // ตรวจจับและแสดงข้อผิดพลาด
    print "Error!:" . $e->getMessage() . "<br/>";
    die();
}
?>