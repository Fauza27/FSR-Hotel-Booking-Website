<?php
$pageTitle = 'About Us';
$currentPage = 'about';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<section class="container mt-4 mb-4">
    <div class="section-title">
        <h1>About FSR Hotel</h1>
    </div>
    <div class="room-card" style="max-width:700px;margin:0 auto;">
        <div class="room-info">
            <p>FSR Hotel adalah hotel modern yang menawarkan kenyamanan, kemewahan, dan pelayanan terbaik untuk setiap tamu. Kami berlokasi strategis di pusat kota, dekat dengan berbagai destinasi wisata dan bisnis.</p>
            <p>Dengan kamar yang elegan, fasilitas lengkap, serta staf profesional, kami berkomitmen memberikan pengalaman menginap yang tak terlupakan untuk Anda dan keluarga.</p>
            <ul style="margin-left:1.5rem;list-style:disc;color:var(--medium-text);">
                <li>Kamar modern & nyaman</li>
                <li>Restoran & layanan kamar 24 jam</li>
                <li>Fasilitas spa, gym, dan kolam renang</li>
                <li>Wi-Fi gratis di seluruh area hotel</li>
                <li>Lokasi strategis di pusat kota</li>
            </ul>
        </div>
    </div>
</section>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>
