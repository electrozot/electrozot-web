<?php
/**
 * Bill Generator for Electrozot
 * Generates service completion bills in HTML format (can be printed as PDF)
 */

class BillGenerator {
    private $mysqli;
    private $booking_id;
    private $booking_data;
    private $site_settings;
    
    public function __construct($mysqli, $booking_id) {
        $this->mysqli = $mysqli;
        $this->booking_id = $booking_id;
        $this->loadSiteSettings();
        $this->loadBookingData();
    }
    
    private function loadSiteSettings() {
        // Load site settings from database
        $query = "SELECT setting_key, setting_value FROM tms_site_settings";
        $result = $this->mysqli->query($query);
        
        $this->site_settings = [];
        if($result) {
            while($row = $result->fetch_assoc()) {
                $this->site_settings[$row['setting_key']] = $row['setting_value'];
            }
        }
        
        // Set defaults if settings don't exist
        if(empty($this->site_settings['site_name'])) {
            $this->site_settings['site_name'] = 'Electrozot';
        }
        if(empty($this->site_settings['site_tagline'])) {
            $this->site_settings['site_tagline'] = 'We Make Perfect';
        }
        if(empty($this->site_settings['primary_phone'])) {
            $this->site_settings['primary_phone'] = '7559606925';
        }
        if(empty($this->site_settings['primary_email'])) {
            $this->site_settings['primary_email'] = 'info@electrozot.com';
        }
        if(empty($this->site_settings['business_address'])) {
            $this->site_settings['business_address'] = 'Electronic and Plumbing Solution Provider';
        }
    }
    
    private function loadBookingData() {
        $query = "SELECT sb.*, 
                         u.u_fname, u.u_lname, u.u_email, u.u_phone, u.u_addr,
                         t.t_name as tech_name, t.t_email as tech_email,
                         s.s_name as service_name, s.s_description as service_desc,
                         pc.pc_amount as payment_amount
                  FROM tms_service_booking sb
                  LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                  LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id
                  LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                  LEFT JOIN tms_payment_collection pc ON sb.sb_id = pc.pc_booking_id
                  WHERE sb.sb_id = ?";
        
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $this->booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->booking_data = $result->fetch_object();
    }
    
    public function generateBillHTML() {
        if(!$this->booking_data) {
            return "<p>Booking not found</p>";
        }
        
        $b = $this->booking_data;
        $bill_number = "EZ-" . str_pad($b->sb_id, 6, '0', STR_PAD_LEFT);
        $bill_date = date('d M Y', strtotime($b->sb_completed_date ?? $b->sb_booking_date));
        $service_date = date('d M Y', strtotime($b->sb_booking_date));
        
        // Determine final amount using priority: payment_amount > sb_final_price > sb_tech_decided_price > sb_total_price
        $final_amount = 0;
        if(!empty($b->payment_amount) && $b->payment_amount > 0) {
            $final_amount = $b->payment_amount;
        } elseif(!empty($b->sb_final_price) && $b->sb_final_price > 0) {
            $final_amount = $b->sb_final_price;
        } elseif(!empty($b->sb_tech_decided_price) && $b->sb_tech_decided_price > 0) {
            $final_amount = $b->sb_tech_decided_price;
        } elseif(!empty($b->sb_total_price) && $b->sb_total_price > 0) {
            $final_amount = $b->sb_total_price;
        }
        
        // Service location
        $service_location = !empty($b->sb_address) ? $b->sb_address : $b->u_addr;
        
        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Bill - ' . $bill_number . '</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; }
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f5f5f5;
            padding: 10px;
        }
        
        .bill-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        
        .bill-header {
            background: linear-gradient(135deg, #dc143c 0%, #8b0000 100%);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .logo {
            width: 60px;
            height: auto;
        }
        
        .company-info {
            text-align: left;
        }
        
        .company-name {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 2px;
            letter-spacing: 1px;
        }
        
        .tagline {
            font-size: 11px;
            font-style: italic;
            opacity: 0.95;
        }
        
        .header-right {
            text-align: right;
        }
        
        .contact-label {
            font-size: 10px;
            opacity: 0.9;
            margin-bottom: 3px;
        }
        
        .contact-number {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        
        .bill-title {
            width: 100%;
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid rgba(255,255,255,0.3);
            text-align: center;
        }
        
        .bill-info {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            background: #f8f9fa;
            border-bottom: 2px solid #dc143c;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .bill-info-section {
            flex: 1;
            min-width: 150px;
        }
        
        .bill-info-section h3 {
            color: #dc143c;
            font-size: 11px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .bill-info-section p {
            color: #333;
            line-height: 1.4;
            font-size: 12px;
        }
        
        .bill-body {
            padding: 15px;
        }
        
        .section-title {
            color: #dc143c;
            font-size: 13px;
            font-weight: bold;
            margin: 15px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #f5f5f5;
            flex-wrap: wrap;
        }
        
        .info-label {
            width: 140px;
            min-width: 140px;
            font-weight: 600;
            color: #555;
            font-size: 12px;
        }
        
        .info-value {
            flex: 1;
            color: #333;
            font-size: 12px;
            word-break: break-word;
        }
        
        .service-details {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            margin: 12px 0;
        }
        
        .amount-section {
            background: transparent;
            color: #333;
            padding: 15px 0;
            margin: 15px 0;
            border-top: 2px solid #e0e0e0;
            border-bottom: 2px solid #e0e0e0;
            text-align: left;
        }
        
        .amount-label {
            font-size: 13px;
            margin-bottom: 8px;
            color: #666;
            font-weight: 600;
        }
        
        .amount-value {
            font-size: 32px;
            font-weight: bold;
            color: #dc143c;
        }
        
        .bill-footer {
            background: #f8f9fa;
            padding: 15px;
            border-top: 2px solid #dc143c;
            text-align: center;
        }
        
        .footer-text {
            color: #666;
            font-size: 12px;
            line-height: 1.6;
        }
        
        .contact-info {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 11px;
        }
        
        /* Responsive Design */
        @media (max-width: 600px) {
            body { padding: 5px; }
            .bill-header { padding: 10px; flex-direction: column; align-items: flex-start; }
            .header-left { width: 100%; }
            .header-right { width: 100%; text-align: left; margin-top: 5px; }
            .logo { width: 50px; }
            .company-name { font-size: 18px; }
            .tagline { font-size: 10px; }
            .contact-number { font-size: 16px; }
            .bill-title { font-size: 12px; margin-top: 8px; padding-top: 8px; }
            .bill-info { padding: 10px; flex-direction: column; gap: 10px; }
            .bill-info-section { min-width: 100%; }
            .bill-body { padding: 10px; }
            .info-label { width: 100%; min-width: 100%; margin-bottom: 3px; }
            .info-row { flex-direction: column; padding: 6px 0; }
            .amount-value { font-size: 28px; }
            .section-title { font-size: 12px; }
        }
    </style>
</head>
<body>
    <div class="bill-container">
        <!-- Header -->
        <div class="bill-header">
            <div class="header-left">
                <img src="' . $this->getLogoPath() . '" alt="' . htmlspecialchars($this->site_settings['site_name']) . ' Logo" class="logo">
                <div class="company-info">
                    <div class="company-name">' . strtoupper(htmlspecialchars($this->site_settings['site_name'])) . '</div>
                    <div class="tagline">' . htmlspecialchars($this->site_settings['site_tagline']) . '</div>
                </div>
            </div>
            <div class="header-right">
                <div class="contact-label">Contact Us</div>
                <div class="contact-number">' . htmlspecialchars($this->site_settings['primary_phone']) . '</div>
            </div>
            <div class="bill-title">SERVICE COMPLETION BILL</div>
        </div>
        
        <!-- Bill Info -->
        <div class="bill-info">
            <div class="bill-info-section">
                <h3>Bill Details</h3>
                <p><strong>Bill No:</strong> ' . $bill_number . '</p>
                <p><strong>Date:</strong> ' . $bill_date . '</p>
                <p><strong>Service Date:</strong> ' . $service_date . '</p>
            </div>
            <div class="bill-info-section" style="text-align: right;">
                <h3>Company Info</h3>
                <p><strong>' . htmlspecialchars($this->site_settings['site_name']) . '</strong></p>
                <p>Dharamshala 176215</p>
                <p>Email: ' . htmlspecialchars($this->site_settings['primary_email']) . '</p>
            </div>
        </div>
        
        <!-- Bill Body -->
        <div class="bill-body">
            <!-- Customer Details -->
            <div class="section-title">CUSTOMER DETAILS</div>
            <div class="info-row">
                <div class="info-label">Customer Name:</div>
                <div class="info-value">' . htmlspecialchars($b->u_fname . ' ' . $b->u_lname) . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone Number:</div>
                <div class="info-value">' . htmlspecialchars($b->u_phone) . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">' . htmlspecialchars($b->u_email) . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">Service Location:</div>
                <div class="info-value">' . htmlspecialchars($service_location) . '</div>
            </div>
            
            <!-- Technician Details -->
            <div class="section-title">TECHNICIAN DETAILS</div>
            <div class="info-row">
                <div class="info-label">Technician Name:</div>
                <div class="info-value">' . htmlspecialchars($b->tech_name) . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">Technician Email:</div>
                <div class="info-value">' . htmlspecialchars($b->tech_email) . '</div>
            </div>
            
            <!-- Service Details -->
            <div class="section-title">SERVICE DETAILS</div>
            <div class="service-details">
                <div class="info-row" style="border: none;">
                    <div class="info-label">Service Name:</div>
                    <div class="info-value"><strong>' . htmlspecialchars($b->service_name ?? $b->sb_service_name) . '</strong></div>
                </div>';
                
        if(!empty($b->sb_completion_notes)) {
            $html .= '
                <div class="info-row" style="border: none;">
                    <div class="info-label">Service Notes:</div>
                    <div class="info-value">' . nl2br(htmlspecialchars($b->sb_completion_notes)) . '</div>
                </div>';
        }
        
        $html .= '
            </div>
            
            <!-- Amount Section -->
            <div class="amount-section">
                <div class="amount-label">TOTAL AMOUNT PAID</div>
                <div class="amount-value">₹' . number_format($final_amount, 2) . '</div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="bill-footer">
            <div class="footer-text">
                <strong>Thank you for choosing ' . htmlspecialchars($this->site_settings['site_name']) . '!</strong>
            </div>
            <div class="contact-info">
                <strong>' . htmlspecialchars($this->site_settings['site_name']) . '</strong> | Phone: ' . htmlspecialchars($this->site_settings['primary_phone']) . ' | Email: ' . htmlspecialchars($this->site_settings['primary_email']) . '<br>
                <em>' . htmlspecialchars($this->site_settings['site_tagline']) . ' - Your Trusted Service Partner</em>
            </div>
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
    
    private function getLogoPath() {
        // Get the base URL dynamically
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        
        // Get the base path (assuming structure like /ez/electrozot/)
        $script_path = dirname(dirname($_SERVER['SCRIPT_NAME']));
        
        // Build absolute URL to logo
        $logo_url = $protocol . "://" . $host . $script_path . "/vendor/EZlogonew.png";
        
        return $logo_url;
    }
    
    public function saveBillRecord() {
        // Save bill generation record in database
        $bill_number = "EZ-" . str_pad($this->booking_id, 6, '0', STR_PAD_LEFT);
        
        $query = "INSERT INTO tms_bills (bill_booking_id, bill_number, bill_generated_at) 
                  VALUES (?, ?, NOW())
                  ON DUPLICATE KEY UPDATE bill_generated_at = NOW()";
        
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('is', $this->booking_id, $bill_number);
        return $stmt->execute();
    }
}
?>
