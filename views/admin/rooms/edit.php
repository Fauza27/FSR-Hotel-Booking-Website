<?php include_once(VIEW_PATH . 'admin/layouts/header.php'); ?>
<?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>

<div class="admin-content-wrapper">
    <div class="admin-content">
    <div class="page-header">
        <h2 class="page-title">Edit Kamar</h2>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form action="<?= APP_URL ?>/admin/rooms/update/<?php echo $room->room_id; ?>" method="post" enctype="multipart/form-data" id="roomEditForm">
        
        <a href="<?= APP_URL ?>/admin/rooms" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
        </a>

        <div class="form-group">
            <label for="room_number">Nomor Kamar</label>
            <input type="text" name="room_number" id="room_number" class="form-control" value="<?php echo htmlspecialchars($room->room_number); ?>" required />
        </div>
        <div class="form-group">
            <label for="category_id">Kategori</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat->category_id; ?>" <?php if($room->category_id == $cat->category_id) echo 'selected'; ?>><?php echo htmlspecialchars($cat->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="price_per_night">Harga per Malam</label>
            <input type="number" name="price_per_night" id="price_per_night" class="form-control" min="1" value="<?php echo htmlspecialchars($room->price_per_night); ?>" required />
        </div>
        <div class="form-group">
            <label for="capacity">Kapasitas (Orang)</label>
            <input type="number" name="capacity" id="capacity" class="form-control" min="1" value="<?php echo htmlspecialchars($room->capacity); ?>" required />
        </div>
        <div class="form-group">
            <label for="size_sqm">Ukuran (m<sup>2</sup>)</label>
            <input type="number" name="size_sqm" id="size_sqm" class="form-control" min="1" value="<?php echo htmlspecialchars($room->size_sqm); ?>" required />
        </div>
        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea name="description" id="description" class="form-control" rows="3" required><?php echo htmlspecialchars($room->description); ?></textarea>
        </div>        <h3 class="form-section-title">Gambar Kamar</h3>
        <div class="form-group">
            <label>Gambar Kamar Saat Ini</label>
            <div class="current-images">
                <?php if (!empty($room->room_id)): ?>
                    <?php $images = $this->roomModel->getRoomImages($room->room_id); ?>
                    <?php foreach ($images as $img): ?>
                        <div class="image-item">
                            <img src="/<?php echo $img['image_url']; ?>" alt="Gambar Kamar" />
                            <a href="/admin/rooms/delete-image/<?php echo $img['image_id']; ?>?room_id=<?php echo $room->room_id; ?>" 
                               onclick="return confirm('Hapus gambar ini?')" 
                               class="delete-image">&times;</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="image">Upload Gambar Baru (bisa lebih dari satu)</label>
            <input type="file" name="image[]" id="image" class="form-control" accept="image/*" multiple />
        </div>        <h3 class="form-section-title">Fasilitas Kamar</h3>
        <div class="form-group">
            <label>Pilih Fasilitas</label>
            <div class="facility-checkboxes">
                <?php 
                    // Jika $roomFacilities adalah array objek, gunakan cara yang benar untuk mengekstrak facility_id
                    $roomFacilityIds = array_map(function($facility) {
                        return $facility->facility_id; // Pastikan ini sesuai dengan struktur objek
                    }, $roomFacilities ?? []);
                ?>
                <?php foreach ($facilities as $f): ?>
                    <div class="facility-option">
                        <input type="checkbox" 
                            id="facility_<?php echo $f->facility_id; ?>"  
                            name="facilities[]" 
                            value="<?php echo $f->facility_id; ?>"  
                            <?php if (in_array($f->facility_id, $roomFacilityIds)) echo 'checked'; ?> />
                        <label for="facility_<?php echo $f->facility_id; ?>">
                            <?php echo htmlspecialchars($f->name); ?> <!-- Pastikan $f->name jika $f adalah objek -->
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="available" <?php if($room->status=='available') echo 'selected'; ?>>Tersedia</option>
                <option value="occupied" <?php if($room->status=='occupied') echo 'selected'; ?>>Terisi</option>
                <option value="maintenance" <?php if($room->status=='maintenance') echo 'selected'; ?>>Perbaikan</option>
            </select>
        </div>
        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>Update
            </button>
            <a href="<?= APP_URL ?>/admin/rooms" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>    
    </form>
</div>
</div> <!-- Closing admin-content-wrapper -->
<script>
// Validasi client-side sederhana
const form = document.getElementById('roomEditForm');
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
    // Tidak wajib upload gambar baru
    // Minimal 1 fasilitas
    const facilityChecked = document.querySelectorAll('input[name="facilities[]"]:checked').length;
    if (facilityChecked === 0) {
        alert('Pilih minimal satu fasilitas!');
        valid = false;
    }
    if (!valid) e.preventDefault();
});
</script>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>
