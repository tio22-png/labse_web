<?php
require_once 'auth_check.php';
require_once '../includes/config.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $tanggal = trim($_POST['tanggal']);
    $lokasi = trim($_POST['lokasi']);
    $penyelenggara = isset($_POST['penyelenggara']) ? trim($_POST['penyelenggara']) : $_SESSION['admin_nama'];
    
    if (empty($judul) || empty($deskripsi) || empty($tanggal) || empty($lokasi) || empty($penyelenggara)) {
        $error = 'Semua field wajib diisi!';
    } else {
        // Handle gambar upload
        $gambar = null;
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['gambar']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                // Check file size (max 5MB)
                if ($_FILES['gambar']['size'] > 5 * 1024 * 1024) {
                    $error = 'Ukuran file terlalu besar! Maksimal 5MB.';
                } else {
                    $new_filename = 'pengabdian_' . time() . '_' . uniqid() . '.' . $ext;
                    $upload_path = '../public/uploads/pengabdian/' . $new_filename;
                    
                    // Create directory if not exists
                    if (!file_exists('../public/uploads/pengabdian/')) {
                        mkdir('../public/uploads/pengabdian/', 0777, true);
                    }
                    
                    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                        $gambar = $new_filename;
                    } else {
                        $error = 'Gagal mengupload gambar.';
                    }
                }
            } else {
                $error = 'Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.';
            }
        }
        
        // Insert pengabdian
        if (empty($error)) {
            $query = "INSERT INTO pengabdian (judul, deskripsi, tanggal, lokasi, penyelenggara, gambar) 
                      VALUES ($1, $2, $3, $4, $5, $6)";
            $result = pg_query_params($conn, $query, array($judul, $deskripsi, $tanggal, $lokasi, $penyelenggara, $gambar));
            
            if ($result) {
                header('Location: manage_pengabdian.php?success=add');
                exit();
            } else {
                $error = 'Gagal menambahkan kegiatan pengabdian.';
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
            <h4 class="mb-0">Tambah Kegiatan Pengabdian</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="manage_pengabdian.php">Kelola Pengabdian</a></li>
                    <li class="breadcrumb-item active">Tambah Baru</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <div class="card" data-aos="fade-up">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Form Tambah Kegiatan Pengabdian</h5>
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
                                       value="<?php echo isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : ''; ?>"
                                       placeholder="Masukkan judul kegiatan pengabdian">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Tanggal Kegiatan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" required 
                                       value="<?php echo isset($_POST['tanggal']) ? htmlspecialchars($_POST['tanggal']) : ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                                <input type="text" name="lokasi" class="form-control" required 
                                       value="<?php echo isset($_POST['lokasi']) ? htmlspecialchars($_POST['lokasi']) : ''; ?>"
                                       placeholder="Contoh: Aula Kelurahan Tembalang, Semarang">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Penyelenggara <span class="text-danger">*</span></label>
                                <input type="text" name="penyelenggara" class="form-control" required 
                                       value="<?php echo isset($_POST['penyelenggara']) ? htmlspecialchars($_POST['penyelenggara']) : htmlspecialchars($_SESSION['admin_nama']); ?>"
                                       placeholder="Nama penyelenggara">
                                <small class="text-muted">Default: <?php echo htmlspecialchars($_SESSION['admin_nama']); ?></small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Deskripsi Kegiatan <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" id="deskripsiKegiatan" class="form-control" rows="10" required 
                                          placeholder="Deskripsikan detail kegiatan pengabdian..."><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                                <small class="text-muted">Minimal 100 karakter</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Gambar Dokumentasi</label>
                                <input type="file" name="gambar" class="form-control" accept="image/*" id="gambarInput">
                                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 5MB.</small>
                                <div id="previewContainer" class="mt-3" style="display: none;">
                                    <img id="previewImage" src="" class="img-thumbnail" style="max-width: 300px;">
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save me-2"></i>Simpan
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                                </button>
                                <a href="manage_pengabdian.php" class="btn btn-outline-danger">
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
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Petunjuk</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Judul</strong> harus jelas dan deskriptif
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Deskripsi</strong> minimal 100 karakter
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <strong>Gambar</strong> mendukung JPG, PNG, GIF
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Pastikan tanggal sudah benar
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<style>
    .form-control:focus, .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }
    
    .btn {
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
</style>

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
    const deskripsi = document.getElementById('deskripsiKegiatan').value.trim();
    
    if (deskripsi.length < 100) {
        e.preventDefault();
        alert('Deskripsi minimal 100 karakter!');
        return false;
    }
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
});
</script>

<?php
pg_close($conn);
include 'includes/admin_footer.php';
?>
