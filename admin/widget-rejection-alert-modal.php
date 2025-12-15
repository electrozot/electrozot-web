<!-- Rejection Alert Modal -->
<div class="modal fade" id="rejectionAlertModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border: 5px solid #dc3545;">
            <div class="modal-header" style="background: #dc3545; color: white;">
                <h5 class="modal-title">⚠️ URGENT: Technician Rejection Alert ⚠️</h5>
            </div>
            <div class="modal-body" id="rejectionAlertContent"></div>
        </div>
    </div>
</div>

<script>
var technicianQueue = [];
var isShowingAlert = false;

window.checkRejectionAlerts = function() {
    // Don't check if modal is currently showing
    if (isShowingAlert) {
        console.log('Modal is open, skipping check...');
        return;
    }
    
    console.log('Checking alerts...');
    if(typeof jQuery === 'undefined') return;
    
    jQuery.ajax({
        url: 'api-check-rejection-threshold.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Response:', response);
            if (response.success && response.has_alerts) {
                window.showRejectionAlert(response.technicians, response.threshold);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
};

window.showRejectionAlert = function(technicians, threshold) {
    if (!technicians || technicians.length === 0) return;
    
    // Don't add to queue if already showing alerts
    if (isShowingAlert) {
        console.log('Already showing alert, skipping...');
        return;
    }
    
    console.log('Showing alert for', technicians.length, 'technicians');
    technicianQueue = technicians;
    window.showNextTechnicianAlert(threshold);
};

window.showNextTechnicianAlert = function(threshold) {
    if (technicianQueue.length === 0) {
        isShowingAlert = false;
        return;
    }
    
    isShowingAlert = true;
    var tech = technicianQueue.shift();
    
    var content = '<div class="alert alert-danger">';
    content += '<h4>Technician: ' + tech.t_name + '</h4>';
    content += '<p>Rejections: ' + tech.rejection_count + '</p>';
    content += '<p>Phone: ' + tech.t_phone + '</p>';
    content += '<textarea class="form-control admin-notes-' + tech.t_id + '" placeholder="Admin notes"></textarea>';
    content += '<div style="margin-top:15px;">';
    content += '<button class="btn btn-danger" onclick="window.takeAction(' + tech.t_id + ', \'lock_account\', ' + threshold + ')">Lock Account</button> ';
    content += '<button class="btn btn-warning" onclick="window.takeAction(' + tech.t_id + ', \'block_bookings\', ' + threshold + ')">Block Bookings</button> ';
    content += '<button class="btn btn-success" onclick="window.takeAction(' + tech.t_id + ', \'no_action\', ' + threshold + ')">No Action</button>';
    content += '</div></div>';
    
    jQuery('#rejectionAlertContent').html(content);
    jQuery('#rejectionAlertModal').modal('show');
};

window.takeAction = function(technicianId, action, threshold) {
    var notes = jQuery('.admin-notes-' + technicianId).val();
    
    if (!notes && action !== 'no_action') {
        alert('Please enter notes explaining your decision');
        return;
    }
    
    var actionText = action === 'lock_account' ? 'LOCK this account for 2 days' : 
                      action === 'block_bookings' ? 'BLOCK bookings for 2 days' : 
                      'mark as reviewed with NO ACTION';
    
    if (!confirm('Are you sure you want to ' + actionText + '?')) {
        return;
    }
    
    jQuery.ajax({
        url: 'api-take-rejection-action.php',
        method: 'POST',
        data: {
            technician_id: technicianId,
            action: action,
            admin_notes: notes
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                jQuery('#rejectionAlertModal').modal('hide');
                setTimeout(function() {
                    window.showNextTechnicianAlert(threshold);
                }, 1000);
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Failed to process action');
        }
    });
};

// Prevent duplicate initialization
if (!window.rejectionAlertSystemInitialized) {
    window.rejectionAlertSystemInitialized = true;
    
    (function init() {
        if (typeof jQuery === 'undefined') {
            setTimeout(init, 100);
            return;
        }
        
        console.log('✅ Rejection alert system loaded');
        console.log('Functions:', typeof window.checkRejectionAlerts, typeof window.showRejectionAlert);
        
        jQuery(document).ready(function() {
            window.checkRejectionAlerts();
            setInterval(window.checkRejectionAlerts, 5000);
        });
    })();
}
</script>
