<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <?php include("vendor/inc/head.php");?>
</head>

<body style="background: linear-gradient(180deg, #f8f9fa 0%, #fff5f7 100%); min-height: 100vh;">

    <?php include("vendor/inc/nav.php");?>

    <!-- Hero Section -->
    <section class="services-hero" style="background: linear-gradient(135deg, #ffe5e8 0%, #fff0f2 50%, #ffe5e8 100%); padding: 116px 0 40px 0; margin-top: -56px;">
    <div class="container">
            <div class="text-center">
                <h1 class="services-title" style="font-size: 2rem; font-weight: 700; color: #2d3748; margin-bottom: 10px;">
                    <i class="fas fa-tools" style="color: #ff4757;"></i> Our Services
                </h1>
                <p class="services-subtitle" style="font-size: 0.95rem; color: #6c757d; max-width: 600px; margin: 0 auto;">
                    Expert installation, maintenance, and repair services for your home and office appliances
                </p>
            </div>
        </div>
    </section>

    <div class="container" style="padding-top: 30px; padding-bottom: 40px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb services-breadcrumb" style="background: rgba(255,255,255,0.8); border-radius: 10px; padding: 10px 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 20px;">
            <li class="breadcrumb-item">
                    <a href="index.php" style="color: #ff4757; text-decoration: none; font-size: 0.9rem; font-weight: 500;">
                        <i class="fas fa-home"></i> Home
                    </a>
            </li>
                <li class="breadcrumb-item active" style="color: #6c757d; font-size: 0.9rem;">Services</li>
        </ol>
        </nav>

        <!-- Service Images -->
        <style>
            @media (max-width: 768px) {
                .service-images-mobile {
                    display: flex !important;
                    flex-direction: row !important;
                    flex-wrap: nowrap !important;
                    overflow-x: auto !important;
                    overflow-y: hidden !important;
                    gap: 15px !important;
                    padding: 10px 5px !important;
                    margin-left: 0 !important;
                    margin-right: 0 !important;
                    -webkit-overflow-scrolling: touch !important;
                    scroll-snap-type: x mandatory !important;
                }
                .service-images-mobile .service-image-col {
                    flex: 0 0 auto !important;
                    width: 75% !important;
                    min-width: 250px !important;
                    max-width: 300px !important;
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                    scroll-snap-align: center !important;
                }
                .service-images-mobile .service-image-wrapper {
                    width: 100% !important;
                    display: block !important;
                }
                .service-images-mobile .service-image {
                    width: 100% !important;
                    height: auto !important;
                    max-height: 220px !important;
                    object-fit: cover !important;
                    display: block !important;
                }
            }
        </style>
        <div class="row text-center mb-4 service-images-mobile" style="margin-bottom: 30px !important;">
            <div class="col-lg-4 col-md-4 mb-3 service-image-col">
                <div class="service-image-wrapper">
                    <img class="img-fluid rounded service-image" src="vendor/img/service1.png" alt="Electronic Service" style="max-height: 150px; width: auto; border-radius: 12px;">
                </div>
            </div>
            <div class="col-lg-4 col-md-4 mb-3 service-image-col">
                <div class="service-image-wrapper">
                    <img class="img-fluid rounded service-image" src="vendor/img/service3.png" alt="Electrical Service" style="max-height: 150px; width: auto; border-radius: 12px;">
                </div>
            </div>
            <div class="col-lg-4 col-md-4 mb-3 service-image-col">
                <div class="service-image-wrapper">
                    <img class="img-fluid rounded service-image" src="vendor/img/service2.png" alt="Plumbing Service" style="max-height: 150px; width: auto; border-radius: 12px;">
                </div>
            </div>
        </div>

        <!-- Service Cards -->
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-3">
                <a href="index.php#booking-form" class="service-card-link" style="text-decoration: none; color: inherit;">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #ffe5e8 100%); border-radius: 15px; overflow: hidden; cursor: pointer;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #ffcccc 0%, #ffb3b3 100%); padding: 12px 15px; border: none;">
                        <h5 class="mb-0" style="font-size: 0.95rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-cog" style="color: #ff4757; margin-right: 8px;"></i>Installation Services
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 15px;">
                        <p class="card-text" style="font-size: 0.8rem; color: #6c757d; margin-bottom: 12px; line-height: 1.5;">
                            Quick, clean, and professional setup for all new appliances and systems.
                        </p>
                        <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>TV Installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Dish installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Wifi installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Washing Machine installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Water Geyser installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Fan & Lights installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Electric Chimney installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Camera installation
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>AC servicing
                            </li>
                        </ul>
                    </div>
                </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <a href="index.php#booking-form" class="service-card-link" style="text-decoration: none; color: inherit;">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #fff0f5 100%); border-radius: 15px; overflow: hidden; cursor: pointer;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #ffe0e6 0%, #ffccd5 100%); padding: 12px 15px; border: none;">
                        <h5 class="mb-0" style="font-size: 0.95rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-wrench" style="color: #ff4757; margin-right: 8px;"></i>Electronic & Appliance Repair
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 15px;">
                        <p class="card-text" style="font-size: 0.8rem; color: #6c757d; margin-bottom: 12px; line-height: 1.5;">
                            Expert diagnosis and repair for all major home appliances and gadgets.
                        </p>
                        <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Basic electrical work
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>AC, TV, Cooler, Fan, Heater Repair
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Refrigerator Repair
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Washing Machine Repair
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Induction Cooktop Repair
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Music system repair
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Expert motherboard & electronics repair
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Hand tools ( Drill/Cutter ) repair
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Replacement of switch sockets
                            </li>
                        </ul>
                    </div>
                </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <a href="index.php#booking-form" class="service-card-link" style="text-decoration: none; color: inherit;">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #f0f8ff 100%); border-radius: 15px; overflow: hidden; cursor: pointer;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #e6f3ff 0%, #cce6ff 100%); padding: 12px 15px; border: none;">
                        <h5 class="mb-0" style="font-size: 0.95rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-tint" style="color: #ff4757; margin-right: 8px;"></i>Plumbing Solutions & Servicing
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 15px;">
                        <p class="card-text" style="font-size: 0.8rem; color: #6c757d; margin-bottom: 12px; line-height: 1.5;">
                            Comprehensive solutions for water systems, leaks, and electrical maintenance.
                        </p>
                        <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Plumbing services (leak fixes, fixture repair)
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Water Filter servicing
                            </li>
                            <li style="font-size: 0.75rem; color: #495057; padding: 4px 0;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Water tank cleaning service
                            </li>
                        </ul>
                    </div>
                </div>
                </a>
            </div>
        </div>

        <!-- Feature Cards -->
        <div class="row mt-4">
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 feature-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #fff5f0 100%); border-radius: 12px; overflow: hidden;">
                    <div class="card-body text-center" style="padding: 20px;">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-bolt" style="font-size: 2rem; color: #ff4757;"></i>
                        </div>
                        <h5 class="card-title" style="font-size: 0.9rem; font-weight: 600; color: #2d3748; margin-bottom: 10px;">
                            Faster And Safe Service
                        </h5>
                        <p class="card-text" style="font-size: 0.8rem; color: #6c757d; line-height: 1.6; margin: 0;">
                            We provide professional technician services with accountability, reliability and ease of booking skilled technicians for all your needs.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 feature-card-compact border-0 shadow-sm warranty-card" style="background: linear-gradient(180deg, #fff 0%, #fff0f5 100%); border-radius: 12px; overflow: hidden; cursor: pointer;" role="button" data-toggle="modal" data-target="#warrantyModal" aria-label="View warranty terms">
                    <div class="card-body text-center" style="padding: 20px;">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-shield-alt" style="font-size: 2rem; color: #ff4757;"></i>
                        </div>
                        <h5 class="card-title" style="font-size: 0.9rem; font-weight: 600; color: #2d3748; margin-bottom: 10px;">
                            1‑Month Warranty on Repair Services
                        </h5>
                        <p class="card-text" style="font-size: 0.8rem; color: #6c757d; line-height: 1.6; margin: 0;">
                            Click to view warranty terms & conditions
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 feature-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #f0fff4 100%); border-radius: 12px; overflow: hidden;">
                    <div class="card-body text-center" style="padding: 20px;">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-star" style="font-size: 2rem; color: #ff4757;"></i>
                        </div>
                        <h5 class="card-title" style="font-size: 0.9rem; font-weight: 600; color: #2d3748; margin-bottom: 10px;">
                            Our Commitment
                        </h5>
                        <p class="card-text" style="font-size: 0.8rem; color: #6c757d; line-height: 1.6; margin: 0;">
                            Your satisfaction is our priority. We are committed to quality workmanship, transparent pricing, and timely service for every project.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Warranty Terms Modal -->
    <div class="modal fade" id="warrantyModal" tabindex="-1" role="dialog" aria-labelledby="warrantyModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="warrantyModalLabel">Warranty Terms & Conditions</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <ul class="list-unstyled mb-0" style="font-size: 0.95rem; color: #4a5568;">
              <li class="mb-2"><i class="fas fa-check-circle" style="color:#ff4757; margin-right:6px;"></i> Electrozot provides a 1‑month warranty on repair services from the date of completion.</li>
              <li class="mb-2"><i class="fas fa-check-circle" style="color:#ff4757; margin-right:6px;"></i> Warranty is void if the product seal is opened, broken, or tampered.</li>
              <li class="mb-2"><i class="fas fa-check-circle" style="color:#ff4757; margin-right:6px;"></i> Warranty does not cover any internal or external physical damage to the product.</li>
              <li class="mb-2"><i class="fas fa-check-circle" style="color:#ff4757; margin-right:6px;"></i> Warranty is void in case of any liquid damage or exposure to moisture.</li>
            </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <?php include("vendor/inc/footer.php");?>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <style>
        /* Service Page Styles */
        .service-image-wrapper {
            transition: transform 0.3s ease;
            display: inline-block;
        }

        .service-image {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            display: inline-block;
        }

        .service-image:hover {
            transform: scale(1.08);
            box-shadow: 0 8px 20px rgba(255, 71, 87, 0.3);
        }

        .service-card-compact {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .service-card-compact:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(255, 71, 87, 0.2) !important;
        }

        .service-card-header {
            transition: all 0.3s ease;
        }

        .service-card-compact:hover .service-card-header {
            background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%) !important;
            color: white !important;
        }

        .service-card-compact:hover .service-card-header h5 {
            color: white !important;
        }

        .service-card-compact:hover .service-card-header i {
            color: white !important;
        }

        .service-list li {
            transition: all 0.2s ease;
        }

        .service-list li:hover {
            color: #ff4757 !important;
            padding-left: 5px;
        }

        .feature-card-compact {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .feature-card-compact:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(255, 71, 87, 0.2) !important;
        }

        .feature-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffe5e8 0%, #ffcccc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            transition: all 0.3s ease;
        }

        .feature-card-compact:hover .feature-icon-wrapper {
            transform: scale(1.15) rotate(5deg);
            background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%);
        }

        .feature-card-compact:hover .feature-icon-wrapper i {
            color: white !important;
        }

        .services-breadcrumb a:hover {
            color: #ff6b9d !important;
            transform: translateX(3px);
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .service-image {
                max-height: 120px !important;
            }
        }
    </style>

    <!-- Bottom Navigation Bar -->
    <?php include("vendor/inc/bottom-nav-home.php"); ?>

</body>

</html>