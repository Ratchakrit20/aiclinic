<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
include 'index.php' ;

try {
    
    $users = array();
    foreach($dbh->query('SELECT q.questionID, q.question, a.answerID, a.answerTEXT FROM questine_has_answer qa JOIN questine q ON q.questionID = qa.questine_questionID JOIN answer a ON qa.answer_answerID = a.answerID;') as $row) {
        array_push($users, array(
            'questionID' => $row['questionID'],
            'question' => $row['question'],
            'answerID' => $row['answerID'],
            'answerTEXT' => $row['answerTEXT']
        ));
    }
    echo json_encode($users);
    $dbh = null;
} catch (PDOException $e) {
    print "Error!:" . $e->getMessage() . "<br/>";
    die();
}
?>