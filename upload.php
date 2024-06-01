<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';

// Fungsi untuk mengunggah file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $user = $_SESSION['username'];
    $file = $_FILES['file'];
    $uploadDir = 'upload/users/' . $user . '/files/';
    $filePath = $uploadDir . basename($file['name']);
    $fileSize = $file['size'];

    // Pastikan direktori target ada, jika tidak, buat direktori baru
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Coba mengunggah file
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        $stmt = $conn->prepare("INSERT INTO uploads (username, filename, filepath, file_size) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $user, $file['name'], $filePath, $fileSize);
        if ($stmt->execute()) {
            // Redirect kembali ke index.php setelah unggah selesai
            header("Location: index.php?upload=success");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Gagal mengunggah file.";
    }
}

$conn->close();
?>

