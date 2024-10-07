<?php
require_once 'config.php';
session_start();

// Patikrinkite, ar naudotojas yra prisijungęs
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$kurinys_id = $_GET["id"];
$author_id = $_SESSION["id"];
$role = $_SESSION["role"];

// Patikrinkite, ar naudotojas yra kūrinio autorius arba vadybininkas
$sql = "SELECT autorius_id FROM kuriniai WHERE kurinys_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $kurinys_id);
    $stmt->execute();
    $stmt->bind_result($db_author_id);
    $stmt->fetch();
    $stmt->close();
}

if ($author_id != $db_author_id && $role != 'administratorius') {
    echo "Neturite teisės atlikti šį veiksmą.";
    exit();
}

// Pranešimo kintamasis
$message = "";

// Tvarkyti formos pateikimą
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $price = trim($_POST["price"]);

    // Patikrinkite, ar įvesti duomenys yra tinkami
    if (empty($title) || empty($price)) {
        $message = "Title and price are required.";
    } elseif (!is_numeric($price)) {
        $message = "Price must be a number.";
    } else {
        // Atnaujinti kūrinį duomenų bazėje
        $sql = "UPDATE kuriniai SET pavadinimas = ?, aprasymas = ?, kaina = ? WHERE kurinys_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssdi", $title, $description, $price, $kurinys_id);
            if ($stmt->execute()) {
                $message = "Artwork updated successfully.";
            } else {
                $message = "Error: Could not update artwork.";
            }
            $stmt->close();
        }
    }
}

// Išgauti kūrinio informaciją
$sql = "SELECT pavadinimas, aprasymas, kaina FROM kuriniai WHERE kurinys_id = ?";
$artwork = [];
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $kurinys_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $artwork = $result->fetch_assoc();
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Artwork</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            margin-top: 0;
            color: #333;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 8px;
            margin: 5px 0 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #4cae4c;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            background: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Artwork</h1>
        <form action="edit_artwork.php?id=<?php echo $kurinys_id; ?>" method="post">
            <div>
                <label>Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($artwork['pavadinimas']); ?>">
            </div>
            <div>
                <label>Description</label>
                <textarea name="description"><?php echo htmlspecialchars($artwork['aprasymas']); ?></textarea>
            </div>
            <div>
                <label>Price</label>
                <input type="text" name="price" value="<?php echo htmlspecialchars($artwork['kaina']); ?>">
            </div>
            <div>
                <input type="submit" value="Update Artwork">
            </div>
        </form>
        <p class="message"><?php echo $message; ?></p>
    </div>
</body>
</html>
