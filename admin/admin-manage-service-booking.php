<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
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
                <?php if(isset($_SESSION['delete_success'])) {?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-left: 4px solid #28a745;">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['delete_success']; unset($_SESSION['delete_success']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php } ?>
                <?php if(isset($_SESSION['delete_error'])) {?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-left: 4px solid #dc3545;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['delete_error']; unset($_SESSION['delete_error']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php } ?>
                
                <!-- Modern Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-1" style="font-weight: 800; color: #2c3e50;">
                            <i class="fas fa-calendar-check" style="color: #667eea;"></i> Service Bookings
                        </h2>
                        <p class="text-muted mb-0">Manage and track all service bookings</p>
                    </div>
                    <div>
                        <a href="admin-quick-booking.php" class="btn btn-primary" style="border-radius: 50px; padding: 10px 25px; font-weight: 700; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                            <i class="fas fa-plus-circle"></i> Quick Booking
                        </a>
                    </div>
                </div>

                 <!--Service Bookings-->
                 <div class="card mb-3" style="border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                     <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px 15px 0 0; padding: 20px; border: none;">
                         <div class="d-flex align-items-center justify-content-between">
                             <h5 class="mb-0" style="color: white; font-weight: 700;">
                                 <i class="fas fa-list"></i> All Bookings
                             </h5>
                             <span class="badge badge-light" style="font-size: 14px; padding: 8px 15px;">
                                 <?php 
                                 $count_query = "SELECT COUNT(*) as total FROM tms_service_booking";
                                 $count_result = $mysqli->query($count_query);
                                 $count_data = $count_result->fetch_object();
                                 echo $count_data->total;
                                 ?> Total
                             </span>
                         </div>
                     </div>
                     
                     <!-- Modern Filter Section -->
                     <div class="card-body" style="background: #f8f9fa; padding: 20px; border-bottom: 1px solid #e9ecef;">
                         <?php
                             // Read filters from query params
                             $status = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : 'all';
                             $q = isset($_GET['q']) ? trim($_GET['q']) : '';
                             $validStatuses = ['all','pending','approved','in progress','completed','cancelled'];
                             if (!in_array($status, $validStatuses)) { $status = 'all'; }
                         ?>
                         
                         <!-- Status Filter Pills -->
                         <div class="mb-3">
                             <label class="d-block mb-2" style="font-weight: 700; color: #495057; font-size: 14px;">
                                 <i class="fas fa-filter"></i> Filter by Status
                             </label>
                             <div class="btn-group btn-group-toggle flex-wrap" role="group">
                                 <a href="admin-manage-service-booking.php?status=all" class="btn <?php echo ($status==='all'?'btn-primary':'btn-outline-secondary'); ?>" style="border-radius: 50px 0 0 50px; font-weight: 600; padding: 8px 20px;">
                                     <i class="fas fa-list"></i> All
                                 </a>
                                 <a href="admin-manage-service-booking.php?status=pending" class="btn <?php echo ($status==='pending'?'btn-warning text-white':'btn-outline-secondary'); ?>" style="font-weight: 600; padding: 8px 20px;">
                                     <i class="fas fa-clock"></i> Pending
                                 </a>
                                 <a href="admin-manage-service-booking.php?status=approved" class="btn <?php echo ($status==='approved'?'btn-info':'btn-outline-secondary'); ?>" style="font-weight: 600; padding: 8px 20px;">
                                     <i class="fas fa-check"></i> Approved
                                 </a>
                                 <a href="admin-manage-service-booking.php?status=in%20progress" class="btn <?php echo ($status==='in progress'?'btn-primary':'btn-outline-secondary'); ?>" style="font-weight: 600; padding: 8px 20px;">
                                     <i class="fas fa-spinner"></i> In Progress
                                 </a>
                                 <a href="admin-manage-service-booking.php?status=completed" class="btn <?php echo ($status==='completed'?'btn-success':'btn-outline-secondary'); ?>" style="font-weight: 600; padding: 8px 20px;">
                                     <i class="fas fa-check-circle"></i> Completed
                                 </a>
                                 <a href="admin-manage-service-booking.php?status=cancelled" class="btn <?php echo ($status==='cancelled'?'btn-danger':'btn-outline-secondary'); ?>" style="border-radius: 0 50px 50px 0; font-weight: 600; padding: 8px 20px;">
                                     <i class="fas fa-times-circle"></i> Cancelled
                                 </a>
                             </div>
                         </div>
                         
                         <!-- Search Bar -->
                         <div>
                             <label class="d-block mb-2" style="font-weight: 700; color: #495057; font-size: 14px;">
                                 <i class="fas fa-search"></i> Search Bookings
                             </label>
                             <form class="form-inline" method="get" action="admin-manage-service-booking.php">
                                 <input type="hidden" name="status" value="<?php echo htmlspecialchars($status); ?>">
                                 <div class="input-group" style="width: 100%; max-width: 500px;">
                                     <input class="form-control" type="search" name="q" placeholder="Search by customer, service, technician..." value="<?php echo htmlspecialchars($q); ?>" style="border-radius: 50px 0 0 50px; border-right: none; padding: 10px 20px;">
                                     <div class="input-group-append">
                                         <button class="btn btn-primary" type="submit" style="border-radius: 0 50px 50px 0; padding: 10px 25px; font-weight: 700;">
                                             <i class="fas fa-search"></i> Search
                                         </button>
                                     </div>
                                 </div>
                                 <?php if($q){ ?>
                                     <a class="btn btn-link ml-2" href="admin-manage-service-booking.php?status=<?php echo urlencode($status); ?>" style="font-weight: 600;">
                                         <i class="fas fa-times"></i> Clear
                                     </a>
                                 <?php } ?>
                             </form>
                         </div>
                     </div>
                     
                     <div class="card-body" style="padding: 0;">
                         <div class="table-responsive">
                             <table class="table table-hover" id="dataTable" width="100%" cellspacing="0" style="margin-bottom: 0;">
                                 <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                     <tr>
                                         <th style="border: none; padding: 15px; font-weight: 700; color: #495057; font-size: 13px; text-transform: uppercase;">#</th>
                                         <th style="border: none; padding: 15px; font-weight: 700; color: #495057; font-size: 13px; text-transform: uppercase;">
                                             <i class="fas fa-user"></i> Customer
                                         </th>
                                         <th style="border: none; padding: 15px; font-weight: 700; color: #495057; font-size: 13px; text-transform: uppercase;">
                                             <i class="fas fa-tools"></i> Service
                                         </th>
                                         <th style="border: none; padding: 15px; font-weight: 700; color: #495057; font-size: 13px; text-transform: uppercase;">
                                             <i class="fas fa-calendar"></i> Date
                                         </th>
                                         <th style="border: none; padding: 15px; font-weight: 700; color: #495057; font-size: 13px; text-transform: uppercase;">
                                             <i class="fas fa-clock"></i> Time
                                         </th>
                                         <th style="border: none; padding: 15px; font-weight: 700; color: #495057; font-size: 13px; text-transform: uppercase;">
                                             <i class="fas fa-user-cog"></i> Technician
                                         </th>
                                         <th style="border: none; padding: 15px; font-weight: 700; color: #495057; font-size: 13px; text-transform: uppercase;">
                                             <i class="fas fa-info-circle"></i> Status
                                         </th>
                                         <th style="border: none; padding: 15px; font-weight: 700; color: #495057; font-size: 13px; text-transform: uppercase;">
                                             <i class="fas fa-cog"></i> Actions
                                         </th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     <?php
                  // Build dynamic query based on filters
                  $sql = "SELECT sb.*, u.u_fname, u.u_lname, s.s_name, t.t_name as tech_name 
                          FROM tms_service_booking sb
                          LEFT JOIN tms_user u ON sb.sb_user_id = u.u_id
                          LEFT JOIN tms_service s ON sb.sb_service_id = s.s_id
                          LEFT JOIN tms_technician t ON sb.sb_technician_id = t.t_id";

                  $where = [];
                  $params = [];
                  $types = '';

                  if ($status !== 'all') {
                      // Normalize status to match DB values capitalization
                      $statusValue = ucwords($status);
                      // Handle "In Progress" properly
                      if ($status === 'in progress') { $statusValue = 'In Progress'; }
                      $where[] = 'sb.sb_status = ?';
                      $params[] = $statusValue;
                      $types .= 's';
                  }

                  if ($q !== '') {
                      $like = '%' . $q . '%';
                      $where[] = '(CONCAT(u.u_fname, " ", u.u_lname) LIKE ? OR s.s_name LIKE ? OR t.t_name LIKE ? OR sb.sb_booking_date LIKE ? OR sb.sb_booking_time LIKE ?)';
                      array_push($params, $like, $like, $like, $like, $like);
                      $types .= 'sssss';
                  }

                  if (!empty($where)) {
                      $sql .= ' WHERE ' . implode(' AND ', $where);
                  }
                  $sql .= ' ORDER BY sb.sb_created_at DESC';

                  $stmt = $mysqli->prepare($sql);
                  if (!empty($params)) {
                      $stmt->bind_param($types, ...$params);
                  }
                  $stmt->execute();
                  $res = $stmt->get_result();
                  $cnt=1;
                  
                  if($res->num_rows == 0) {
                      // Don't show any rows - DataTables will handle the "No data" message
                      // echo '<tr><td colspan="8" class="text-center">No service bookings found.</td></tr>';
                  } else {
                      while($row=$res->fetch_object())
                      {
                  ?>
                                     <tr style="border-bottom: 1px solid #e9ecef;">
                                         <td style="padding: 15px; vertical-align: middle; font-weight: 600; color: #6c757d;">
                                             #<?php echo $row->sb_id;?>
                                         </td>
                                         <td style="padding: 15px; vertical-align: middle;">
                                             <div style="font-weight: 600; color: #2c3e50;">
                                                 <?php echo htmlspecialchars($row->u_fname . ' ' . $row->u_lname);?>
                                             </div>
                                         </td>
                                         <td style="padding: 15px; vertical-align: middle;">
                                             <span style="color: #667eea; font-weight: 600;">
                                                 <?php echo htmlspecialchars($row->s_name);?>
                                             </span>
                                         </td>
                                         <td style="padding: 15px; vertical-align: middle; white-space: nowrap;">
                                             <i class="fas fa-calendar-alt text-muted"></i>
                                             <?php echo date('M d, Y', strtotime($row->sb_booking_date));?>
                                         </td>
                                         <td style="padding: 15px; vertical-align: middle; white-space: nowrap;">
                                             <i class="fas fa-clock text-muted"></i>
                                             <?php echo date('h:i A', strtotime($row->sb_booking_time));?>
                                         </td>
                                         <td style="padding: 15px; vertical-align: middle;">
                                             <?php if($row->tech_name): ?>
                                                 <span style="background: #e3f2fd; color: #1976d2; padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                                                     <i class="fas fa-user-check"></i> <?php echo htmlspecialchars($row->tech_name);?>
                                                 </span>
                                             <?php else: ?>
                                                 <span style="background: #fff3cd; color: #856404; padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                                                     <i class="fas fa-user-times"></i> Not Assigned
                                                 </span>
                                             <?php endif; ?>
                                         </td>
                                         <td style="padding: 15px; vertical-align: middle;">
                                             <?php 
                                             if($row->sb_status == "Pending"){ 
                                                 echo '<span style="background: #fff3cd; color: #856404; padding: 6px 15px; border-radius: 20px; font-size: 13px; font-weight: 700; display: inline-block;"><i class="fas fa-clock"></i> Pending</span>'; 
                                             } elseif($row->sb_status == "Approved"){ 
                                                 echo '<span style="background: #d1ecf1; color: #0c5460; padding: 6px 15px; border-radius: 20px; font-size: 13px; font-weight: 700; display: inline-block;"><i class="fas fa-check"></i> Approved</span>'; 
                                             } elseif($row->sb_status == "In Progress"){ 
                                                 echo '<span style="background: #cce5ff; color: #004085; padding: 6px 15px; border-radius: 20px; font-size: 13px; font-weight: 700; display: inline-block;"><i class="fas fa-spinner"></i> In Progress</span>'; 
                                             } elseif($row->sb_status == "Completed"){ 
                                                 echo '<span style="background: #d4edda; color: #155724; padding: 6px 15px; border-radius: 20px; font-size: 13px; font-weight: 700; display: inline-block;"><i class="fas fa-check-circle"></i> Completed</span>'; 
                                             } else { 
                                                 echo '<span style="background: #f8d7da; color: #721c24; padding: 6px 15px; border-radius: 20px; font-size: 13px; font-weight: 700; display: inline-block;"><i class="fas fa-times-circle"></i> '.$row->sb_status.'</span>'; 
                                             }
                                             ?>
                                         </td>
                                         <td style="padding: 15px; vertical-align: middle; white-space: nowrap;">
                                             <a href="admin-view-service-booking.php?sb_id=<?php echo $row->sb_id;?>" class="btn btn-sm btn-info" style="border-radius: 20px; padding: 5px 15px; font-weight: 600; margin: 2px;" title="View Details">
                                                 <i class="fas fa-eye"></i>
                                             </a>
                                             <?php if($row->sb_status != 'Cancelled' && $row->sb_status != 'Completed'): ?>
                                             <a href="admin-assign-technician.php?sb_id=<?php echo $row->sb_id;?>" class="btn btn-sm btn-success" style="border-radius: 20px; padding: 5px 15px; font-weight: 600; margin: 2px;" title="Assign Technician">
                                                 <i class="fas fa-user-plus"></i>
                                             </a>
                                             <a href="admin-cancel-service-booking.php?sb_id=<?php echo $row->sb_id;?>" class="btn btn-sm btn-warning" style="border-radius: 20px; padding: 5px 15px; font-weight: 600; margin: 2px;" onclick="return confirm('Cancel this booking?');" title="Cancel Booking">
                                                 <i class="fas fa-ban"></i>
                                             </a>
                                             <?php endif; ?>
                                             <a href="admin-delete-service-booking.php?sb_id=<?php echo $row->sb_id;?>" class="btn btn-sm btn-danger" style="border-radius: 20px; padding: 5px 15px; font-weight: 600; margin: 2px;" onclick="return confirm('Delete permanently?');" title="Delete">
                                                 <i class="fas fa-trash"></i>
                                             </a>
                                         </td>
                                     </tr>
                                     <?php $cnt = $cnt+1; 
                      }
                  }?>

                                 </tbody>
                             </table>
                         </div>
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

     <!-- Demo scripts for this page - REMOVED to prevent conflicts -->
     <!-- <script src="vendor/js/demo/datatables-demo.js"></script> -->
     
     <style>
     /* Modern Table Styling */
     #dataTable tbody tr {
         transition: all 0.3s ease;
     }
     
     #dataTable tbody tr:hover {
         background: #f8f9ff !important;
         transform: translateY(-2px);
         box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
     }
     
     /* Modern Button Hover Effects */
     .btn-sm {
         transition: all 0.3s ease;
     }
     
     .btn-sm:hover {
         transform: translateY(-2px);
         box-shadow: 0 4px 12px rgba(0,0,0,0.2);
     }
     
     /* Status Filter Pills */
     .btn-group a {
         transition: all 0.3s ease;
     }
     
     .btn-group a:hover {
         transform: translateY(-2px);
         box-shadow: 0 4px 12px rgba(0,0,0,0.15);
     }
     
     /* Search Input Focus */
     .form-control:focus {
         border-color: #667eea;
         box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
     }
     
     /* DataTables Custom Styling */
     .dataTables_wrapper .dataTables_paginate .paginate_button {
         border-radius: 50px !important;
         margin: 0 3px;
         padding: 5px 12px !important;
     }
     
     .dataTables_wrapper .dataTables_paginate .paginate_button.current {
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
         border: none !important;
         color: white !important;
     }
     
     .dataTables_wrapper .dataTables_length select,
     .dataTables_wrapper .dataTables_filter input {
         border-radius: 20px;
         border: 2px solid #e9ecef;
         padding: 5px 15px;
     }
     
     .dataTables_wrapper .dataTables_info {
         font-weight: 600;
         color: #6c757d;
     }
     
     /* Card Shadow on Hover */
     .card {
         transition: all 0.3s ease;
     }
     
     .card:hover {
         box-shadow: 0 8px 30px rgba(0,0,0,0.12) !important;
     }
     
     /* Smooth Animations */
     @keyframes fadeIn {
         from { opacity: 0; transform: translateY(20px); }
         to { opacity: 1; transform: translateY(0); }
     }
     
     .card {
         animation: fadeIn 0.5s ease;
     }
     </style>
     <script>
       // Initialize DataTable with proper configuration
       $(function(){
         var $table = $('#dataTable');
         
         // Always initialize DataTable - it handles empty state automatically
         var table = $table.DataTable({
           "columnDefs": [
             { "orderable": false, "targets": 7 } // Disable sorting on Action column
           ],
           "order": [[0, "desc"]], // Sort by ID descending by default
           "language": {
             "emptyTable": "No service bookings found",
             "zeroRecords": "No matching bookings found"
           }
         });
         
         var status = '<?php echo htmlspecialchars($status); ?>';
         var map = {
           'pending': 'Pending',
           'approved': 'Approved',
           'in progress': 'In Progress',
           'completed': 'Completed',
           'cancelled': 'Cancelled',
           'all': null
         };
         var display = map[status] || null;
         if (display) {
           table.column(6).search(display).draw();
         } else {
           table.column(6).search('').draw();
         }

         // Wire search form to DataTables as well (without reload)
         var $searchInput = $('input[name="q"]');
         if ($searchInput.length) {
           $searchInput.on('keyup change', function(){
             table.search(this.value).draw();
           });
         }
       });
     </script>
     
     <!-- Success Modal -->
     <?php include("vendor/inc/success-modal.php");?>
 </body>

 </html>

