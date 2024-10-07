<?php
session_start();

// Patikrinkite, ar naudotojas yra prisijungęs
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$role = $_SESSION["role"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
        }
        .wrapper {
            width: 600px;
            padding: 20px;
            margin: auto;
            margin-top: 100px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1 class="text-center">Welcome, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b></h1>
        <p class="text-center">Your role: <b><?php echo htmlspecialchars($role); ?></b></p>
        <div class="text-center">
            <?php if($role == 'administratorius'): ?>
                <p><a href="administratorius.php" class="btn btn-primary">Go to admin page</a></p>
            <?php elseif($role == 'skaitytojas'): ?>
                <p><a href="pirkejas.php" class="btn btn-primary">Go to pirkejas page</a></p>
            <?php elseif($role == 'vadybininkas'): ?>
                <p><a href="vadybininkas.php" class="btn btn-primary">Go to vadybininkas page</a></p>
            <?php elseif($role == 'redaguotojas'): ?>
                <p><a href="menininkas.php" class="btn btn-primary">Go to menininkas page</a></p>
            <?php endif; ?>
            <p><a href="logout.php" class="btn btn-danger">Logout</a></p>
        </div>
    </div>
</body>
</html>
