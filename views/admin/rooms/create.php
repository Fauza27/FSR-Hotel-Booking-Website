<?php
// views/admin/rooms/create.php
?>
<div class="admin-content">
    <h2>Tambah Kamar Baru</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form action="" method="post" enctype="multipart/form-data" id="roomForm">
        <div class="form-group">
            <label for="room_number">Nomor Kamar</label>
            <input type="text" name="room_number" id="room_number" class="form-control" required />
        </div>
        <div class="form-group">
            <label for="category_id">Kategori</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="price_per_night">Harga per Malam</label>
            <input type="number" name="price_per_night" id="price_per_night" class="form-control" min="1" required />
        </div>
        <div class="form-group">
            <label for="capacity">Kapasitas (Orang)</label>
            <input type="number" name="capacity" id="capacity" class="form-control" min="1" required />
        </div>
        <div class="form-group">
            <label for="size_sqm">Ukuran (m<sup>2</sup>)</label>
            <input type="number" name="size_sqm" id="size_sqm" class="form-control" min="1" required />
        </div>
        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="image">Upload Gambar (bisa lebih dari satu)</label>
            <input type="file" name="image[]" id="image" class="form-control" accept="image/*" multiple required />
        </div>
        <div class="form-group">
            <label>Fasilitas</label>
            <div class="facility-checkboxes" style="display: flex; flex-wrap: wrap; gap: 10px;">
                <?php foreach ($facilities as $f): ?>
                    <label style="min-width: 150px;">
                        <input type="checkbox" name="facilities[]" value="<?php echo $f['facility_id']; ?>" />
                        <?php echo htmlspecialchars($f['name']); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="available">Tersedia</option>
                <option value="occupied">Terisi</option>
                <option value="maintenance">Perbaikan</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="/admin/rooms" class="btn btn-secondary">Batal</a>
    </form>
</div>
<script>
// Validasi client-side sederhana
const form = document.getElementById('roomForm');
form.addEventListener('submit', function(e) {
    let valid = true;
    const requiredFields = ['room_number', 'category_id', 'price_per_night', 'capacity', 'size_sqm', 'description', 'status'];
    requiredFields.forEach(function(id) {
        const el = document.getElementById(id);
        if (!el.value) {
            el.classList.add('is-invalid');
            valid = false;
        } else {
            el.classList.remove('is-invalid');
        }
    });
    // Minimal 1 gambar
    const img = document.getElementById('image');
    if (!img.files || img.files.length === 0) {
        img.classList.add('is-invalid');
        valid = false;
    } else {
        img.classList.remove('is-invalid');
    }
    // Minimal 1 fasilitas
    const facilityChecked = document.querySelectorAll('input[name="facilities[]"]:checked').length;
    if (facilityChecked === 0) {
        alert('Pilih minimal satu fasilitas!');
        valid = false;
    }
    if (!valid) e.preventDefault();
});
</script>
