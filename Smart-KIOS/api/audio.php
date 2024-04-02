<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: multipart/form-data; charset=utf-8");

try {
    // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if file is sent
    if (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK && isset($_POST['userID']) && isset($_POST['slideIndex'])) {
        // Get uploaded file details
        $fileTmpPath = $_FILES['audio']['tmp_name'];
        $fileName = $_FILES['audio']['name'];
        $fileSize = $_FILES['audio']['size'];
        $fileType = $_FILES['audio']['type'];
    
        $userID = $_POST['userID'];
        $slideIndex = $_POST['slideIndex'];
        //http://localhost:5555/
        // Specify the directory where you want to save the uploaded audio files
        //$uploadDirectory = 'C:\MAMP\htdocs\api\Smart-KIOS\ai\path_user';
        $uploadDirectory = '/usr/src/app/';
        // Move the uploaded file to the specified directory
        //$destPath = $uploadDirectory . $fileName;
        $destPath = $UPLOAD_DIRECTORY . $fileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // File upload successful, you can now save the file path to the database
            // Connect to the database (make sure to include your db.php file)
            include 'index.php';
            
            // Prepare SQL statement to insert data into the audio_files table
            //$sql = "INSERT INTO audio_files (file_name, file_url) VALUES (?,?)";
            //$sql = "INSERT INTO `log` (`userID`, `questionID`, `path`) VALUES (?,?,?)";
            $sql = "UPDATE `log` SET `path`= (?) WHERE `questionID` = (?) AND `userID` = ? AND DATE(`date`) = CURDATE()";
            $stmt = $dbh->prepare($sql);
            
            // Bind parameters
            $stmt->bind_param("sii", $destPath, $slideIndex, $userID);

            
            // Execute the statement
            if ($stmt->execute()) {
                echo json_encode(array('message' => 'File uploaded and data saved successfully.', 'filePath' => $destPath));
            } else {
                echo json_encode(array('error' => 'Error saving data to the database.'));
            }

        } else {
            echo json_encode(array('error' => 'Error uploading file.'));
        }
    } else {
        echo json_encode(array('error' => 'No file sent.'));
    }
// } else {
//     echo json_encode(array('error' => 'Invalid request method.'));
// }
   
    $dbh = null;
} catch (PDOException $e) {
    print "Error!:" . $e->getMessage() . "<br/>";
    die();
}

?>
