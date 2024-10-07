<?php
session_start();
require_once 'config.php';

// Patikrinkite, ar naudotojas yra prisijungęs
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Patikrinkite, ar pateiktas kūrinio ID
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $artwork_id = $_GET["id"];
    $author_id = $_SESSION["id"];

    // Patikrinkite, ar prisijungęs naudotojas yra kūrinio autorius
    $sql = "SELECT autorius_id FROM kuriniai WHERE kurinys_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $artwork_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($db_author_id);
        $stmt->fetch();

        if ($stmt->num_rows == 1 && $db_author_id == $author_id) {
            $stmt->close();

            // Ištrinkite kūrinį iš duomenų bazės
            $sql = "DELETE FROM kuriniai WHERE kurinys_id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $artwork_id);
                if ($stmt->execute()) {
                    $message = "Kūrinys sėkmingai ištrintas.";
                } else {
                    $message = "Klaida: Nepavyko ištrinti kūrinio.";
                }
                $stmt->close();
            } else {
                $message = "Klaida ruošiant užklausą.";
            }
        } else {
            $message = "Neturite teisės atlikti šį veiksmą.";
        }
    } else {
        $message = "Klaida ruošiant užklausą.";
    }
} else {
    $message = "Neteisingas užklausos formatas.";
}

$conn->close();
header("location: menininkas.php?message=" . urlencode($message));
exit;
?>
