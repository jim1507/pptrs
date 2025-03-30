<?php
session_start();
require_once "../db.php";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $userId = mysqli_real_escape_string($con, $_POST['user_id']);
    $firstName = mysqli_real_escape_string($con, $_POST['first_name']);
    $lastName = mysqli_real_escape_string($con, $_POST['last_name']);
    $middleName = isset($_POST['middle_name']) ? mysqli_real_escape_string($con, $_POST['middle_name']) : '';
    $teamName = mysqli_real_escape_string($con, $_POST['team_name']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $role = mysqli_real_escape_string($con, $_POST['role']);

    // Fetch the Team_ID based on the selected team name
    $teamQuery = "SELECT TeamID FROM tbl_team WHERE Team_Name = '$teamName'";
    $teamResult = mysqli_query($con, $teamQuery);

    if ($teamRow = mysqli_fetch_assoc($teamResult)) {
        $teamId = $teamRow['TeamID'];
    } else {
        $_SESSION['error'] = "Invalid team selected.";
        header("Location: ../../UserMGT.php");
        exit();
    }

    // Get current data before update
    $sqlCurrent = "SELECT a.FN, a.LN, a.MN, a.AgentTeam_ID, acc.username, acc.role 
                   FROM tbl_agents_info a
                   JOIN tbl_acc_agent acc ON a.Agent_infoID = acc.AgentInfo_Id
                   WHERE a.Agent_infoID = '$userId'";
    $result = mysqli_query($con, $sqlCurrent);
    $currentData = mysqli_fetch_assoc($result);

    // Build update query dynamically
    $updateFields = [];

    if ($firstName != $currentData['FN']) {
        $updateFields[] = "FN = '$firstName'";
    }
    if ($lastName != $currentData['LN']) {
        $updateFields[] = "LN = '$lastName'";
    }
    if ($middleName != $currentData['MN']) {
        $updateFields[] = "MN = '$middleName'";
    }
    if ($teamId != $currentData['AgentTeam_ID']) {
        $updateFields[] = "AgentTeam_ID = '$teamId'";
    }

    // Start transaction
    mysqli_begin_transaction($con);

    try {
        // Update only if there are changes
        if (!empty($updateFields)) {
            $updateSQL = "UPDATE tbl_agents_info 
                          SET " . implode(', ', $updateFields) . "
                          WHERE Agent_infoID = '$userId'";

            if (!mysqli_query($con, $updateSQL)) {
                throw new Exception("Error updating agent info: " . mysqli_error($con));
            }
        }

        // Check if username or role changed
        $updateAccFields = [];
        if ($username != $currentData['username']) {
            $updateAccFields[] = "username = '$username'";
        }
        if ($role != $currentData['role']) {
            $updateAccFields[] = "role = '$role'";
        }

        if (!empty($updateAccFields)) {
            $updateAccSQL = "UPDATE tbl_acc_agent 
                             SET " . implode(', ', $updateAccFields) . "
                             WHERE AgentInfo_Id = '$userId'";

            if (!mysqli_query($con, $updateAccSQL)) {
                throw new Exception("Error updating account: " . mysqli_error($con));
            }
        }

        // Commit transaction
        mysqli_commit($con);
        $_SESSION['message'] = "Account updated successfully!";
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
