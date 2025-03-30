<?php
session_start();
require_once "../db.php";

header('Content-Type: application/json');  // Ensure JSON output
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$role = $_SESSION['auth_user']['role'];
  $agent_id =  $_SESSION["user_id"];
  $team = $_SESSION['AgentTeam_ID'];

// Get the start of the current week (Monday) and the end of the current week (Sunday)
$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));

$sql = "";

// Role 1 - Sales Summary for All Houses
if ($role == 1) {
    $sql = "
   SELECT 
    DATE(p.date_sold) AS date, 
    COUNT(*) AS num_of_sales
FROM 
    house_purchase p 
    INNER JOIN tbl_house h ON p.house_id = h.HouseID
    LEFT JOIN tbl_customer c ON p.customer_id = c.customer_id
    LEFT JOIN tbl_agents_info ai ON p.agentID = ai.Agent_infoID
    LEFT JOIN tbl_acc_agent a ON ai.Agent_infoID = a.AgentInfo_Id
    LEFT JOIN tbl_team t ON ai.AgentTeam_ID = t.TeamID
WHERE 
    p.date_sold BETWEEN '$startOfWeek' AND '$endOfWeek'
GROUP BY 
    DATE(p.date_sold)
ORDER BY 
    DATE(p.date_sold)
    ";
}

// Role 2 - Sales Summary Partitioned by Agent Team
elseif ($role == 2) {
    $sql = "
   SELECT 
    DATE(p.date_sold) AS date, 
    COUNT(*) AS num_of_sales
FROM 
    house_purchase p 
    INNER JOIN tbl_house h ON p.house_id = h.HouseID
    LEFT JOIN tbl_customer c ON p.customer_id = c.customer_id
    LEFT JOIN tbl_agents_info ai ON p.agentID = ai.Agent_infoID
    LEFT JOIN tbl_acc_agent a ON ai.Agent_infoID = a.AgentInfo_Id
    LEFT JOIN tbl_team t ON ai.AgentTeam_ID = t.TeamID
WHERE 
    p.date_sold BETWEEN '$startOfWeek' AND '$endOfWeek' AND ai.AgentTeam_ID = $team
GROUP BY 
    DATE(p.date_sold), ai.AgentTeam_ID
ORDER BY 
    DATE(p.date_sold)
    ";
}

// Default - Sales Summary for Specific Agent
else {
    $sql = "
   SELECT 
    DATE(p.date_sold) AS date, 
    COUNT(*) AS num_of_sales
FROM 
    house_purchase p 
    INNER JOIN tbl_house h ON p.house_id = h.HouseID
    LEFT JOIN tbl_customer c ON p.customer_id = c.customer_id
    LEFT JOIN tbl_agents_info ai ON p.agentID = ai.Agent_infoID
    LEFT JOIN tbl_acc_agent a ON ai.Agent_infoID = a.AgentInfo_Id
    LEFT JOIN tbl_team t ON ai.AgentTeam_ID = t.TeamID
WHERE 
    p.date_sold BETWEEN '$startOfWeek' AND '$endOfWeek' AND a.Agent_accID = '$agent_id'
GROUP BY 
    DATE(p.date_sold), a.Agent_accID
ORDER BY 
    DATE(p.date_sold)
    ";
}



// Execute the query
$result = $con->query($sql);

// Fetch the data and prepare it for JSON output
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($data);

// Close connection
$con->close();
?>