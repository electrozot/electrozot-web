<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  
  if(isset($_POST['update_feedback'])) {
    $f_id = $_GET['f_id'];
    $f_uname = $_POST['f_uname'];
    $f_content = $_POST['f_content'];
    $f_status = $_POST['f_status'];
    
    // Get current photo
    $ret = "SELECT f_photo FROM tms_feedback WHERE f_id=?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $f_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $current_photo = $res->fetch_object()->f_photo;
    
    $f_photo = $current_photo;
    
    // Handle photo upload
    if(isset($_FILES['f_photo']) && $_FILES['f_photo']['error'] == 0) {
        $target_dir = "../vendor/img/feedbacks/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["f_photo"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        
        if(in_array($file_extension, $allowed_extensions)) {
            // Delete old photo
            if($current_photo && file_exists("../" . $current_photo)) {
                unlink("../" . $current_photo);
            }
            
            $new_filename = "feedback_" . time() . "." . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            if(move_uploaded_file($_FILES["f_photo"]["tmp_name"], $target_file)) {
                $f_photo = "vendor/img/feedbacks/" . $new_filename;
            }
        }
    }
    
    // Handle photo removal
    if(isset($_POST['remove_photo']) && $_POST['remove_photo'] == '1') {
        if($current_photo && file_exists("../" . $current_photo)) {
            unlink("../" . $current_photo);
        }
        $f_photo = NULL;
    }
    
    $query = "UPDATE tms_feedback SET f_uname=?, f_content=?, f_status=?, f_photo=? WHERE f_id=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ssssi', $f_uname, $f_content, $f_status, $f_photo, $f_id);
    
    if($stmt->execute()) {
        $succ = "Feedback Updated Successfully";
    } else {
        $err = "Failed to update feedback";
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php');?>
<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php');?>
        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Feedbacks</a></li>
                    <li class="breadcrumb-item active">Edit Feedback</li>
                </ol>
                
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-edit"></i> Edit Feedback
                    </div>
                    <div class="card-body">
                        <?php if(isset($succ)) { ?>
                            <div class="alert alert-success"><?php echo $succ; ?></div>
                        <?php } ?>
                        <?php if(isset($err)) { ?>
                            <div class="alert alert-danger"><?php echo $err; ?></div>
                        <?php } ?>
                        
                        <?php
                        $f_id = $_GET['f_id'];
                        $ret = "SELECT * FROM tms_feedback WHERE f_id=?";
                        $stmt = $mysqli->prepare($ret);
                        $stmt->bind_param('i', $f_id);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $row = $res->fetch_object();
                        ?>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Client Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="f_uname" value="<?php echo $row->f_uname; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Feedback Content <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="f_content" rows="4" required><?php echo $row->f_content; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Client Photo</label>
                                <?php if($row->f_photo) { ?>
                                    <div class="mb-2">
                                        <img src="../<?php echo $row->f_photo; ?>" alt="Current Photo" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                                        <div class="form-check mt-2">
                                            <input type="checkbox" class="form-check-input" name="remove_photo" value="1" id="removePhoto">
                                            <label class="form-check-label" for="removePhoto">Remove current photo</label>
                                        </div>
                                    </div>
                                <?php } ?>
                                <input type="file" class="form-control" name="f_photo" accept="image/*">
                                <small class="form-text text-muted">Upload new photo to replace current one (JPG, PNG, GIF)</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select class="form-control" name="f_status" required>
                                    <option value="Published" <?php if($row->f_status == 'Published') echo 'selected'; ?>>Published</option>
                                    <option value="Pending" <?php if($row->f_status == 'Pending') echo 'selected'; ?>>Pending</option>
                                </select>
                            </div>
                            
                            <button type="submit" name="update_feedback" class="btn btn-success">
                                <i class="fas fa-save"></i> Update Feedback
                            </button>
                            <a href="admin-manage-feedback.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </form>
                    </div>
                </div>
            </div>
            <?php include("vendor/inc/footer.php");?>
        </div>
    </div>
    
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal">
                        <span>×</span>
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
    <script src="js/sb-admin.min.js"></script>
</body>
</html>
