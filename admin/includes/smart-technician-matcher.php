<?php
/**
 * Smart Technician Matcher
 * Analyzes booking descriptions and matches with technicians based on skills
 */

class SmartTechnicianMatcher {
    private $mysqli;
    
    public function __construct($mysqli_connection) {
        $this->mysqli = $mysqli_connection;
    }
    
    /**
     * Extract keywords from booking description
     */
    public function extractKeywords($description) {
        if(empty($description)) {
            return [];
        }
        
        // Convert to lowercase for matching
        $text = strtolower($description);
        
        // Common keywords to look for
        $keyword_patterns = [
            // Electrical
            'wiring' => ['wiring', 'wire', 'rewiring'],
            'switch' => ['switch', 'switches', 'switchboard'],
            'socket' => ['socket', 'plug point', 'power point'],
            'fan' => ['fan', 'ceiling fan', 'exhaust fan', 'table fan'],
            'light' => ['light', 'lighting', 'lamp'],
            'bulb' => ['bulb', 'led', 'cfl'],
            'tube' => ['tube light', 'tubelight', 'fluorescent'],
            'mcb' => ['mcb', 'circuit breaker', 'breaker', 'fuse'],
            'inverter' => ['inverter', 'battery backup'],
            'ups' => ['ups', 'uninterruptible power'],
            'stabilizer' => ['stabilizer', 'voltage stabilizer'],
            'regulator' => ['regulator', 'dimmer'],
            
            // Appliances
            'ac' => ['ac', 'air conditioner', 'air conditioning', 'cooling'],
            'fridge' => ['fridge', 'refrigerator', 'freezer'],
            'washing machine' => ['washing machine', 'washer', 'laundry'],
            'geyser' => ['geyser', 'water heater', 'heater'],
            'microwave' => ['microwave', 'oven'],
            'tv' => ['tv', 'television', 'led tv', 'smart tv'],
            'chimney' => ['chimney', 'kitchen chimney', 'exhaust'],
            
            // Plumbing
            'tap' => ['tap', 'faucet', 'mixer'],
            'pipe' => ['pipe', 'pipeline', 'piping'],
            'leak' => ['leak', 'leaking', 'leakage', 'dripping'],
            'drain' => ['drain', 'drainage', 'clogged', 'blocked'],
            'toilet' => ['toilet', 'commode', 'wc'],
            'basin' => ['basin', 'sink', 'wash basin'],
            'tank' => ['tank', 'water tank', 'overhead tank'],
            
            // Actions
            'repair' => ['repair', 'fix', 'fixing', 'broken', 'not working', 'damaged'],
            'install' => ['install', 'installation', 'setup', 'fitting', 'new'],
            'replace' => ['replace', 'replacement', 'change'],
            'service' => ['service', 'servicing', 'maintenance', 'check'],
        ];
        
        $found_keywords = [];
        
        foreach($keyword_patterns as $main_keyword => $variations) {
            foreach($variations as $variation) {
                if(strpos($text, $variation) !== false) {
                    $found_keywords[] = $main_keyword;
                    break; // Found this keyword, move to next
                }
            }
        }
        
        return array_unique($found_keywords);
    }
    
    /**
     * Get suggested category based on keywords
     */
    public function suggestCategory($keywords) {
        if(empty($keywords)) {
            return null;
        }
        
        $category_mapping = [
            'ELECTRICAL' => ['wiring', 'switch', 'socket', 'light', 'bulb', 'tube', 'mcb', 'inverter', 'ups', 'stabilizer', 'regulator', 'fan'],
            'APPLIANCE REPAIR' => ['ac', 'fridge', 'washing machine', 'geyser', 'microwave', 'tv', 'chimney'],
            'PLUMBING' => ['tap', 'pipe', 'leak', 'drain', 'toilet', 'basin', 'tank'],
        ];
        
        $category_scores = [];
        
        foreach($category_mapping as $category => $category_keywords) {
            $score = 0;
            foreach($keywords as $keyword) {
                if(in_array($keyword, $category_keywords)) {
                    $score++;
                }
            }
            if($score > 0) {
                $category_scores[$category] = $score;
            }
        }
        
        if(empty($category_scores)) {
            return null;
        }
        
        // Return category with highest score
        arsort($category_scores);
        return array_key_first($category_scores);
    }
    
    /**
     * Find matching services based on keywords
     */
    public function findMatchingServices($keywords) {
        if(empty($keywords)) {
            return [];
        }
        
        $matching_services = [];
        
        // Build query to find services with matching keywords
        $placeholders = implode(',', array_fill(0, count($keywords), '?'));
        $query = "SELECT DISTINCT s.s_id, s.s_name, s.s_category, sk.sk_weight,
                  COUNT(*) as keyword_matches
                  FROM tms_service s
                  JOIN tms_service_keywords sk ON s.s_id = sk.sk_service_id
                  WHERE sk.sk_keyword IN ($placeholders)
                  GROUP BY s.s_id
                  ORDER BY keyword_matches DESC, sk.sk_weight DESC
                  LIMIT 10";
        
        $stmt = $this->mysqli->prepare($query);
        if($stmt) {
            $types = str_repeat('s', count($keywords));
            $stmt->bind_param($types, ...$keywords);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while($row = $result->fetch_assoc()) {
                $matching_services[] = $row;
            }
        }
        
        return $matching_services;
    }
    
    /**
     * Find technicians who can handle the extracted keywords
     * For custom bookings, filters by subcategory match
     */
    public function findMatchingTechnicians($keywords, $booking_date, $booking_time, $suggested_subcategory = null) {
        if(empty($keywords)) {
            // If no keywords, return all available technicians
            return $this->getAllAvailableTechnicians($booking_date, $booking_time);
        }
        
        // Find services that match keywords
        $matching_services = $this->findMatchingServices($keywords);
        
        if(empty($matching_services)) {
            // No matching services, return all available technicians
            return $this->getAllAvailableTechnicians($booking_date, $booking_time);
        }
        
        // Get service IDs
        $service_ids = array_column($matching_services, 's_id');
        
        // Find technicians with these skills
        $placeholders = implode(',', array_fill(0, count($service_ids), '?'));
        $query = "SELECT DISTINCT t.t_id, t.t_name, t.t_phone, t.t_category, t.t_specialization,
                  t.t_current_bookings, t.t_booking_limit, t.t_status,
                  GROUP_CONCAT(DISTINCT s.s_name SEPARATOR ', ') as matched_skills,
                  GROUP_CONCAT(DISTINCT s.s_subcategory SEPARATOR ', ') as matched_subcategories,
                  COUNT(DISTINCT ts.ts_service_id) as skill_count
                  FROM tms_technician t
                  JOIN tms_technician_skills ts ON t.t_id = ts.ts_technician_id
                  JOIN tms_service s ON ts.ts_service_id = s.s_id
                  WHERE ts.ts_service_id IN ($placeholders)
                  AND t.t_status IN ('Available', 'Busy')
                  AND t.t_current_bookings < t.t_booking_limit
                  GROUP BY t.t_id
                  ORDER BY skill_count DESC, t.t_current_bookings ASC";
        
        $stmt = $this->mysqli->prepare($query);
        if($stmt) {
            $types = str_repeat('i', count($service_ids));
            $stmt->bind_param($types, ...$service_ids);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $technicians = [];
            while($row = $result->fetch_assoc()) {
                $row['match_score'] = $row['skill_count'];
                $row['is_matched'] = true;
                $technicians[] = $row;
            }
            
            return $technicians;
        }
        
        return [];
    }
    
    /**
     * Find technicians by subcategory (for custom bookings)
     * Shows only technicians who have skills in the specified subcategory
     */
    public function findTechniciansBySubcategory($subcategory, $booking_date, $booking_time) {
        if(empty($subcategory)) {
            return $this->getAllAvailableTechnicians($booking_date, $booking_time);
        }
        
        // Find all services in this subcategory
        $service_query = "SELECT s_id, s_name, s_category FROM tms_service WHERE s_subcategory = ?";
        $stmt = $this->mysqli->prepare($service_query);
        $stmt->bind_param('s', $subcategory);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $service_ids = [];
        while($row = $result->fetch_assoc()) {
            $service_ids[] = $row['s_id'];
        }
        
        if(empty($service_ids)) {
            return [];
        }
        
        // Find technicians with skills in these services
        $placeholders = implode(',', array_fill(0, count($service_ids), '?'));
        $tech_query = "SELECT DISTINCT t.t_id, t.t_name, t.t_phone, t.t_category, t.t_specialization,
                       t.t_current_bookings, t.t_booking_limit, t.t_status,
                       GROUP_CONCAT(DISTINCT s.s_name SEPARATOR ', ') as matched_skills,
                       COUNT(DISTINCT ts.ts_service_id) as skill_count
                       FROM tms_technician t
                       JOIN tms_technician_skills ts ON t.t_id = ts.ts_technician_id
                       JOIN tms_service s ON ts.ts_service_id = s.s_id
                       WHERE ts.ts_service_id IN ($placeholders)
                       AND t.t_status IN ('Available', 'Busy')
                       AND t.t_current_bookings < t.t_booking_limit
                       GROUP BY t.t_id
                       ORDER BY skill_count DESC, t.t_current_bookings ASC";
        
        $tech_stmt = $this->mysqli->prepare($tech_query);
        if($tech_stmt) {
            $types = str_repeat('i', count($service_ids));
            $tech_stmt->bind_param($types, ...$service_ids);
            $tech_stmt->execute();
            $tech_result = $tech_stmt->get_result();
            
            $technicians = [];
            while($row = $tech_result->fetch_assoc()) {
                $row['match_score'] = $row['skill_count'];
                $row['is_matched'] = true;
                $row['subcategory'] = $subcategory;
                $technicians[] = $row;
            }
            
            return $technicians;
        }
        
        return [];
    }
    
    /**
     * Get all available technicians (fallback)
     */
    private function getAllAvailableTechnicians($booking_date, $booking_time) {
        $query = "SELECT t.t_id, t.t_name, t.t_phone, t.t_category, t.t_specialization,
                  t.t_current_bookings, t.t_booking_limit, t.t_status
                  FROM tms_technician t
                  WHERE t.t_status IN ('Available', 'Busy')
                  AND t.t_current_bookings < t.t_booking_limit
                  ORDER BY t.t_current_bookings ASC";
        
        $result = $this->mysqli->query($query);
        $technicians = [];
        
        if($result) {
            while($row = $result->fetch_assoc()) {
                $row['is_matched'] = false;
                $row['match_score'] = 0;
                $technicians[] = $row;
            }
        }
        
        return $technicians;
    }
    
    /**
     * Analyze booking and update database
     */
    public function analyzeAndUpdateBooking($booking_id) {
        // Get booking details
        $query = "SELECT sb_description, sb_custom_service FROM tms_service_booking WHERE sb_id = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows == 0) {
            return false;
        }
        
        $booking = $result->fetch_object();
        $description = $booking->sb_description . ' ' . $booking->sb_custom_service;
        
        // Extract keywords
        $keywords = $this->extractKeywords($description);
        
        if(empty($keywords)) {
            return false;
        }
        
        // Get suggested category
        $category = $this->suggestCategory($keywords);
        
        // Update booking
        $update_query = "UPDATE tms_service_booking 
                        SET sb_extracted_keywords = ?,
                            sb_suggested_category = ?
                        WHERE sb_id = ?";
        $update_stmt = $this->mysqli->prepare($update_query);
        $keywords_str = implode(', ', $keywords);
        $update_stmt->bind_param('ssi', $keywords_str, $category, $booking_id);
        
        return $update_stmt->execute();
    }
}
?>
