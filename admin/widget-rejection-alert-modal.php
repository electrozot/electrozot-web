<!-- Rejection Alert Modal -->
<div class="modal fade" id="rejectionAlertModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 15px; border: 3px solid #dc3545;">
            <div class="modal-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Technician Rejection Alert
                </h5>
            </div>
            <div class="modal-body" id="rejectionAlertContent" style="max-height: 70vh; overflow-y: auto;">
                <!-- Content loaded via JavaScript -->
            </div>
        </div>
    </div>
</div>

<style>
.rejection-card {
    border: 2px solid #dc3545;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    background: #fff5f5;
}

.rejection-header {
    background: #dc3545;
    color: white;
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-weight: 600;
}

.rejection-details {
    background: white;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 10px;
    border-left: 4px solid #ffc107;
}

.contact-info {
    background: #e3f2fd;
    padding: 12px;
    border-radius: 8px;
    margin: 10px 0;
}

.action-buttons {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.action-btn {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-lock {
    background: #dc3545;
    color: white;
}

.btn-lock:hover {
    background: #c82333;
    transform: translateY(-2px);
}

.btn-block {
    background: #ffc107;
    color: #000;
}

.btn-block:hover {
    background: #e0a800;
    transform: translateY(-2px);
}

.btn-no-action {
    background: #28a745;
    color: white;
}

.btn-no-action:hover {
    background: #218838;
    transform: translateY(-2px);
}
</style>

<script>
// Check for rejection alerts on page load
$(document).ready(function() {
    // Wait a bit for page to fully load
    setTimeout(function() {
        checkRejectionAlerts();
    }, 2000);
    
    // Check every 5 minutes
    setInterval(checkRejectionAlerts, 300000);
});

function checkRejectionAlerts() {
    $.ajax({
        url: 'api-check-rejection-threshold.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Rejection check response:', response);
            if (response.success && response.has_alerts) {
                showRejectionAlert(response.technicians, response.threshold);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error checking rejections:', error);
            console.log('Response:', xhr.responseText);
        }
    });
}

function showRejectionAlert(technicians, threshold) {
    let content = `
        <div class="alert alert-danger">
            <strong><i class="fas fa-bell"></i> Alert!</strong> 
            ${technicians.length} technician(s) have rejected ${threshold}+ bookings in the last 7 days.
            Please review and take appropriate action.
        </div>
    `;
    
    technicians.forEach(function(tech) {
        content += `
            <div class="rejection-card" data-tech-id="${tech.t_id}">
                <div class="rejection-header">
                    <i class="fas fa-user-times"></i> ${tech.t_name} - ${tech.rejection_count} Rejections
                </div>
                
                <div class="contact-info">
                    <strong><i class="fas fa-phone"></i> Technician Contact:</strong><br>
                    Phone: <a href="tel:${tech.t_phone}" class="btn btn-sm btn-primary mt-1">
                        <i class="fas fa-phone"></i> ${tech.t_phone}
                    </a>
                    ${tech.t_email ? `<br>Email: ${tech.t_email}` : ''}
                </div>
                
                <h6 class="mt-3"><strong>Rejected Bookings:</strong></h6>
        `;
        
        tech.rejection_list.forEach(function(rejection) {
            const bookingMatch = rejection.match(/Booking #(\d+)/);
            const bookingId = bookingMatch ? bookingMatch[1] : null;
            const customer = bookingId && tech.customers[bookingId] ? tech.customers[bookingId] : null;
            
            content += `
                <div class="rejection-details">
                    <div>${rejection}</div>
                    ${customer ? `
                        <div class="contact-info mt-2">
                            <strong><i class="fas fa-user"></i> Customer:</strong> ${customer.name}<br>
                            <strong><i class="fas fa-phone"></i> Phone:</strong> 
                            <a href="tel:${customer.phone}" class="btn btn-sm btn-success mt-1">
                                <i class="fas fa-phone"></i> ${customer.phone}
                            </a>
                        </div>
                    ` : ''}
                </div>
            `;
        });
        
        content += `
                <div class="mt-3">
                    <label><strong>Admin Notes:</strong></label>
                    <textarea class="form-control admin-notes-${tech.t_id}" rows="2" placeholder="Enter reason for action or notes..."></textarea>
                </div>
                
                <div class="action-buttons">
                    <button class="action-btn btn-lock" onclick="takeAction(${tech.t_id}, 'lock_account')">
                        <i class="fas fa-lock"></i> Lock Account (2 Days)
                    </button>
                    <button class="action-btn btn-block" onclick="takeAction(${tech.t_id}, 'block_bookings')">
                        <i class="fas fa-ban"></i> Block Bookings (2 Days)
                    </button>
                    <button class="action-btn btn-no-action" onclick="takeAction(${tech.t_id}, 'no_action')">
                        <i class="fas fa-check"></i> No Action
                    </button>
                </div>
            </div>
        `;
    });
    
    $('#rejectionAlertContent').html(content);
    $('#rejectionAlertModal').modal('show');
}

function takeAction(technicianId, action) {
    const notes = $(`.admin-notes-${technicianId}`).val();
    
    if (!notes && action !== 'no_action') {
        alert('Please enter notes explaining your decision');
        return;
    }
    
    const actionText = action === 'lock_account' ? 'lock this account' : 
                      action === 'block_bookings' ? 'block bookings' : 
                      'mark as reviewed with no action';
    
    if (!confirm(`Are you sure you want to ${actionText}?`)) {
        return;
    }
    
    $.ajax({
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
                $(`.rejection-card[data-tech-id="${technicianId}"]`).fadeOut();
                
                // Check if all cards are gone
                setTimeout(function() {
                    if ($('.rejection-card:visible').length === 0) {
                        $('#rejectionAlertModal').modal('hide');
                    }
                }, 500);
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Failed to process action');
        }
    });
}
</script>
