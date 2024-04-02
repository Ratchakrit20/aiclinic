<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
include 'index.php'; // ถ้าไฟล์ db.php มีการเชื่อมต่อฐานข้อมูลของคุณ

$userID = $_POST['userID'];

try {
    $users = array();
    $stmt = $dbh->prepare('SELECT `questionID`,`text` FROM `log` WHERE `userID` = ? AND DATE(`date`) = CURDATE()');
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $users[] = array(
            'questionID' => $row['questionID'],
            'text' => $row['text']
        );
    }
    
    // ตอบกลับด้วย JSON
    echo json_encode(array('users' => $users));
} catch (PDOException $e) {
    // ในกรณีของข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล
    echo json_encode(array('error' => $e->getMessage()));
}
?>
