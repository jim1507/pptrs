<?php
session_start();
include "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$agent_id = $_SESSION['user_id'];
$success = false;
$errors = [];
$credentials_changed = false; // Flag to track if credentials were changed

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    // Get current user data for comparison
    $sql = "SELECT username FROM tbl_acc_agent WHERE Agent_accID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $agent_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_user = $result->fetch_assoc();
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Check if email is being changed
    if ($current_user['username'] !== $email) {
        $credentials_changed = true;
    }

    // Handle password change if provided
    if (!empty($password)) {
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $credentials_changed = true;
        }
    }

    // Handle file upload
    $avatar_path = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'parakalan/uploads/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Validate file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file_info, $_FILES['avatar']['tmp_name']);
        
        if (!in_array($mime_type, $allowed_types)) {
            $errors[] = "Only JPG, PNG, and GIF files are allowed";
        } elseif ($_FILES['avatar']['size'] > 2 * 1024 * 1024) { // 2MB max
            $errors[] = "File size must be less than 2MB";
        } else {
            // Generate unique filename
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $agent_id . '_' . time() . '.' . $ext;
            $target_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_path)) {
                $avatar_path = $filename;
            } else {
                $errors[] = "Failed to upload image";
            }
        }
    }

    // Update database if no errors
    if (empty($errors)) {
        try {
            // Start transaction
            $con->begin_transaction();
            
            // Update email in account table
            $sql = "UPDATE tbl_acc_agent SET username = ? WHERE Agent_accID = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("si", $email, $agent_id);
            $stmt->execute();
            
            // Update password if provided
            if (!empty($hashed_password)) {
                $sql = "UPDATE tbl_acc_agent SET password = ? WHERE Agent_accID = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("si", $hashed_password, $agent_id);
                $stmt->execute();
            }
            
            // Update avatar if uploaded
            if ($avatar_path) {
                $sql = "UPDATE tbl_agents_info SET IMAGEID = ? WHERE Agent_infoID = (
                    SELECT AgentInfo_Id FROM tbl_acc_agent WHERE Agent_accID = ?
                )";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("si", $avatar_path, $agent_id);
                $stmt->execute();
            }
            
            // Commit transaction
            $con->commit();
            $success = true;
            
            // Update session if email changed (but not if credentials were changed)
            if ($_SESSION['auth_user']['username'] !== $email && !$credentials_changed) {
                $_SESSION['auth_user']['username'] = $email;
            }
            
        } catch (Exception $e) {
            $con->rollback();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Store update status in session
$_SESSION['update_status'] = [
    'success' => $success,
    'errors' => $errors
];

// If credentials were changed, log out the user
if ($success && $credentials_changed) {
    // Clear all session variables
    $_SESSION = array();
    
    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page with success message
    header("Location: ../login.php?update=success");
    exit();
}

// Redirect back to profile page if no credential changes
header("Location: ../profile.php");
exit();
?>