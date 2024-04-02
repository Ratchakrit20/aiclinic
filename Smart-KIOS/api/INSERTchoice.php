<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
include 'index.php' ;
// กำหนดค่าตัวแปร
$userID = $_POST['userID'];
$questionId = $_POST['questionId'];


try {
    // เช็คการเชื่อมต่อ
    if ($dbh->connect_error) {
        die("Connection failed: " . $dbh->connect_error);
    }
    // กำหนด query
    $sql = "INSERT INTO `log` (`userID`, `questionID`) VALUES (?, ?)";
    $stmt = $dbh->prepare($sql);

    // Bind parameters
    $stmt->bind_param("ii", $userID, $questionId); // "ii" หมายถึง integer, integer

    // Execute the query
    $stmt->execute();

    // ปิดคำสั่ง
    $stmt->close();
    // ปิดการเชื่อมต่อ
    $dbh->close();

    
} catch (PDOException $e) {
    // ตรวจจับและแสดงข้อผิดพลาด
    print "Error!:" . $e->getMessage() . "<br/>";
    die();
}
?>