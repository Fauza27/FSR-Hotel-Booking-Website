<?php
$pageTitle = 'About Us';
$currentPage = 'about';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<section class="container mt-4 mb-4">
    <div class="section-title">
        <h1>About FSR Hotel</h1>
    </div>
    <div  class="room-card" style="max-width:700px;margin:0 auto; padding: bottom 10px;">
        <div class="room-info">
            <div class="owner-image-container" style="flex-shrink: 0;">
                <img src="assets\images\owner fsr hotel v3.jpg" alt="Foto Owner FSR Hotel" style="width: 350px; height: 350px; object-fit: cover; border: 3px solid var(--accent-color);">
            </div>
            <div class="owner-details">
                <h3 style="margin-bottom: 0.5rem; color: var(--light-text);">Fauza Sapri Rizky</h3>
                <p style="font-size: 0.9rem; color: var(--light-accent); margin-bottom: 1rem;">Founder CEO Commissioner</p>
                <p style="color: var(--medium-text); font-size: 0.95rem; line-height: 1.5;">
                    Selamat datang di FSR Hotel! kami owner FSR Hotel, dan merupakan kehormatan bagi saya untuk menyambut Anda.
                    Visi kami adalah menciptakan sebuah oase kenyamanan dan kemewahan yang tak terlupakan bagi setiap tamu. Kami berharap
                    pengalaman menginap Anda di sini akan melebihi ekspektasi dan menjadi kenangan indah.
                </p>
            </div>
        </div> 
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