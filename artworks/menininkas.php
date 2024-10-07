<?php
session_start();
require_once 'config.php';

// Patikrinkite, ar naudotojas yra prisijungęs ir ar jis yra menininkas
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'redaguotojas') {
    header("location: login.php");
    exit;
}

// Pranešimo kintamasis
$message = isset($_GET['message']) ? $_GET['message'] : "";

// Tvarkyti formos pateikimą
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $price = trim($_POST["price"]);
    $author_id = $_SESSION["id"]; // Naudojame prisijungusio menininko ID
    $username = $_SESSION["username"];

    // Patikrinkite, ar įvesti duomenys yra tinkami
    if (empty($title) || empty($price)) {
        $message = "Title and price are required.";
    } elseif (!is_numeric($price)) {
        $message = "Price must be a number.";
    } else {
        // Patikrinkite, ar menininkas jau yra autorius
        $sql = "SELECT * FROM autoriai WHERE autorius_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $author_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 0) {
                // Jei menininkas nėra įtrauktas, įtraukiame jį
                $stmt->close();
                $pavarde = $username; // Mokymosi tikslais naudojame username kaip pavarde
                $el_pastas = $username . "1@gmail.com"; // Formuojame el. pašto adresą
                $sql = "INSERT INTO autoriai (autorius_id, vardas, pavarde, el_pastas) VALUES (?, ?, ?, ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("isss", $author_id, $username, $pavarde, $el_pastas);
                    $stmt->execute();
                    $stmt->close();
                }
            } else {
                $stmt->close();
            }
        }

        // Įterpti naują kūrinį į duomenų bazę
        $sql = "INSERT INTO kuriniai (pavadinimas, aprasymas, kaina, autorius_id) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssdi", $title, $description, $price, $author_id);
            if ($stmt->execute()) {
                $message = "Artwork added successfully.";
            } else {
                $message = "Error: Could not add artwork.";
            }
            $stmt->close();
        }
    }
}

// Išgauti visus prisijungusio menininko kūrinius
$sql = "SELECT * FROM kuriniai WHERE autorius_id = ?";
$artworks = [];
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $_SESSION["id"]);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $artworks[] = $row;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Artist Dashboard</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?> (Artist)</h1>
        <p><a href="logout.php">Logout</a></p>
        <h2>Add New Artwork</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Title</label>
                <input type="text" name="title">
            </div>
            <div>
                <label>Description</label>
                <textarea name="description"></textarea>
            </div>
            <div>
                <label>Price</label>
                <input type="text" name="price">
            </div>
            <div>
                <input type="submit" value="Add Artwork">
            </div>
        </form>
        <p><?php echo $message; ?></p>
        <h2>Your Artworks</h2>
        <table>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($artworks as $artwork): ?>
                <tr>
                    <td><?php echo htmlspecialchars($artwork["pavadinimas"]); ?></td>
                    <td><?php echo htmlspecialchars($artwork["aprasymas"]); ?></td>
                    <td><?php echo htmlspecialchars($artwork["kaina"]); ?></td>
                    <td>
                        <a href="edit_artwork.php?id=<?php echo $artwork["kurinys_id"]; ?>">Edit</a>
                        <a href="delete_artwork.php?id=<?php echo $artwork["kurinys_id"]; ?>">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
