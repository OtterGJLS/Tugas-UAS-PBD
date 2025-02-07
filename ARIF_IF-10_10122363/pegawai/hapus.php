<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_pegawai = $_GET['id'] ?? 0;

try {
    $stmt = $pdo->prepare("DELETE FROM pegawai WHERE id_pegawai = ?");
    $stmt->execute([$id_pegawai]);
    
    header("Location: index.php");
    exit();
} catch (PDOException $e) {
    die("Error menghapus data: " . $e->getMessage());
}
?>
