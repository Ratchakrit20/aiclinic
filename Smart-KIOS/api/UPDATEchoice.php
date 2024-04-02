<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
include 'index.php' ;
// กำหนดค่าตัวแปร

$text = $_POST['text'];
$questionId = $_POST['questionId'];
$userID = $_POST['userID'];
echo json_encode(array('text' => $text));
echo json_encode(array('questionId' => $questionId));
echo json_encode(array('userID' => $userID));
try {
    

    // กำหนด query
    $sql = "UPDATE `log` SET `text`= (?) WHERE `questionID` = (?) AND `userID` = ? AND DATE(`date`) = CURDATE()";
    $stmt = $dbh->prepare($sql);

    // Bind parameters
    $stmt->bind_param("sii", $text, $questionId, $userID);

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