<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'event_management';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['signin'])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // 1. Check admin
    $stmt = $conn->prepare("SELECT id, name, password FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["admin_id"] = $id;
            $_SESSION["name"] = $name;
            header("Location: dashboard.php?page=dashboard");
            exit();
        }
    }

    $stmt->close();

    // 2. Check regular user
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["user_name"] = $name;
            header("Location: user_home.php"); // placeholder for now
            exit();
        } else {
            $_SESSION["login_error"] = "Incorrect password.";
        }
    } else {
        $_SESSION["login_error"] = "No admin or user account found.";
    }

    $stmt->close();
    header("Location: index.php");
    exit();
}
?>
