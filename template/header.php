<?php
session_start();
include('config/db.php');

// Check if user is logged in
if (!isset($_SESSION['auth_user']['user_id'])) {
  header("Location: login.php");
  exit();
}

$role = $_SESSION['auth_user']['role'] ?? ''; // Fetch user role
$agent_id = $_SESSION['auth_user']['user_id'] ?? ''; // Fetch user ID
$team = $_SESSION['auth_user']['AgentTeam_ID'] ?? ''; // Fetch Agent Team ID (if applicable)

// Set a default page identifier if none is provided
$currentPage = basename($_SERVER['SCRIPT_NAME']);

// Define role-based page restrictions
$restrictionForRoles = [
  '1' => [], // Admin - No restrictions (Full Access)
  '2' => ['UserMGT.php','HousingMGT.php'], // Agent
  '0' => ['UserMGT.php', 'HousingMGT.php'], // Basic User
];

// Check if the current role has restrictions
if (isset($restrictionForRoles[$role])) {
  // Restrict access if the page is in the restricted list
  if (in_array($currentPage, $restrictionForRoles[$role])) {
    $_SESSION['bypassMSG'] = "You are not authorized to access this page with your role.";
    
    // Optionally log this action if you have a logging system
    // logAction($agent_id, "Unauthorized Access Attempt on $currentPage");

    // Redirect to an error/403 page
    header('Location: pages-404.php');
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Meta tags -->
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

  <title>Parakalan System</title>
  <link rel="icon" type="image/png" href="images/icon.png">

  <!-- CSS Assets -->
  <link rel="stylesheet" href="css/app.css">

  <!-- Javascript Assets -->
  <script src="js/app.js" defer></script>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
  
  <script>
    /**
     * THIS SCRIPT REQUIRED FOR PREVENT FLICKERING IN SOME BROWSERS
     */
    localStorage.getItem("_x_darkMode_on") === "true" &&
      document.documentElement.classList.add("dark");
  </script>
