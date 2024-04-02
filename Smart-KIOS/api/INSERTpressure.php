<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
include 'index.php' ;
// กำหนดค่าตัวแปร
$userID = $_POST['userID'];
$pressure = $_POST['pressure'];

try {
    

    // กำหนด query
    $sql = "INSERT INTO `measurement`(`userID`) VALUES (?)";
    $stmt = $dbh->prepare($sql);

    // Bind parameters
    $stmt->bind_param("i", $userID); // "id" หมายถึง integer, double

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