<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  include('vendor/inc/soft-delete.php');
  check_login();
  $aid=$_SESSION['a_id'];

  if(isset($_GET['del']))
  {
      $id=intval($_GET['del']);
      
      // Use soft delete function
      if(softDeleteService($mysqli, $id, $aid, 'Deleted by admin')) {
        $succ = "Service deleted and sent to Recycle Bin";
      } else {
        $err = "Failed to delete service";
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
                         <a href="#">Services</a>
                     </li>
                     <li class="breadcrumb-item active">Manage Services</li>
                 </ol>
                 <?php if(isset($succ)) {?>
                 <script>
                 setTimeout(function() {
                         swal("Success!", "<?php echo $succ;?>!", "success");
                     },
                     100);
                 </script>

                 <?php } ?>
                 <?php if(isset($err)) {?>
                 <script>
                 setTimeout(function() {
                         swal("Failed!", "<?php echo $err;?>!", "Failed");
                     },
                     100);
                 </script>

                 <?php } ?>

                 <!-- Quick Stats -->
                 <div class="row mb-4">
                     <?php
                     // Get service statistics
                     $total_services = $mysqli->query("SELECT COUNT(*) as count FROM tms_service")->fetch_object()->count;
                     $active_services = $mysqli->query("SELECT COUNT(*) as count FROM tms_service WHERE s_status='Active'")->fetch_object()->count;
                     $inactive_services = $mysqli->query("SELECT COUNT(*) as count FROM tms_service WHERE s_status='Inactive'")->fetch_object()->count;
                     $popular_services = $mysqli->query("SELECT COUNT(*) as count FROM tms_service WHERE is_popular=1")->fetch_object()->count;
                     $total_bookings = $mysqli->query("SELECT COUNT(*) as count FROM tms_service_booking")->fetch_object()->count;
                     ?>
                     <div class="col-md-3">
                         <div class="card border-left-primary shadow h-100 py-2">
                             <div class="card-body">
                                 <div class="row no-gutters align-items-center">
                                     <div class="col mr-2">
                                         <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Services</div>
                                         <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_services; ?></div>
                                     </div>
                                     <div class="col-auto">
                                         <i class="fas fa-cogs fa-2x text-gray-300"></i>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="col-md-3">
                         <div class="card border-left-success shadow h-100 py-2">
                             <div class="card-body">
                                 <div class="row no-gutters align-items-center">
                                     <div class="col mr-2">
                                         <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Services</div>
                                         <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $active_services; ?></div>
                                     </div>
                                     <div class="col-auto">
                                         <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="col-md-3">
                         <div class="card border-left-warning shadow h-100 py-2">
                             <div class="card-body">
                                 <div class="row no-gutters align-items-center">
                                     <div class="col mr-2">
                                         <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Inactive Services</div>
                                         <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $inactive_services; ?></div>
                                     </div>
                                     <div class="col-auto">
                                         <i class="fas fa-pause-circle fa-2x text-gray-300"></i>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="col-md-3">
                         <div class="card border-left-warning shadow h-100 py-2" style="border-left: 4px solid #ffc107 !important;">
                             <div class="card-body">
                                 <div class="row no-gutters align-items-center">
                                     <div class="col mr-2">
                                         <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #ffc107;">Popular Services (Max 3)</div>
                                         <div class="h5 mb-0 font-weight-bold" style="color: <?php echo $popular_services >= 3 ? '#dc3545' : '#28a745'; ?>">
                                             <?php echo $popular_services; ?> / 3
                                         </div>
                                         <?php if($popular_services >= 3): ?>
                                         <small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Limit reached</small>
                                         <?php endif; ?>
                                     </div>
                                     <div class="col-auto">
                                         <i class="fas fa-star fa-2x text-gray-300"></i>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="col-md-3">
                         <div class="card border-left-info shadow h-100 py-2">
                             <div class="card-body">
                                 <div class="row no-gutters align-items-center">
                                     <div class="col mr-2">
                                         <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Bookings</div>
                                         <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_bookings; ?></div>
                                     </div>
                                     <div class="col-auto">
                                         <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>

                 <!-- Filter and Add Button -->
                 <div class="card mb-3">
                     <div class="card-body">
                         <div class="row align-items-center">
                             <div class="col-md-8">
                                 <div class="form-inline">
                                     <label class="mr-2"><strong>Filter by Category:</strong></label>
                                     <select id="categoryFilter" class="form-control mr-3">
                                         <option value="">All Categories</option>
                                         <option value="Electrical">Electrical</option>
                                         <option value="Plumbing">Plumbing</option>
                                         <option value="HVAC">HVAC</option>
                                         <option value="Appliance">Appliance</option>
                                         <option value="General">General</option>
                                     </select>
                                     <label class="mr-2"><strong>Status:</strong></label>
                                     <select id="statusFilter" class="form-control">
                                         <option value="">All Status</option>
                                         <option value="Active">Active</option>
                                         <option value="Inactive">Inactive</option>
                                     </select>
                                 </div>
                             </div>
                             <div class="col-md-4 text-right">
                                 <a href="admin-add-service.php" class="btn btn-primary">
                                     <i class="fas fa-plus"></i> Add New Service
                                 </a>
                             </div>
                         </div>
                     </div>
                 </div>

                 <!-- Services Grid -->
                 <?php
                 $ret="SELECT s.*, 
                       (SELECT COUNT(*) FROM tms_service_booking WHERE sb_service_id = s.s_id) as booking_count,
                       (SELECT COUNT(*) FROM tms_service_booking WHERE sb_service_id = s.s_id AND sb_status='Completed') as completed_count
                       FROM tms_service s 
                       ORDER BY 
                           FIELD(s.s_category, 'BASIC ELECTRICAL WORK', 'ELECTRONIC REPAIR', 'INSTALLATION & SETUP', 'SERVICING & MAINTENANCE', 'PLUMBING WORK'),
                           FIELD(s.s_subcategory, 'Wiring & Fixtures', 'Safety & Power', 'Major Appliances', 'Other Gadgets', 'Appliance Setup', 'Tech & Security', 'Routine Care', 'Fixtures & Taps'),
                           s.s_name ASC"; 
                 $stmt= $mysqli->prepare($ret);
                 $stmt->execute();
                 $res=$stmt->get_result();
                 
                 // Group services by category and subcategory
                 $services_grouped = [];
                 while($row=$res->fetch_object()) {
                     $category = $row->s_category;
                     $subcategory = !empty($row->s_subcategory) ? $row->s_subcategory : 'Other';
                     $services_grouped[$category][$subcategory][] = $row;
                 }
                 ?>

                 <?php foreach($services_grouped as $category => $subcategories): ?>
                 <div class="card mb-4 category-section" data-category="<?php echo $category; ?>">
                     <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                         <h4 class="mb-0">
                             <i class="fas fa-wrench"></i> <?php echo $category; ?>
                             <?php 
                             $total_in_category = 0;
                             foreach($subcategories as $services) {
                                 $total_in_category += count($services);
                             }
                             ?>
                             <span class="badge badge-light ml-2"><?php echo $total_in_category; ?> services</span>
                         </h4>
                     </div>
                     <div class="card-body p-0">
                         <?php foreach($subcategories as $subcategory => $services): ?>
                         <!-- Subcategory Section -->
                         <div class="subcategory-section">
                             <div class="subcategory-header">
                                 <h5 class="mb-0">
                                     <i class="fas fa-angle-double-right text-primary"></i> 
                                     <strong><?php echo htmlspecialchars($subcategory); ?></strong>
                                     <span class="badge badge-primary ml-2"><?php echo count($services); ?> services</span>
                                 </h5>
                             </div>
                             <div class="row p-3">
                             <?php foreach($services as $service): ?>
                             <div class="col-md-6 mb-3 service-card" data-status="<?php echo $service->s_status; ?>">
                                 <div class="card h-100 shadow-sm">
                                     <div class="card-body">
                                         <div class="d-flex justify-content-between align-items-start mb-2">
                                             <h5 class="card-title mb-0">
                                                 <?php echo $service->s_name; ?>
                                                 <?php if(isset($service->is_popular) && $service->is_popular == 1): ?>
                                                 <i class="fas fa-star text-warning" title="Popular Service"></i>
                                                 <?php endif; ?>
                                             </h5>
                                             <?php if($service->s_status == "Active"): ?>
                                             <span class="badge badge-success">Active</span>
                                             <?php else: ?>
                                             <span class="badge badge-secondary">Inactive</span>
                                             <?php endif; ?>
                                         </div>
                                         
                                         <p class="card-text text-muted small mb-3">
                                             <?php 
                                             $desc = $service->s_description;
                                             echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                                             ?>
                                         </p>
                                         
                                         <div class="row mb-3">
                                             <div class="col-6">
                                                 <div class="text-center p-2 bg-light rounded">
                                                     <div class="text-primary font-weight-bold">৳<?php echo number_format($service->s_price, 2); ?></div>
                                                     <small class="text-muted">Price</small>
                                                 </div>
                                             </div>
                                             <div class="col-6">
                                                 <div class="text-center p-2 bg-light rounded">
                                                     <div class="text-info font-weight-bold"><?php echo $service->s_duration; ?></div>
                                                     <small class="text-muted">Duration</small>
                                                 </div>
                                             </div>
                                         </div>
                                         
                                         <div class="row mb-3">
                                             <div class="col-6">
                                                 <small class="text-muted">
                                                     <i class="fas fa-calendar-check"></i> 
                                                     <?php echo $service->booking_count; ?> Total Bookings
                                                 </small>
                                             </div>
                                             <div class="col-6">
                                                 <small class="text-success">
                                                     <i class="fas fa-check-circle"></i> 
                                                     <?php echo $service->completed_count; ?> Completed
                                                 </small>
                                             </div>
                                         </div>
                                         
                                         <div class="btn-group btn-group-sm w-100" role="group">
                                             <a href="admin-manage-single-service.php?s_id=<?php echo $service->s_id;?>" 
                                                class="btn btn-outline-primary">
                                                 <i class="fas fa-edit"></i> Edit
                                             </a>
                                             <a href="admin-manage-service.php?del=<?php echo $service->s_id;?>" 
                                                class="btn btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this service?');">
                                                 <i class="fas fa-trash"></i> Delete
                                             </a>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <?php endforeach; ?>
                             </div>
                         </div>
                         <?php endforeach; ?>
                     </div>
                 </div>
                 <?php endforeach; ?>

                 <?php if(empty($services_by_category)): ?>
                 <div class="card">
                     <div class="card-body text-center py-5">
                         <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                         <h5>No Services Found</h5>
                         <p class="text-muted">Start by adding your first service.</p>
                         <a href="admin-add-service.php" class="btn btn-primary">
                             <i class="fas fa-plus"></i> Add Service
                         </a>
                     </div>
                 </div>
                 <?php endif; ?>
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
     <script src="vendor/js/swal.js"></script>
     
     <!-- Custom styling for subcategories -->
     <style>
         /* Category Card Styling */
         .category-section {
             border: none;
             box-shadow: 0 4px 15px rgba(0,0,0,0.1);
             overflow: hidden;
         }
         
         .category-section .card-header {
             border: none;
             padding: 20px 25px;
         }
         
         .category-section .card-header h4 {
             font-size: 22px;
             font-weight: 700;
             margin: 0;
             text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
         }
         
         /* Subcategory Section Styling */
         .subcategory-section {
             border-bottom: 2px solid #e5e7eb;
             transition: all 0.3s ease;
         }
         
         .subcategory-section:last-child {
             border-bottom: none;
         }
         
         .subcategory-section:hover {
             background: #f9fafb;
         }
         
         /* Subcategory Header - Enhanced Highlighting */
         .subcategory-header {
             background: linear-gradient(135deg, #f0f4ff 0%, #e6f0ff 100%);
             padding: 18px 25px;
             border-left: 6px solid #667eea;
             margin: 0;
             cursor: pointer;
             transition: all 0.3s ease;
             box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
         }
         
         .subcategory-header:hover {
             background: linear-gradient(135deg, #e6f0ff 0%, #dce7ff 100%);
             border-left-color: #5568d3;
             box-shadow: inset 0 2px 5px rgba(0,0,0,0.08);
             transform: translateX(3px);
         }
         
         .subcategory-header h5 {
             color: #1e3a8a;
             font-size: 17px;
             font-weight: 700;
             margin: 0;
             display: flex;
             align-items: center;
             gap: 10px;
         }
         
         .subcategory-header h5 i {
             color: #667eea;
             font-size: 18px;
             transition: transform 0.3s ease;
         }
         
         .subcategory-header:hover h5 i {
             transform: translateX(5px);
         }
         
         .subcategory-header .badge {
             background: #667eea;
             color: white;
             font-size: 13px;
             font-weight: 600;
             padding: 6px 12px;
             border-radius: 20px;
             box-shadow: 0 2px 5px rgba(102, 126, 234, 0.3);
         }
         
         /* Service Cards Container */
         .subcategory-section .row {
             background: white;
         }
         
         /* Service Card Enhancements */
         .service-card .card {
             border: 2px solid #e5e7eb;
             transition: all 0.3s ease;
         }
         
         .service-card .card:hover {
             border-color: #667eea;
             box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15) !important;
             transform: translateY(-3px);
         }
         
         /* Responsive Design */
         @media (max-width: 768px) {
             .subcategory-header {
                 padding: 15px 20px;
             }
             
             .subcategory-header h5 {
                 font-size: 15px;
             }
             
             .category-section .card-header h4 {
                 font-size: 18px;
             }
         }
     </style>
     
     <!-- Custom filtering script -->
     <script>
     $(document).ready(function() {
         // Category and Status filtering
         $('#categoryFilter, #statusFilter').on('change', function() {
             var selectedCategory = $('#categoryFilter').val();
             var selectedStatus = $('#statusFilter').val();
             
             // Show/hide category sections
             if(selectedCategory === '') {
                 $('.category-section').show();
             } else {
                 $('.category-section').hide();
                 $('.category-section[data-category="' + selectedCategory + '"]').show();
             }
             
             // Show/hide service cards based on status
             if(selectedStatus === '') {
                 $('.service-card').show();
             } else {
                 $('.service-card').hide();
                 $('.service-card[data-status="' + selectedStatus + '"]').show();
             }
             
             // Hide empty category sections
             $('.category-section').each(function() {
                 var visibleCards = $(this).find('.service-card:visible').length;
                 if(visibleCards === 0) {
                     $(this).hide();
                 }
             });
         });
     });
     </script>
 </body>

 </html>

