<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$response = ['success' => false, 'message' => 'Invalid action'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'create':
                $stmt = $pdo->prepare("INSERT INTO departemen (nama_departemen) VALUES (?)");
                $stmt->execute([$_POST['nama_departemen']]);
                $response = ['success' => true];
                break;
                
            case 'update':
                $stmt = $pdo->prepare("UPDATE departemen SET nama_departemen = ? WHERE id_departemen = ?");
                $stmt->execute([$_POST['nama_departemen'], $_POST['id_departemen']]);
                $response = ['success' => true];
                break;
                
            case 'delete':
                // Check if department has employees
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM pegawai WHERE id_departemen = ?");
                $stmt->execute([$_POST['id_departemen']]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception('Tidak dapat menghapus departemen yang masih memiliki pegawai');
                }
                
                $stmt = $pdo->prepare("DELETE FROM departemen WHERE id_departemen = ?");
                $stmt->execute([$_POST['id_departemen']]);
                $response = ['success' => true];
                break;
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => $e->getMessage()];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
