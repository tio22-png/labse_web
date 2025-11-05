<?php
// Proteksi halaman - harus login dulu
require_once 'auth_check.php';
require_once '../includes/config.php';

$page_title = 'Kelola Artikel';
include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Kelola Artikel</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelola Artikel</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="card">
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>Halaman ini untuk mengelola artikel. Fitur CRUD akan ditambahkan sesuai kebutuhan.
            </div>
            <a href="index.php" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>
    
</div>

<?php
pg_close($conn);
include 'includes/admin_footer.php';
?>