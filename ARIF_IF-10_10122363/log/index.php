<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Ambil log aktivitas
$stmt = $pdo->query("
    SELECT 
        l.*,
        CONCAT(p.nama_depan, ' ', p.nama_belakang) as nama_pegawai
    FROM log_aktivitas_pegawai l
    LEFT JOIN pegawai p ON l.id_pegawai = p.id_pegawai
    ORDER BY l.tanggal_aksi DESC
    LIMIT 100
");
$logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../layouts/navbar.php'; ?>

    <div class="container mt-4">
        <h2>Log Aktivitas Pegawai</h2>
        <p class="text-muted">Menampilkan 100 aktivitas terakhir</p>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pegawai</th>
                        <th>Aksi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($log['tanggal_aksi'])); ?></td>
                        <td><?php echo htmlspecialchars($log['nama_pegawai'] ?? '(Pegawai dihapus)'); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $log['aksi'] === 'INSERT' ? 'success' : 
                                    ($log['aksi'] === 'UPDATE' ? 'warning' : 'danger'); 
                            ?>">
                                <?php echo $log['aksi']; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($log['keterangan']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
