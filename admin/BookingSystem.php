<?php
/**
 * COMPLETE BOOKING SYSTEM CLASS
 * Handles all booking logic according to requirements
 */

class BookingSystem {
    private $conn;
    
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }
    
    /**
     * Check if technician can accept new booking
     */
    public function canAssignToTechnician($technician_id) {
        $stmt = $this->conn->prepare("
            SELECT t_current_bookings, t_booking_limit, t_name 
            FROM tms_technician 
            WHERE t_id = ?
        ");
        $stmt->execute([$technician_id]);
        $tech = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$tech) {
            return ['success' => false, 'message' => 'Technician not found'];
        }
        
        if ($tech['t_current_bookings'] >= $tech['t_booking_limit']) {
            return [
                'success' => false, 
                'message' => "Cannot assign. {$tech['t_name']} has reached limit ({$tech['t_current_bookings']}/{$tech['t_booking_limit']})"
            ];
        }
        
        return [
            'success' => true, 
            'message' => "Can assign ({$tech['t_current_bookings']}/{$tech['t_booking_limit']})",
            'current' => $tech['t_current_bookings'],
            'limit' => $tech['t_booking_limit']
        ];
    }

    /**
     * Assign booking to technician
     */
    public function assignBooking($booking_id, $technician_id, $admin_id) {
        // Check if can assign
        $canAssign = $this->canAssignToTechnician($technician_id);
        if (!$canAssign['success']) {
            return $canAssign;
        }
        
        // Get current booking details
        $stmt = $this->conn->prepare("SELECT * FROM tms_service_booking WHERE sb_id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$booking) {
            return ['success' => false, 'message' => 'Booking not found'];
        }
        
        $old_technician_id = $booking['sb_technician_id'];
        
        // Start transaction
        $this->conn->beginTransaction();
        
        try {
            // Update booking
            $stmt = $this->conn->prepare("
                UPDATE tms_service_booking 
                SET sb_technician_id = ?,
                    sb_status = 'Approved',
                    sb_assigned_by = ?,
                    sb_assigned_at = NOW(),
                    sb_can_user_cancel = 0,
                    sb_previous_technician_id = ?,
                    sb_reassignment_count = sb_reassignment_count + 1
                WHERE sb_id = ?
            ");
            $stmt->execute([$technician_id, $admin_id, $old_technician_id, $booking_id]);

            // If reassigning, decrement old technician count
            if ($old_technician_id && $old_technician_id != $technician_id) {
                $stmt = $this->conn->prepare("
                    UPDATE tms_technician 
                    SET t_current_bookings = GREATEST(t_current_bookings - 1, 0)
                    WHERE t_id = ?
                ");
                $stmt->execute([$old_technician_id]);
                
                // Notify old technician
                $this->createTechnicianNotification(
                    $old_technician_id,
                    'booking_reassigned',
                    $booking_id,
                    'Booking Reassigned',
                    "Booking #{$booking_id} has been reassigned to another technician"
                );
            }
            
            // Increment new technician count
            $stmt = $this->conn->prepare("
                UPDATE tms_technician 
                SET t_current_bookings = t_current_bookings + 1,
                    t_daily_assigned = t_daily_assigned + 1
                WHERE t_id = ?
            ");
            $stmt->execute([$technician_id]);
            
            // Create booking history
            $this->createBookingHistory(
                $booking_id,
                $booking['sb_status'],
                'Approved',
                $old_technician_id,
                $technician_id,
                'admin',
                $admin_id,
                'Booking assigned to technician'
            );

            // Create technician notification
            $this->createTechnicianNotification(
                $technician_id,
                'new_assignment',
                $booking_id,
                'New Booking Assigned',
                "You have been assigned booking #{$booking_id}"
            );
            
            // Create user notification
            $this->createUserNotification(
                $booking['sb_user_id'],
                $booking_id,
                'technician_assigned',
                'Technician Assigned',
                "A technician has been assigned to your booking #{$booking_id}"
            );
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'message' => 'Booking assigned successfully',
                'booking_id' => $booking_id,
                'technician_id' => $technician_id
            ];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Technician accepts booking
     */
    public function acceptBooking($booking_id, $technician_id) {
        $stmt = $this->conn->prepare("
            UPDATE tms_service_booking 
            SET sb_accepted_at = NOW()
            WHERE sb_id = ? AND sb_technician_id = ?
        ");
        $stmt->execute([$booking_id, $technician_id]);
        
        // Create admin notification
        $this->createAdminNotification(
            'booking_accepted',
            $booking_id,
            'Booking Accepted',
            "Technician accepted booking #{$booking_id}"
        );
        
        return ['success' => true, 'message' => 'Booking accepted'];
    }
    
    /**
     * Technician rejects booking
     */
    public function rejectBooking($booking_id, $technician_id, $reason = '') {
        $this->conn->beginTransaction();
        
        try {
            // Update booking status
            $stmt = $this->conn->prepare("
                UPDATE tms_service_booking 
                SET sb_status = 'Rejected by Technician',
                    sb_rejected_at = NOW(),
                    sb_rejection_reason = ?,
                    sb_technician_id = NULL
                WHERE sb_id = ? AND sb_technician_id = ?
            ");
            $stmt->execute([$reason, $booking_id, $technician_id]);

            // Decrement technician count
            $stmt = $this->conn->prepare("
                UPDATE tms_technician 
                SET t_current_bookings = GREATEST(t_current_bookings - 1, 0),
                    t_daily_rejected = t_daily_rejected + 1
                WHERE t_id = ?
            ");
            $stmt->execute([$technician_id]);
            
            // Get booking details for notification
            $stmt = $this->conn->prepare("SELECT sb_user_id FROM tms_service_booking WHERE sb_id = ?");
            $stmt->execute([$booking_id]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Create admin notification
            $this->createAdminNotification(
                'booking_rejected',
                $booking_id,
                'Booking Rejected',
                "Technician rejected booking #{$booking_id}. Reason: {$reason}"
            );
            
            // Create user notification
            $this->createUserNotification(
                $booking['sb_user_id'],
                $booking_id,
                'booking_rejected',
                'Booking Status Update',
                "Your booking #{$booking_id} will be reassigned. Don't worry!"
            );
            
            $this->conn->commit();
            
            return ['success' => true, 'message' => 'Booking rejected. Admin will reassign.'];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Technician completes booking
     */
    public function completeBooking($booking_id, $technician_id, $notes = '', $image = '') {
        $this->conn->beginTransaction();
        
        try {
            // Update booking status
            $stmt = $this->conn->prepare("
                UPDATE tms_service_booking 
                SET sb_status = 'Completed',
                    sb_completed_at = NOW(),
                    sb_completion_notes = ?,
                    sb_completion_image = ?
                WHERE sb_id = ? AND sb_technician_id = ?
            ");
            $stmt->execute([$notes, $image, $booking_id, $technician_id]);
            
            // Decrement technician count and increment completed
            $stmt = $this->conn->prepare("
                UPDATE tms_technician 
                SET t_current_bookings = GREATEST(t_current_bookings - 1, 0),
                    t_daily_completed = t_daily_completed + 1
                WHERE t_id = ?
            ");
            $stmt->execute([$technician_id]);
            
            // Get booking details
            $stmt = $this->conn->prepare("SELECT sb_user_id FROM tms_service_booking WHERE sb_id = ?");
            $stmt->execute([$booking_id]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Create admin notification
            $this->createAdminNotification(
                'booking_completed',
                $booking_id,
                'Booking Completed',
                "Booking #{$booking_id} has been completed by technician"
            );

            // Create user notification
            $this->createUserNotification(
                $booking['sb_user_id'],
                $booking_id,
                'booking_completed',
                'Booking Completed',
                "Your booking #{$booking_id} has been completed successfully!"
            );
            
            $this->conn->commit();
            
            return ['success' => true, 'message' => 'Booking completed successfully'];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Cancel booking (Admin or User)
     */
    public function cancelBooking($booking_id, $cancelled_by, $cancelled_by_id) {
        $this->conn->beginTransaction();
        
        try {
            // Get booking details
            $stmt = $this->conn->prepare("SELECT * FROM tms_service_booking WHERE sb_id = ?");
            $stmt->execute([$booking_id]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                return ['success' => false, 'message' => 'Booking not found'];
            }
            
            // Check if user can cancel
            if ($cancelled_by == 'user' && $booking['sb_can_user_cancel'] == 0) {
                return ['success' => false, 'message' => 'Cannot cancel. Technician already assigned.'];
            }

            // Update booking
            $stmt = $this->conn->prepare("
                UPDATE tms_service_booking 
                SET sb_status = 'Cancelled',
                    sb_cancelled_at = NOW(),
                    sb_cancelled_by = ?
                WHERE sb_id = ?
            ");
            $stmt->execute([$cancelled_by, $booking_id]);
            
            // If technician was assigned, decrement count
            if ($booking['sb_technician_id']) {
                $stmt = $this->conn->prepare("
                    UPDATE tms_technician 
                    SET t_current_bookings = GREATEST(t_current_bookings - 1, 0)
                    WHERE t_id = ?
                ");
                $stmt->execute([$booking['sb_technician_id']]);
                
                // Notify technician
                $this->createTechnicianNotification(
                    $booking['sb_technician_id'],
                    'booking_cancelled',
                    $booking_id,
                    'Booking Cancelled',
                    "Booking #{$booking_id} has been cancelled"
                );
            }
            
            $this->conn->commit();
            
            return ['success' => true, 'message' => 'Booking cancelled successfully'];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get available technicians for assignment
     * Enhanced to match both category and gadget type/service type
     */
    public function getAvailableTechnicians($service_category = null, $service_gadget_type = null) {
        $sql = "
            SELECT t_id, t_name, t_specialization, t_category, t_current_bookings, t_booking_limit,
                   (t_booking_limit - t_current_bookings) as available_slots
            FROM tms_technician 
            WHERE t_status = 'Available'
              AND t_current_bookings < t_booking_limit
        ";
        
        $params = [];
        
        // Match by service category
        if ($service_category) {
            $sql .= " AND (t_category = ? OR t_category LIKE ?)";
            $params[] = $service_category;
            $params[] = '%' . $service_category . '%';
        }
        
        // Match by gadget type/service type in specialization
        if ($service_gadget_type) {
            $sql .= " AND (t_specialization LIKE ? OR t_category LIKE ?)";
            $params[] = '%' . $service_gadget_type . '%';
            $params[] = '%' . $service_gadget_type . '%';
        }
        
        $sql .= " ORDER BY available_slots DESC, t_current_bookings ASC";
        
        if (!empty($params)) {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
        } else {
            $stmt = $this->conn->query($sql);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get technician daily stats
     */
    public function getTechnicianDailyStats($technician_id) {
        $stmt = $this->conn->prepare("
            SELECT t_daily_assigned, t_daily_completed, t_daily_rejected, 
                   t_current_bookings, t_booking_limit
            FROM tms_technician 
            WHERE t_id = ?
        ");
        $stmt->execute([$technician_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create admin notification
     */
    private function createAdminNotification($type, $booking_id, $title, $message) {
        $stmt = $this->conn->prepare("
            INSERT INTO tms_admin_notifications 
            (an_type, an_booking_id, an_title, an_message) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$type, $booking_id, $title, $message]);
    }
    
    /**
     * Create technician notification
     */
    private function createTechnicianNotification($technician_id, $type, $booking_id, $title, $message) {
        $stmt = $this->conn->prepare("
            INSERT INTO tms_technician_notifications 
            (tn_technician_id, tn_type, tn_booking_id, tn_title, tn_message) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$technician_id, $type, $booking_id, $title, $message]);
    }
    
    /**
     * Create user notification
     */
    private function createUserNotification($user_id, $booking_id, $type, $title, $message) {
        $stmt = $this->conn->prepare("
            INSERT INTO tms_user_notifications 
            (un_user_id, un_booking_id, un_type, un_title, un_message) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $booking_id, $type, $title, $message]);
    }

    /**
     * Create booking history record
     */
    private function createBookingHistory($booking_id, $old_status, $new_status, $old_tech_id, $new_tech_id, $changed_by, $changed_by_id, $notes) {
        $stmt = $this->conn->prepare("
            INSERT INTO tms_booking_history 
            (bh_booking_id, bh_old_status, bh_new_status, bh_old_technician_id, 
             bh_new_technician_id, bh_changed_by, bh_changed_by_id, bh_notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $booking_id, $old_status, $new_status, $old_tech_id, 
            $new_tech_id, $changed_by, $changed_by_id, $notes
        ]);
    }
    
    /**
     * Get new bookings count for admin
     */
    public function getNewBookingsCount() {
        $stmt = $this->conn->query("
            SELECT COUNT(*) as count 
            FROM tms_service_booking 
            WHERE sb_status = 'Pending'
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    /**
     * Get unread admin notifications
     */
    public function getUnreadAdminNotifications() {
        $stmt = $this->conn->query("
            SELECT * FROM tms_admin_notifications 
            WHERE an_is_read = 0 
            ORDER BY an_created_at DESC 
            LIMIT 10
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

    /**
     * Check if technician is available at specific date/time
     */
    public function checkTechnicianTimeSlot($technician_id, $date, $start_time, $end_time) {
        $stmt = $this->conn->prepare("CALL sp_check_technician_availability(?, ?, ?, ?, @available, @message)");
        $stmt->execute([$technician_id, $date, $start_time, $end_time]);
        
        $result = $this->conn->query("SELECT @available as available, @message as message")->fetch(PDO::FETCH_ASSOC);
        
        return [
            'available' => (bool)$result['available'],
            'message' => $result['message']
        ];
    }
    
    /**
     * Auto-assign booking to best available technician
     */
    public function autoAssignBooking($booking_id) {
        // Get booking details including service category and gadget type
        $stmt = $this->conn->prepare("
            SELECT sb.*, s.s_category, s.s_gadget_name, s.s_subcategory 
            FROM tms_service_booking sb
            LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
            WHERE sb.sb_id = ?
        ");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$booking) {
            return ['success' => false, 'message' => 'Booking not found'];
        }
        
        // Get service gadget type (prefer s_gadget_name, fallback to s_subcategory)
        $gadget_type = !empty($booking['s_gadget_name']) ? $booking['s_gadget_name'] : $booking['s_subcategory'];
        
        // Find best matching technician based on category and gadget type
        $available_technicians = $this->getAvailableTechnicians($booking['s_category'], $gadget_type);
        
        if (!empty($available_technicians)) {
            // Get the best technician (first one with most available slots)
            $best_technician = $available_technicians[0];
            
            // Assign to found technician
            return $this->assignBooking($booking_id, $best_technician['t_id'], 0); // 0 = auto-assigned
        } else {
            // Try without gadget type filter as fallback
            $available_technicians = $this->getAvailableTechnicians($booking['s_category'], null);
            
            if (!empty($available_technicians)) {
                $best_technician = $available_technicians[0];
                return $this->assignBooking($booking_id, $best_technician['t_id'], 0);
            } else {
                return ['success' => false, 'message' => 'No available technicians found for this service category'];
            }
        }
    }

    /**
     * Calculate cancellation charge based on policy
     */
    public function calculateCancellationCharge($booking_id) {
        $stmt = $this->conn->prepare("CALL sp_calculate_cancellation_charge(?, @charge, @percentage)");
        $stmt->execute([$booking_id]);
        
        $result = $this->conn->query("SELECT @charge as charge, @percentage as percentage")->fetch(PDO::FETCH_ASSOC);
        
        return [
            'charge_amount' => $result['charge'],
            'charge_percentage' => $result['percentage']
        ];
    }
    
    /**
     * Set technician on leave
     */
    public function setTechnicianLeave($technician_id, $start_date, $end_date, $reason) {
        $this->conn->beginTransaction();
        
        try {
            // Insert leave record
            $stmt = $this->conn->prepare("
                INSERT INTO tms_technician_leaves 
                (tl_technician_id, tl_start_date, tl_end_date, tl_reason, tl_status)
                VALUES (?, ?, ?, ?, 'Approved')
            ");
            $stmt->execute([$technician_id, $start_date, $end_date, $reason]);
            
            // Update technician status
            $stmt = $this->conn->prepare("
                UPDATE tms_technician 
                SET t_is_on_leave = 1,
                    t_leave_start_date = ?,
                    t_leave_end_date = ?,
                    t_leave_reason = ?
                WHERE t_id = ?
            ");
            $stmt->execute([$start_date, $end_date, $reason, $technician_id]);
            
            $this->conn->commit();
            
            return ['success' => true, 'message' => 'Leave set successfully'];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Remove technician from leave
     */
    public function removeTechnicianLeave($technician_id) {
        $stmt = $this->conn->prepare("
            UPDATE tms_technician 
            SET t_is_on_leave = 0,
                t_leave_start_date = NULL,
                t_leave_end_date = NULL,
                t_leave_reason = NULL
            WHERE t_id = ?
        ");
        $stmt->execute([$technician_id]);
        
        return ['success' => true, 'message' => 'Leave removed successfully'];
    }
    
    /**
     * Add rating and review for technician
     */
    public function addTechnicianRating($booking_id, $technician_id, $user_id, $rating, $review = '', $punctuality = null, $professionalism = null, $quality = null) {
        $this->conn->beginTransaction();
        
        try {
            // Insert rating
            $stmt = $this->conn->prepare("
                INSERT INTO tms_technician_ratings 
                (tr_technician_id, tr_booking_id, tr_user_id, tr_rating, tr_review, 
                 tr_punctuality, tr_professionalism, tr_quality)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $technician_id, $booking_id, $user_id, $rating, $review,
                $punctuality, $professionalism, $quality
            ]);
            
            // Update booking with rating
            $stmt = $this->conn->prepare("
                UPDATE tms_service_booking 
                SET sb_rating = ?,
                    sb_review = ?,
                    sb_reviewed_at = NOW()
                WHERE sb_id = ?
            ");
            $stmt->execute([$rating, $review, $booking_id]);
            
            // Update technician metrics
            $this->conn->query("CALL sp_update_technician_metrics($technician_id)");
            
            $this->conn->commit();
            
            return ['success' => true, 'message' => 'Rating submitted successfully'];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Modify booking details (before technician assignment)
     */
    public function modifyBooking($booking_id, $field_name, $new_value, $modified_by, $modified_by_id, $reason = '') {
        // Check if booking can be modified
        $stmt = $this->conn->prepare("
            SELECT sb_status, sb_technician_id 
            FROM tms_service_booking 
            WHERE sb_id = ?
        ");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$booking) {
            return ['success' => false, 'message' => 'Booking not found'];
        }
        
        // Only allow modification if not assigned or by admin
        if ($booking['sb_technician_id'] && $modified_by != 'admin') {
            return ['success' => false, 'message' => 'Cannot modify after technician assigned'];
        }
        
        $this->conn->beginTransaction();
        
        try {
            // Get old value
            $stmt = $this->conn->prepare("SELECT $field_name as old_value FROM tms_service_booking WHERE sb_id = ?");
            $stmt->execute([$booking_id]);
            $old_value = $stmt->fetch(PDO::FETCH_ASSOC)['old_value'];
            
            // Update booking
            $stmt = $this->conn->prepare("UPDATE tms_service_booking SET $field_name = ? WHERE sb_id = ?");
            $stmt->execute([$new_value, $booking_id]);
            
            // Log modification
            $stmt = $this->conn->prepare("
                INSERT INTO tms_booking_modifications 
                (bm_booking_id, bm_field_name, bm_old_value, bm_new_value, 
                 bm_modified_by, bm_modified_by_id, bm_reason)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $booking_id, $field_name, $old_value, $new_value,
                $modified_by, $modified_by_id, $reason
            ]);
            
            $this->conn->commit();
            
            return ['success' => true, 'message' => 'Booking modified successfully'];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Auto-expire old pending bookings
     */
    public function autoExpireBookings() {
        $this->conn->query("CALL sp_auto_expire_bookings()");
        
        $stmt = $this->conn->query("SELECT ROW_COUNT() as count");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        return ['success' => true, 'expired_count' => $count];
    }
    
    /**
     * Get technician performance metrics
     */
    public function getTechnicianPerformance($technician_id) {
        $stmt = $this->conn->prepare("
            SELECT 
                t_name,
                t_avg_rating,
                t_total_reviews,
                t_completion_rate,
                t_current_bookings,
                t_booking_limit,
                t_daily_assigned,
                t_daily_completed,
                t_daily_rejected
            FROM tms_technician 
            WHERE t_id = ?
        ");
        $stmt->execute([$technician_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get booking reminders to send
     */
    public function getPendingReminders() {
        $stmt = $this->conn->query("
            SELECT br.*, sb.sb_user_id, sb.sb_technician_id, sb.sb_booking_date, sb.sb_booking_time
            FROM tms_booking_reminders br
            JOIN tms_service_booking sb ON br.br_booking_id = sb.sb_id
            WHERE br.br_is_sent = 0
              AND br.br_reminder_time <= NOW()
              AND sb.sb_status = 'Approved'
            ORDER BY br.br_reminder_time ASC
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Mark reminder as sent
     */
    public function markReminderSent($reminder_id) {
        $stmt = $this->conn->prepare("
            UPDATE tms_booking_reminders 
            SET br_is_sent = 1, br_sent_at = NOW()
            WHERE br_id = ?
        ");
        $stmt->execute([$reminder_id]);
    }
}
?>
