<?php
session_start();
require_once 'config.php';

// Patikrinkite, ar naudotojas yra prisijungęs ir ar jis yra pirkėjas
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'skaitytojas') {
    header("location: login.php");
    exit;
}

// Išgauti visus kūrinius iš kuriniai lentelės
$sql = "SELECT * FROM kuriniai";
$artworks = [];
if ($stmt = $conn->prepare($sql)) {
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $artworks[] = $row;
        }
        $stmt->close();
    }
}

// Išgauti visus pirkimus, kuriuos atliko prisijungęs naudotojas
$pirkejas_id = $_SESSION["id"];
$sql = "SELECT * FROM pardavimai WHERE pirkejas_id = ?";
$purchases = [];
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $pirkejas_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $purchases[] = $row;
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
    <title>Buyer Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?> (Buyer)</h1>
        <p><a href="logout.php" class="btn btn-danger">Logout</a></p>
        <h2>Artworks for Sale</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($artworks as $artwork): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($artwork["pavadinimas"]); ?></td>
                        <td><?php echo htmlspecialchars($artwork["aprasymas"]); ?></td>
                        <td><?php echo htmlspecialchars($artwork["kaina"]); ?></td>
                        <td>
                            <a href="buy_artwork.php?id=<?php echo $artwork["kurinys_id"]; ?>" class="btn btn-primary">Pirkti</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Your Purchases</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Artwork ID</th>
                    <th>Purchase Date</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($purchases as $purchase): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($purchase["pardavimas_id"]); ?></td>
                        <td><?php echo htmlspecialchars($purchase["kurinys_id"]); ?></td>
                        <td><?php echo htmlspecialchars($purchase["pirkimo_data"]); ?></td>
                        <td><?php echo htmlspecialchars($purchase["kaina"]); ?></td>
                        <td>
                            <a href="cancel_purchase.php?id=<?php echo $purchase["pardavimas_id"]; ?>" class="btn btn-danger">Cancel</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
