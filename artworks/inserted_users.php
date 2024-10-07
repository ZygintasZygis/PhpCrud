<?php
require_once 'config.php';

session_start(); // Pradėkite sesiją

// Naudotojų duomenys su atitinkamomis rolėmis
$users = [
    'menininkas' => ['password' => '1234', 'role' => 'skaitytojas'],
    'autorius' => ['password' => '1234', 'role' => 'redaguotojas'],
    'vadybininkas' => ['password' => '1234', 'role' => 'administratorius'],
    'pirkejas' => ['password' => '1234', 'role' => 'skaitytojas'],
    'adminas' => ['password' => '1234', 'role' => 'administratorius']
];

// Ištriname visus naudotojus, kad galėtume įterpti naujus
$sql = "DELETE FROM users";
if ($conn->query($sql) === TRUE) {
    echo "All existing users deleted successfully.<br>";
} else {
    echo "Error deleting users: " . $conn->error . "<br>";
}

foreach ($users as $username => $details) {
    // Užšifruoti slaptažodį
    $hashed_password = password_hash($details['password'], PASSWORD_DEFAULT);
    $role = $details['role'];

    // SQL užklausa įterpti naudotoją
    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("sss", $username, $hashed_password, $role);
        if($stmt->execute()){
            echo "User $username created successfully with role $role.<br>";

            // Nustatome sesijos kintamuosius kiekvienam naujai sukurtam naudotojui
            $_SESSION["username"] = $username;
            $_SESSION["role"] = $role;
            $_SESSION["id"] = $stmt->insert_id;

            // Patikriname, ar tik menininkas yra įtrauktas į autoriai lentelę
            if ($username == 'menininkas') {
                $author_id = $_SESSION["id"];
                $pavarde = $username; // Mokymosi tikslais naudojame username kaip pavarde
                $el_pastas = $username . "1@gmail.com"; // Formuojame el. pašto adresą

                $sql = "SELECT * FROM autoriai WHERE autorius_id = ?";
                if ($stmt2 = $conn->prepare($sql)) {
                    $stmt2->bind_param("i", $author_id);
                    $stmt2->execute();
                    $stmt2->store_result();
                    if ($stmt2->num_rows == 0) {
                        // Jei menininkas nėra įtrauktas, įtraukiame jį
                        $stmt2->close();
                        $sql = "INSERT INTO autoriai (autorius_id, vardas, pavarde, el_pastas) VALUES (?, ?, ?, ?)";
                        if ($stmt2 = $conn->prepare($sql)) {
                            $stmt2->bind_param("isss", $author_id, $username, $pavarde, $el_pastas);
                            $stmt2->execute();
                            $stmt2->close();
                            echo "Author added successfully.<br>";
                        } else {
                            echo "Error preparing statement: " . $conn->error . "<br>";
                        }
                    } else {
                        echo "Author already exists.<br>";
                    }
                } else {
                    echo "Error preparing statement: " . $conn->error . "<br>";
                }
            }
        } else {
            echo "Error: Could not create user $username. " . $conn->error . "<br>";
        }
        $stmt->close();
    }
}

$conn->close();
?>
