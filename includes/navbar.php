<?php
// Start session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login sebagai admin
$is_admin_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-start" href="<?php echo BASE_URL; ?>/" style="padding-top: 0.5rem;">
            <img src="<?php echo BASE_URL; ?>/assets/img/logo-pnm.png" alt="Logo Politeknik Negeri Malang" class="navbar-logo me-3">
            <div class="brand-text">
                <div class="fw-bold text-primary" style="font-size: 1rem; line-height: 1.3;">Jurusan Teknologi Informasi</div>
                <div class="text-muted" style="font-size: 0.85rem; line-height: 1.3;">Politeknik Negeri Malang</div>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Profil
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/profile/tentang.php">Tentang</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/profile/visi_misi.php">Visi & Misi</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/profile/roadmap.php">Roadmap</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/pages/profile/focus_scope.php">Focus & Scope</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/pages/personil/">Personil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/pages/blog/">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/pages/recruitment/">Recruitment</a>
                </li>
                
                <?php if ($is_admin_logged_in): ?>
                <!-- Menu khusus admin -->
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/index.php">
                        <i class="bi bi-speedometer2 me-1"></i>Admin Dashboard
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
    /* Override container padding untuk logo mepet */
    .navbar .container {
        padding-left: 0 !important;
    }
    
    .navbar-logo {
        height: 70px;
        width: auto;
        object-fit: contain;
        margin-top: -5px;
        margin-left: -100px;
    }
    
    .brand-text {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        padding-top: 2px;
    }
    
    .navbar-brand {
        gap: 0;
        margin-left: 0 !important;
        padding-left: 0 !important;
    }
    
    @media (max-width: 768px) {
        .navbar-logo {
            height: 50px;
            margin-left: 8px;
        }
        
        .brand-text {
            font-size: 0.85rem;
        }
        
        .brand-text .fw-bold {
            font-size: 0.9rem !important;
        }
        
        .brand-text div:last-child {
            font-size: 0.75rem !important;
        }
    }
    
    @media (max-width: 576px) {
        .navbar-logo {
            height: 45px;
            margin-left: 5px;
        }
        
        .brand-text .fw-bold {
            font-size: 0.8rem !important;
        }
        
        .brand-text div:last-child {
            font-size: 0.7rem !important;
        }
    }
</style>