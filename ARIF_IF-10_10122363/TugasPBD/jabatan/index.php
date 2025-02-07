<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM jabatan ORDER BY id_jabatan ASC");
$jabatan = $stmt->fetchAll();

// Hitung jumlah pegawai per jabatan
$stmt = $pdo->query("
    SELECT j.id_jabatan, COUNT(p.id_pegawai) as jumlah_pegawai
    FROM jabatan j
    LEFT JOIN pegawai p ON j.id_jabatan = p.id_jabatan
    GROUP BY j.id_jabatan
    ORDER BY j.id_jabatan ASC
");
$jumlah_pegawai = [];
while ($row = $stmt->fetch()) {
    $jumlah_pegawai[$row['id_jabatan']] = $row['jumlah_pegawai'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Jabatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../layouts/navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Data Jabatan</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahJabatan">
                Tambah Jabatan
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Jabatan</th>
                        <th>Jumlah Pegawai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jabatan as $jab): ?>
                    <tr>
                        <td><?php echo $jab['id_jabatan']; ?></td>
                        <td><?php echo htmlspecialchars($jab['nama_jabatan']); ?></td>
                        <td><?php echo $jumlah_pegawai[$jab['id_jabatan']] ?? 0; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editJabatan(<?php echo $jab['id_jabatan']; ?>, '<?php echo htmlspecialchars($jab['nama_jabatan']); ?>')">
                                Ubah
                            </button>
                            <?php if (($jumlah_pegawai[$jab['id_jabatan']] ?? 0) == 0): ?>
                            <button class="btn btn-sm btn-danger" onclick="if(confirm('Apakah Anda yakin ingin menghapus jabatan ini?')) hapusJabatan(<?php echo $jab['id_jabatan']; ?>)">
                                Hapus
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="tambahJabatan" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTambah" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_jabatan" class="form-label">Nama Jabatan</label>
                            <input type="text" class="form-control" id="nama_jabatan" name="nama_jabatan" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editJabatan" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEdit" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_jabatan" name="id_jabatan">
                        <div class="mb-3">
                            <label for="edit_nama_jabatan" class="form-label">Nama Jabatan</label>
                            <input type="text" class="form-control" id="edit_nama_jabatan" name="nama_jabatan" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editJabatan(id, nama) {
            document.getElementById('edit_id_jabatan').value = id;
            document.getElementById('edit_nama_jabatan').value = nama;
            new bootstrap.Modal(document.getElementById('editJabatan')).show();
        }

        function hapusJabatan(id) {
            fetch('proses.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=delete&id_jabatan=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal menghapus jabatan: ' + data.message);
                }
            });
        }

        document.getElementById('formTambah').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'create');
            
            fetch('proses.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal menambah jabatan: ' + data.message);
                }
            });
        };

        document.getElementById('formEdit').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update');
            
            fetch('proses.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal mengubah jabatan: ' + data.message);
                }
            });
        };
    </script>
</body>
</html>
