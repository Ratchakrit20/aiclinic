<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
include 'index.php' ;
// กำหนดค่าตัวแปร
$text = $_POST['text'];
$answerid = $_POST['answerid'];
echo json_encode(array('text' => $text));
echo json_encode(array('questionId' => $answerid));
try {
    

    // กำหนด query
    $sql = "UPDATE `answer` SET `answerTEXT`= ? WHERE `answerID` = ? ";
    $stmt = $dbh->prepare($sql);

    // Bind parameters
    $stmt->bind_param("si", $text, $answerid); // "si" หมายถึง string, integer

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