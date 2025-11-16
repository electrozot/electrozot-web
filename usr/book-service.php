<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['u_id'];

$selected_category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php'); ?>

<body id="page-top">
    <?php include('vendor/inc/nav.php'); ?>

    <div id="wrapper">
        <?php include('vendor/inc/sidebar.php'); ?>

        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="user-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Book Service</li>
                </ol>

                <!-- Search Bar -->
                <div class="card mb-3 shadow">
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search for services..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                                <?php if (!empty($selected_category)): ?>
                                <input type="hidden" name="category" value="<?php echo htmlspecialchars($selected_category); ?>">
                                <?php endif; ?>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Category Selection -->
                <div class="card mb-4 shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-th-large"></i> Select Service Category</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 mb-3">
                                <a href="?category=Basic Electrical Work" 
                                   class="btn btn-lg btn-block category-card <?php echo $selected_category == 'Basic Electrical Work' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <i class="fas fa-bolt fa-3x d-block mb-2"></i>
                                    <strong>Basic Electrical Work</strong>
                                    <small class="d-block mt-1">Wiring & Safety</small>
                                </a>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <a href="?category=Electronic Repair" 
                                   class="btn btn-lg btn-block category-card <?php echo $selected_category == 'Electronic Repair' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <i class="fas fa-tools fa-3x d-block mb-2"></i>
                                    <strong>Electronic Repair</strong>
                                    <small class="d-block mt-1">Appliances & Gadgets</small>
                                </a>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <a href="?category=Installation & Setup" 
                                   class="btn btn-lg btn-block category-card <?php echo $selected_category == 'Installation & Setup' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <i class="fas fa-wrench fa-3x d-block mb-2"></i>
                                    <strong>Installation & Setup</strong>
                                    <small class="d-block mt-1">Appliances & Security</small>
                                </a>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <a href="?category=Servicing & Maintenance" 
                                   class="btn btn-lg btn-block category-card <?php echo $selected_category == 'Servicing & Maintenance' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <i class="fas fa-cog fa-3x d-block mb-2"></i>
                                    <strong>Servicing & Maintenance</strong>
                                    <small class="d-block mt-1">Routine Care</small>
                                </a>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <a href="?category=Plumbing Work" 
                                   class="btn btn-lg btn-block category-card <?php echo $selected_category == 'Plumbing Work' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <i class="fas fa-faucet fa-3x d-block mb-2"></i>
                                    <strong>Plumbing Work</strong>
                                    <small class="d-block mt-1">Fixtures & Taps</small>
                                </a>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <a href="book-service.php" 
                                   class="btn btn-lg btn-block category-card <?php echo empty($selected_category) && empty($search) ? 'btn-success' : 'btn-outline-success'; ?>">
                                    <i class="fas fa-th fa-3x d-block mb-2"></i>
                                    <strong>All Services</strong>
                                    <small class="d-block mt-1">View Everything</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                // Build query
                $conditions = ["s_status = 'Active'"];
                $params = [];
                $types = '';

                if (!empty($selected_category)) {
                    $conditions[] = "s_category = ?";
                    $params[] = $selected_category;
                    $types .= 's';
                }

                if (!empty($search)) {
                    $conditions[] = "(s_name LIKE ? OR s_description LIKE ?)";
                    $search_param = "%$search%";
                    $params[] = $search_param;
                    $params[] = $search_param;
                    $types .= 'ss';
                }

                $query = "SELECT * FROM tms_service WHERE " . implode(' AND ', $conditions) . " ORDER BY s_category, s_name ASC";
                $stmt = $mysqli->prepare($query);
                
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }
                
                $stmt->execute();
                $res = $stmt->get_result();

                // Group services
                $services_grouped = [];
                while ($row = $res->fetch_object()) {
                    $category = $row->s_category;
                    // Check if subcategory column exists
                    $subcategory = (property_exists($row, 's_subcategory') && !empty($row->s_subcategory)) ? $row->s_subcategory : 'All Services';
                    $services_grouped[$category][$subcategory][] = $row;
                }

                if (!empty($services_grouped)):
                    foreach ($services_grouped as $category => $subcategories):
                ?>
                <!-- Category Section -->
                <div class="card mb-4 shadow-lg">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h3 class="mb-0">
                            <i class="fas fa-list-ul"></i> <?php echo htmlspecialchars($category); ?>
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <?php foreach ($subcategories as $subcategory => $services): ?>
                        <!-- Subcategory Section -->
                        <div class="subcategory-section">
                            <div class="subcategory-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-angle-double-right text-primary"></i> 
                                    <strong><?php echo htmlspecialchars($subcategory); ?></strong>
                                    <span class="badge badge-primary ml-2"><?php echo count($services); ?> Services</span>
                                </h5>
                            </div>
                            <div class="row p-3">
                                <?php foreach ($services as $service): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card service-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="service-title">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <?php echo htmlspecialchars($service->s_name); ?>
                                            </h6>
                                            <p class="service-desc">
                                                <?php echo htmlspecialchars(substr($service->s_description, 0, 90)); ?>...
                                            </p>
                                            <div class="service-meta">
                                                <span class="badge badge-success badge-pill px-3 py-2">
                                                    <i class="fas fa-rupee-sign"></i> <?php echo number_format($service->s_price, 0); ?>
                                                </span>
                                                <span class="badge badge-info badge-pill px-3 py-2">
                                                    <i class="far fa-clock"></i> <?php echo htmlspecialchars($service->s_duration); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white border-0">
                                            <a href="confirm-booking.php?s_id=<?php echo $service->s_id; ?>" 
                                               class="btn btn-primary btn-block">
                                                <i class="fas fa-calendar-check"></i> Book Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php
                    endforeach;
                else:
                ?>
                <div class="alert alert-warning shadow">
                    <i class="fas fa-exclamation-triangle"></i> No services found. Try adjusting your search or filter.
                </div>
                <?php endif; ?>

            </div>
            <?php include("vendor/inc/footer.php"); ?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/js/sb-admin.min.js"></script>

    <style>
        .category-card {
            padding: 30px 20px;
            transition: all 0.3s ease;
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .category-card small {
            font-size: 12px;
            opacity: 0.9;
        }
        .subcategory-header {
            background: linear-gradient(to right, #f8f9fc, #ffffff);
            padding: 15px 20px;
            border-bottom: 2px solid #e3e6f0;
            border-left: 5px solid #667eea;
        }
        .service-card {
            transition: all 0.3s ease;
            border: 1px solid #e3e6f0;
            border-radius: 10px;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
            border-color: #667eea;
        }
        .service-title {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            min-height: 45px;
            line-height: 1.4;
        }
        .service-desc {
            font-size: 13px;
            color: #666;
            min-height: 60px;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        .service-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .badge-pill {
            font-size: 12px;
            font-weight: 600;
        }
        
        /* Tablet Responsive */
        @media (min-width: 768px) and (max-width: 1024px) {
            .category-card {
                min-height: 160px;
                padding: 25px 15px;
            }
            .category-card i {
                font-size: 2.5rem !important;
            }
            .service-title {
                font-size: 14px;
            }
            .service-desc {
                font-size: 12px;
            }
        }
        
        /* Mobile Responsive */
        @media (max-width: 767px) {
            .category-card {
                min-height: 140px;
                padding: 18px 12px;
            }
            .category-card i {
                font-size: 2rem !important;
            }
            .category-card strong {
                font-size: 13px;
            }
            .category-card small {
                font-size: 11px;
            }
            .service-title {
                min-height: auto;
                font-size: 14px;
            }
            .service-desc {
                min-height: auto;
                font-size: 12px;
            }
            .subcategory-header h5 {
                font-size: 14px;
            }
        }
        
        /* Large Desktop */
        @media (min-width: 1200px) {
            .container-fluid {
                max-width: 1400px;
                margin: 0 auto;
            }
            .category-card {
                min-height: 200px;
            }
        }
    </style>
</body>
</html>
