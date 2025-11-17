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
     */
    public function getAvailableTechnicians($service_category = null) {
        $sql = "
            SELECT t_id, t_name, t_specialization, t_current_bookings, t_booking_limit,
                   (t_booking_limit - t_current_bookings) as available_slots
            FROM tms_technician 
            WHERE t_status = 'Available'
        ";
        
        if ($service_category) {
            $sql .= " AND t_category = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$service_category]);
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
