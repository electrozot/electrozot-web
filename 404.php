<?php
// Set proper 404 header
http_response_code(404);

// Include head for consistent styling
include 'vendor/inc/head.php';
?>

<body>
    <?php include 'vendor/inc/nav.php'; ?>
    
    <main id="main-content" role="main">
        <!-- Page Header -->
        <header class="bg-primary py-5 mb-5" style="background: linear-gradient(135deg, #8b0000 0%, #dc143c 100%) !important;">
            <div class="container h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-lg-12">
                        <h1 class="display-4 text-white mt-5 mb-2">Page Not Found</h1>
                        <p class="lead mb-5 text-white-50">The page you're looking for doesn't exist, but we can help you find what you need.</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- 404 Content -->
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                            </div>
                            <h2 class="card-title mb-4">Oops! Page Not Found</h2>
                            <p class="card-text mb-4">The page you requested could not be found. This might be because:</p>
                            
                            <div class="row text-left mb-4">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success mr-2"></i> The URL was typed incorrectly</li>
                                        <li><i class="fas fa-check text-success mr-2"></i> The page has been moved or deleted</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success mr-2"></i> The link you followed is outdated</li>
                                        <li><i class="fas fa-check text-success mr-2"></i> You don't have permission to access this page</li>
                                    </ul>
                                </div>
                            </div>

                            <h4 class="mb-3">What would you like to do?</h4>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <a href="/" class="btn btn-primary btn-block">
                                        <i class="fas fa-home mr-2"></i>Go Home
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="/services.php" class="btn btn-success btn-block">
                                        <i class="fas fa-cog mr-2"></i>View Services
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="/contact.php" class="btn btn-info btn-block">
                                        <i class="fas fa-phone mr-2"></i>Contact Us
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Popular Services -->
                    <div class="card shadow-lg border-0 mt-4">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-star mr-2"></i>Popular Services</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <a href="/installation-services.php" class="text-decoration-none">
                                        <div class="text-center p-3 border rounded hover-shadow">
                                            <i class="fas fa-cog text-primary mb-2" style="font-size: 2rem;"></i>
                                            <h6>Installation Services</h6>
                                            <small class="text-muted">TV, AC, WiFi Setup</small>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="/electronic-appliance-repair.php" class="text-decoration-none">
                                        <div class="text-center p-3 border rounded hover-shadow">
                                            <i class="fas fa-wrench text-success mb-2" style="font-size: 2rem;"></i>
                                            <h6>Appliance Repair</h6>
                                            <small class="text-muted">AC, TV, Refrigerator</small>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="/plumbing-solutions.php" class="text-decoration-none">
                                        <div class="text-center p-3 border rounded hover-shadow">
                                            <i class="fas fa-tint text-info mb-2" style="font-size: 2rem;"></i>
                                            <h6>Plumbing Solutions</h6>
                                            <small class="text-muted">Leak Repair, Tank Cleaning</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search Box -->
                    <div class="card shadow-lg border-0 mt-4">
                        <div class="card-body">
                            <h5 class="card-title text-center mb-3">
                                <i class="fas fa-search mr-2"></i>Search Our Site
                            </h5>
                            <form action="/" method="get" class="text-center">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" placeholder="Search for services, repairs, installation..." required>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'vendor/inc/footer.php'; ?>

    <style>
        .hover-shadow:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #8b0000 0%, #dc143c 100%) !important;
        }
    </style>
</body>
</html>