<?php
session_start();
require_once 'config.php';

// Patikrinkite, ar naudotojas yra prisijungęs ir ar jis yra pirkėjas
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'skaitytojas') {
    header("location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $kurinys_id = $_GET['id'];
    $pirkejas_id = $_SESSION["id"];
    $kaina = 0;

    // Gauti kūrinio kainą
    $sql = "SELECT kaina FROM kuriniai WHERE kurinys_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $kurinys_id);
        $stmt->execute();
        $stmt->bind_result($kaina);
        $stmt->fetch();
        $stmt->close();
    }

    // Įterpti į pardavimai lentelę
    $sql = "INSERT INTO pardavimai (kurinys_id, pirkimo_data, kaina, pirkejas_id) VALUES (?, NOW(), ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("idi", $kurinys_id, $kaina, $pirkejas_id);
        if ($stmt->execute()) {
            header("location: pirkejas.php");
            exit;
        } else {
            echo "Error: Could not complete purchase.";
        }
        $stmt->close();
    }
}

$conn->close();
?>
