<?php
session_start();
require_once "../db.php";

// Define the uploads directory
$targetDir = "../../uploads/"; // Adjust path if necessary
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
$maxFileSize = 2 * 1024 * 1024; // 2MB

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $houseId = mysqli_real_escape_string($con, $_POST['house_id']);
    
    // Get current house data from database
    $sqlGetCurrent = "SELECT * FROM tbl_house WHERE HouseID = $houseId";
    $result = mysqli_query($con, $sqlGetCurrent);
    $currentData = mysqli_fetch_assoc($result);
    
    // Initialize update fields array
    $updateFields = array();
    
    // Check each field for changes
    $fieldsToCheck = [
        'house_name' => 'HouseName',
        'price' => 'Price',
        'downpayment' => 'DownPayment',
        'property_condition' => 'HouseCondition',
        'description' => 'Description',
        'house_type' => 'HouseType',
        'floor_area' => 'HouseSize',
        'bedrooms' => 'RoomNum',
        'bathrooms' => 'BathNum',
        'location' => 'Location',
        'avail' => 'unit_available'
    ];
    
    foreach ($fieldsToCheck as $postField => $dbField) {
        $newValue = mysqli_real_escape_string($con, $_POST[$postField]);
        if ($newValue != $currentData[$dbField]) {
            $updateFields[$dbField] = $newValue;
        }
    }
    
    // Handle image upload if a new file was uploaded
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
                // Generate unique file name to prevent overwriting
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

                // Ensure uploads directory exists
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }

                $destPath = $targetDir . $newFileName;

                // Move the file to the target directory
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    // File uploaded successfully, store path
                    $logoPath = '../../uploads/' . $newFileName;
                    $updateFields['Image'] = $logoPath;

                    // Delete the previous image if it exists
                    $oldImage = $currentData['Image'];
                    if (file_exists($oldImage) && $oldImage !== '../../uploads/default.png') {
                        unlink($oldImage); // Delete old image
                    }
                } else {
                    $_SESSION['message'] = "Error: There was an error moving the uploaded file.";
                    header("Location: ../../HousingMGT.php");
                    exit();
                }
            } else {
                $_SESSION['message'] = "Error: The uploaded file exceeds the maximum allowed size of 2MB.";
                header("Location: ../../HousingMGT.php");
                exit();
            }
        } else {
            $_SESSION['message'] = "Error: Only JPG, JPEG, PNG, and GIF files are allowed.";
            header("Location: ../../HousingMGT.php");
            exit();
        }
    }
    
    // Only proceed with update if there are changes
    if (!empty($updateFields)) {
        // Build the update query
        $setParts = array();
        foreach ($updateFields as $field => $value) {
            $setParts[] = "$field = '$value'";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE tbl_house SET $setClause WHERE HouseID = $houseId";
        
        // Execute query
        if (mysqli_query($con, $sql)) {
            $_SESSION['message'] = "Success: House information updated successfully.";
        } else {
            $_SESSION['message'] = "ERROR: Could not execute query: " . mysqli_error($con);
        }
    } else {
        $_SESSION['message'] = "Info: No changes were made to the house information.";
    }
    
    header("Location: ../../HousingMGT.php");
    exit();
}

// Close connection
mysqli_close($con);
?>