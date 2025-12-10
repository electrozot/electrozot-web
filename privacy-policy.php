<?php
  session_start();
  include('admin/vendor/inc/config.php');
?>
<!DOCTYPE html>
<html lang="en">

<?php include("vendor/inc/head.php");?>

<body>

    <!-- Navigation -->
    <?php include("vendor/inc/nav.php");?>
    <!--End Navigation-->

    <!-- Page Content -->
    <div class="container-fluid px-0" style="margin-top: 0px;">
        
        <!-- Header Section -->
        <section class="py-5" style="background: linear-gradient(135deg, #dc143c 0%, #8b0000 100%); color: white;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h1 class="display-4 font-weight-bold mb-3">
                            <i class="fas fa-shield-alt"></i> Privacy Policy
                        </h1>
                        <p class="lead">Your privacy is important to us. Learn how we protect your personal information.</p>
                        <small class="text-light">Last updated: <?php echo date('F d, Y'); ?></small>
                    </div>
                </div>
            </div>
        </section>

        <!-- Privacy Policy Content -->
        <section class="py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card shadow-lg border-0">
                            <div class="card-body p-5">
                                
                                <!-- Introduction -->
                                <div class="mb-5">
                                    <h2 class="text-primary mb-3">
                                        <i class="fas fa-info-circle"></i> Introduction
                                    </h2>
                                    <p class="lead">
                                        Electrozot ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our electrical and technical service booking platform.
                                    </p>
                                </div>

                                <!-- Information We Collect -->
                                <div class="mb-5">
                                    <h2 class="text-primary mb-3">
                                        <i class="fas fa-database"></i> Information We Collect
                                    </h2>
                                    
                                    <h4 class="text-secondary mb-3">Personal Information</h4>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Name and contact information</li>
                                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Phone number and email address</li>
                                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Service address and location details</li>
                                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Service preferences and booking history</li>
                                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Payment information (processed securely)</li>
                                    </ul>

                                    <h4 class="text-secondary mb-3 mt-4">Technical Information</h4>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> IP address and device information</li>
                                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Browser type and version</li>
                                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Usage patterns and preferences</li>
                                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Location data (with your permission)</li>
                                    </ul>
                                </div>

                                <!-- How We Use Information -->
                                <div class="mb-5">
                                    <h2 class="text-primary mb-3">
                                        <i class="fas fa-cogs"></i> How We Use Your Information
                                    </h2>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body">
                                                    <h5 class="card-title text-primary">
                                                        <i class="fas fa-wrench"></i> Service Delivery
                                                    </h5>
                                                    <ul class="list-unstyled">
                                                        <li><i class="fas fa-arrow-right text-primary mr-2"></i> Process and manage bookings</li>
                                                        <li><i class="fas fa-arrow-right text-primary mr-2"></i> Assign qualified technicians</li>
                                                        <li><i class="fas fa-arrow-right text-primary mr-2"></i> Provide customer support</li>
                                                        <li><i class="fas fa-arrow-right text-primary mr-2"></i> Send service updates</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body">
                                                    <h5 class="card-title text-primary">
                                                        <i class="fas fa-chart-line"></i> Platform Improvement
                                                    </h5>
                                                    <ul class="list-unstyled">
                                                        <li><i class="fas fa-arrow-right text-primary mr-2"></i> Analyze usage patterns</li>
                                                        <li><i class="fas fa-arrow-right text-primary mr-2"></i> Improve our services</li>
                                                        <li><i class="fas fa-arrow-right text-primary mr-2"></i> Develop new features</li>
                                                        <li><i class="fas fa-arrow-right text-primary mr-2"></i> Ensure platform security</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Information Sharing -->
                                <div class="mb-5">
                                    <h2 class="text-primary mb-3">
                                        <i class="fas fa-share-alt"></i> Information Sharing
                                    </h2>
                                    <div class="alert alert-info">
                                        <h5 class="alert-heading">
                                            <i class="fas fa-shield-alt"></i> We Never Sell Your Data
                                        </h5>
                                        <p class="mb-0">We do not sell, trade, or rent your personal information to third parties for marketing purposes.</p>
                                    </div>
                                    
                                    <h4 class="text-secondary mb-3">We may share information with:</h4>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="fas fa-user-tie text-primary mr-2"></i> <strong>Technicians:</strong> Only necessary details to complete your service</li>
                                        <li class="mb-2"><i class="fas fa-credit-card text-primary mr-2"></i> <strong>Payment Processors:</strong> Secure payment processing only</li>
                                        <li class="mb-2"><i class="fas fa-balance-scale text-primary mr-2"></i> <strong>Legal Requirements:</strong> When required by law or legal process</li>
                                        <li class="mb-2"><i class="fas fa-shield-alt text-primary mr-2"></i> <strong>Safety:</strong> To protect rights, property, or safety</li>
                                    </ul>
                                </div>

                                <!-- Data Security -->
                                <div class="mb-5">
                                    <h2 class="text-primary mb-3">
                                        <i class="fas fa-lock"></i> Data Security
                                    </h2>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="text-center">
                                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                    <i class="fas fa-encrypt fa-2x"></i>
                                                </div>
                                                <h5 class="mt-3">Encryption</h5>
                                                <p class="text-muted">All data transmitted is encrypted using industry-standard SSL/TLS protocols.</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="text-center">
                                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                    <i class="fas fa-server fa-2x"></i>
                                                </div>
                                                <h5 class="mt-3">Secure Storage</h5>
                                                <p class="text-muted">Your data is stored on secure servers with restricted access and regular backups.</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="text-center">
                                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                    <i class="fas fa-user-shield fa-2x"></i>
                                                </div>
                                                <h5 class="mt-3">Access Control</h5>
                                                <p class="text-muted">Strict access controls ensure only authorized personnel can access your information.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Your Rights -->
                                <div class="mb-5">
                                    <h2 class="text-primary mb-3">
                                        <i class="fas fa-user-check"></i> Your Rights
                                    </h2>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-3">
                                                    <div class="d-flex">
                                                        <i class="fas fa-eye text-success mt-1 mr-3"></i>
                                                        <div>
                                                            <strong>Access:</strong> Request a copy of your personal data
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="mb-3">
                                                    <div class="d-flex">
                                                        <i class="fas fa-edit text-warning mt-1 mr-3"></i>
                                                        <div>
                                                            <strong>Correction:</strong> Update or correct your information
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="mb-3">
                                                    <div class="d-flex">
                                                        <i class="fas fa-trash text-danger mt-1 mr-3"></i>
                                                        <div>
                                                            <strong>Deletion:</strong> Request deletion of your data
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-3">
                                                    <div class="d-flex">
                                                        <i class="fas fa-download text-info mt-1 mr-3"></i>
                                                        <div>
                                                            <strong>Portability:</strong> Export your data in a readable format
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="mb-3">
                                                    <div class="d-flex">
                                                        <i class="fas fa-ban text-secondary mt-1 mr-3"></i>
                                                        <div>
                                                            <strong>Objection:</strong> Object to certain data processing
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="mb-3">
                                                    <div class="d-flex">
                                                        <i class="fas fa-pause text-primary mt-1 mr-3"></i>
                                                        <div>
                                                            <strong>Restriction:</strong> Limit how we process your data
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cookies -->
                                <div class="mb-5">
                                    <h2 class="text-primary mb-3">
                                        <i class="fas fa-cookie-bite"></i> Cookies and Tracking
                                    </h2>
                                    <p>We use cookies and similar technologies to:</p>
                                    <ul>
                                        <li>Remember your preferences and settings</li>
                                        <li>Analyze website traffic and usage patterns</li>
                                        <li>Improve user experience and functionality</li>
                                        <li>Provide personalized content and services</li>
                                    </ul>
                                    <p>You can control cookie settings through your browser preferences.</p>
                                </div>

                                <!-- Contact Information -->
                                <div class="mb-5">
                                    <h2 class="text-primary mb-3">
                                        <i class="fas fa-envelope"></i> Contact Us
                                    </h2>
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <p class="mb-3">If you have questions about this Privacy Policy or want to exercise your rights, contact us:</p>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-2">
                                                        <i class="fas fa-envelope text-primary mr-2"></i>
                                                        <strong>Email:</strong> electrozot@outlook.com
                                                    </p>
                                                    <p class="mb-2">
                                                        <i class="fas fa-phone text-primary mr-2"></i>
                                                        <strong>Phone:</strong> 7559606925
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-2">
                                                        <i class="fas fa-clock text-primary mr-2"></i>
                                                        <strong>Hours:</strong> 7:00 AM - 9:00 PM
                                                    </p>
                                                    <p class="mb-2">
                                                        <i class="fas fa-calendar text-primary mr-2"></i>
                                                        <strong>Days:</strong> Monday - Sunday
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Updates -->
                                <div class="mb-5">
                                    <h2 class="text-primary mb-3">
                                        <i class="fas fa-sync-alt"></i> Policy Updates
                                    </h2>
                                    <div class="alert alert-warning">
                                        <h5 class="alert-heading">
                                            <i class="fas fa-bell"></i> Stay Informed
                                        </h5>
                                        <p class="mb-0">
                                            We may update this Privacy Policy from time to time. We will notify you of any material changes by posting the new policy on this page and updating the "Last updated" date.
                                        </p>
                                    </div>
                                </div>

                                <!-- Back to Top -->
                                <div class="text-center">
                                    <a href="#top" class="btn btn-primary btn-lg">
                                        <i class="fas fa-arrow-up"></i> Back to Top
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
    <!-- /.container -->

    <!-- Footer -->
    <?php include("vendor/inc/footer.php");?>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>