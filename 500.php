<?php
// Set proper 500 header
http_response_code(500);

// Include head for consistent styling
include 'vendor/inc/head.php';
?>

<body>
    <?php include 'vendor/inc/nav.php'; ?>
    
    <main id="main-content" role="main">
        <!-- Page Header -->
        <header class="bg-danger py-5 mb-5">
            <div class="container h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-lg-12">
                        <h1 class="display-4 text-white mt-5 mb-2">Server Error</h1>
                        <p class="lead mb-5 text-white-50">We're experiencing technical difficulties. Please try again later.</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- 500 Content -->
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <i class="fas fa-server text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h2 class="card-title mb-4">Internal Server Error</h2>
                            <p class="card-text mb-4">We're sorry, but something went wrong on our end. Our technical team has been notified and is working to fix the issue.</p>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <a href="/" class="btn btn-primary btn-block">
                                        <i class="fas fa-home mr-2"></i>Return Home
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="/contact.php" class="btn btn-success btn-block">
                                        <i class="fas fa-phone mr-2"></i>Contact Support
                                    </a>
                                </div>
                            </div>
                            
                            <p class="text-muted mt-4">
                                <small>If this problem persists, please contact us at <strong>7559606925</strong></small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'vendor/inc/footer.php'; ?>
</body>
</html>