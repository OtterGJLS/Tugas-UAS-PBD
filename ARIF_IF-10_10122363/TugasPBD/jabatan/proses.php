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
                $stmt = $pdo->prepare("INSERT INTO jabatan (nama_jabatan) VALUES (?)");
                $stmt->execute([$_POST['nama_jabatan']]);
                $response = ['success' => true];
                break;
                
            case 'update':
                $stmt = $pdo->prepare("UPDATE jabatan SET nama_jabatan = ? WHERE id_jabatan = ?");
                $stmt->execute([$_POST['nama_jabatan'], $_POST['id_jabatan']]);
                $response = ['success' => true];
                break;
                
            case 'delete':
                // cek apakah jabatan masih memiliki pegawai
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM pegawai WHERE id_jabatan = ?");
                $stmt->execute([$_POST['id_jabatan']]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception('Tidak dapat menghapus jabatan yang masih memiliki pegawai');
                }
                
                $stmt = $pdo->prepare("DELETE FROM jabatan WHERE id_jabatan = ?");
                $stmt->execute([$_POST['id_jabatan']]);
                $response = ['success' => true];
                break;
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => $e->getMessage()];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
