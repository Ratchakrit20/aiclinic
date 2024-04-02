<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
include 'index.php' ;

try {
    $users = array();
    foreach($dbh->query('SELECT * from answer') as $row) {
        array_push($users, array(
            'answerID' => $row['answerID'],
            'answerTEXT' => $row['answerTEXT'],
        ));
    }
    echo json_encode($users);
    $dbh = null;
} catch (PDOException $e) {
    print "Error!:" . $e->getMessage() . "<br/>";
    die();
}
?>