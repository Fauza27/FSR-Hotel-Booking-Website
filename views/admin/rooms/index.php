<?php include_once(VIEW_PATH . 'admin/layouts/header.php'); // Memasukkan bagian header dari layout admin ?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); // Memasukkan sidebar admin ?>
    </div>
    <div class="admin-content">
        <h2>Daftar Kamar</h2> <!-- Judul halaman daftar kamar -->
        
        <!-- Bagian filter pencarian dan kategori kamar -->
        <div class="admin-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <form method="get" style="display: flex; gap: 10px; align-items: center;">
                <!-- Input untuk mencari nomor kamar -->
                <input type="text" name="search" class="form-control" placeholder="Cari nomor kamar..." 
                    value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" /> <!-- Nilai input akan berisi apa yang dimasukkan sebelumnya -->
                
                <!-- Dropdown untuk memilih kategori kamar -->
                <select name="category" class="form-control">
                    <option value="">Semua Kategori</option> <!-- Opsi untuk semua kategori kamar -->
                    <?php foreach ($categories as $category): // Iterasi setiap kategori kamar ?>
                        <option value="<?php echo $category->category_id; ?>" 
                            <?php echo (isset($_GET['category']) && $_GET['category'] == $category->category_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category->name); ?>
                        </option> <!-- Menampilkan kategori dengan kondisi selected jika sesuai dengan kategori yang dipilih -->
                    <?php endforeach; ?>
                </select>
                
                <!-- Tombol submit untuk filter pencarian -->
                <button type="submit" class="btn btn-primary">Filter</button>
                
                <!-- Tombol untuk mereset pencarian dan kategori -->
                <?php if (!empty($_GET['search']) || !empty($_GET['category'])): ?>
                    <a href="<?= APP_URL ?>/admin/rooms" class="btn btn-secondary">Reset</a> <!-- Link reset untuk menghapus filter -->
                <?php endif; ?>
            </form>
            
            <!-- Tombol untuk menambahkan kamar baru -->
            <a href="<?= APP_URL ?>/admin/rooms/create" class="btn btn-primary">+ Tambah Kamar</a>
        </div>
        
        <!-- Bagian tabel daftar kamar -->
        <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No</th> <!-- Kolom nomor urut -->
                    <th>Nomor Kamar</th> <!-- Kolom nomor kamar -->
                    <th>Kategori</th> <!-- Kolom kategori kamar -->
                    <th>Harga / Malam</th> <!-- Kolom harga per malam -->
                    <th>Kapasitas</th> <!-- Kolom kapasitas kamar -->
                    <th>Status</th> <!-- Kolom status kamar -->
                    <th>Aksi</th> <!-- Kolom aksi untuk melihat, mengedit, atau menghapus -->
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rooms)): // Jika ada kamar yang ditemukan ?>
                    <?php $no = ($page - 1) * $limit + 1; // Menentukan nomor urut berdasarkan halaman dan limit ?>
                    <?php foreach ($rooms as $room): // Iterasi setiap kamar ?>
                        <tr>
                            <td><?php echo $no++; ?></td> <!-- Menampilkan nomor urut kamar -->
                            <td><?php echo htmlspecialchars($room->room_number); ?></td> <!-- Menampilkan nomor kamar -->
                            <td><?php echo htmlspecialchars($room->category_name ?? ''); ?></td> <!-- Menampilkan kategori kamar -->
                            <td>Rp <?php echo number_format($room->price_per_night, 0, ',', '.'); ?></td> <!-- Menampilkan harga per malam dengan format rupiah -->
                            <td><?php echo htmlspecialchars($room->capacity); ?> org</td> <!-- Menampilkan kapasitas kamar dalam orang -->
                            <td>
                                <!-- Menampilkan status kamar dengan badge yang berbeda warna tergantung pada status -->
                                <span class="badge badge-<?php echo $room->status == 'available' ? 'confirmed' : ($room->status == 'occupied' ? 'pending' : 'completed'); ?>">
                                    <?php echo ucfirst($room->status); ?>
                                </span>
                            </td>
                            <td>
                                <!-- Tombol untuk melihat detail kamar -->
                                <a href="<?= APP_URL ?>/admin/rooms/view/<?php echo (int)$room->room_id; ?>" class="btn btn-info btn-sm">View</a>
                                
                                <!-- Tombol untuk mengedit kamar -->
                                <a href="<?= APP_URL ?>/admin/rooms/edit/<?php echo (int)$room->room_id; ?>" class="btn btn-warning btn-sm">Edit</a>
                                
                                <!-- Tombol untuk menghapus kamar dengan konfirmasi -->
                                <a href="<?= APP_URL ?>/admin/rooms/delete/<?php echo (int)$room->room_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus kamar ini?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Jika tidak ada kamar yang ditemukan -->
                    <tr><td colspan="7" style="text-align:center;">Tidak ada data kamar.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        
        <!-- Bagian untuk pagination jika total halaman lebih dari 1 -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <nav class="pagination-nav">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): // Menampilkan tombol pagination ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>"> <!-- Menandai halaman aktif -->
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) && $_GET['search'] !== '' ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['category']) && $_GET['category'] !== '' ? '&category=' . urlencode($_GET['category']) : ''; ?>">
                            <?php echo $i; ?> <!-- Menampilkan nomor halaman -->
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); // Memasukkan bagian footer dari layout admin ?>
