<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
include 'index.php' ;

try {
    
    $users = array();
    $result = $dbh->query('SELECT * from user');
    if ($result !== false) {
        foreach($result as $row) {
            array_push($users, array(
                'userID' => $row['userID'],
                'clinicID' => $row['clinicID'],
                'name' => $row['name'],
                'lastname' => $row['lastname'],
                'role' => $row['role'],
            ));
        }
    echo json_encode($users);
    $dbh=null;
    } else {
        echo "Error executing query" . mysqli_error($dbh);
    }
} catch (PDOException $e) {
    print "Error!:" . $e->getMessage() . "<br/>";
    die();
}
?>