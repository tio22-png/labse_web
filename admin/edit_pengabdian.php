<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: manage_pengabdian.php');
    exit();
}

$id = intval($_GET['id']);
$error = '';

// Get pengabdian data
$query = "SELECT * FROM pengabdian WHERE id = $1";
$result = pg_query_params($conn, $query, array($id));
$pengabdian = pg_fetch_assoc($result);

if (!$pengabdian) {
    header('Location: manage_pengabdian.php?error=notfound');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $tanggal = trim($_POST['tanggal']);
    $lokasi = trim($_POST['lokasi']);
    $penyelenggara = trim($_POST['penyelenggara']);
    
    if (empty($judul) || empty($deskripsi) || empty($tanggal) || empty($lokasi) || empty($penyelenggara)) {
        $error = 'Semua field wajib diisi!';
    } else {
        // Handle gambar upload
        $gambar = $pengabdian['gambar']; // Keep existing
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['gambar']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                if ($_FILES['gambar']['size'] > 5 * 1024 * 1024) {
                    $error = 'Ukuran file terlalu besar! Maksimal 5MB.';
                } else {
                    $new_filename = 'pengabdian_' . time() . '_' . uniqid() . '.' . $ext;
                    $upload_path = '../public/uploads/pengabdian/' . $new_filename;
                    
                    if (!file_exists('../public/uploads/pengabdian/')) {
                        mkdir('../public/uploads/pengabdian/', 0777, true);
                    }
                    
                    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                        // Delete old image
                        if ($pengabdian['gambar'] && file_exists('../public/uploads/pengabdian/' . $pengabdian['gambar'])) {
                            unlink('../public/uploads/pengabdian/' . $pengabdian['gambar']);
                        }
                        $gambar = $new_filename;
                    } else {
                        $error = 'Gagal mengupload gambar.';
                    }
                }
            } else {
                $error = 'Format file tidak didukung!';
            }
        }
        
        // Update pengabdian
        if (empty($error)) {
            $query = "UPDATE pengabdian SET judul = $1, deskripsi = $2, tanggal = $3, lokasi = $4, penyelenggara = $5, gambar = $6, updated_at = NOW() 
                      WHERE id = $7";
            $result = pg_query_params($conn, $query, array($judul, $deskripsi, $tanggal, $lokasi, $penyelenggara, $gambar, $id));
            
            if ($result) {
                header('Location: manage_pengabdian.php?success=edit');
                exit();
            } else {
                $error = 'Gagal mengupdate kegiatan pengabdian.';
            }
        }
    }
}

include 'includes/admin_header.php';
include 'includes/admin_sidebar.php';
?>

<!-- Main Content -->
<div class="admin-content">
    
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div>
            <h4 class="mb-0">Edit Kegiatan Pengabdian</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="manage_pengabdian.php">Kelola Pengabdian</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <div class="card" data-aos="fade-up">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Form Edit Kegiatan Pengabdian</h5>
                    </div>
                    <div class="card-body">
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" id="formPengabdian">
                            
                            <div class="mb-3">
                                <label class="form-label">Judul Kegiatan <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control" required 
                                       value="<?php echo htmlspecialchars($pengabdian['judul']); ?>"
                                       placeholder="Masukkan judul kegiatan">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Tanggal Kegiatan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" required 
                                       value="<?php echo htmlspecialchars($pengabdian['tanggal']); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                                <input type="text" name="lokasi" class="form-control" required 
                                       value="<?php echo htmlspecialchars($pengabdian['lokasi']); ?>"
                                       placeholder="Lokasi kegiatan">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Penyelenggara <span class="text-danger">*</span></label>
                                <input type="text" name="penyelenggara" class="form-control" required 
                                       value="<?php echo htmlspecialchars($pengabdian['penyelenggara']); ?>"
                                       placeholder="Nama penyelenggara">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Deskripsi Kegiatan <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" id="deskripsiKegiatan" class="form-control" rows="10" required 
                                          placeholder="Deskripsi kegiatan..."><?php echo htmlspecialchars($pengabdian['deskripsi']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Gambar Dokumentasi</label>
                                <?php if ($pengabdian['gambar']): ?>
                                <div class="mb-2">
                                    <img src="<?php echo BASE_URL; ?>/public/uploads/pengabdian/<?php echo htmlspecialchars($pengabdian['gambar']); ?>" 
                                         class="img-thumbnail" style="max-width: 300px;">
                                    <p class="text-muted small mb-0">Gambar saat ini</p>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="gambar" class="form-control" accept="image/*" id="gambarInput">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar. Format: JPG, PNG, GIF. Maksimal 5MB.</small>
                                <div id="previewContainer" class="mt-3" style="display: none;">
                                    <img id="previewImage" src="" class="img-thumbnail" style="max-width: 300px;">
                                    <p class="text-muted small">Preview gambar baru</p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-save me-2"></i>Update
                                </button>
                                <a href="manage_pengabdian.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                            </div>
                            
                        </form>
                        
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3">
                <div class="card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Info</h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-2"><strong>Dibuat:</strong><br><?php echo date('d M Y H:i', strtotime($pengabdian['created_at'])); ?></p>
                        <?php if ($pengabdian['updated_at']): ?>
                        <p class="small mb-0"><strong>Terakhir diupdate:</strong><br><?php echo date('d M Y H:i', strtotime($pengabdian['updated_at'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<script>
// Preview image
document.getElementById('gambarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('previewContainer').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});

// Form validation
document.getElementById('formPengabdian').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
});
</script>

<?php
pg_close($conn);
include 'includes/admin_footer.php';
?>
