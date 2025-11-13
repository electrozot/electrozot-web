<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Handle Image Upload
if(isset($_POST['upload_slider'])) {
    $target_dir = "../vendor/img/";
    $image_name = basename($_FILES["slider_image"]["name"]);
    $target_file = $target_dir . "slide_" . time() . "_" . $image_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is actual image
    $check = getimagesize($_FILES["slider_image"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $err = "File is not an image.";
        $uploadOk = 0;
    }
    
    // Check file size (5MB max)
    if ($_FILES["slider_image"]["size"] > 5000000) {
        $err = "Sorry, your file is too large. Max 5MB allowed.";
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        $err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["slider_image"]["tmp_name"], $target_file)) {
            $succ = "Slider image uploaded successfully!";
        } else {
            $err = "Sorry, there was an error uploading your file.";
        }
    }
}

// Handle Image Delete
if(isset($_GET['delete'])) {
    $image_path = $_GET['delete'];
    if(file_exists($image_path)) {
        if(unlink($image_path)) {
            $succ = "Image deleted successfully!";
        } else {
            $err = "Error deleting image.";
        }
    }
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
                    <li class="breadcrumb-item active">Manage Home Slider</li>
                </ol>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Note:</strong> These images will be displayed on the home page booking form background slider. Recommended size: 1920x1080px for best results.
                </div>

                <!-- Upload Form -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-upload"></i> Upload Slider Image
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Select Image (JPG, PNG, GIF - Max 5MB)</label>
                                <input type="file" class="form-control" name="slider_image" accept="image/*" required>
                            </div>
                            <button type="submit" name="upload_slider" class="btn btn-success">
                                <i class="fas fa-upload"></i> Upload Image
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Slider Images -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-sliders-h"></i> Home Slider Images
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            $slider_dir = "../vendor/img/";
                            $images = glob($slider_dir . "slide*.{jpg,jpeg,png,gif}", GLOB_BRACE);
                            
                            if(count($images) > 0) {
                                foreach($images as $image) {
                                    $image_name = basename($image);
                            ?>
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="card">
                                    <img src="<?php echo $image; ?>" class="card-img-top" alt="Slider Image" style="height: 200px; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <p class="card-text small"><?php echo $image_name; ?></p>
                                        <a href="?delete=<?php echo $image; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this image?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                }
                            } else {
                                echo '<div class="col-12"><p class="text-center">No slider images found. Upload your first image!</p></div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Work Portfolio Images -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-briefcase"></i> Work Portfolio Images (Carousel)
                    </div>
                    <div class="card-body">
                        <p class="text-muted">These images are displayed in the "Our Work Portfolio" section on the home page.</p>
                        <div class="row">
                            <?php
                            $work_dir = "../vendor/img/completions/";
                            $work_images = glob($work_dir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
                            
                            if(count($work_images) > 0) {
                                foreach($work_images as $image) {
                                    $image_name = basename($image);
                            ?>
                            <div class="col-md-3 col-sm-6 mb-4">
                                <div class="card">
                                    <img src="<?php echo $image; ?>" class="card-img-top" alt="Work Image" style="height: 150px; object-fit: cover;">
                                    <div class="card-body text-center p-2">
                                        <p class="card-text small mb-2"><?php echo substr($image_name, 0, 20); ?>...</p>
                                        <a href="?delete=<?php echo $image; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this image?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                }
                            } else {
                                echo '<div class="col-12"><p class="text-center">No work portfolio images found.</p></div>';
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
