<?php
session_start();
include "db.php";

// Check if the user is already logged in
if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
    // Redirect to the appropriate dashboard based on the user's role
    if ($_SESSION['auth_role'] == 2) {
        header("Location: ../index.php");
        exit;
    } elseif ($_SESSION['auth_role'] == 0 || $_SESSION['auth_role'] == 1) {
        header("Location: ../index.php");
        exit;
    }
}

// Retrieve input values and sanitize
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Check if email and password are not empty
if (empty($email) || empty($password)) {
    $_SESSION['message-error'] = 'Email and password are required.';
    header('Location: ../login.php');
    exit();
}

// Prepare SQL statement to prevent SQL injection
$sql = "SELECT * FROM tbl_acc_agent INNER JOIN tbl_agents_info ON tbl_acc_agent.AgentInfo_Id = tbl_agents_info.Agent_infoID
LEFT JOIN tbl_team ON tbl_agents_info.AgentTeam_ID = tbl_team.TeamID WHERE username=?";
$stmt = $con->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Verify the password
    if (password_verify($password, $row['password'])) {
        // Set session variables
        $_SESSION["user_id"] = $row["Agent_accID"];
        $_SESSION['auth'] = true;
        $_SESSION['auth_role'] = $row["role"];
        $NameOfUser = $row['FN'] . ' ' . $row['LN'];
        
        $_SESSION['NameOfUser'] = $NameOfUser;
        $_SESSION['AgentTeam_ID'] = $row['AgentTeam_ID'];
        $_SESSION['Org'] = $row['Team_Name'];
        $_SESSION['auth_user'] = [
            'user_id' => $row["Agent_accID"],
            'org_id' => $row["TeamID "], // Add org_id to session
            'role' => $row["role"],
            'NameOfUser' => $NameOfUser,           
            'Org' => $row['Team_Name'],
            'Profile' => $row['IMAGEID'],
            'username'=> $row['username'],
        ];

        // Redirect based on role
        if ($row["role"] == 2) { // Assuming 2 is for admin
            header("Location: ../index.php");
        } elseif ($row["role"] == 0 || $row["role"] == 1) { // For roles 0 and 1
            header("Location: ../index.php");
        } else {
            // Default redirect for other roles
            header("Location: ../index.php");
        }
        exit();
    } else {
        // Invalid password
        $_SESSION['message-error'] = 'Invalid email or password';
        header('Location: ../login.php');
        exit();
    }
} else {
    // Invalid email
    $_SESSION['message-error'] = 'Invalid email or password';
    header('Location: ../login.php');
    exit();
}

$stmt->close();
$con->close();
?>
