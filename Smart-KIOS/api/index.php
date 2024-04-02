<?php 
//$dsn = 'mysql:host=localhost:8000;dbname=mydb';
//$user = 'root';
//$pass = '1234';
//$dbh = new PDO($dsn, $user, $pass); // แก้ชื่อคลาสเป็น PDO

?>
<?php 
$host = 'db:3306'; // ชื่อของ Docker service ของ MySQL
//$port = '9906'; // Port ของ MySQL
$dbname = 'mydb';
$user = 'root';
$pass = '1234';



try {
    $dbh = new mysqli($host, $user, $pass,$dbname);
    
} catch (PDOException $e) {
    
}
?>