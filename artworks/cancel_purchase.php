<?php
session_start();
require_once 'config.php';

// Patikrinkite, ar naudotojas yra prisijungęs ir ar jis yra pirkėjas
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'skaitytojas') {
    header("location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $pardavimas_id = $_GET['id'];
    $pirkejas_id = $_SESSION["id"];

    // Patikrinkite, ar pirkimas priklauso prisijungusiam pirkėjui
    $sql = "SELECT pirkejas_id FROM pardavimai WHERE pardavimas_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $pardavimas_id);
        $stmt->execute();
        $stmt->bind_result($db_pirkejas_id);
        $stmt->fetch();
        $stmt->close();
    }

    if ($pirkejas_id == $db_pirkejas_id) {
        // Ištrinti pirkimą iš pardavimai lentelės
        $sql = "DELETE FROM pardavimai WHERE pardavimas_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $pardavimas_id);
            if ($stmt->execute()) {
                header("location: pirkejas.php");
                exit;
            } else {
                echo "Error: Could not cancel purchase.";
            }
            $stmt->close();
        }
    } else {
        echo "Error: Unauthorized action.";
    }
}

$conn->close();
?>
