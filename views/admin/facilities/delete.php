<?php
// views/admin/facilities/delete.php
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>
<div class="container mt-4">
    <h2 class="mb-3">Hapus Fasilitas</h2>
    <div class="card mb-4">
        <div class="card-header bg-light fw-bold">Konfirmasi Hapus Fasilitas</div>
        <div class="card-body">
            <table class="table table-borderless mb-0">
                <tr><th>Nama Fasilitas</th><td><?= htmlspecialchars($facility['name']) ?></td></tr>
                <tr><th>Icon</th><td><i class="bi bi-<?= htmlspecialchars($facility['icon']) ?>" style="font-size: 1.5em;"></i> <span class="ms-2 text-muted">(<?= htmlspecialchars($facility['icon']) ?>)</span></td></tr>
                <tr><th>Deskripsi</th><td><?= htmlspecialchars($facility['description']) ?></td></tr>
            </table>
            <hr>
            <form method="post" action="">
                <div class="alert alert-danger">
                    Apakah Anda yakin ingin menghapus fasilitas ini? Tindakan ini tidak dapat dibatalkan.
                </div>
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                <a href="/admin/facilities" class="btn btn-secondary ms-2">Batal</a>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
