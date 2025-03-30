<?php
session_start();
require_once "../db.php";

// Debugging: Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Define the uploads directory
    $targetDir = "../../uploads/";
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    // Function to handle file uploads
    function handleFileUpload($fileKey, $targetDir, $allowedTypes, $maxFileSize) {
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES[$fileKey]['tmp_name'];
            $fileName = $_FILES[$fileKey]['name'];
            $fileSize = $_FILES[$fileKey]['size'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedTypes)) {
                return [false, "Error: Only JPG, JPEG, PNG, and GIF files are allowed."];
            }
            if ($fileSize > $maxFileSize) {
                return [false, "Error: The uploaded file exceeds the maximum allowed size of 2MB."];
            }

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $destPath = $targetDir . $newFileName;

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                return [true, '../../uploads/' . $newFileName];
            } else {
                return [false, "Error: There was an error moving the uploaded file."];
            }
        }
        return [false, NULL]; // Return NULL if no file uploaded
    }

    // Retrieve form data
    $house_id = mysqli_real_escape_string($con, $_POST['house_id']);
    $fn = mysqli_real_escape_string($con, $_POST['fname']);
    $ln = mysqli_real_escape_string($con, $_POST['lname']);
    $mn = mysqli_real_escape_string($con, $_POST['mname']);
    $dob = $_POST['dob'] ?? NULL;
    $civilStatus = $_POST['civil_status'] ?? NULL;
    $nationality = $_POST['nationality'] ?? NULL;
    $contactNumber = $_POST['contact_number'] ?? NULL;
    $address = $_POST['address'] ?? NULL;
    $occupation = $_POST['occupation'] ?? NULL;
    $employer = $_POST['employer'] ?? NULL;
    $coBorrowerName = $_POST['co_borrower_name'] ?? NULL;
    $relationship = $_POST['relationship'] ?? NULL;
    $paymentMode = $_POST['payment_mode'] ?? NULL;
    $preferredBank = $_POST['preferred_bank'] ?? NULL;
    $tin = $_POST['tin'] ?? NULL;
    $salary = $_POST['salary'] ?? NULL;
    $co_borrower_name_work = $_POST['co_borrower_name_work'] ?? NULL;
    $co_borrower_company = $_POST['co_borrower_company'] ?? NULL;
    $co_borrower_salary = $_POST['co_borrower_salary'] ?? NULL;
    $agent = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

    // Handle uploaded files
    list($successMarriageCert, $marriageCert) = handleFileUpload('marriage_certificate', $targetDir, $allowedTypes, $maxFileSize);
    list($successBillingProof, $billingProof) = handleFileUpload('proof_of_billing', $targetDir, $allowedTypes, $maxFileSize);

    // Set marriage certificate to NULL if no file is uploaded
    if (!$successMarriageCert) {
        $marriageCert = NULL;
    }

    // Handle file upload errors only for required files (billing proof is required)
    if (!$successBillingProof && $billingProof === NULL) {
        $_SESSION['message'] = "❌ Error: Proof of billing is required.";
        header("Location: ../../HousePortal.php");
        exit();
    }

    // 1️⃣ Insert into tbl_customer first
    $sql_customer = "INSERT INTO tbl_customer 
                    (FN,LN,MN, dob, civil_status, nationality, contact_number, address) 
                    VALUES (?,?,?, ?, ?, ?, ?, ?)";

    $stmt_customer = mysqli_prepare($con, $sql_customer);
    mysqli_stmt_bind_param($stmt_customer, "ssssssss", 
        $fn,$ln,$mn, $dob, $civilStatus, $nationality, $contactNumber, $address);

    if (mysqli_stmt_execute($stmt_customer)) {
        // Get the generated customer_id
        $customer_id = mysqli_insert_id($con);

        // 2️⃣ Insert into house_purchase after getting customer_id
        $sql_house = "INSERT INTO house_purchase 
                    (house_id, customer_id, occupation, employer, co_borrower_name, relationship, 
                    payment_mode, preferred_bank, tin, agentID, marriage_certificate, proof_of_billing, 
                    co_borrower_name_work, co_borrower_company, co_borrower_salary, salary) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt_house = mysqli_prepare($con, $sql_house);
        mysqli_stmt_bind_param($stmt_house, "iissssssssssssss", 
            $house_id, $customer_id, $occupation, $employer, $coBorrowerName, $relationship,
            $paymentMode, $preferredBank, $tin, $agent, $marriageCert, $billingProof,
            $co_borrower_name_work, $co_borrower_company, $co_borrower_salary, $salary);

        // Execute house_purchase insertion
        if (mysqli_stmt_execute($stmt_house)) {
            // 3️⃣ Deduct 1 from unit_available in tbl_house
            $sql_update_house = "UPDATE tbl_house SET unit_available = unit_available - 1 WHERE HouseID  = ?";
            $stmt_update_house = mysqli_prepare($con, $sql_update_house);
            mysqli_stmt_bind_param($stmt_update_house, "i", $house_id);

            if (mysqli_stmt_execute($stmt_update_house)) {
                $_SESSION['message'] = "✅ Success: House purchase details added successfully, and unit availability updated.";
                header("Location: ../../HousePortal.php");
            } else {
                $_SESSION['message'] = "⚠️ Warning: House purchased successfully, but unit availability could not be updated.";
                header("Location: ../../HousePortal.php");
            }
        } else {
            $_SESSION['message'] = "❌ ERROR: Could not execute house_purchase query: " . mysqli_error($con);
            header("Location: ../../HousePortal.ph p");
        }
    } else {
        $_SESSION['message'] = "❌ ERROR: Could not insert customer data: " . mysqli_error($con);
        header("Location: ../../HousePortal.php");
    }

    // Close the statement and connection
    mysqli_stmt_close($stmt_customer);
    mysqli_stmt_close($stmt_house);
    mysqli_stmt_close($stmt_update_house);
    mysqli_close($con);
    exit();
}
?>
