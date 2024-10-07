<?php
require_once 'config.php';
session_start();

// Patikrinkite, ar naudotojas yra prisijungęs ir ar jis yra administratorius
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'administratorius') {
    header("location: login.php");
    exit;
}

// Pranešimo kintamasis
$message = isset($_GET['message']) ? $_GET['message'] : "";

// Ištrinti naudotoją
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $message = "User deleted successfully.";
        } else {
            $message = "Error deleting user.";
        }
        $stmt->close();
    }
}

// Tvarkyti formos pateikimą
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $username = trim($_POST["username"]);
    $role = trim($_POST["role"]);

    // Atlikti atnaujinimą
    $sql = "UPDATE users SET username = ?, role = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $username, $role, $edit_id);
        if ($stmt->execute()) {
            $message = "User updated successfully.";
        } else {
            $message = "Error updating user.";
        }
        $stmt->close();
    }
}

// Išgauti visus naudotojus
$sql = "SELECT id, username, role, created_at FROM users";
$users = [];
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $result->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Administrator Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
        }
        .container {
            margin-top: 50px;
        }
        .message {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Users Management</h2>
        <p><?php echo htmlspecialchars($message); ?></p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <form action="administratorius.php" method="post">
                            <td><?php echo htmlspecialchars($user["id"]); ?></td>
                            <td><input type="text" name="username" value="<?php echo htmlspecialchars($user["username"]); ?>" class="form-control"></td>
                            <td><input type="text" name="role" value="<?php echo htmlspecialchars($user["role"]); ?>" class="form-control"></td>
                            <td><?php echo htmlspecialchars($user["created_at"]); ?></td>
                            <td>
                                <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($user["id"]); ?>">
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                <a href="administratorius.php?delete_id=<?php echo $user["id"]; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="logout.php" class="btn btn-secondary">Logout</a>
    </div>
</body>
</html>
