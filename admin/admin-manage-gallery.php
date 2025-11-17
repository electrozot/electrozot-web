<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Create gallery table if not exists
$mysqli->query("CREATE TABLE IF NOT EXISTS tms_gallery (
    g_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    g_title VARCHAR(255) NOT NULL,
    g_image VARCHAR(255) NOT NULL,
    g_service_id INT NULL,
    g_description TEXT NULL,
    g_status VARCHAR(20) NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

// Handle Image Upload
if(isset($_POST['upload_gallery'])) {
    $target_dir = "../vendor/img/gallery/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $image_name = basename($_FILES["gallery_image"]["name"]);
    $new_image_name = time() . "_" . $image_name;
    $target_file = $target_dir . $new_image_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Get title and description
    $g_title = isset($_POST['g_title']) ? $_POST['g_title'] : 'Gallery Image';
    $g_description = isset($_POST['g_description']) ? $_POST['g_description'] : '';
    
    // Check if image file is actual image
    $check = getimagesize($_FILES["gallery_image"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $err = "File is not an image.";
        $uploadOk = 0;
    }
    
    // Check file size (5MB max)
    if ($_FILES["gallery_image"]["size"] > 5000000) {
        $err = "Sorry, your file is too large. Max 5MB allowed.";
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        $err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["gallery_image"]["tmp_name"], $target_file)) {
            // Save to database
            $image_path = "vendor/img/gallery/" . $new_image_name;
            $stmt = $mysqli->prepare("INSERT INTO tms_gallery (g_title, g_image, g_description, g_status) VALUES (?, ?, ?, 'Active')");
            $stmt->bind_param('sss', $g_title, $image_path, $g_description);
            
            if($stmt->execute()) {
                $succ = "Gallery image uploaded successfully!";
            } else {
                $err = "Image uploaded but failed to save to database.";
            }
            $stmt->close();
        } else {
            $err = "Sorry, there was an error uploading your file.";
        }
    }
}

// Handle Image Delete
if(isset($_GET['delete'])) {
    $g_id = intval($_GET['delete']);
    
    // Get image path from database
    $stmt = $mysqli->prepare("SELECT g_image FROM tms_gallery WHERE g_id = ?");
    $stmt->bind_param('i', $g_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($row = $result->fetch_object()) {
        $image_path = "../" . $row->g_image;
        
        // Delete from database
        $delete_stmt = $mysqli->prepare("DELETE FROM tms_gallery WHERE g_id = ?");
        $delete_stmt->bind_param('i', $g_id);
        
        if($delete_stmt->execute()) {
            // Delete physical file
            if(file_exists($image_path)) {
                unlink($image_path);
            }
            $succ = "Image deleted successfully!";
        } else {
            $err = "Error deleting image from database.";
        }
        $delete_stmt->close();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php');?>

<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    <div id="wrapper">
        <?php include("vendor/inc/sidebar.php");?>
        <div id="content-wrapper">
            <div class="container-fluid">
                <?php if(isset($succ)) {?>
                <script>
                    setTimeout(function() {
                        swal("Success!", "<?php echo $succ;?>", "success");
                    }, 100);
                </script>
                <?php } ?>
                <?php if(isset($err)) {?>
                <script>
                    setTimeout(function() {
                        swal("Failed!", "<?php echo $err;?>", "error");
                    }, 100);
                </script>
                <?php } ?>

                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="admin-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item active">Manage Gallery</li>
                </ol>

                <!-- Upload Form -->
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-upload"></i> <strong>Upload Gallery Image</strong>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Image Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="g_title" placeholder="e.g., AC Installation Work" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Select Image <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control-file" name="gallery_image" accept="image/*" required>
                                        <small class="text-muted">JPG, PNG, GIF - Max 5MB</small>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Description (Optional)</label>
                                        <textarea class="form-control" name="g_description" rows="2" placeholder="Brief description of the image"></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="upload_gallery" class="btn btn-success">
                                <i class="fas fa-upload"></i> Upload Image
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Gallery Images -->
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-images"></i> <strong>Gallery Images</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            $gallery_query = $mysqli->query("SELECT * FROM tms_gallery ORDER BY created_at DESC");
                            
                            if($gallery_query && $gallery_query->num_rows > 0) {
                                while($gallery_item = $gallery_query->fetch_object()) {
                            ?>
                            <div class="col-md-3 col-sm-6 mb-4">
                                <div class="card shadow-sm">
                                    <img src="../<?php echo $gallery_item->g_image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($gallery_item->g_title); ?>" style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <h6 class="card-title" style="font-size: 0.9rem;"><?php echo htmlspecialchars($gallery_item->g_title); ?></h6>
                                        <?php if(!empty($gallery_item->g_description)): ?>
                                        <p class="card-text small text-muted"><?php echo htmlspecialchars(substr($gallery_item->g_description, 0, 50)); ?><?php echo strlen($gallery_item->g_description) > 50 ? '...' : ''; ?></p>
                                        <?php endif; ?>
                                        <div class="text-center mt-2">
                                            <span class="badge badge-<?php echo $gallery_item->g_status == 'Active' ? 'success' : 'secondary'; ?> mb-2">
                                                <?php echo $gallery_item->g_status; ?>
                                            </span>
                                            <br>
                                            <a href="?delete=<?php echo $gallery_item->g_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this image?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                }
                            } else {
                                echo '<div class="col-12"><div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle"></i> No gallery images found. Upload your first image above!
                                </div></div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>

            </div>
            <?php include("vendor/inc/footer.php");?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-danger" href="admin-logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/js/sb-admin.min.js"></script>
    <script src="vendor/js/swal.js"></script>
</body>
</html>
