<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

// Handle form submission
if(isset($_POST['update_settings'])) {
    $success_count = 0;
    $error_count = 0;
    
    foreach($_POST as $key => $value) {
        if(strpos($key, 'setting_') === 0) {
            $setting_key = str_replace('setting_', '', $key);
            $setting_value = trim($value);
            
            $update_query = "UPDATE tms_site_settings SET setting_value = ? WHERE setting_key = ?";
            $stmt = $mysqli->prepare($update_query);
            $stmt->bind_param('ss', $setting_value, $setting_key);
            
            if($stmt->execute()) {
                $success_count++;
            } else {
                $error_count++;
            }
        }
    }
    
    if($success_count > 0) {
        $_SESSION['settings_success'] = "Successfully updated $success_count setting(s)!";
    }
    if($error_count > 0) {
        $_SESSION['settings_error'] = "Failed to update $error_count setting(s).";
    }
    
    header("Location: admin-site-settings.php");
    exit();
}

// Get all settings grouped
$settings_query = "SELECT * FROM tms_site_settings ORDER BY setting_group, display_order";
$settings_result = $mysqli->query($settings_query);

$settings_by_group = [];
while($row = $settings_result->fetch_assoc()) {
    $settings_by_group[$row['setting_group']][] = $row;
}

$success_msg = isset($_SESSION['settings_success']) ? $_SESSION['settings_success'] : '';
$error_msg = isset($_SESSION['settings_error']) ? $_SESSION['settings_error'] : '';
unset($_SESSION['settings_success']);
unset($_SESSION['settings_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Site Contact Settings - Admin</title>
    
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="vendor/css/sb-admin.css" rel="stylesheet">
    
    <style>
        .settings-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }
        .settings-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .settings-card-body {
            padding: 25px;
            background: #f8f9fc;
        }
        .form-group label {
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-control {
            border-radius: 8px;
            border: 2px solid #e3e6f0;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 40px;
            font-weight: 600;
            border-radius: 8px;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .info-box {
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            border-left: 4px solid #00acc1;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .info-box i {
            color: #00acc1;
            font-size: 1.2rem;
        }
        .preview-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #1976d2;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-left: 10px;
        }
    </style>
</head>

<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    
    <div id="wrapper">
        <?php include("vendor/inc/sidebar.php");?>
        
        <div id="content-wrapper">
            <div class="container-fluid">
                
                <!-- Breadcrumbs -->
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="admin-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item active">Site Contact Info</li>
                </ol>
                
                <!-- Success/Error Messages -->
                <?php if(!empty($success_msg)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($error_msg)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>
                
                <!-- Info Box -->
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <strong>Centralized Contact Management:</strong> Update your contact information here and it will automatically reflect across your entire website - homepage, contact page, footer, and all other locations.
                </div>
                
                <!-- Settings Form -->
                <form method="POST" action="">
                    
                    <?php
                    $group_icons = [
                        'general' => 'fa-building',
                        'contact' => 'fa-address-book',
                        'social' => 'fa-share-alt'
                    ];
                    
                    $group_titles = [
                        'general' => 'Business Information',
                        'contact' => 'Contact Information',
                        'social' => 'Social Media'
                    ];
                    
                    foreach($settings_by_group as $group => $settings):
                    ?>
                    
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <i class="fas <?php echo $group_icons[$group] ?? 'fa-cog'; ?>"></i>
                            <?php echo $group_titles[$group] ?? ucfirst($group); ?>
                        </div>
                        <div class="settings-card-body">
                            <div class="row">
                                <?php foreach($settings as $setting): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label>
                                                <?php
                                                // Icon based on setting type
                                                $icon = 'fa-cog';
                                                if(strpos($setting['setting_key'], 'phone') !== false || strpos($setting['setting_key'], 'whatsapp') !== false) {
                                                    $icon = 'fa-phone';
                                                } elseif(strpos($setting['setting_key'], 'email') !== false) {
                                                    $icon = 'fa-envelope';
                                                } elseif(strpos($setting['setting_key'], 'instagram') !== false) {
                                                    $icon = 'fa-instagram';
                                                } elseif(strpos($setting['setting_key'], 'facebook') !== false) {
                                                    $icon = 'fa-facebook';
                                                } elseif(strpos($setting['setting_key'], 'twitter') !== false) {
                                                    $icon = 'fa-twitter';
                                                } elseif(strpos($setting['setting_key'], 'address') !== false) {
                                                    $icon = 'fa-map-marker-alt';
                                                }
                                                ?>
                                                <i class="fas <?php echo $icon; ?>"></i>
                                                <?php echo $setting['setting_label']; ?>
                                            </label>
                                            
                                            <?php if($setting['setting_type'] == 'textarea'): ?>
                                                <textarea 
                                                    name="setting_<?php echo $setting['setting_key']; ?>" 
                                                    class="form-control" 
                                                    rows="3"
                                                    placeholder="Enter <?php echo strtolower($setting['setting_label']); ?>"
                                                ><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                            <?php else: ?>
                                                <input 
                                                    type="<?php echo $setting['setting_type']; ?>" 
                                                    name="setting_<?php echo $setting['setting_key']; ?>" 
                                                    class="form-control" 
                                                    value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                    placeholder="Enter <?php echo strtolower($setting['setting_label']); ?>"
                                                    <?php if($setting['setting_type'] == 'tel'): ?>
                                                        pattern="[0-9]{10}"
                                                        maxlength="10"
                                                    <?php endif; ?>
                                                >
                                            <?php endif; ?>
                                            
                                            <?php if(!empty($setting['setting_value'])): ?>
                                                <span class="preview-badge">
                                                    <i class="fas fa-check"></i> Active
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php endforeach; ?>
                    
                    <!-- Save Button -->
                    <div class="text-center mb-4">
                        <button type="submit" name="update_settings" class="btn btn-save">
                            <i class="fas fa-save"></i> Save All Changes
                        </button>
                    </div>
                    
                </form>
                
            </div>
            <!-- /.container-fluid -->
            
            <!-- Sticky Footer -->
            <?php include("vendor/inc/footer.php");?>
            
        </div>
        <!-- /.content-wrapper -->
    </div>
    <!-- /#wrapper -->
    
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <!-- Logout Modal-->
    <?php include("vendor/inc/logout.php");?>
    
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    
    <!-- Custom scripts for all pages-->
    <script src="vendor/js/sb-admin.min.js"></script>
    
</body>
</html>
