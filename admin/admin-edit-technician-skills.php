<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$t_id = isset($_GET['t_id']) ? intval($_GET['t_id']) : 0;

// Get technician details
$tech_query = "SELECT * FROM tms_technician WHERE t_id = ?";
$stmt = $mysqli->prepare($tech_query);
$stmt->bind_param('i', $t_id);
$stmt->execute();
$tech = $stmt->get_result()->fetch_object();

if (!$tech) {
    die("Technician not found");
}

// Get all services
$services_query = "SELECT s_id, s_name, s_category, s_subcategory FROM tms_service ORDER BY s_category, s_subcategory, s_name";
$services = $mysqli->query($services_query);

// Get current skills as array
$current_skills = $tech->t_skills ? array_map('trim', explode(',', $tech->t_skills)) : [];

// Handle form submission
if ($_POST && isset($_POST['update_skills'])) {
    $selected_skills = isset($_POST['skills']) ? $_POST['skills'] : [];
    $skills_string = implode(',', $selected_skills);
    
    $update_query = "UPDATE tms_technician SET t_skills = ? WHERE t_id = ?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param('si', $skills_string, $t_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Skills updated successfully!";
        header("Location: admin-edit-technician-skills.php?t_id=" . $t_id);
        exit;
    } else {
        $error = "Error updating skills: " . $mysqli->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Technician Skills - <?php echo htmlspecialchars($tech->t_name); ?></title>
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .tech-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        .category-section {
            margin-bottom: 30px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }
        .category-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .category-content {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        .skill-item {
            display: flex;
            align-items: center;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .skill-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }
        .skill-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
        }
        .skill-item label {
            cursor: pointer;
            flex: 1;
            font-size: 0.95rem;
        }
        .skill-item.checked {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 2px solid #28a745;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .actions {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 20px 0;
            border-top: 2px solid #e9ecef;
            margin-top: 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        .selected-count {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <i class="fas fa-tools"></i>
            Edit Skills for <?php echo htmlspecialchars($tech->t_name); ?>
        </h1>
        
        <div class="tech-info">
            <strong>Category:</strong> <?php echo htmlspecialchars($tech->t_category); ?> | 
            <strong>Specialization:</strong> <?php echo htmlspecialchars($tech->t_specialization); ?> |
            <strong>Experience:</strong> <?php echo htmlspecialchars($tech->t_experience); ?> years
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="selected-count">
            <i class="fas fa-check-double"></i> 
            Selected Skills: <span id="count"><?php echo count($current_skills); ?></span>
        </div>
        
        <form method="POST">
            <?php
            $current_category = '';
            $services->data_seek(0);
            
            while ($service = $services->fetch_object()) {
                if ($current_category != $service->s_category) {
                    if ($current_category != '') {
                        echo "</div></div>"; // Close previous category
                    }
                    $current_category = $service->s_category;
                    $category_id = str_replace(' ', '_', strtolower($current_category));
                    
                    echo "<div class='category-section'>";
                    echo "<div class='category-header' onclick='toggleCategory(\"{$category_id}\")'>";
                    echo "<span><i class='fas fa-wrench'></i> {$current_category}</span>";
                    echo "<i class='fas fa-chevron-down' id='icon_{$category_id}'></i>";
                    echo "</div>";
                    echo "<div class='category-content' id='{$category_id}'>";
                }
                
                $is_checked = in_array($service->s_name, $current_skills);
                $checked_class = $is_checked ? 'checked' : '';
                
                echo "<div class='skill-item {$checked_class}' onclick='toggleSkill(this)'>";
                echo "<input type='checkbox' name='skills[]' value='" . htmlspecialchars($service->s_name) . "' " . ($is_checked ? 'checked' : '') . " onchange='updateCount()'>";
                echo "<label>" . htmlspecialchars($service->s_name) . "</label>";
                echo "</div>";
            }
            
            if ($current_category != '') {
                echo "</div></div>"; // Close last category
            }
            ?>
            
            <div class="actions">
                <button type="submit" name="update_skills" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Skills
                </button>
                <a href="admin-manage-technician.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Technicians
                </a>
            </div>
        </form>
    </div>
    
    <script>
        function toggleCategory(id) {
            const content = document.getElementById(id);
            const icon = document.getElementById('icon_' + id);
            
            if (content.style.display === 'none') {
                content.style.display = 'grid';
                icon.className = 'fas fa-chevron-down';
            } else {
                content.style.display = 'none';
                icon.className = 'fas fa-chevron-right';
            }
        }
        
        function toggleSkill(element) {
            const checkbox = element.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            
            if (checkbox.checked) {
                element.classList.add('checked');
            } else {
                element.classList.remove('checked');
            }
            
            updateCount();
        }
        
        function updateCount() {
            const count = document.querySelectorAll('input[type="checkbox"]:checked').length;
            document.getElementById('count').textContent = count;
        }
        
        // Initialize count on page load
        updateCount();
    </script>
</body>
</html>
