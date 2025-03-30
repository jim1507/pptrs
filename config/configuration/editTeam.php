<?php
session_start();
include('../db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape user inputs for security
    $teamID = mysqli_real_escape_string($con, $_POST['team_id']);
    $teamName = mysqli_real_escape_string($con, $_POST['team_name']);
    $teamLeaderId = mysqli_real_escape_string($con, $_POST['team_leader']);

    // Validate inputs
    if (empty($teamID)) {
        $_SESSION['message'] = "ERROR: Team ID is required.";
        header("Location: ../../Team_Listing.php");
        exit();
    }

    if (empty($teamName)) {
        $_SESSION['message'] = "ERROR: Team name cannot be empty.";
        header("Location: ../../Team_Listing.php");
        exit();
    }

    if (empty($teamLeaderId)) {
        $_SESSION['message'] = "ERROR: Team leader must be selected.";
        header("Location: ../../Team_Listing.php");
        exit();
    }

    // Begin transaction
    mysqli_begin_transaction($con);

    try {
        // Step 1: Update team name in tbl_team
        $sqlUpdateTeam = "UPDATE tbl_team 
                          SET Team_Name = '$teamName' 
                          WHERE TeamID = $teamID";
        if (!mysqli_query($con, $sqlUpdateTeam)) {
            throw new Exception("Failed to update team name.");
        }

        // Step 2: Get current team leader (if any)
        $sqlGetCurrentLeader = "SELECT ac.AgentInfo_Id 
                               FROM tbl_acc_agent ac 
                               INNER JOIN tbl_agents_info ai ON ac.AgentInfo_Id = ai.Agent_infoID
                               WHERE ac.role = 2 AND ai.AgentTeam_ID = $teamID";
        $result = mysqli_query($con, $sqlGetCurrentLeader);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $currentLeaderID = $row['AgentInfo_Id'];

            // Only reset if the new leader is different from current leader
            if ($currentLeaderID != $teamLeaderId) {
                // Reset current leader's role to 0
                $sqlResetRole = "UPDATE tbl_acc_agent 
                                SET role = 0 
                                WHERE AgentInfo_Id = $currentLeaderID";
                if (!mysqli_query($con, $sqlResetRole)) {
                    throw new Exception("Failed to reset current leader role.");
                }
            }
        }

        // Step 3: Update new leader
        // First check if the selected leader exists
        $checkLeader = "SELECT Agent_infoID FROM tbl_agents_info WHERE Agent_infoID = $teamLeaderId";
        $leaderResult = mysqli_query($con, $checkLeader);
        
        if (!$leaderResult || mysqli_num_rows($leaderResult) == 0) {
            throw new Exception("Selected team leader does not exist.");
        }

        // Update new leader's role and team assignment
        // Update role in tbl_acc_agent
        $sqlUpdateLeaderRole = "UPDATE tbl_acc_agent 
                              SET role = 2 
                              WHERE AgentInfo_Id = $teamLeaderId";
        if (!mysqli_query($con, $sqlUpdateLeaderRole)) {
            throw new Exception("Failed to update new leader role.");
        }

        // Update team assignment in tbl_agents_info
        $sqlUpdateTeamAssignment = "UPDATE tbl_agents_info 
                                   SET AgentTeam_ID = $teamID 
                                   WHERE Agent_infoID = $teamLeaderId";
        if (!mysqli_query($con, $sqlUpdateTeamAssignment)) {
            throw new Exception("Failed to update team assignment for new leader.");
        }

        // Commit transaction
        mysqli_commit($con);

        $_SESSION['message'] = "Success: Team and team leader updated successfully.";
        header("Location: ../../Team_Listing.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($con);
        $_SESSION['message'] = "ERROR: " . $e->getMessage();
        header("Location: ../../Team_Listing.php");
        exit();
    }

    // Close connection
    mysqli_close($con);
}
?>