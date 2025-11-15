<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$results = [];
$errors = [];

// 1. Add unique constraint to t_ez_id
try {
    $mysqli->query("ALTER TABLE tms_technician ADD UNIQUE KEY unique_ez_id (t_ez_id)");
    $results[] = "✅ Added UNIQUE constraint to EZ ID";
} catch(Exception $e) {
    if(strpos($e->getMessage(), 'Duplicate') !== false) {
        $errors[] = "⚠️ EZ ID constraint already exists or duplicate values found";
    } else {
        $errors[] = "❌ Error adding EZ ID constraint: " . $e->getMessage();
    }
}

// 2. Add unique constraint to t_phone
try {
    $mysqli->query("ALTER TABLE tms_technician ADD UNIQUE KEY unique_phone (t_phone)");
    $results[] = "✅ Added UNIQUE constraint to Mobile Number";
} catch(Exception $e) {
    if(strpos($e->getMessage(), 'Duplicate') !== false) {
        $errors[] = "⚠️ Mobile Number constraint already exists or duplicate values found";
    } else {
        $errors[] = "❌ Error adding Mobile Number constraint: " . $e->getMessage();
    }
}

// 3. Check for duplicate EZ IDs
$dup_ez = $mysqli->query("SELECT t_ez_id, COUNT(*) as count FROM tms_technician WHERE t_ez_id IS NOT NULL AND t_ez_id != '' GROUP BY t_ez_id HAVING count > 1");
if($dup_ez && $dup_ez->num_rows > 0) {
    $errors[] = "⚠️ Found " . $dup_ez->num_rows . " duplicate EZ IDs - Please fix manually";
    while($row = $dup_ez->fetch_object()) {
        $errors[] = "   - EZ ID: " . $row->t_ez_id . " (used " . $row->count . " times)";
    }
} else {
    $results[] = "✅ No duplicate EZ IDs found";
}

// 4. Check for duplicate phone numbers
$dup_phone = $mysqli->query("SELECT t_phone, COUNT(*) as count FROM tms_technician WHERE t_phone IS NOT NULL AND t_phone != '' GROUP BY t_phone HAVING count > 1");
if($dup_phone && $dup_phone->num_rows > 0) {
    $errors[] = "⚠️ Found " . $dup_phone->num_rows . " duplicate Mobile Numbers - Please fix manually";
    while($row = $dup_phone->fetch_object()) {
        $errors[] = "   - Mobile: " . $row->t_phone . " (used " . $row->count . " times)";
    }
} else {
    $results[] = "✅ No duplicate Mobile Numbers found";
}

// 5. Check current constraints
$constraints = $mysqli->query("SHOW KEYS FROM tms_technician WHERE Key_name LIKE 'unique%'");
$constraint_list = [];
while($row = $constraints->fetch_object()) {
    $constraint_list[] = $row->Key_name . " on " . $row->Column_name;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setup Unique Constraints</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        .card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .result-item { padding: 10px; border-bottom: 1px solid #eee; }
        .result-item:last-child { border-bottom: none; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-shield-alt"></i> Setup Unique Constraints</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">Ensuring EZ ID and Mobile Number are unique for all technicians</p>
                <hr>
                
                <?php if(count($errors) > 0): ?>
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> Warnings/Errors</h5>
                    <?php foreach($errors as $error): ?>
                    <div class="result-item warning"><?php echo $error; ?></div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <?php if(count($results) > 0): ?>
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle"></i> Success</h5>
                    <?php foreach($results as $result): ?>
                    <div class="result-item success"><?php echo $result; ?></div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> Current Unique Constraints</h5>
                    <?php if(count($constraint_list) > 0): ?>
                        <?php foreach($constraint_list as $constraint): ?>
                        <div class="result-item">✓ <?php echo $constraint; ?></div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="mb-0">No unique constraints found</p>
                    <?php endif; ?>
                </div>
                
                <div class="mt-4">
                    <a href="admin-dashboard.php" class="btn btn-primary">
                        <i class="fas fa-home"></i> Back to Dashboard
                    </a>
                    <a href="admin-add-technician.php" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> Add Technician
                    </a>
                    <button onclick="location.reload()" class="btn btn-secondary">
                        <i class="fas fa-sync"></i> Re-run Setup
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
