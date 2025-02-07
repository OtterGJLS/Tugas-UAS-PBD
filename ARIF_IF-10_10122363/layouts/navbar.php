<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="../index.php">PT. AJ Abadi</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../pegawai/index.php">Data Pegawai</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../departemen/index.php">Departemen</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../jabatan/index.php">Jabatan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../log/index.php">Log Aktivitas</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link">Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../auth/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>