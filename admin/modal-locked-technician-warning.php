<!-- Locked Technician Warning Modal -->
<div class="modal fade" id="lockedTechnicianModal" tabindex="-1" role="dialog" aria-labelledby="lockedTechnicianModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border: 3px solid #dc3545; border-radius: 15px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" id="lockedTechnicianModalLabel">
                    <i class="fas fa-lock fa-lg"></i> Technician Account Locked
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <div class="text-center mb-4">
                    <i class="fas fa-user-lock fa-5x text-danger mb-3"></i>
                    <h4 class="text-danger font-weight-bold" id="lockedTechName">Technician Name</h4>
                </div>
                
                <div class="alert alert-danger" style="border-left: 5px solid #dc3545;">
                    <h6 class="font-weight-bold mb-2">
                        <i class="fas fa-exclamation-triangle"></i> Cannot Assign Booking
                    </h6>
                    <p class="mb-0">
                        This technician's account is currently <strong>LOCKED</strong> and cannot accept new bookings or reassignments.
                    </p>
                </div>
                
                <div class="card mb-3" style="background-color: #fff3cd; border: 1px solid #ffc107;">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-warning">
                            <i class="fas fa-info-circle"></i> Lock Reason:
                        </h6>
                        <p class="mb-0" id="lockReason" style="color: #856404;">
                            Account locked by system
                        </p>
                    </div>
                </div>
                
                <div class="alert alert-info" style="border-left: 5px solid #17a2b8;">
                    <h6 class="font-weight-bold mb-2">
                        <i class="fas fa-lightbulb"></i> What You Can Do:
                    </h6>
                    <ul class="mb-0" style="padding-left: 20px;">
                        <li>Unlock this technician's account to allow assignments</li>
                        <li>Select a different available technician for this booking</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #f8f9fa; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <a href="admin-unlock-technician.php" class="btn btn-warning" id="unlockTechnicianBtn">
                    <i class="fas fa-unlock"></i> Go to Unlock Technician
                </a>
            </div>
        </div>
    </div>
</div>

<style>
#lockedTechnicianModal .modal-content {
    box-shadow: 0 10px 40px rgba(220, 53, 69, 0.3);
}

#lockedTechnicianModal .modal-header {
    padding: 20px 25px;
}

#lockedTechnicianModal .modal-title {
    font-size: 1.3rem;
    font-weight: 700;
}

#lockedTechnicianModal .btn {
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s;
}

#lockedTechnicianModal .btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    border: none;
    color: #000;
}

#lockedTechnicianModal .btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
}
</style>

<script>
// Function to show locked technician modal
function showLockedTechnicianModal(technicianName, lockReason, technicianId) {
    $('#lockedTechName').text(technicianName);
    $('#lockReason').text(lockReason || 'Account locked by system');
    
    // Update unlock button to go directly to unlock page with technician ID
    if(technicianId) {
        // Pass technician ID to highlight it on the unlock page
        $('#unlockTechnicianBtn').attr('href', 'admin-unlock-technician.php?highlight=' + technicianId);
    } else {
        // Just go to unlock page without highlighting
        $('#unlockTechnicianBtn').attr('href', 'admin-unlock-technician.php');
    }
    
    // Show the modal
    $('#lockedTechnicianModal').modal('show');
}

// Check if error message contains locked technician info
$(document).ready(function() {
    <?php if(isset($err) && strpos($err, 'LOCKED') !== false): ?>
        // Extract technician name and reason from error message
        var errorMsg = <?php echo json_encode($err); ?>;
        var techName = errorMsg.match(/to (.+?)\. This/);
        var lockReason = errorMsg.match(/Reason: (.+?)\./);
        
        if(techName && techName[1]) {
            showLockedTechnicianModal(
                techName[1], 
                lockReason ? lockReason[1] : 'Account locked by system',
                null
            );
        }
    <?php endif; ?>
});
</script>
