<?php
session_start();
require_once "../db.php";

// Define the uploads directory
$targetDir = "../../uploads/"; // Adjust the path as needed
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
$maxFileSize = 2 * 1024 * 1024; // 2MB

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $house_name = mysqli_real_escape_string($con, $_POST['house_name']);
    $price = mysqli_real_escape_string($con, $_POST['price']);
    $downpayment = mysqli_real_escape_string($con, $_POST['downpayment']);
    $property_condition = mysqli_real_escape_string($con, $_POST['property_condition']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $location = mysqli_real_escape_string($con, $_POST['location']);
    $house_type = mysqli_real_escape_string($con, $_POST['house_type']);
    $floor_area = mysqli_real_escape_string($con, $_POST['floor_area']);
    $bedrooms = mysqli_real_escape_string($con, $_POST['bedrooms']);
    $bathrooms = mysqli_real_escape_string($con, $_POST['bathrooms']);
    $avail = mysqli_real_escape_string($con, $_POST['avail']);

    // Handle the uploaded image
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['logo']['tmp_name'];
        $fileName = $_FILES['logo']['name'];
        $fileSize = $_FILES['logo']['size'];
        $fileType = $_FILES['logo']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Validate file type
        if (in_array($fileExtension, $allowedTypes)) {
            // Validate file size
            if ($fileSize <= $maxFileSize) {
                // Generate a unique file name to prevent overwriting
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

                // Ensure the uploads directory exists
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }

                $destPath = $targetDir . $newFileName;

                // Move the file to the target directory
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    // File upload successful, prepare to insert into the database
                    // Store the relative path to the uploaded file
                    $logoPath = '../../uploads/' . $newFileName;
                } else {
                    // Failed to move the file
                    $_SESSION['message'] = "Error: There was an error moving the uploaded file.";
                    header("Location: ../../HousingMGT.php");
                    exit();
                }
            } else {
                // File size exceeds the limit
                $_SESSION['message'] = "Error: The uploaded file exceeds the maximum allowed size of 2MB.";
                header("Location: ../../HousingMGT.php");
                exit();
            }
        } else {
            // Invalid file type
            $_SESSION['message'] = "Error: Only JPG, JPEG, PNG, and GIF files are allowed.";
            header("Location: ../../HousingMGT.php");
            exit();
        }
    } else {
        // No file uploaded or there was an upload error
        $_SESSION['message'] = "Error: Please upload a logo image.";
        header("Location: ../../HousingMGT.php");
        exit();
    }

    // Attempt insert query execution
    $sql = "INSERT INTO  tbl_house (HouseName,Price,DownPayment,HouseCondition,	Description,	HouseType,	HouseSize,	BathNum,	RoomNum,    Location,	Image, unit_available) 
            VALUES (' $house_name','$price','$downpayment','$property_condition','$description','$house_type','$floor_area','$bathrooms','$bedrooms','$location','$logoPath','$avail')";
    if (mysqli_query($con, $sql)) {
        // Department added successfully, redirect to a success page
        $_SESSION['message'] = "Success: Department added successfully.";
        header("Location: ../../HousingMGT.php");
        exit(); // Ensure that script execution stops after redirection
    } else {
        // Store error message in the session
        $_SESSION['message'] = "ERROR: Could not execute query: $sql. " . mysqli_error($con);
        header("Location: ../../HousingMGT.php");
        exit();
    }
    
    // Close connection
    mysqli_close($con);
}
?>
