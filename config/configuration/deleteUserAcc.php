<?php
session_start();
require_once "../db.php";

// Check if the request is POST and has an ID
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);

    // Start transaction
    mysqli_begin_transaction($con);

    try {
        // First, get the image path to delete the file later
        $imageQuery = "SELECT IMAGEID FROM tbl_agents_info WHERE Agent_infoID = '$id'";
        $imageResult = mysqli_query($con, $imageQuery);
        
        if (!$imageResult) {
            throw new Exception("Error fetching user image: " . mysqli_error($con));
        }
        
        $imageData = mysqli_fetch_assoc($imageResult);
        $imagePath = $imageData['IMAGEID'];

        // Delete from tbl_acc_agent first (due to foreign key constraint)
        $sql1 = "DELETE FROM tbl_acc_agent WHERE AgentInfo_Id = '$id'";
        
        if (!mysqli_query($con, $sql1)) {
            throw new Exception("Error deleting account: " . mysqli_error($con));
        }

        // Then delete from tbl_agents_info
        $sql = "DELETE FROM tbl_agents_info WHERE Agent_infoID = '$id'";
        
        if (!mysqli_query($con, $sql)) {
            throw new Exception("Error deleting agent info: " . mysqli_error($con));
        }

        // Commit transaction
        mysqli_commit($con);
        
        // Delete the image file if it's not the default
        if ($imagePath && $imagePath !== 'default.png') {
            $targetDir = "../../parakalan/uploads/";
            $fileToDelete = $targetDir . $imagePath;
            
            if (file_exists($fileToDelete)) {
                unlink($fileToDelete);
            }
        }
        
        $_SESSION['message'] = "Account deleted successfully!";
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
    // Redirect if accessed directly or missing ID
    $_SESSION['error'] = "Invalid request or missing ID.";
    header("Location: ../../UserMGT.php");
    exit();
}
?>