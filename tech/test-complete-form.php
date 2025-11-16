<?php
/**
 * Test Complete Booking Form
 * This page helps debug form submission issues
 */
session_start();
include('../admin/vendor/inc/config.php');

// Check if form was submitted
if(isset($_POST['test_submit'])) {
    echo "<h2>Form Submitted Successfully!</h2>";
    echo "<h3>POST Data:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h3>FILES Data:</h3>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    
    echo "<hr>";
    echo "<a href='test-complete-form.php'>Try Again</a>";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Complete Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #667eea;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #5568d3;
        }
        .preview {
            max-width: 200px;
            margin-top: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Test Complete Booking Form</h1>
    <p>This is a simplified version to test if form submission works.</p>
    
    <form method="POST" enctype="multipart/form-data" id="testForm">
        <div class="form-group">
            <label>Service Image:</label>
            <input type="file" name="service_image" id="serviceImage" accept="image/*" required>
            <img id="servicePreview" class="preview" style="display: none;">
        </div>
        
        <div class="form-group">
            <label>Bill Image:</label>
            <input type="file" name="bill_image" id="billImage" accept="image/*" required>
            <img id="billPreview" class="preview" style="display: none;">
        </div>
        
        <div class="form-group">
            <label>Amount Charged:</label>
            <input type="number" name="amount_charged" step="0.01" min="0" required>
        </div>
        
        <button type="submit" name="test_submit">Submit Test Form</button>
    </form>
    
    <script>
        // Preview images
        document.getElementById('serviceImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('servicePreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
        
        document.getElementById('billImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('billPreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Log form submission
        document.getElementById('testForm').addEventListener('submit', function(e) {
            console.log('Form submitting...');
            console.log('Service image files:', document.getElementById('serviceImage').files.length);
            console.log('Bill image files:', document.getElementById('billImage').files.length);
        });
    </script>
</body>
</html>
