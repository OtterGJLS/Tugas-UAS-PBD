<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_pegawai = $_GET['id'] ?? 0;

// Ambil data pegawai
$stmt = $pdo->prepare("SELECT * FROM pegawai WHERE id_pegawai = ?");
$stmt->execute([$id_pegawai]);
$pegawai = $stmt->fetch();

if (!$pegawai) {
    header("Location: index.php");
    exit();
}

// Ambil data departemen dan jabatan untuk dropdown
$stmt = $pdo->query("SELECT * FROM departemen ORDER BY nama_departemen");
$departemen = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM jabatan ORDER BY nama_jabatan");
$jabatan = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            UPDATE pegawai SET 
                nama_depan = ?, 
                nama_belakang = ?, 
                email = ?, 
                telepon = ?, 
                id_departemen = ?, 
                id_jabatan = ?, 
                tanggal_bergabung = ?, 
                gaji = ?, 
                status = ?
            WHERE id_pegawai = ?
        ");
        
        $stmt->execute([
            $_POST['nama_depan'],
            $_POST['nama_belakang'],
            $_POST['email'],
            $_POST['telepon'],
            $_POST['id_departemen'],
            $_POST['id_jabatan'],
            $_POST['tanggal_bergabung'],
            $_POST['gaji'],
            $_POST['status'],
            $id_pegawai
        ]);

        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pegawai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../layouts/navbar.php'; ?>

    <div class="container mt-4">
        <h2>Edit Data Pegawai</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="mt-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_depan" class="form-label">Nama Depan</label>
                    <input type="text" class="form-control" id="nama_depan" name="nama_depan" value="<?php echo htmlspecialchars($pegawai['nama_depan']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nama_belakang" class="form-label">Nama Belakang</label>
                    <input type="text" class="form-control" id="nama_belakang" name="nama_belakang" value="<?php echo htmlspecialchars($pegawai['nama_belakang']); ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($pegawai['email']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="telepon" class="form-label">Telepon</label>
                    <input type="text" class="form-control" id="telepon" name="telepon" value="<?php echo htmlspecialchars($pegawai['telepon']); ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="id_departemen" class="form-label">Departemen</label>
                    <select class="form-select" id="id_departemen" name="id_departemen" required>
                        <option value="">Pilih Departemen</option>
                        <?php foreach ($departemen as $dept): ?>
                            <option value="<?php echo $dept['id_departemen']; ?>" <?php echo $dept['id_departemen'] == $pegawai['id_departemen'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept['nama_departemen']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="id_jabatan" class="form-label">Jabatan</label>
                    <select class="form-select" id="id_jabatan" name="id_jabatan" required>
                        <option value="">Pilih Jabatan</option>
                        <?php foreach ($jabatan as $jab): ?>
                            <option value="<?php echo $jab['id_jabatan']; ?>" <?php echo $jab['id_jabatan'] == $pegawai['id_jabatan'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($jab['nama_jabatan']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="tanggal_bergabung" class="form-label">Tanggal Bergabung</label>
                    <input type="date" class="form-control" id="tanggal_bergabung" name="tanggal_bergabung" value="<?php echo $pegawai['tanggal_bergabung']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="gaji" class="form-label">Gaji</label>
                    <input type="number" class="form-control" id="gaji" name="gaji" value="<?php echo $pegawai['gaji']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="aktif" <?php echo $pegawai['status'] == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="nonaktif" <?php echo $pegawai['status'] == 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
