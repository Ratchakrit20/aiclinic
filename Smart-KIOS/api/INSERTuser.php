<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
include 'index.php' ;
// กำหนดค่าตัวแปร
$clinicID = $_POST['clinicID'];
$name = $_POST['name'];
$lastname = $_POST['lastname'];
$role = $_POST['role'];
try {
    

    // กำหนด query
    $sql = "INSERT INTO `user`(`clinicID`, `name`, `lastname`, `role`) VALUES (?, ?, ?, ?)";
    $stmt = $dbh->prepare($sql);

    // Bind parameters
    $stmt->bind_param("ssss", $clinicID, $name, $lastname, $role); // "ssss" หมายถึง string, string, string, string

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