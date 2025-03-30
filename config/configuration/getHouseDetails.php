<?php
require_once "../db.php";

if (isset($_GET['houseId'])) {
    $houseId = $_GET['houseId'];
    
    $query = "SELECT * FROM tbl_house WHERE HouseID = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $houseId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($house = mysqli_fetch_assoc($result)) {
        echo json_encode([
            'success' => true,
            'house' => $house
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'House not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No house ID provided'
    ]);
}
?>