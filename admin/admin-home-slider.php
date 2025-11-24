<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Create slider table if not exists
$create_table = "CREATE TABLE IF NOT EXISTS tms_home_slider (
    slider_id INT AUTO_INCREMENT PRIMARY KEY,
    slider_image VARCHAR(255) NOT NULL,
    slider_title VARCHAR(255) NOT NULL,
    slider_description TEXT,
    slider_order INT DEFAULT 0,
    slider_status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX(slider_status),
    INDEX(slider_order)
)";
$mysqli->query($create_table);

// Handle Add Slider
if(isset($_POST['add_slider'])) {
    $slider_title = trim($_POST['slider_title']);
    $slider_description = trim($_POST['slider_description']);
    $slider_order = intval($_POST['slider_order']);
    $slider_status = $_POST['slider_status'];
    
    // Handle image upload
    if(isset($_FILES['slider_image']) && $_FILES['slider_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['slider_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            $new_filename = 'slider_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_path = 'vendor/img/slider/' . $new_filename;
            
            // Create directory if not exists
            if(!is_dir('vendor/img/slider')) {
                mkdir('vendor/img/slider', 0777, true);
            }
            
            if(move_uploaded_file($_FILES['slider_image']['tmp_name'], $upload_path)) {
                $insert = "INSERT INTO tms_home_slider (slider_image, slider_title, slider_description, slider_order, slider_status) 
                          VALUES (?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($insert);
                $stmt->bind_param('sssis', $new_filename, $slider_title, $slider_description, $slider_order, $slider_status);
                
                if($stmt->execute()) {
                    $success = "Slider image added successfully!";
                } else {
                    $err = "Failed to add slider. Please try again.";
                }
            } else {
                $err = "Failed to upload image.";
            }
        } else {
            $err = "Invalid file type. Only JPG, PNG, and GIF allowed.";
        }
    } else {
        $err = "Please select an image.";
    }
}

// Handle Delete Slider
if(isset($_GET['delete'])) {
    $slider_id = intval($_GET['delete']);
    
    // Get image filename
    $get_img = "SELECT slider_image FROM tms_home_slider WHERE slider_id = ?";
    $stmt = $mysqli->prepare($get_img);
    $stmt->bind_param('i', $slider_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $slider = $result->fetch_object();
    
    if($slider) {
        // Delete image file
        $img_path = 'vendor/img/slider/' . $slider->slider_image;
        if(file_exists($img_path)) {
            unlink($img_path);
        }
        
        // Delete from database
        $delete = "DELETE FROM tms_home_slider WHERE slider_id = ?";
        $stmt_del = $mysqli->prepare($delete);
        $stmt_del->bind_param('i', $slider_id);
        
        if($stmt_del->execute()) {
            $success = "Slider deleted successfully!";
        } else {
            $err = "Failed to delete slider.";
        }
    }
}

// Handle Update Status
if(isset($_GET['toggle_status'])) {
    $slider_id = intval($_GET['toggle_status']);
    
    $update = "UPDATE tms_home_slider SET slider_status = IF(slider_status='Active', 'Inactive', 'Active') WHERE slider_id = ?";
    $stmt = $mysqli->prepare($update);
    $stmt->bind_param('i', $slider_id);
    
    if($stmt->execute()) {
        $success = "Status updated successfully!";
    } else {
        $err = "Failed to update status.";
    }
}

// Get all sliders
$query = "SELECT * FROM tms_home_slider ORDER BY slider_order ASC, slider_id DESC";
$result = $mysqli->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Home Slider Management - Admin</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
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
                    <li class="breadcrumb-item active">Home Slider Management</li>
                </ol>

                <?php if(isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <?php if(isset($err)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $err; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <!-- Add New Slider -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-plus-circle"></i> Add New Slider Image
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Slider Image <span class="text-danger">*</span></label>
                                        <input type="file" name="slider_image" class="form-control" required accept="image/*">
                                        <small class="form-text text-muted">Recommended size: 1200x500px (JPG, PNG, GIF)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Title <span class="text-danger">*</span></label>
                                        <input type="text" name="slider_title" class="form-control" required placeholder="e.g., Professional Service Completed">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="slider_description" class="form-control" rows="2" placeholder="Brief description of the work"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Display Order</label>
                                        <input type="number" name="slider_order" class="form-control" value="0" min="0">
                                        <small class="form-text text-muted">Lower number = shows first</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="slider_status" class="form-control">
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="add_slider" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Slider
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Existing Sliders -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-images"></i> Manage Slider Images
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Order</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($result->num_rows > 0): ?>
                                        <?php while($slider = $result->fetch_object()): ?>
                                            <tr>
                                                <td class="text-center"><?php echo $slider->slider_order; ?></td>
                                                <td>
                                                    <img src="vendor/img/slider/<?php echo $slider->slider_image; ?>" 
                                                         alt="<?php echo htmlspecialchars($slider->slider_title); ?>" 
                                                         style="width: 150px; height: 80px; object-fit: cover; border-radius: 5px;">
                                                </td>
                                                <td><?php echo htmlspecialchars($slider->slider_title); ?></td>
                                                <td><?php echo htmlspecialchars($slider->slider_description); ?></td>
                                                <td class="text-center">
                                                    <?php if($slider->slider_status == 'Active'): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="?toggle_status=<?php echo $slider->slider_id; ?>" 
                                                       class="btn btn-sm btn-warning" 
                                                       title="Toggle Status">
                                                        <i class="fas fa-toggle-on"></i>
                                                    </a>
                                                    <a href="admin-edit-slider.php?id=<?php echo $slider->slider_id; ?>" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete=<?php echo $slider->slider_id; ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this slider?')" 
                                                       title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No slider images found. Add your first slider above.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
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
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="vendor/js/sb-admin.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "order": [[0, "asc"]]
            });
        });
    </script>
</body>
</html>
