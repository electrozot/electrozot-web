<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];

  // AUTO-FIX: Sync technician slots and availability
  include('auto-fix-technician-slots.php');

  // Include soft delete helper
  include('vendor/inc/soft-delete.php');

  if(isset($_GET['del']))
  {
      $id=intval($_GET['del']);
      $reason = isset($_GET['reason']) ? $_GET['reason'] : 'Deleted by admin';
      
      // Use soft delete function
      if(softDeleteTechnician($mysqli, $id, $aid, $reason)) {
          $succ = "Technician deleted and sent to Recycle Bin";
      } else {
          $err = "Failed to delete technician";
      }
  }
?>
 <!DOCTYPE html>
 <html lang="en">

 <?php include('vendor/inc/head.php');?>

 <body id="page-top">

     <?php include("vendor/inc/nav.php");?>

     <div id="wrapper">

         <!-- Sidebar -->
         <?php include('vendor/inc/sidebar.php');?>

         <div id="content-wrapper">

             <div class="container-fluid">
                 <!-- Breadcrumbs-->
                 <ol class="breadcrumb">
                     <li class="breadcrumb-item">
                         <a href="#">Technicians</a>
                     </li>
                     <li class="breadcrumb-item active">Manage Technicians</li>
                 </ol>
                 <?php if(isset($succ)) {?>
                 <!--This code for injecting an alert-->
                 <script>
                 setTimeout(function() {
                         swal("Success!", "<?php echo $succ;?>!", "success");
                     },
                     100);
                 </script>

                 <?php } ?>
                 <?php if(isset($err)) {?>
                 <!--This code for injecting an alert-->
                 <script>
                 setTimeout(function() {
                         swal("Failed!", "<?php echo $err;?>!", "Failed");
                     },
                     100);
                 </script>

                 <?php } ?>

                 <!-- Technician Stats Summary -->
                 <?php
                 // Get technician statistics
                 $stats_query = "SELECT 
                     COUNT(*) as total,
                     SUM(CASE WHEN t_status = 'Available' THEN 1 ELSE 0 END) as available,
                     SUM(CASE WHEN t_status = 'Not Available' THEN 1 ELSE 0 END) as not_available
                     FROM tms_technician";
                 $stats_result = $mysqli->query($stats_query);
                 $stats = $stats_result->fetch_object();
                 
                 // Get engaged technicians count
                 $engaged_query = "SELECT COUNT(DISTINCT sb_technician_id) as engaged 
                                  FROM tms_service_booking 
                                  WHERE sb_status IN ('Pending', 'Approved', 'In Progress')
                                  AND sb_technician_id IS NOT NULL";
                 $engaged_result = $mysqli->query($engaged_query);
                 $engaged_data = $engaged_result->fetch_object();
                 ?>
                 
                 <div class="row mb-3">
                     <div class="col-xl-3 col-md-6 mb-2">
                         <div class="card border-left-primary shadow-sm h-100 py-2">
                             <div class="card-body p-2">
                                 <div class="row no-gutters align-items-center">
                                     <div class="col mr-2">
                                         <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Technicians</div>
                                         <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats->total;?></div>
                                     </div>
                                     <div class="col-auto">
                                         <i class="fas fa-users fa-2x text-gray-300"></i>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     
                     <div class="col-xl-3 col-md-6 mb-2">
                         <div class="card border-left-success shadow-sm h-100 py-2">
                             <div class="card-body p-2">
                                 <div class="row no-gutters align-items-center">
                                     <div class="col mr-2">
                                         <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Available</div>
                                         <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats->available;?></div>
                                     </div>
                                     <div class="col-auto">
                                         <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     
                     <div class="col-xl-3 col-md-6 mb-2">
                         <div class="card border-left-warning shadow-sm h-100 py-2">
                             <div class="card-body p-2">
                                 <div class="row no-gutters align-items-center">
                                     <div class="col mr-2">
                                         <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Currently Engaged</div>
                                         <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $engaged_data->engaged;?></div>
                                     </div>
                                     <div class="col-auto">
                                         <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     
                     <div class="col-xl-3 col-md-6 mb-2">
                         <div class="card border-left-danger shadow-sm h-100 py-2">
                             <div class="card-body p-2">
                                 <div class="row no-gutters align-items-center">
                                     <div class="col mr-2">
                                         <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Not Available</div>
                                         <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats->not_available;?></div>
                                     </div>
                                     <div class="col-auto">
                                         <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>

                 <!-- DataTables Example -->
                 <div class="card mb-3 shadow-sm">
                     <div class="card-header bg-primary text-white">
                         <i class="fas fa-users-cog"></i>
                         <strong>Manage Technicians</strong>
                     </div>
                     <div class="card-body p-3">
                         <!-- Search and Filter Section -->
                         <div class="row mb-3">
                             <div class="col-md-4">
                                 <label for="searchTechnician" class="font-weight-bold" style="font-size: 0.875rem;">Search:</label>
                                 <input type="text" id="searchTechnician" class="form-control form-control-sm" placeholder="Search by name or ID number...">
                             </div>
                             <div class="col-md-3">
                                 <label for="filterCategory" class="font-weight-bold" style="font-size: 0.875rem;">Category:</label>
                                 <select id="filterCategory" class="form-control form-control-sm">
                                     <option value="">All Categories</option>
                                     <?php
                                     // Get unique categories
                                     $cat_query = "SELECT DISTINCT t_category FROM tms_technician ORDER BY t_category";
                                     $cat_result = $mysqli->query($cat_query);
                                     while($cat = $cat_result->fetch_object()) {
                                         echo '<option value="'.$cat->t_category.'">'.$cat->t_category.'</option>';
                                     }
                                     ?>
                                 </select>
                             </div>
                             <div class="col-md-3">
                                 <label for="filterAvailability" class="font-weight-bold" style="font-size: 0.875rem;">Availability:</label>
                                 <select id="filterAvailability" class="form-control form-control-sm">
                                     <option value="">All Status</option>
                                     <option value="Available">Available</option>
                                     <option value="Not Available">Not Available</option>
                                 </select>
                             </div>
                             <div class="col-md-2">
                                 <label for="filterBooking" class="font-weight-bold" style="font-size: 0.875rem;">Booking:</label>
                                 <select id="filterBooking" class="form-control form-control-sm">
                                     <option value="">All</option>
                                     <option value="Free">Free</option>
                                     <option value="Engaged">Engaged</option>
                                 </select>
                             </div>
                         </div>
                         
                         <!-- Quick Filter Buttons -->
                         <div class="row mb-3">
                             <div class="col-12">
                                 <button class="btn btn-sm btn-outline-primary" onclick="showAllTechnicians()">
                                     <i class="fas fa-users"></i> All Technicians
                                 </button>
                                 <button class="btn btn-sm" style="background: linear-gradient(135deg, #0575E6 0%, #00F260 100%); color: white; font-weight: 700;" onclick="showGuestTechnicians()">
                                     <i class="fas fa-user-clock"></i> Guest Technicians Only
                                 </button>
                                 <button class="btn btn-sm btn-warning" onclick="showPendingApprovals()">
                                     <i class="fas fa-hourglass-half"></i> Pending Approvals
                                 </button>
                             </div>
                         </div>
                         <div class="table-responsive">
                             <table class="table table-bordered table-hover table-sm" id="dataTable" width="100%" cellspacing="0" style="font-size: 0.875rem; table-layout: fixed;">
                                 <thead class="thead-light">
                                     <tr>
                                         <th style="width: 3%;">#</th>
                                         <th style="width: 10%;">Name</th>
                                         <th style="width: 8%;">ID Number</th>
                                         <th style="width: 9%;">Mobile (Login)</th>
                                         <th style="width: 11%;">Category</th>
                                         <th style="width: 13%;">Specialization</th>
                                         <th style="width: 10%;">Availability</th>
                                         <th style="width: 12%;">Capacity</th>
                                         <th style="width: 18%;">Actions</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                 <?php
                    // Get technicians with their booking status (Guest technicians first)
                    $ret="SELECT t.*, 
                          COUNT(CASE WHEN sb.sb_status IN ('Pending', 'Approved', 'In Progress') THEN 1 END) as active_bookings,
                          COUNT(CASE WHEN sb.sb_status = 'Completed' THEN 1 END) as completed_bookings
                          FROM tms_technician t
                          LEFT JOIN tms_service_booking sb ON t.t_id = sb.sb_technician_id
                          GROUP BY t.t_id
                          ORDER BY t.t_is_guest DESC, t.t_status = 'Pending' DESC, t.t_name"; 
                    $stmt= $mysqli->prepare($ret);
                    $stmt->execute();
                    $res=$stmt->get_result();
                    $cnt=1;
                    while($row=$res->fetch_object())
                    {
                        // Availability status
                        $avail_badge = '';
                        if($row->t_status == 'Available') {
                            $avail_badge = 'badge-success';
                        } elseif($row->t_status == 'Not Available') {
                            $avail_badge = 'badge-danger';
                        } else {
                            $avail_badge = 'badge-secondary';
                        }
                ?>
                                     <tr data-category="<?php echo strtolower($row->t_category);?>" 
                                         data-availability="<?php echo strtolower($row->t_status);?>"
                                         style="<?php echo (isset($row->t_is_guest) && $row->t_is_guest == 1) ? 'background: linear-gradient(135deg, rgba(5, 117, 230, 0.05) 0%, rgba(0, 242, 96, 0.05) 100%);' : ''; ?>">
                                         <td class="text-center"><?php echo $cnt;?></td>
                                         <td>
                                             <strong><?php echo $row->t_name;?></strong>
                                             <?php if(isset($row->t_is_guest) && $row->t_is_guest == 1): ?>
                                                 <span class="badge badge-pill" style="background: linear-gradient(135deg, #0575E6 0%, #00F260 100%); color: white; font-weight: 800; font-size: 0.7rem; margin-left: 8px; animation: pulse 2s infinite;">
                                                     <i class="fas fa-user-clock"></i> GUEST
                                                 </span>
                                             <?php endif; ?>
                                             <?php if(isset($row->t_status) && $row->t_status == 'Pending'): ?>
                                                 <span class="badge badge-warning badge-pill" style="font-weight: 800; font-size: 0.7rem; margin-left: 8px;">
                                                     <i class="fas fa-hourglass-half"></i> PENDING APPROVAL
                                                 </span>
                                             <?php endif; ?>
                                         </td>
                                         <td class="text-center"><?php echo $row->t_id_no;?></td>
                                         <td class="text-center">
                                             <i class="fas fa-mobile-alt text-success"></i> 
                                             <strong><?php echo !empty($row->t_phone) ? $row->t_phone : 'N/A';?></strong>
                                         </td>
                                         <td class="text-center">
                                             <span class="badge badge-info badge-pill" style="font-size: 0.75rem; white-space: nowrap;">
                                                 <?php echo $row->t_category;?>
                                             </span>
                                         </td>
                                         <td style="word-wrap: break-word;"><?php echo $row->t_specialization;?></td>
                                         <td class="text-center">
                                             <span class="badge <?php echo $avail_badge;?> badge-pill" style="font-size: 0.75rem; min-width: 70px;">
                                                 <?php echo $row->t_status;?>
                                             </span>
                                         </td>
                                         <td class="text-center">
                                             <?php 
                                             $current = isset($row->t_current_bookings) ? $row->t_current_bookings : 0;
                                             $limit = isset($row->t_booking_limit) ? $row->t_booking_limit : 1;
                                             $percentage = ($limit > 0) ? ($current / $limit) * 100 : 0;
                                             $capacity_color = ($percentage >= 100) ? 'danger' : (($percentage >= 80) ? 'warning' : 'success');
                                             ?>
                                             <span class="badge badge-<?php echo $capacity_color;?> badge-pill" style="font-size: 0.75rem;" title="Current/Maximum bookings">
                                                 <i class="fas fa-layer-group"></i> <?php echo $current;?>/<?php echo $limit;?>
                                             </span>
                                         </td>
                                         <td class="text-center" style="white-space: nowrap; padding: 0.75rem;">
                                             <a href="admin-manage-single-technician.php?t_id=<?php echo $row->t_id;?>" class="badge badge-primary px-3 py-2 mr-2" title="Edit" style="font-size: 0.9rem;"><i class="fas fa-edit"></i></a>
                                             <a href="admin-view-technician.php?t_id=<?php echo $row->t_id;?>" class="badge badge-info px-3 py-2 mr-2" title="View" style="font-size: 0.9rem;"><i class="fas fa-eye"></i></a>
                                             <a href="admin-manage-technician.php?del=<?php echo $row->t_id;?>" class="badge badge-danger px-3 py-2" onclick="return confirm('Delete this technician?');" title="Delete" style="font-size: 0.9rem;"><i class="fas fa-trash"></i></a>
                                         </td>
                                     </tr>
                                 <?php $cnt = $cnt+1; }?>
                                 </tbody>
                             </table>
                         </div>
                     </div>
                     <div class="card-footer small text-muted py-1" style="font-size: 0.75rem;">
                         <?php
                         date_default_timezone_set("Africa/Nairobi");
                         echo "Updated: " . date("M d, Y h:i A");
                         ?>
                     </div>
                 </div>
             </div>
             <!-- /.container-fluid -->

             <!-- Sticky Footer -->
             <?php include("vendor/inc/footer.php");?>
         </div>
         <!-- /.content-wrapper -->
     </div>
     <!-- /#wrapper -->

     <!-- Scroll to Top Button-->
     <a class="scroll-to-top rounded" href="#page-top">
         <i class="fas fa-angle-up"></i>
     </a>
     <!-- Logout Modal-->
     <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                     <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>
                 </div>
                 <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                 <div class="modal-footer">
                     <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                     <a class="btn btn-danger" href="admin-logout.php">Logout</a>
                 </div>
             </div>
         </div>
     </div>
     <!-- Bootstrap core JavaScript-->
     <script src="vendor/jquery/jquery.min.js"></script>
     <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

     <!-- Core plugin JavaScript-->
     <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

     <!-- Page level plugin JavaScript-->
     <script src="vendor/datatables/jquery.dataTables.js"></script>
     <script src="vendor/datatables/dataTables.bootstrap4.js"></script>

     <!-- Custom scripts for all pages-->
     <script src="vendor/js/sb-admin.min.js"></script>

     <!-- Demo scripts for this page-->
     <script src="vendor/js/demo/datatables-demo.js"></script>
     
     <!-- Custom Filter Script -->
     <script>
     $(document).ready(function() {
         // Custom filtering function
         function filterTable() {
             var searchValue = $('#searchTechnician').val().toLowerCase();
             var categoryValue = $('#filterCategory').val().toLowerCase();
             var availabilityValue = $('#filterAvailability').val().toLowerCase();
             var bookingValue = $('#filterBooking').val().toLowerCase();
             
             $('#dataTable tbody tr').each(function() {
                 var row = $(this);
                 var name = row.find('td:eq(1)').text().toLowerCase();
                 var idNumber = row.find('td:eq(2)').text().toLowerCase();
                 var category = row.find('td:eq(3)').text().toLowerCase();
                 var availability = row.find('td:eq(5)').text().toLowerCase();
                 var bookingStatus = row.find('td:eq(6)').text().toLowerCase();
                 
                 var matchSearch = (name.indexOf(searchValue) > -1 || idNumber.indexOf(searchValue) > -1);
                 var matchCategory = (categoryValue === '' || category.indexOf(categoryValue) > -1);
                 var matchAvailability = (availabilityValue === '' || availability.indexOf(availabilityValue) > -1);
                 var matchBooking = (bookingValue === '' || bookingStatus.indexOf(bookingValue.toLowerCase()) > -1);
                 
                 if (matchSearch && matchCategory && matchAvailability && matchBooking) {
                     row.show();
                 } else {
                     row.hide();
                 }
             });
             
             // Update visible count
             updateCount();
         }
         
         // Update count of visible rows
         function updateCount() {
             var visibleRows = $('#dataTable tbody tr:visible').length;
             var totalRows = $('#dataTable tbody tr').length;
             
             if (visibleRows < totalRows) {
                 if ($('#filterInfo').length === 0) {
                     $('#dataTable_wrapper').prepend('<div id="filterInfo" class="alert alert-info alert-sm py-1 mb-2" style="font-size: 0.875rem;"><i class="fas fa-filter"></i> Showing ' + visibleRows + ' of ' + totalRows + ' technicians</div>');
                 } else {
                     $('#filterInfo').html('<i class="fas fa-filter"></i> Showing ' + visibleRows + ' of ' + totalRows + ' technicians');
                 }
             } else {
                 $('#filterInfo').remove();
             }
         }
         
         // Bind filter events
         $('#searchTechnician').on('keyup', filterTable);
         $('#filterCategory').on('change', filterTable);
         $('#filterAvailability').on('change', filterTable);
         $('#filterBooking').on('change', filterTable);
     });
     
     // Show only guest technicians
     function showGuestTechnicians() {
         $('#dataTable tbody tr').each(function() {
             var row = $(this);
             var nameCell = row.find('td:eq(1)').html();
             
             if (nameCell.indexOf('GUEST') > -1) {
                 row.show();
             } else {
                 row.hide();
             }
         });
         
         var visibleRows = $('#dataTable tbody tr:visible').length;
         var totalRows = $('#dataTable tbody tr').length;
         
         if ($('#filterInfo').length === 0) {
             $('#dataTable_wrapper').prepend('<div id="filterInfo" class="alert alert-info alert-sm py-2 mb-2"><i class="fas fa-user-clock"></i> <strong>Showing ' + visibleRows + ' Guest Technicians</strong> (out of ' + totalRows + ' total)</div>');
         } else {
             $('#filterInfo').html('<i class="fas fa-user-clock"></i> <strong>Showing ' + visibleRows + ' Guest Technicians</strong> (out of ' + totalRows + ' total)');
         }
     }
     
     // Show only pending approvals
     function showPendingApprovals() {
         $('#dataTable tbody tr').each(function() {
             var row = $(this);
             var nameCell = row.find('td:eq(1)').html();
             
             if (nameCell.indexOf('PENDING APPROVAL') > -1) {
                 row.show();
             } else {
                 row.hide();
             }
         });
         
         var visibleRows = $('#dataTable tbody tr:visible').length;
         var totalRows = $('#dataTable tbody tr').length;
         
         if ($('#filterInfo').length === 0) {
             $('#dataTable_wrapper').prepend('<div id="filterInfo" class="alert alert-warning alert-sm py-2 mb-2"><i class="fas fa-hourglass-half"></i> <strong>Showing ' + visibleRows + ' Pending Approvals</strong> (out of ' + totalRows + ' total)</div>');
         } else {
             $('#filterInfo').html('<i class="fas fa-hourglass-half"></i> <strong>Showing ' + visibleRows + ' Pending Approvals</strong> (out of ' + totalRows + ' total)');
         }
     }
     
     // Show all technicians
     function showAllTechnicians() {
         $('#dataTable tbody tr').show();
         $('#filterInfo').remove();
         
         // Reset filters
         $('#searchTechnician').val('');
         $('#filterCategory').val('');
         $('#filterAvailability').val('');
         $('#filterBooking').val('');
     }
     </script>
     
     <style>
     @keyframes pulse {
         0%, 100% { transform: scale(1); opacity: 1; }
         50% { transform: scale(1.05); opacity: 0.9; }
     }
     </style>
 </body>

 </html>

 </body>

 </html>