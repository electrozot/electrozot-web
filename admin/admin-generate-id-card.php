<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

$technician = null;
$error = '';

// Handle form submission
if(isset($_POST['search_technician'])) {
    $phone = preg_replace('/\D/', '', $_POST['technician_phone']);
    
    if(strlen($phone) === 10) {
        $query = "SELECT * FROM tms_technician WHERE t_phone = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('s', $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $technician = $result->fetch_object();
        } else {
            $error = "No technician found with this phone number.";
        }
    } else {
        $error = "Please enter a valid 10-digit phone number.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Generate Technician ID Card - Admin</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="vendor/css/sb-admin.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        .id-card-container {
            perspective: 1000px;
            margin: 30px auto;
        }
        
        .id-card {
            width: 350px;
            height: auto;
            min-height: 550px;
            position: relative;
            background: linear-gradient(135deg, #dc143c 0%, #8b0000 100%);
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: visible;
            margin: 0 auto;
            padding-bottom: 55px;
        }
        
        .id-card-header {
            background: rgba(255,255,255,0.1);
            padding: 12px;
            text-align: center;
            border-bottom: 2px solid rgba(255,255,255,0.2);
        }
        
        .id-card-logo {
            width: 80px;
            height: auto;
            margin-bottom: 5px;
        }
        
        .id-card-company {
            color: white;
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .id-card-tagline {
            color: white;
            font-size: 12px;
            font-style: italic;
            opacity: 0.9;
        }
        
        .id-card-body {
            padding: 15px;
        }
        
        .id-card-photo-container {
            width: 100px;
            height: 100px;
            margin: 0 auto 12px;
            border-radius: 12px;
            overflow: hidden;
            border: 3px solid white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            background: white;
        }
        
        .id-card-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .id-card-info {
            background: rgba(255,255,255,0.95);
            padding: 12px;
            border-radius: 12px;
            margin-top: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .id-card-field {
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .id-card-field:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .id-card-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 2px;
            letter-spacing: 0.3px;
        }
        
        .id-card-value {
            font-size: 11px;
            color: #333;
            font-weight: 700;
            line-height: 1.3;
            word-wrap: break-word;
        }
        
        .id-card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.4);
            padding: 8px 10px;
            text-align: center;
            color: white;
            font-size: 10px;
            line-height: 1.4;
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }
        
        .id-card-footer strong {
            font-size: 11px;
            display: block;
            margin-bottom: 2px;
        }
        
        .action-buttons {
            margin-top: 30px;
            text-align: center;
        }
        
        .action-buttons .btn {
            margin: 5px;
            min-width: 150px;
        }
    </style>
</head>

<body id="page-top">
    <?php include("vendor/inc/nav.php");?>
    
    <div id="wrapper">
        <?php include("vendor/inc/sidebar.php");?>
        
        <div id="content-wrapper">
            <div class="container-fluid">
                
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="admin-dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item active">Generate ID Card</li>
                </ol>
                
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-id-card"></i> Generate Technician ID Card</h5>
                    </div>
                    <div class="card-body">
                        
                        <!-- Search Form -->
                        <form method="POST" class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enter Technician Mobile Number</label>
                                        <input type="tel" name="technician_phone" class="form-control" 
                                               placeholder="10-digit mobile number" 
                                               maxlength="10" 
                                               pattern="[0-9]{10}"
                                               required
                                               oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>&nbsp;</label>
                                    <button type="submit" name="search_technician" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> Search Technician
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <?php if($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($technician): ?>
                        
                        <!-- ID Card Preview -->
                        <div class="id-card-container">
                            <div class="id-card" id="idCard">
                                <div class="id-card-header">
                                    <img src="../vendor/EZlogonew.png" alt="Electrozot Logo" class="id-card-logo">
                                    <div class="id-card-company">ELECTROZOT</div>
                                    <div class="id-card-tagline">We Make Perfect</div>
                                </div>
                                
                                <div class="id-card-body">
                                    <div class="id-card-photo-container">
                                        <?php if(!empty($technician->t_pic)): ?>
                                        <img src="../vendor/img/<?php echo htmlspecialchars($technician->t_pic); ?>" 
                                             alt="Technician Photo" class="id-card-photo">
                                        <?php else: ?>
                                        <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#f0f0f0;">
                                            <i class="fas fa-user" style="font-size:60px; color:#ccc;"></i>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="id-card-info" style="display: flex; gap: 10px;">
                                        <!-- Left side - Technician Details -->
                                        <div style="flex: 1;">
                                            <div class="id-card-field">
                                                <div class="id-card-label">Name</div>
                                                <div class="id-card-value"><?php echo htmlspecialchars($technician->t_name); ?></div>
                                            </div>
                                            
                                            <div class="id-card-field">
                                                <div class="id-card-label">Employee ID</div>
                                                <div class="id-card-value">
                                                    <?php 
                                                    $emp_id = '';
                                                    if(!empty($technician->t_ez_id)) {
                                                        $emp_id = $technician->t_ez_id;
                                                    } elseif(!empty($technician->t_id_no)) {
                                                        $emp_id = $technician->t_id_no;
                                                    } else {
                                                        $emp_id = 'EZ' . str_pad($technician->t_id, 4, '0', STR_PAD_LEFT);
                                                    }
                                                    echo htmlspecialchars($emp_id);
                                                    ?>
                                                </div>
                                            </div>
                                        
                                        <?php if(!empty($technician->t_email)): ?>
                                        <div class="id-card-field">
                                            <div class="id-card-label">Email</div>
                                            <div class="id-card-value" style="font-size: 13px;"><?php echo htmlspecialchars($technician->t_email); ?></div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="id-card-field">
                                            <div class="id-card-label">Category</div>
                                            <div class="id-card-value" style="font-size: 13px;"><?php echo htmlspecialchars($technician->t_category); ?></div>
                                        </div>
                                        
                                        <?php if(!empty($technician->t_specialization)): ?>
                                        <div class="id-card-field">
                                            <div class="id-card-label">Specialization</div>
                                            <div class="id-card-value" style="font-size: 13px;"><?php echo htmlspecialchars($technician->t_specialization); ?></div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($technician->t_experience)): ?>
                                        <div class="id-card-field">
                                            <div class="id-card-label">Experience</div>
                                            <div class="id-card-value"><?php echo htmlspecialchars($technician->t_experience); ?> Years</div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($technician->t_service_pincode)): ?>
                                        <div class="id-card-field">
                                            <div class="id-card-label">Service Area</div>
                                            <div class="id-card-value">Pincode: <?php echo htmlspecialchars($technician->t_service_pincode); ?></div>
                                        </div>
                                        <?php endif; ?>
                                        
                                            <?php if(!empty($technician->t_addr)): ?>
                                            <div class="id-card-field">
                                                <div class="id-card-label">Address</div>
                                                <div class="id-card-value" style="font-size: 11px; line-height: 1.3;"><?php echo htmlspecialchars($technician->t_addr); ?></div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Right side - QR Code -->
                                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px;">
                                            <div id="qrcode" style="background: white; padding: 8px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);"></div>
                                            <div style="margin-top: 5px; font-size: 7px; color: #666; text-align: center; font-weight: 600;">
                                                Scan to Visit<br>electrozot.in
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="id-card-footer">
                                    <strong>Authorized Technician</strong><br>
                                    Contact: 7559606925 | www.electrozot.in
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button onclick="downloadAsImage()" class="btn btn-success btn-lg">
                                <i class="fas fa-download"></i> Download as Image
                            </button>
                            <button onclick="downloadAsPDF()" class="btn btn-danger btn-lg">
                                <i class="fas fa-file-pdf"></i> Download as PDF
                            </button>
                            <button onclick="sendToWhatsApp()" class="btn btn-success btn-lg" style="background: #25D366;">
                                <i class="fab fa-whatsapp"></i> Send to WhatsApp
                            </button>
                            <button onclick="deleteCard()" class="btn btn-danger btn-lg">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                        
                        <!-- WhatsApp Instructions -->
                        <div class="alert alert-info mt-3" style="font-size: 0.9rem;">
                            <strong><i class="fab fa-whatsapp"></i> WhatsApp Instructions:</strong><br>
                            1. Click "Send to WhatsApp" - PDF and Image will download automatically<br>
                            2. WhatsApp will open with welcome message ready to send<br>
                            3. Click the attachment icon (📎) in WhatsApp<br>
                            4. Select "Document" and choose the downloaded PDF from your Downloads folder<br>
                            5. Click Send to deliver the ID card with the message<br>
                            <small class="text-muted">Note: Files download directly to your device. Simply attach the PDF from your Downloads folder in WhatsApp.</small>
                        </div>
                        
                        <input type="hidden" id="techPhone" value="<?php echo htmlspecialchars($technician->t_phone); ?>">
                        <input type="hidden" id="techName" value="<?php echo htmlspecialchars($technician->t_name); ?>">
                        
                        <?php endif; ?>
                        
                    </div>
                </div>
                
            </div>
            
            <?php include("vendor/inc/footer.php");?>
        </div>
    </div>
    
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/js/sb-admin.min.js"></script>
    <script src="vendor/js/swal.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    
    <?php if($technician): ?>
    <script>
        // Generate QR Code with dark maroon color
        document.addEventListener('DOMContentLoaded', function() {
            var qrcode = new QRCode(document.getElementById("qrcode"), {
                text: "https://electrozot.in",
                width: 90,
                height: 90,
                colorDark: "#8b0000",  // Dark maroon matching card theme
                colorLight: "#ffffff",  // White background
                correctLevel: QRCode.CorrectLevel.H
            });
            
            console.log('✅ QR Code generated with dark maroon color');
        });
    </script>
    <?php endif; ?>
    
    <script>
        // Download as Image
        function downloadAsImage() {
            const card = document.getElementById('idCard');
            const techName = document.getElementById('techName').value;
            
            html2canvas(card, {
                scale: 2,
                backgroundColor: null,
                logging: false
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = `ID_Card_${techName.replace(/\s+/g, '_')}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
                
                swal("Success!", "ID Card downloaded successfully!", "success");
            }).catch(error => {
                console.error('Error:', error);
                swal("Error!", "Failed to download ID card", "error");
            });
        }
        
        // Download as PDF
        function downloadAsPDF() {
            const card = document.getElementById('idCard');
            const techName = document.getElementById('techName').value;
            
            html2canvas(card, {
                scale: 2,
                backgroundColor: null,
                logging: false
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });
                
                const imgWidth = 100;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                const x = (pdf.internal.pageSize.getWidth() - imgWidth) / 2;
                const y = 20;
                
                pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);
                pdf.save(`ID_Card_${techName.replace(/\s+/g, '_')}.pdf`);
                
                swal("Success!", "ID Card PDF downloaded successfully!", "success");
            }).catch(error => {
                console.error('Error:', error);
                swal("Error!", "Failed to generate PDF", "error");
            });
        }
        
        // Send to WhatsApp
        function sendToWhatsApp() {
            const card = document.getElementById('idCard');
            const techPhone = document.getElementById('techPhone').value;
            const techName = document.getElementById('techName').value;
            
            swal({
                title: "Preparing ID Card...",
                text: "Generating PDF file for download...",
                icon: "info",
                buttons: false,
                closeOnClickOutside: false
            });
            
            html2canvas(card, {
                scale: 2,
                backgroundColor: '#dc143c',
                logging: false,
                useCORS: true,
                allowTaint: true
            }).then(canvas => {
                // Generate PDF
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });
                
                const imgData = canvas.toDataURL('image/png');
                const imgWidth = 100;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                const x = (pdf.internal.pageSize.getWidth() - imgWidth) / 2;
                const y = 20;
                
                pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);
                
                // Download PDF automatically
                const pdfFilename = `ID_Card_${techName.replace(/\s+/g, '_')}.pdf`;
                pdf.save(pdfFilename);
                
                // Also download image
                const link = document.createElement('a');
                link.download = `ID_Card_${techName.replace(/\s+/g, '_')}.png`;
                link.href = imgData;
                link.click();
                
                // Format phone number for WhatsApp
                const whatsappNumber = '91' + techPhone;
                
                // Create WhatsApp message
                const message = `Hi ${techName}! 👋\n\n` +
                    `Welcome to Electrozot! ⚡\n\n` +
                    `Mohit Choudhary welcomes you to the Electrozot Team! 🎉\n\n` +
                    `📋 Your Technician ID Card has been generated!\n\n` +
                    `The PDF and Image files have been downloaded to your device.\n\n` +
                    `Please save your ID card and keep it with you during service visits.\n\n` +
                    `We're excited to have you on board! 💪\n\n` +
                    `Best regards,\n` +
                    `Mohit Choudhary\n` +
                    `Electrozot Management\n\n` +
                    `📞 Contact: 7559606925\n` +
                    `🌐 Website: www.electrozot.in`;
                
                const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
                
                swal.close();
                
                // Show success message with instructions
                swal({
                    title: "ID Card Downloaded!",
                    html: `<div style="text-align: left;">
                           <p><strong>✅ Files downloaded successfully!</strong></p>
                           <p>📄 <strong>PDF:</strong> ${pdfFilename}</p>
                           <p>🖼️ <strong>Image:</strong> ID_Card_${techName.replace(/\s+/g, '_')}.png</p>
                           <hr>
                           <p><strong>Next Steps:</strong></p>
                           <ol style="font-size: 0.9rem;">
                               <li>Click "Open WhatsApp" below</li>
                               <li>In WhatsApp, click the <strong>📎 attachment icon</strong></li>
                               <li>Select <strong>"Document"</strong></li>
                               <li>Choose the downloaded PDF file from your Downloads folder</li>
                               <li>Click <strong>Send</strong></li>
                           </ol>
                           <p class="text-info" style="font-size: 0.85rem; margin-top: 10px;">
                               <strong>💡 Tip:</strong> Check your Downloads folder for the PDF file.
                           </p>
                           </div>`,
                    icon: "success",
                    buttons: {
                        cancel: {
                            text: "Later",
                            value: false,
                            visible: true,
                            className: "btn-secondary"
                        },
                        confirm: {
                            text: "Open WhatsApp",
                            value: true,
                            className: "btn-success"
                        }
                    }
                }).then((value) => {
                    if(value) {
                        // Open WhatsApp
                        window.open(whatsappUrl, '_blank');
                        
                        // Show reminder after delay
                        setTimeout(() => {
                            swal({
                                title: "📎 Attach the PDF",
                                html: `<div style="text-align: left;">
                                        <p><strong>WhatsApp is now open!</strong></p>
                                        <hr>
                                        <p><strong>To attach the ID card PDF:</strong></p>
                                        <ol>
                                            <li>Click the <strong>📎 attachment icon</strong> in WhatsApp</li>
                                            <li>Select <strong>"Document"</strong></li>
                                            <li>Browse to your <strong>Downloads</strong> folder</li>
                                            <li>Select <strong>${pdfFilename}</strong></li>
                                            <li>Click <strong>Send</strong></li>
                                        </ol>
                                        <p class="text-success" style="font-size: 0.9rem; margin-top: 10px;">
                                            ✅ The welcome message is already in the chat!
                                        </p>
                                       </div>`,
                                icon: "info",
                                button: {
                                    text: "Got it!",
                                    className: "btn-primary"
                                }
                            });
                        }, 1500);
                    }
                });
                
            }).catch(error => {
                console.error('Error:', error);
                swal("Error!", "Failed to generate ID card. Please try again.", "error");
            });
        }
        
        // Delete Card
        function deleteCard() {
            swal({
                title: "Are you sure?",
                text: "Do you want to clear this ID card?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    window.location.href = 'admin-generate-id-card.php';
                }
            });
        }
    </script>

</body>
</html>
