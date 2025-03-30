<?php
session_start();
require_once "../db.php";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['house_id'])) {
    // Retrieve house ID to delete
    $houseId = mysqli_real_escape_string($con, $_POST['house_id']);

    // First, get the image path to delete the file
    $sqlGetImage = "SELECT Image FROM tbl_house WHERE HouseID = $houseId";
    $result = mysqli_query($con, $sqlGetImage);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $imagePath = $row['Image'];
        
        // Delete the image file if it exists and isn't the default image
        if (file_exists($imagePath) && $imagePath !== '../../uploads/default.png') {
            unlink($imagePath);
        }

        // Now delete the record from the database
        $sqlDelete = "DELETE FROM tbl_house WHERE HouseID = $houseId";
        
        if (mysqli_query($con, $sqlDelete)) {
            $_SESSION['message'] = "Success: House record deleted successfully.";
        } else {
            $_SESSION['message'] = "ERROR: Could not delete house record. " . mysqli_error($con);
        }
    } else {
        $_SESSION['message'] = "ERROR: House record not found.";
    }

    header("Location: ../../HousingMGT.php");
    exit();
} else {
    $_SESSION['message'] = "ERROR: Invalid request.";
    header("Location: ../../HousingMGT.php");
    exit();
}

// Close connection
mysqli_close($con);
?>