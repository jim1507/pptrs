<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Display a loading message before redirecting
echo "
<!DOCTYPE html>
<html>
<head>
    <title>Logging Out...</title>
    <script>
        setTimeout(function() {
            window.location.href = '../login.php';
        }, 5000); // 5-second delay
    </script>
    <style>
        body {
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            background-color:rgb(50, 84, 118);
            font-family: Arial, sans-serif;
        }
        .loading-message {
            text-align: center;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class='loading-message'>
        <p>Logging out... Please wait.</p>
        <p><i class='mdi mdi-loading mdi-spin' style='font-size: 30px;'></i></p>
    </div>
</body>
</html>
";
exit();
?>
