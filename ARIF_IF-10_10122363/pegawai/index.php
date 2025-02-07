<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';

$stmt = $pdo->query("SELECT p.*, d.nama_departemen, j.nama_jabatan 
                     FROM pegawai p 
                     LEFT JOIN departemen d ON p.id_departemen = d.id_departemen
                     LEFT JOIN jabatan j ON p.id_jabatan = j.id_jabatan
                     ORDER BY p.id_pegawai");
$pegawai = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pegawai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../layouts/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col">
                <h2>Data Pegawai</h2>
            </div>
            <div class="col text-end">
                <a href="tambah.php" class="btn btn-primary">Tambah Pegawai</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Depan</th>
                        <th>Nama Belakang</th>
                        <th>Email</th>
                        <th>Departemen</th>
                        <th>Jabatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pegawai as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['id_pegawai']); ?></td>
                            <td><?php echo htmlspecialchars($p['nama_depan']); ?></td>
                            <td><?php echo htmlspecialchars($p['nama_belakang']); ?></td>
                            <td><?php echo htmlspecialchars($p['email']); ?></td>
                            <td><?php echo htmlspecialchars($p['nama_departemen']); ?></td>
                            <td><?php echo htmlspecialchars($p['nama_jabatan']); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $p['id_pegawai']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="hapus.php?id=<?php echo $p['id_pegawai']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
