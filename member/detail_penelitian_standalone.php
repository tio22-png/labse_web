<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

if (!isset($_GET['id'])) {
    header('Location: review_penelitian.php');
    exit();
}

$penelitian_id = intval($_GET['id']);
$member_id = $_SESSION['member_id'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = trim($_POST['status']);
    $query_update = "UPDATE penelitian SET status = $1 WHERE id = $2";
    $result_update = pg_query_params($conn, $query_update, array($new_status, $penelitian_id));
    
    if ($result_update) {
        $success = "Status berhasil diperbarui";
    }
}

// Get research details
$query = "SELECT p.*, m.nama as nama_mahasiswa, m.nim, m.jurusan, m.email
          FROM penelitian p 
          JOIN mahasiswa m ON p.mahasiswa_id = m.id 
          WHERE p.id = $1 AND m.dosen_pembimbing_id = $2";
$result = pg_query_params($conn, $query, array($penelitian_id, $member_id));
$penelitian = pg_fetch_assoc($result);

if (!$penelitian) {
    header('Location: review_penelitian.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Penelitian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4A90E2;
            --sidebar-width: 260px;
            --sidebar-bg: #2C3E50;
            --sidebar-hover: #34495E;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
        }
        
        /* Sidebar Styles - COPIED FROM member_header.php */
        .member-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            color: white;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000;
        }
        
        .member-sidebar .sidebar-header {
            padding: 1.5rem;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .member-sidebar .sidebar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .member-sidebar .sidebar-menu {
            padding: 1rem 0;
        }
        
        .member-sidebar .menu-item {
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .member-sidebar .menu-item:hover {
            background: var(--sidebar-hover);
            color: white;
            border-left-color: var(--primary-color);
        }
        
        .member-sidebar .menu-item.active {
            background: var(--sidebar-hover);
            color: white;
            border-left-color: var(--primary-color);
        }
        
        .member-sidebar .menu-item i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
            width: 25px;
            text-align: center;
        }
        
        .member-sidebar .menu-label {
            padding: 1.5rem 1.5rem 0.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.5);
            font-weight: 600;
        }
        
        .member-sidebar .user-info {
            padding: 1rem 1.5rem;
            background: rgba(0,0,0,0.2);
            border-top: 1px solid rgba(255,255,255,0.1);
            position: sticky;
            bottom: 0;
        }
        
        .member-sidebar .user-info .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 0.75rem;
        }
        
        /* Main Content */
        .member-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
        }
        
        .member-topbar {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .member-topbar .breadcrumb {
            margin: 0;
            background: transparent;
            padding: 0;
        }
    </style>
</head>
<body>

<!-- SIDEBAR - COPIED STRUCTURE FROM member_sidebar.php -->
<div class="member-sidebar">
    <div class="sidebar-header">
        <a href="index.php" class="sidebar-brand" style="align-items: flex-start;">
            <img src="../assets/img/logo-pnm.png" alt="Logo PNM" style="width: 55px; height: 55px; object-fit: contain; margin-right: 12px; margin-top: -2px;">
            <div style="padding-top: 3px;">
                <div style="font-size: 1rem; line-height: 1.3;">JTI - POLINEMA</div>
                <small style="font-size: 0.7rem; opacity: 0.7; line-height: 1.2;">Member Panel</small>
            </div>
        </a>
    </div>
    
    <div class="sidebar-menu">
        <div class="menu-label">Main Menu</div>
        
        <a href="index.php" class="menu-item">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        
        <div class="menu-label">Content Management</div>
        
        <a href="my_articles.php" class="menu-item">
            <i class="bi bi-file-text"></i>
            <span>Artikel Saya</span>
        </a>
        
        <a href="add_article.php" class="menu-item">
            <i class="bi bi-plus-circle"></i>
            <span>Buat Artikel</span>
        </a>

        <div class="menu-label">Bimbingan</div>

        <a href="review_penelitian.php" class="menu-item active">
            <i class="bi bi-people"></i>
            <span>Review Penelitian</span>
        </a>
        
        <a href="edit_profile.php" class="menu-item">
            <i class="bi bi-person-circle"></i>
            <span>Edit Profil</span>
        </a>
        
        <a href="logout.php" class="menu-item">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </div>
    
    <!-- User Info at Bottom -->
    <div class="user-info">
        <div class="d-flex align-items-center">
            <div class="user-avatar">
                <i class="bi bi-person"></i>
            </div>
            <div class="flex-grow-1">
                <div style="font-size: 0.9rem; font-weight: 600;">
                    <?php echo htmlspecialchars($_SESSION['member_nama'] ?? 'Member'); ?>
                </div>
                <div style="font-size: 0.75rem; opacity: 0.7;">
                    <?php echo htmlspecialchars($_SESSION['member_jabatan'] ?? ''); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="member-content">
    <div class="member-topbar">\
        <h4 class="mb-0">Detail Penelitian</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="review_penelitian.php">Review Penelitian</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>

    <?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-4"><?php echo htmlspecialchars($penelitian['judul']); ?></h5>
                    
                    <div class="mb-4">
                        <label class="text-muted small">Mahasiswa</label>
                        <div class="fw-bold"><?php echo htmlspecialchars($penelitian['nama_mahasiswa']); ?> (<?php echo htmlspecialchars($penelitian['nim']); ?>)</div>
                        <div><?php echo htmlspecialchars($penelitian['jurusan']); ?></div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="text-muted small">Keterangan</label>
                        <p><?php echo nl2br(htmlspecialchars($penelitian['keterangan'] ?? '-')); ?></p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="text-muted small">File / Link</label>
                        <div class="d-flex gap-2">
                            <?php if (!empty($penelitian['file_path'])): ?>
                                <a href="../uploads/penelitian/<?php echo htmlspecialchars($penelitian['file_path']); ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-file-earmark-text me-2"></i>Download File
                                </a>
                            <?php endif; ?>
                            
                            <?php if (!empty($penelitian['link_drive'])): ?>
                                <a href="<?php echo htmlspecialchars($penelitian['link_drive']); ?>" target="_blank" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-google me-2"></i>Buka Link Drive
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Status Penelitian</h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="update_status" value="1">
                        <div class="mb-3">
                            <select class="form-select" name="status">
                                <option value="submitted" <?php echo $penelitian['status'] == 'submitted' ? 'selected' : ''; ?>>Submitted</option>
                                <option value="reviewed" <?php echo $penelitian['status'] == 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                <option value="approved" <?php echo $penelitian['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo $penelitian['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                    </form>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Info Mahasiswa</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-person-circle" style="font-size: 3rem; color: #6c757d;"></i>
                    </div>
                    <h6><?php echo htmlspecialchars($penelitian['nama_mahasiswa']); ?></h6>
                    <p class="text-muted small"><?php echo htmlspecialchars($penelitian['email']); ?></p>
                    <a href="mailto:<?php echo htmlspecialchars($penelitian['email']); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-envelope me-2"></i>Kirim Email
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
