<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM departemen ORDER BY id_departemen ASC");
$departemen = $stmt->fetchAll();

// Hitung jumlah pegawai per departemen
$stmt = $pdo->query("
    SELECT d.id_departemen, COUNT(p.id_pegawai) as jumlah_pegawai
    FROM departemen d
    LEFT JOIN pegawai p ON d.id_departemen = p.id_departemen
    GROUP BY d.id_departemen
    ORDER BY d.id_departemen ASC
");
$jumlah_pegawai = [];
while ($row = $stmt->fetch()) {
    $jumlah_pegawai[$row['id_departemen']] = $row['jumlah_pegawai'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Departemen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../layouts/navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Data Departemen</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahDepartemen">
                Tambah Departemen
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Departemen</th>
                        <th>Jumlah Pegawai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departemen as $dept): ?>
                    <tr>
                        <td><?php echo $dept['id_departemen']; ?></td>
                        <td><?php echo htmlspecialchars($dept['nama_departemen']); ?></td>
                        <td><?php echo $jumlah_pegawai[$dept['id_departemen']] ?? 0; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editDepartemen(<?php echo $dept['id_departemen']; ?>, '<?php echo htmlspecialchars($dept['nama_departemen']); ?>')">
                                Ubah
                            </button>
                            <?php if (($jumlah_pegawai[$dept['id_departemen']] ?? 0) == 0): ?>
                            <button class="btn btn-sm btn-danger" onclick="if(confirm('Apakah Anda yakin ingin menghapus departemen ini?')) hapusDepartemen(<?php echo $dept['id_departemen']; ?>)">
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
    <div class="modal fade" id="tambahDepartemen" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Departemen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTambah" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_departemen" class="form-label">Nama Departemen</label>
                            <input type="text" class="form-control" id="nama_departemen" name="nama_departemen" required>
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
    <div class="modal fade" id="editDepartemen" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Departemen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEdit" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_departemen" name="id_departemen">
                        <div class="mb-3">
                            <label for="edit_nama_departemen" class="form-label">Nama Departemen</label>
                            <input type="text" class="form-control" id="edit_nama_departemen" name="nama_departemen" required>
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
        function editDepartemen(id, nama) {
            document.getElementById('edit_id_departemen').value = id;
            document.getElementById('edit_nama_departemen').value = nama;
            new bootstrap.Modal(document.getElementById('editDepartemen')).show();
        }

        function hapusDepartemen(id) {
            fetch('proses.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=delete&id_departemen=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal menghapus departemen: ' + data.message);
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
                    alert('Gagal menambah departemen: ' + data.message);
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
                    alert('Gagal mengubah departemen: ' + data.message);
                }
            });
        };
    </script>
</body>
</html>
