<?php
session_start();
include('../db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape user inputs for security
    $teamID = mysqli_real_escape_string($con, $_POST['team_id']);

    // ✅ Check if team ID is provided
    if (!empty($teamID)) {
        // ✅ Step 1: Check if the team exists
        $sqlCheck = "SELECT TeamID FROM tbl_team WHERE TeamID = $teamID";
        $result = mysqli_query($con, $sqlCheck);

        if (mysqli_num_rows($result) > 0) {
            // ✅ Step 2: Delete the team
            $sqlDelete = "DELETE FROM tbl_team WHERE TeamID = $teamID";
            if (mysqli_query($con, $sqlDelete)) {
                $_SESSION['message'] = "Success: Team deleted successfully.";
            } else {
                $_SESSION['message'] = "ERROR: Could not delete team. " . mysqli_error($con);
            }
        } else {
            $_SESSION['message'] = "ERROR: Team not found.";
        }
    } else {
        $_SESSION['message'] = "ERROR: Team ID cannot be empty.";
    }

    // ✅ Redirect back to Org.php
    header("Location: ../../Team_Listing.php");
    exit();
}
?>
