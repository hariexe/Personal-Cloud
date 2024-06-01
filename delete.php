<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';

if (isset($_GET['filepath'])) {
    $filepath = $_GET['filepath'];
    $filename = basename($filepath);


    // Hapus file dari server
    if (file_exists($filepath)) {
        if (unlink($filepath)) {
            // Hapus file dari database
            $sql = "DELETE FROM uploads WHERE username = ? AND filename = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $_SESSION['username'], $filename);
            if ($stmt->execute()) {
                header("Location: index.php?delete=success");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "Gagal menghapus file.";
        }
    } else {
        echo "File tidak ditemukan.";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit();
}
?>
