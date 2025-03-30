<?php
session_start();
require_once "../db.php";

// Define the uploads directory
$targetDir = "../../parakalan/uploads/";
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
$maxFileSize = 2 * 1024 * 1024; // 2MB

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstName = mysqli_real_escape_string($con, $_POST['first_name']);
    $lastName = mysqli_real_escape_string($con, $_POST['last_name']);
    $middleName = isset($_POST['middle_name']) ? mysqli_real_escape_string($con, $_POST['middle_name']) : '';
    $team = mysqli_real_escape_string($con, $_POST['team_name']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $role = mysqli_real_escape_string($con, $_POST['role']);
    
    // Initialize variables for file upload
    $logoPath = 'default.png'; // Default image

    // Check if file was uploaded at all
    if (!isset($_FILES['logo']) || $_FILES['logo']['error'] == UPLOAD_ERR_NO_FILE) {
        $_SESSION['error'] = "Profile image is required.";
        header("Location: ../../UserMGT.php");
        exit();
    }
    
    // Handle the uploaded image if provided
    if ($_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['logo']['tmp_name'];
        $fileName = $_FILES['logo']['name'];
        $fileSize = $_FILES['logo']['size'];
        $fileType = $_FILES['logo']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Validate file type
        if (!in_array($fileExtension, $allowedTypes)) {
            $_SESSION['error'] = " Only JPG, JPEG, PNG, and GIF files are allowed.";
            header("Location: ../../UserMGT.php");
            exit();
        }

        // Validate file size
        if ($fileSize > $maxFileSize) {
            $_SESSION['error'] = "E The uploaded file exceeds the maximum allowed size of 2MB.";
            header("Location: ../../UserMGT.php");
            exit();
        }

        // Generate a unique file name to prevent overwriting
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        
        // Ensure the uploads directory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $destPath = $targetDir . $newFileName;

        // Move the file to the destination directory
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $logoPath = $newFileName;
        } else {
            $_SESSION['error'] = "Failed to move the uploaded file.";
            header("Location: ../../UserMGT.php");
            exit();
        }
    } elseif ($_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle other upload errors
        switch ($_FILES['logo']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $_SESSION['error'] = " The uploaded file exceeds the maximum allowed size.";
                break;
            case UPLOAD_ERR_PARTIAL:
                $_SESSION['error'] = "The file was only partially uploaded.";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $_SESSION['error'] = "Missing temporary folder.";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $_SESSION['error'] = "Failed to write file to disk.";
                break;
            case UPLOAD_ERR_EXTENSION:
                $_SESSION['error'] = " A PHP extension stopped the file upload.";
                break;
            default:
                $_SESSION['error'] = "Unknown upload error occurred.";
        }
        header("Location: ../../UserMGT.php");
        exit();
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Start transaction
    mysqli_begin_transaction($con);

    try {
        // Insert into tbl_agents_info
        $sql = "INSERT INTO tbl_agents_info (FN, LN, MN, AgentTeam_ID, IMAGEID)
                VALUES ('$firstName', '$lastName', '$middleName', '$team', '$logoPath')";
        
        if (!mysqli_query($con, $sql)) {
            throw new Exception("Error inserting agent info: " . mysqli_error($con));
        }

        // Get the last inserted ID
        $info_id = mysqli_insert_id($con);

        // Insert into tbl_acc_agent
        $sql1 = "INSERT INTO tbl_acc_agent (AgentInfo_Id, username, password, role)
                 VALUES ('$info_id', '$username', '$hashedPassword', '$role')";
        
        if (!mysqli_query($con, $sql1)) {
            throw new Exception("Error creating account: " . mysqli_error($con));
        }

        // Commit transaction
        mysqli_commit($con);
        
        $_SESSION['message'] = "Account created successfully!";
        header("Location: ../../UserMGT.php");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($con);
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../../UserMGT.php");
        exit();
    }
} else {
    // Redirect if accessed directly
    $_SESSION['error'] = "Invalid request method.";
    header("Location: ../../UserMGT.php");
    exit();
}
?>