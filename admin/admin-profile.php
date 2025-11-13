<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Get admin details
$query = "SELECT * FROM tms_admin WHERE a_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $aid);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_object();

// Ensure photo column exists
try {
    $mysqli->query("ALTER TABLE tms_admin ADD COLUMN IF NOT EXISTS a_photo VARCHAR(200) DEFAULT NULL");
} catch(Exception $e) {}

// Update profile
if(isset($_POST['update_profile'])) {
    $a_name = $_POST['a_name'];
    $a_email = $_POST['a_email'];
    $a_photo = $admin->a_photo; // Keep existing photo by default
    
    // Handle photo upload
    if(isset($_FILES['a_photo']) && $_FILES['a_photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['a_photo']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(in_array(strtolower($filetype), $allowed)) {
            $new_filename = 'admin_' . $aid . '_' . time() . '.' . $filetype;
            $upload_path = '../vendor/img/' . $new_filename;
            
            if(move_uploaded_file($_FILES['a_photo']['tmp_name'], $upload_path)) {
                // Delete old photo if exists
                if(!empty($admin->a_photo) && file_exists('../vendor/img/' . $admin->a_photo)) {
                    unlink('../vendor/img/' . $admin->a_photo);
                }
                $a_photo = $new_filename;
            }
        }
    }
    
    $update_query = "UPDATE tms_admin SET a_name = ?, a_email = ?, a_photo = ? WHERE a_id = ?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param('sssi', $a_name, $a_email, $a_photo, $aid);
    
    if($update_stmt->execute()) {
        $_SESSION['a_name'] = $a_name;
        $_SESSION['a_photo'] = $a_photo;
        $success = "Profile updated successfully!";
        
        // Refresh admin data
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_object();
    } else {
        $error = "Failed to update profile: " . $mysqli->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php'); ?>
<body id="page-top">
    <?php include('vendor/inc/nav.php'); ?>

    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php'); ?>

        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="admin-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">My Profile</li>
                </ol>

                <?php if(isset($success)): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Profile Card -->
                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 bg-primary text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-user-circle"></i> Admin Profile
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-4">
                                    <?php if(isset($admin->a_photo) && !empty($admin->a_photo)): ?>
                                        <img src="../vendor/img/<?php echo htmlspecialchars($admin->a_photo); ?>" 
                                             class="rounded-circle" 
                                             style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #4e73df;"
                                             alt="Admin Photo">
                                    <?php else: ?>
                                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                                    <?php endif; ?>
                                </div>
                                <h4 class="mb-2"><?php echo htmlspecialchars($admin->a_name); ?></h4>
                                <p class="text-muted mb-3">
                                    <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($admin->a_email); ?>
                                </p>
                                <hr>
                                <div class="text-left">
                                    <p class="mb-2">
                                        <strong>Admin ID:</strong> #<?php echo $admin->a_id; ?>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Role:</strong> <span class="badge badge-primary">Administrator</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 bg-secondary text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-cog"></i> Quick Actions
                                </h6>
                            </div>
                            <div class="card-body">
                                <a href="admin-change-password.php" class="btn btn-warning btn-block mb-2">
                                    <i class="fas fa-key"></i> Change Password
                                </a>
                                <a href="admin-dashboard.php" class="btn btn-info btn-block mb-2">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                                <a href="admin-view-syslogs.php" class="btn btn-secondary btn-block">
                                    <i class="fas fa-history"></i> System Logs
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Profile Form -->
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 bg-success text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-edit"></i> Edit Profile
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="a_photo">
                                            <i class="fas fa-camera"></i> Profile Photo
                                        </label>
                                        <div class="custom-file">
                                            <input type="file" 
                                                   class="custom-file-input" 
                                                   id="a_photo" 
                                                   name="a_photo" 
                                                   accept="image/*"
                                                   onchange="previewPhoto(this)">
                                            <label class="custom-file-label" for="a_photo">Choose photo...</label>
                                        </div>
                                        <small class="form-text text-muted">Accepted formats: JPG, JPEG, PNG, GIF (Max 2MB)</small>
                                        <?php if(isset($admin->a_photo) && !empty($admin->a_photo)): ?>
                                            <div class="mt-2">
                                                <img src="../vendor/img/<?php echo htmlspecialchars($admin->a_photo); ?>" 
                                                     id="photoPreview"
                                                     class="img-thumbnail" 
                                                     style="max-width: 150px; max-height: 150px;"
                                                     alt="Current Photo">
                                            </div>
                                        <?php else: ?>
                                            <div class="mt-2" id="photoPreviewContainer" style="display: none;">
                                                <img id="photoPreview" class="img-thumbnail" style="max-width: 150px; max-height: 150px;" alt="Preview">
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="a_name">
                                            <i class="fas fa-user"></i> Admin Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="a_name" 
                                               name="a_name" 
                                               value="<?php echo htmlspecialchars($admin->a_name); ?>" 
                                               required>
                                        <small class="form-text text-muted">This name will be displayed in the navigation bar</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="a_email">
                                            <i class="fas fa-envelope"></i> Email Address <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="a_email" 
                                               name="a_email" 
                                               value="<?php echo htmlspecialchars($admin->a_email); ?>" 
                                               required>
                                        <small class="form-text text-muted">Used for login and notifications</small>
                                    </div>

                                    <div class="form-group">
                                        <label>
                                            <i class="fas fa-key"></i> Password
                                        </label>
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control" 
                                                   value="••••••••" 
                                                   disabled>
                                            <div class="input-group-append">
                                                <a href="admin-change-password.php" class="btn btn-warning">
                                                    <i class="fas fa-edit"></i> Change Password
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="form-group mb-0">
                                        <button type="submit" name="update_profile" class="btn btn-success btn-lg">
                                            <i class="fas fa-save"></i> Update Profile
                                        </button>
                                        <a href="admin-dashboard.php" class="btn btn-secondary btn-lg">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 bg-info text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-info-circle"></i> Account Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-left-primary shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Bookings</div>
                                                        <?php
                                                        $bookings_count = $mysqli->query("SELECT COUNT(*) as total FROM tms_service_booking")->fetch_object()->total;
                                                        ?>
                                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $bookings_count; ?></div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card border-left-success shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Technicians</div>
                                                        <?php
                                                        $techs_count = $mysqli->query("SELECT COUNT(*) as total FROM tms_technician")->fetch_object()->total;
                                                        ?>
                                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $techs_count; ?></div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <i class="fas fa-user-cog fa-2x text-gray-300"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('vendor/inc/footer.php'); ?>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    
    <script>
        // Photo preview
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    $('#photoPreview').attr('src', e.target.result);
                    $('#photoPreviewContainer').show();
                }
                
                reader.readAsDataURL(input.files[0]);
                
                // Update file label
                var fileName = input.files[0].name;
                $(input).next('.custom-file-label').html(fileName);
            }
        }
    </script>
</body>
</html>
