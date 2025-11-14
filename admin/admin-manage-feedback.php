<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
  
  // Add f_photo column if it doesn't exist
  $check_column = $mysqli->query("SHOW COLUMNS FROM tms_feedback LIKE 'f_photo'");
  if($check_column->num_rows == 0) {
      $mysqli->query("ALTER TABLE tms_feedback ADD COLUMN f_photo VARCHAR(255) DEFAULT NULL");
  }
  
  // Create feedbacks directory if it doesn't exist
  $dir = "../vendor/img/feedbacks/";
  if (!file_exists($dir)) {
      mkdir($dir, 0777, true);
  }
  
  // Delete feedback
  if(isset($_GET['delete'])) {
    $f_id = $_GET['delete'];
    
    // Get photo path before deleting
    $ret = "SELECT f_photo FROM tms_feedback WHERE f_id=?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $f_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if($row = $res->fetch_object()) {
        if($row->f_photo && file_exists("../" . $row->f_photo)) {
            unlink("../" . $row->f_photo);
        }
    }
    
    // Delete feedback
    $query = "DELETE FROM tms_feedback WHERE f_id=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $f_id);
    if($stmt->execute()) {
        $succ = "Feedback deleted successfully";
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
                    <li class="breadcrumb-item active">Manage</li>
                </ol>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-comments"></i> Manage Feedbacks
                        <a href="admin-add-feedback.php" class="btn btn-sm btn-success float-right">
                            <i class="fas fa-plus"></i> Add New Feedback
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if(isset($succ)) { ?>
                            <div class="alert alert-success"><?php echo $succ; ?></div>
                        <?php } ?>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="dataTable" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Feedback</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret = "SELECT * FROM tms_feedback ORDER BY f_id DESC";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $cnt = 1;
                                    while($row = $res->fetch_object()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $cnt; ?></td>
                                        <td>
                                            <?php if(isset($row->f_photo) && $row->f_photo) { ?>
                                                <img src="../<?php echo $row->f_photo; ?>" alt="Client" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                            <?php } else { ?>
                                                <div style="width: 50px; height: 50px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                                    <?php echo strtoupper(substr($row->f_uname, 0, 1)); ?>
                                                </div>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo $row->f_uname; ?></td>
                                        <td><?php echo substr($row->f_content, 0, 100); ?>...</td>
                                        <td>
                                            <?php if($row->f_status == 'Published') { ?>
                                                <span class="badge badge-success"><?php echo $row->f_status; ?></span>
                                            <?php } else { ?>
                                                <span class="badge badge-warning"><?php echo $row->f_status; ?></span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <a href="admin-edit-feedback.php?f_id=<?php echo $row->f_id; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="admin-manage-feedback.php?delete=<?php echo $row->f_id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this feedback?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $cnt++; } ?>
                                </tbody>
                            </table>
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
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="js/sb-admin.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
</body>
</html>
