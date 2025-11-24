<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

$slider_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get slider details
$query = "SELECT * FROM tms_home_slider WHERE slider_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $slider_id);
$stmt->execute();
$result = $stmt->get_result();
$slider = $result->fetch_object();

if(!$slider) {
    $_SESSION['error'] = "Slider not found.";
    header("Location: admin-home-slider.php");
    exit();
}

// Handle Update
if(isset($_POST['update_slider'])) {
    $slider_title = trim($_POST['slider_title']);
    $slider_description = trim($_POST['slider_description']);
    $slider_order = intval($_POST['slider_order']);
    $slider_status = $_POST['slider_status'];
    
    $new_filename = $slider->slider_image; // Keep old image by default
    
    // Handle new image upload
    if(isset($_FILES['slider_image']) && $_FILES['slider_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['slider_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            $new_filename = 'slider_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_path = 'vendor/img/slider/' . $new_filename;
            
            if(move_uploaded_file($_FILES['slider_image']['tmp_name'], $upload_path)) {
                // Delete old image
                $old_img_path = 'vendor/img/slider/' . $slider->slider_image;
                if(file_exists($old_img_path)) {
                    unlink($old_img_path);
                }
            } else {
                $err = "Failed to upload new image.";
                $new_filename = $slider->slider_image; // Keep old image
            }
        } else {
            $err = "Invalid file type. Only JPG, PNG, and GIF allowed.";
        }
    }
    
    if(!isset($err)) {
        $update = "UPDATE tms_home_slider SET slider_image=?, slider_title=?, slider_description=?, slider_order=?, slider_status=? WHERE slider_id=?";
        $stmt_update = $mysqli->prepare($update);
        $stmt_update->bind_param('sssisi', $new_filename, $slider_title, $slider_description, $slider_order, $slider_status, $slider_id);
        
        if($stmt_update->execute()) {
            $_SESSION['success'] = "Slider updated successfully!";
            header("Location: admin-home-slider.php");
            exit();
        } else {
            $err = "Failed to update slider.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Slider - Admin</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/css/sb-admin.css" rel="stylesheet">
</head>
<body id="page-top">
    <?php include("vendor/inc/nav.php"); ?>

    <div id="wrapper">
        <?php include("vendor/inc/sidebar.php"); ?>

        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="admin-home-slider.php">Home Slider</a></li>
                    <li class="breadcrumb-item active">Edit Slider</li>
                </ol>

                <?php if(isset($err)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $err; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-edit"></i> Edit Slider Image
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Current Image</label>
                                        <div>
                                            <img src="vendor/img/slider/<?php echo $slider->slider_image; ?>" 
                                                 alt="Current Slider" 
                                                 style="max-width: 100%; height: auto; border-radius: 5px; border: 2px solid #ddd;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>New Image (Optional)</label>
                                        <input type="file" name="slider_image" class="form-control" accept="image/*">
                                        <small class="form-text text-muted">Leave empty to keep current image</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Title <span class="text-danger">*</span></label>
                                        <input type="text" name="slider_title" class="form-control" required 
                                               value="<?php echo htmlspecialchars($slider->slider_title); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="slider_description" class="form-control" rows="3"><?php echo htmlspecialchars($slider->slider_description); ?></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Display Order</label>
                                                <input type="number" name="slider_order" class="form-control" 
                                                       value="<?php echo $slider->slider_order; ?>" min="0">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="slider_status" class="form-control">
                                                    <option value="Active" <?php echo $slider->slider_status == 'Active' ? 'selected' : ''; ?>>Active</option>
                                                    <option value="Inactive" <?php echo $slider->slider_status == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" name="update_slider" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Slider
                            </button>
                            <a href="admin-home-slider.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <?php include("vendor/inc/footer.php"); ?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal"><span>×</span></button>
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
</body>
</html>
