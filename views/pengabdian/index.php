<?php
// Public view - List all pengabdian
require_once __DIR__ . '/../../includes/config.php';
$page_title = 'Pengabdian Masyarakat & Pelatihan';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

// Pagination
$items_per_page = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Get total
$count_query = "SELECT COUNT(*) as total FROM pengabdian";
$count_result = pg_query($conn, $count_query);
$total_items = pg_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_items / $items_per_page);

// Get data
$query = "SELECT * FROM pengabdian ORDER BY tanggal DESC, created_at DESC LIMIT $items_per_page OFFSET $offset";
$result = pg_query($conn, $query);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container text-center">
        <h1 data-aos="fade-down">Pengabdian Masyarakat & Pelatihan</h1>
        <p class="lead" data-aos="fade-up" data-aos-delay="100">Kegiatan pengabdian pada masyarakat dan pelatihan yang telah dilaksanakan oleh Lab Software Engineering</p>
    </div>
</div>

<!-- Content Section -->
<section class="content-section">
    <div class="container">
        
        <?php if (pg_num_rows($result) > 0): ?>
        
        <div class="row g-4">
            <?php while ($item = pg_fetch_assoc($result)): ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up">
                <div class="card h-100 shadow-sm border-0 hover-card">
                    
                    <!-- Image -->
                    <?php if ($item['gambar']): ?>
                    <img src="<?php echo BASE_URL; ?>/public/uploads/pengabdian/<?php echo htmlspecialchars($item['gambar']); ?>" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($item['judul']); ?>"
                         style="height: 200px; object-fit: cover;"
                         onerror="this.src='<?php echo BASE_URL; ?>/assets/img/no-image.png'">
                    <?php else: ?>
                    <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="bi bi-image text-white" style="font-size: 3rem;"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                        <!-- Date -->
                        <div class="mb-2">
                            <span class="badge bg-primary">
                                <i class="bi bi-calendar3 me-1"></i><?php echo date('d M Y', strtotime($item['tanggal'])); ?>
                            </span>
                        </div>
                        
                        <!-- Title -->
                        <h5 class="card-title"><?php echo htmlspecialchars($item['judul']); ?></h5>
                        
                        <!-- Description -->
                        <p class="card-text text-muted small">
                            <?php 
                            $deskripsi = strip_tags($item['deskripsi']);
                            echo strlen($deskripsi) > 120 ? substr($deskripsi, 0, 120) . '...' : $deskripsi; 
                            ?>
                        </p>
                        
                        <!-- Meta -->
                        <div class="mt-auto">
                            <div class="d-flex align-items-center text-muted small mb-2">
                                <i class="bi bi-geo-alt me-1"></i>
                                <span><?php echo htmlspecialchars($item['lokasi']); ?></span>
                            </div>
                            <div class="d-flex align-items-center text-muted small mb-3">
                                <i class="bi bi-person me-1"></i>
                                <span><?php echo htmlspecialchars($item['penyelenggara']); ?></span>
                            </div>
                            
                            <!-- Button -->
                            <a href="detail.php?id=<?php echo $item['id']; ?>" class="btn btn-outline-primary w-100">
                                Lihat Detail <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page-1; ?>">
                        <i class="bi bi-chevron-left"></i> Sebelumnya
                    </a>
                </li>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                
                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page+1; ?>">
                        Selanjutnya <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        
        <?php else: ?>
        
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size: 5rem;"></i>
            <h4 class="mt-3 text-muted">Belum Ada Kegiatan</h4>
            <p class="text-muted">Kegiatan pengabdian masyarakat akan ditampilkan di sini</p>
        </div>
        
        <?php endif; ?>
        
    </div>
</section>

<style>
.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.card-img-top {
    transition: transform 0.3s ease;
}

.hover-card:hover .card-img-top {
    transform: scale(1.05);
    overflow: hidden;
}
</style>

<?php
pg_close($conn);
require_once __DIR__ . '/../../includes/footer.php';
?>
