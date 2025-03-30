<?php
session_start();
include('../db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape and sanitize user inputs
    $teamName = mysqli_real_escape_string($con, $_POST['team_name']);
    $teamleader = mysqli_real_escape_string($con, $_POST['team_leader']);

    // Start transaction to ensure consistency
    mysqli_begin_transaction($con);

    try {
        // ✅ Insert new team into tbl_team
        $sql = "INSERT INTO tbl_team (Team_Name) VALUES ('$teamName')";
        if (!mysqli_query($con, $sql)) {
            throw new Exception("Error inserting new team: " . mysqli_error($con));
        }

        // ✅ Get the last inserted TeamID
        $newTeamID = mysqli_insert_id($con);

        // ✅ Update the team leader's role to 2 in tbl_acc_agent
        $updateLeaderSQL = "
            UPDATE tbl_agents_info a
            JOIN tbl_acc_agent aa ON aa.AgentInfo_Id = a.Agent_infoID
            SET a.AgentTeam_ID = $newTeamID, aa.role = 2
            WHERE a.Agent_infoID = $teamleader
        ";

        if (!mysqli_query($con, $updateLeaderSQL)) {
            throw new Exception("Error updating team leader role: " . mysqli_error($con));
        }

        // ✅ Commit the transaction if everything is successful
        mysqli_commit($con);

        // ✅ Success message and redirect
        $_SESSION['message'] = "Success: Team and leader updated successfully.";
        header("Location: ../../Team_Listing.php");
        exit();
    } catch (Exception $e) {
        // ❌ Rollback in case of error
        mysqli_rollback($con);
        $_SESSION['message'] = "Error: " . $e->getMessage();
        header("Location: ../../Team_Listing.php");
        exit();
    }

    // Close connection
    mysqli_close($con);
}
?>
