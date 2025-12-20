<?php
// Include database configuration
include('admin/vendor/inc/config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <?php include("vendor/inc/head.php");?>
</head>

<body style="background: linear-gradient(180deg, #f8f9fa 0%, #fff5f7 100%); min-height: 100vh; padding-bottom: 70px;">

    <?php include("vendor/inc/nav.php");?>

    <!-- Hero Section -->
    <section class="services-hero" style="background: linear-gradient(135deg, #E0F2FE 0%, #FECDD3 25%, #D1FAE5 50%, #FBCFE8 75%, #FED7D7 100%); background-size: 200% 200%; animation: gradientShift 10s ease infinite; padding: 140px 0 50px 0; margin-top: -56px;">
        <style>
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
        </style>
        <div class="container">
            <div class="text-center">
                <h1 class="services-title" style="font-size: 2.5rem; font-weight: 800; color: #2d3748; margin-bottom: 15px; text-shadow: 2px 2px 4px rgba(255,255,255,0.5);">
                    <i class="fas fa-tools" style="color: #EC4899;"></i> Our Services
                </h1>
                <p class="services-subtitle" style="font-size: 1.1rem; color: #6B7280; max-width: 650px; margin: 0 auto; font-weight: 500;">
                    Expert installation, maintenance, and repair services for your home and office appliances
                </p>
            </div>
        </div>
    </section>

    <div class="container" style="padding-top: 40px; padding-bottom: 80px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb services-breadcrumb" style="background: rgba(255,255,255,0.95); border-radius: 12px; padding: 12px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; display: flex; flex-wrap: nowrap; align-items: center;">
                <li class="breadcrumb-item" style="display: inline-flex; align-items: center;">
                    <a href="index.php" style="color: #EC4899; text-decoration: none; font-size: 0.95rem; font-weight: 600; white-space: nowrap;">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item active" style="color: #6c757d; font-size: 0.95rem; font-weight: 500; display: inline-flex; align-items: center; white-space: nowrap;">Services</li>
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

        <!-- Complete Service Categories with ALL Services -->
        <div class="row">
            <!-- ELECTRICAL SERVICES -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden; cursor: pointer;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #FBCFE8 0%, #F9A8D4 100%); padding: 15px 20px; border: none;">
                        <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-bolt" style="color: #EC4899; margin-right: 8px;"></i>⚡ ELECTRICAL SERVICES
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 20px;">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 style="font-size: 0.9rem; font-weight: 600; color: #EC4899; margin-bottom: 10px;">Wiring & Fixtures</h6>
                                <ul class="service-list" style="list-style: none; padding: 0; margin: 0 0 15px 0;">
                                    <li class="service-item" data-service="home-wiring" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Home Wiring (New installation and repair)
                                    </li>
                                    <li class="service-item" data-service="switch-socket" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Switch/Socket Installation and Replacement
                                    </li>
                                    <li class="service-item" data-service="light-fixture" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Light Fixture Installation (Tube lights, LED panels, chandeliers)
                                    </li>
                                    <li class="service-item" data-service="festive-lighting" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Light Decoration/Festive Lighting Setup
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 style="font-size: 0.9rem; font-weight: 600; color: #EC4899; margin-bottom: 10px;">Safety & Power</h6>
                                <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                                    <li class="service-item" data-service="circuit-breaker" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Circuit Breaker and Fuse Box troubleshooting
                                    </li>
                                    <li class="service-item" data-service="inverter-ups" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Inverter, UPS, and Voltage Stabilizer installation
                                    </li>
                                    <li class="service-item" data-service="grounding-earthing" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Grounding and Earthing system installation
                                    </li>
                                    <li class="service-item" data-service="electrical-outlet" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>New Electrical Outlet/Point installation
                                    </li>
                                    <li class="service-item" data-service="fan-regulator" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Ceiling Fan Regulator repair/replacement
                                    </li>
                                    <li class="service-item" data-service="electrical-fault" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Electrical fault finding and short-circuit repair
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REPAIR SERVICES -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 15px; overflow: hidden; cursor: pointer;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #FECDD3 0%, #FDA4AF 100%); padding: 15px 20px; border: none;">
                        <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-wrench" style="color: #EC4899; margin-right: 8px;"></i>🔧 REPAIR SERVICES
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 20px;">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 style="font-size: 0.9rem; font-weight: 600; color: #EC4899; margin-bottom: 10px;">Major Appliances</h6>
                                <ul class="service-list" style="list-style: none; padding: 0; margin: 0 0 15px 0;">
                                    <li class="service-item" data-service="ac-repair" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Air Conditioner (AC) Repair (Split, Window, Central)
                                    </li>
                                    <li class="service-item" data-service="refrigerator-repair" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Refrigerator Repair and Gas Charging
                                    </li>
                                    <li class="service-item" data-service="washing-machine-repair" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Washing Machine Repair (Semi/Fully automatic, Front/Top Load)
                                    </li>
                                    <li class="service-item" data-service="microwave-repair" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Microwave Oven Repair
                                    </li>
                                    <li class="service-item" data-service="geyser-repair" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Geyser (Water Heater) Repair
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 style="font-size: 0.9rem; font-weight: 600; color: #EC4899; margin-bottom: 10px;">Other Gadgets</h6>
                                <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                                    <li class="service-item" data-service="fan-repair" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Fan Repair (Ceiling, Table, Exhaust)
                                    </li>
                                    <li class="service-item" data-service="tv-repair" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Television (TV) Repair and Troubleshooting
                                    </li>
                                    <li class="service-item" data-service="iron-repair" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Electric Iron/Press Repair
                                    </li>
                                    <li class="service-item" data-service="music-system-repair" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Music System/Home Theatre Repair
                                    </li>
                                    <li class="service-item" data-service="heater-repair" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Electric Heater Repair (Room Heaters, Rods)
                                    </li>
                                    <li class="service-item" data-service="induction-repair" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Induction Cooktop and Electric Stove Repair
                                    </li>
                                    <li class="service-item" data-service="cooler-repair" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Air Cooler Repair
                                    </li>
                                    <li class="service-item" data-service="power-tools-repair" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Power Tools Repair (Drills, Cutters, Grinders, etc.)
                                    </li>
                                    <li class="service-item" data-service="water-filter-repair" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Water Filter/Purifier Repair
                                    </li>
                                    <li class="service-item" data-service="mixer-grinder-repair" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Mixer Grinder / Juicer Repair and Clean
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- INSTALLATION SERVICES -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #F0FDFA 100%); border-radius: 15px; overflow: hidden; cursor: pointer;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #A7F3D0 0%, #6EE7B7 100%); padding: 15px 20px; border: none;">
                        <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-cog" style="color: #10B981; margin-right: 8px;"></i>🔌 INSTALLATION SERVICES
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 20px;">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 style="font-size: 0.9rem; font-weight: 600; color: #10B981; margin-bottom: 10px;">Appliance Setup</h6>
                                <ul class="service-list" style="list-style: none; padding: 0; margin: 0 0 15px 0;">
                                    <li class="service-item" data-service="tv-dish-installation" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>TV/DTH Dish Installation and Tuning
                                    </li>
                                    <li class="service-item" data-service="chimney-installation" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Electric Chimney Installation
                                    </li>
                                    <li class="service-item" data-service="fan-installation" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Ceiling and Wall Fan Installation
                                    </li>
                                    <li class="service-item" data-service="washing-machine-installation" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Washing Machine Installation and Uninstallation
                                    </li>
                                    <li class="service-item" data-service="cooler-installation" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Air Cooler Installation
                                    </li>
                                    <li class="service-item" data-service="water-filter-installation" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Water Filter/Purifier Installation
                                    </li>
                                    <li class="service-item" data-service="geyser-installation" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Geyser/Water Heater Installation
                                    </li>
                                    <li class="service-item" data-service="light-installation" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Light Fixture Installation
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 style="font-size: 0.9rem; font-weight: 600; color: #10B981; margin-bottom: 10px;">Tech & Security</h6>
                                <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                                    <li class="service-item" data-service="cctv-installation" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>CCTV and Security Camera Installation
                                    </li>
                                    <li class="service-item" data-service="wifi-setup" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Wi-Fi Router and Modem Setup/Troubleshooting
                                    </li>
                                    <li class="service-item" data-service="smart-home-installation" style="font-size: 0.75rem; color: #495057; padding: 2px 0; cursor: pointer; transition: all 0.3s ease;">
                                        <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Smart Home Device Installation (Smart switches, smart lights)
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAINTENANCE SERVICES -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FDF5FF 100%); border-radius: 15px; overflow: hidden; cursor: pointer;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #DDD6FE 0%, #C4B5FD 100%); padding: 15px 20px; border: none;">
                        <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-tools" style="color: #8B5CF6; margin-right: 8px;"></i>🛠️ MAINTENANCE SERVICES
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 20px;">
                        <h6 style="font-size: 0.9rem; font-weight: 600; color: #8B5CF6; margin-bottom: 15px;">Routine Care</h6>
                        <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                            <li class="service-item" data-service="ac-servicing" style="font-size: 0.8rem; color: #495057; padding: 3px 0; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>AC Wet and Dry Servicing
                            </li>
                            <li class="service-item" data-service="washing-machine-maintenance" style="font-size: 0.8rem; color: #495057; padding: 3px 0; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Washing Machine General Maintenance and Cleaning
                            </li>
                            <li class="service-item" data-service="geyser-servicing" style="font-size: 0.8rem; color: #495057; padding: 3px 0; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Geyser Descaling and Service
                            </li>
                            <li class="service-item" data-service="water-filter-servicing" style="font-size: 0.8rem; color: #495057; padding: 3px 0; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Water Filter Cartridge Replacement and General Service
                            </li>
                            <li class="service-item" data-service="water-tank-cleaning" style="font-size: 0.8rem; color: #495057; padding: 3px 0; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Water Tank Cleaning (Manual and Motorized)
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- PLUMBING SERVICES -->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card h-100 service-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #F0FDFA 100%); border-radius: 15px; overflow: hidden; cursor: pointer;">
                    <div class="card-header service-card-header" style="background: linear-gradient(135deg, #A7F3D0 0%, #6EE7B7 100%); padding: 15px 20px; border: none;">
                        <h5 class="mb-0" style="font-size: 1.1rem; font-weight: 600; color: #2d3748;">
                            <i class="fas fa-tint" style="color: #10B981; margin-right: 8px;"></i>💧 PLUMBING SERVICES
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 20px;">
                        <h6 style="font-size: 0.9rem; font-weight: 600; color: #10B981; margin-bottom: 15px;">Fixtures & Taps</h6>
                        <ul class="service-list" style="list-style: none; padding: 0; margin: 0;">
                            <li class="service-item" data-service="tap-repair" style="font-size: 0.8rem; color: #495057; padding: 3px 0; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Tap, Faucet, and Shower Installation/Repair
                            </li>
                            <li class="service-item" data-service="basin-installation" style="font-size: 0.8rem; color: #495057; padding: 3px 0; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Washbasin and Sink Installation/Repair
                            </li>
                            <li class="service-item" data-service="toilet-installation" style="font-size: 0.8rem; color: #495057; padding: 3px 0; cursor: pointer; transition: all 0.3s ease;">
                                <i class="fas fa-check-circle" style="color: #ff4757; font-size: 0.7rem; margin-right: 6px;"></i>Toilet, Commode, and Flush Tank Installation
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature Cards -->
        <div class="row mt-4">
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100 feature-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #FDF5FF 100%); border-radius: 12px; overflow: hidden;">
                    <div class="card-body text-center" style="padding: 20px;">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-bolt" style="font-size: 2rem; color: #A855F7;"></i>
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
                <div class="card h-100 feature-card-compact border-0 shadow-sm warranty-card" style="background: linear-gradient(180deg, #fff 0%, #FFF5F7 100%); border-radius: 12px; overflow: hidden; cursor: pointer;" role="button" data-toggle="modal" data-target="#warrantyModal" aria-label="View warranty terms">
                    <div class="card-body text-center" style="padding: 20px;">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-shield-alt" style="font-size: 2rem; color: #EC4899;"></i>
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
                <div class="card h-100 feature-card-compact border-0 shadow-sm" style="background: linear-gradient(180deg, #fff 0%, #F0FDFA 100%); border-radius: 12px; overflow: hidden;">
                    <div class="card-body text-center" style="padding: 20px;">
                        <div class="feature-icon-wrapper mb-3">
                            <i class="fas fa-star" style="font-size: 2rem; color: #10B981;"></i>
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
    
    <!-- Enhanced Mobile menu fix -->
    <script>
        $(document).ready(function() {
            // Enhanced mobile menu toggle
            $('.navbar-toggler').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var target = $(this).attr('data-target') || $(this).attr('data-bs-target');
                var $target = $(target);
                
                if ($target.length) {
                    $target.toggleClass('show');
                    var isExpanded = $target.hasClass('show');
                    $(this).attr('aria-expanded', isExpanded);
                }
            });
            
            // Close menu when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.navbar').length) {
                    $('.navbar-collapse').removeClass('show');
                    $('.navbar-toggler').attr('aria-expanded', 'false');
                }
            });
            
            // Close menu when clicking on menu items
            $('.navbar-nav .nav-link').on('click', function() {
                $('.navbar-collapse').removeClass('show');
                $('.navbar-toggler').attr('aria-expanded', 'false');
            });
            
            // Close menu with arrow button
            $('.mobile-menu-arrow-close').on('click', function(e) {
                e.preventDefault();
                $('.navbar-collapse').removeClass('show');
                $('.navbar-toggler').attr('aria-expanded', 'false');
            });
            
            // Prevent menu from closing when clicking inside it
            $('.navbar-collapse').on('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>

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
            background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%) !important;
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
            color: #EC4899 !important;
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
            background: linear-gradient(135deg, #FDF2F8 0%, #FCE7F3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            transition: all 0.3s ease;
        }

        .feature-card-compact:hover .feature-icon-wrapper {
            transform: scale(1.15) rotate(5deg);
            background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%);
        }

        .feature-card-compact:hover .feature-icon-wrapper i {
            color: white !important;
        }

        .services-breadcrumb {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
        }
        
        .services-breadcrumb .breadcrumb-item {
            display: inline-flex !important;
            align-items: center !important;
            white-space: nowrap !important;
        }
        
        .services-breadcrumb a:hover {
            color: #F472B6 !important;
            transform: translateX(3px);
            transition: all 0.3s ease;
        }
        
        @media (max-width: 576px) {
            .services-breadcrumb {
                padding: 10px 15px !important;
                font-size: 0.85rem !important;
            }
            
            .services-breadcrumb .breadcrumb-item a,
            .services-breadcrumb .breadcrumb-item {
                font-size: 0.85rem !important;
            }
        }

        @media (max-width: 768px) {
            .services-hero {
                padding: 100px 0 40px 0 !important;
            }
        }

        @media (max-width: 768px) {
            .service-image {
                max-height: 120px !important;
            }
        }
    </style>

    <!-- Service Details Modal -->
    <div class="modal fade" id="serviceDetailsModal" tabindex="-1" role="dialog" aria-labelledby="serviceDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 15px; overflow: hidden; max-width: 500px; margin: 0 auto;">
                <div class="modal-header" id="modalHeader" style="background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%); color: white; border: none; padding: 12px 20px;">
                    <h6 class="modal-title font-weight-bold" id="serviceDetailsModalLabel" style="font-size: 1rem; margin: 0;">
                        <i class="fas fa-tools"></i> Service Details
                    </h6>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="font-size: 1.3rem; opacity: 1; padding: 0; margin: 0;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 15px;">
                    <div id="serviceDetailsContent">
                        <!-- Service details will be loaded here -->
                    </div>
                    <div class="text-center mt-3">
                        <a href="index.php#booking-form" class="btn btn-primary btn-block" style="background: linear-gradient(135deg, #EC4899 0%, #F472B6 100%); border: none; border-radius: 20px; padding: 10px 20px; font-weight: 600; font-size: 0.9rem;">
                            <i class="fas fa-calendar-check"></i> Book This Service Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compact Mobile CSS for Service Modal -->
    <style>
        .modal-dialog {
            max-width: 480px;
            margin: 1.75rem auto;
        }
        
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }
        
        @media (min-width: 576px) {
            .modal-dialog-centered {
                min-height: calc(100% - 3.5rem);
            }
            .modal-dialog {
                margin: 1.75rem auto;
            }
        }
        
        @media (max-width: 768px) {
            .modal-dialog {
                margin: 20px !important;
                max-width: calc(100% - 40px) !important;
            }
            
            .modal-content {
                border-radius: 12px !important;
            }
            
            .modal-header {
                padding: 10px 15px !important;
            }
            
            .modal-title {
                font-size: 0.9rem !important;
            }
            
            .modal-body {
                padding: 12px !important;
            }
            
            .service-description {
                margin-bottom: 15px !important;
            }
            
            .service-description h6 {
                font-size: 0.85rem !important;
                margin-bottom: 8px !important;
            }
            
            .service-description p {
                font-size: 0.8rem !important;
                margin-bottom: 10px !important;
                line-height: 1.4 !important;
            }
            
            .service-highlights {
                margin-bottom: 15px !important;
            }
            
            .service-highlights h6 {
                font-size: 0.85rem !important;
                margin-bottom: 8px !important;
            }
            
            .highlights-grid div {
                font-size: 0.75rem !important;
                padding: 2px 0 !important;
            }
            
            .service-info-compact {
                padding: 12px !important;
                margin-bottom: 10px !important;
            }
            
            .service-info-compact div {
                font-size: 0.7rem !important;
            }
            
            .btn-block {
                font-size: 0.85rem !important;
                padding: 8px 15px !important;
            }
        }
        
        @media (max-width: 480px) {
            .modal-dialog {
                margin: 15px !important;
                max-width: calc(100% - 30px) !important;
            }
            
            .modal-header {
                padding: 8px 12px !important;
            }
            
            .modal-body {
                padding: 10px !important;
            }
            
            .service-description h6,
            .service-highlights h6 {
                font-size: 0.8rem !important;
            }
            
            .service-description p {
                font-size: 0.75rem !important;
            }
            
            .highlights-grid div {
                font-size: 0.7rem !important;
            }
            
            .service-info-compact {
                padding: 10px !important;
            }
        }
    </style>

    <!-- Service Details JavaScript -->
    <script>
        // Service details data
        const serviceDetails = {
            'home-wiring': {
                title: 'Home Wiring Installation & Repair',
                icon: 'fas fa-plug',
                description: 'Professional electrical wiring services for new installations and repairs in residential and commercial properties.',
                details: [
                    'New electrical wiring installation for homes and offices',
                    'Old wiring replacement and upgrades',
                    'Wire damage repair and troubleshooting',
                    'Safety compliance checks and certifications',
                    'Emergency wiring repair services'
                ],
                duration: '2-6 hours',
                price: 'Starting from ₹500'
            },
            'switch-socket': {
                title: 'Switch & Socket Installation',
                icon: 'fas fa-toggle-on',
                description: 'Expert installation and replacement of electrical switches, sockets, and power outlets.',
                details: [
                    'New switch and socket installation',
                    'Replacement of faulty switches and sockets',
                    'Modular switch upgrades',
                    'USB charging socket installation',
                    'Dimmer switch installation'
                ],
                duration: '30 minutes - 2 hours',
                price: 'Starting from ₹150'
            },
            'light-fixture': {
                title: 'Light Fixture Installation',
                icon: 'fas fa-lightbulb',
                description: 'Professional installation of all types of lighting fixtures including LED panels, tube lights, and chandeliers.',
                details: [
                    'LED tube light and panel installation',
                    'Chandelier and decorative light fitting',
                    'Ceiling and wall light fixtures',
                    'Track lighting and spot lights',
                    'Smart lighting system setup'
                ],
                duration: '1-3 hours',
                price: 'Starting from ₹200'
            },
            'festive-lighting': {
                title: 'Festive & Decorative Lighting',
                icon: 'fas fa-star',
                description: 'Beautiful festive and decorative lighting setup for special occasions and celebrations.',
                details: [
                    'Diwali and festival lighting decoration',
                    'Wedding and party lighting setup',
                    'Garden and outdoor lighting',
                    'LED strip and fairy lights installation',
                    'Temporary event lighting solutions'
                ],
                duration: '2-4 hours',
                price: 'Starting from ₹800'
            },
            'circuit-breaker': {
                title: 'Circuit Breaker & Fuse Box Service',
                icon: 'fas fa-bolt',
                description: 'Professional troubleshooting and repair of circuit breakers, fuse boxes, and main electrical panels.',
                details: [
                    'Circuit breaker replacement and repair',
                    'Main panel troubleshooting',
                    'Fuse box upgrades and maintenance',
                    'Electrical load balancing',
                    'Safety switch installation'
                ],
                duration: '1-4 hours',
                price: 'Starting from ₹800'
            },
            'inverter-ups': {
                title: 'Inverter & UPS Installation',
                icon: 'fas fa-battery-full',
                description: 'Complete installation and wiring services for inverters, UPS systems, and voltage stabilizers.',
                details: [
                    'Home inverter installation and wiring',
                    'UPS system setup for computers',
                    'Voltage stabilizer installation',
                    'Battery connection and maintenance',
                    'Automatic changeover setup'
                ],
                duration: '2-4 hours',
                price: 'Starting from ₹600'
            },
            'grounding-earthing': {
                title: 'Grounding and Earthing System Installation',
                icon: 'fas fa-plug',
                description: 'Professional installation of grounding and earthing systems for electrical safety and protection.',
                details: [
                    'Earth pit installation and setup',
                    'Grounding rod installation',
                    'Earth wire connection to main panel',
                    'Electrical safety testing',
                    'Compliance with safety standards'
                ],
                duration: '2-4 hours',
                price: 'Starting from ₹800'
            },
            'electrical-outlet': {
                title: 'New Electrical Outlet/Point Installation',
                icon: 'fas fa-plug',
                description: 'Installation of new electrical outlets, power points, and charging sockets for homes and offices.',
                details: [
                    'New power outlet installation',
                    'USB charging socket setup',
                    'Modular socket installation',
                    'Wiring and connection work',
                    'Safety testing and certification'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹300'
            },
            'fan-regulator': {
                title: 'Ceiling Fan Regulator Repair/Replacement',
                icon: 'fas fa-sliders-h',
                description: 'Professional repair and replacement of ceiling fan regulators and speed controllers.',
                details: [
                    'Fan regulator diagnosis and repair',
                    'Speed controller replacement',
                    'Dimmer switch installation',
                    'Wiring and connection repair',
                    'Remote control setup'
                ],
                duration: '30 minutes - 1 hour',
                price: 'Starting from ₹200'
            },
            'electrical-fault': {
                title: 'Electrical Fault Finding and Short-Circuit Repair',
                icon: 'fas fa-search',
                description: 'Expert diagnosis and repair of electrical faults, short circuits, and power issues.',
                details: [
                    'Electrical fault diagnosis and testing',
                    'Short circuit identification and repair',
                    'Power outage troubleshooting',
                    'Wiring inspection and repair',
                    'Safety system restoration'
                ],
                duration: '1-3 hours',
                price: 'Starting from ₹400'
            },
            'ac-repair': {
                title: 'Air Conditioner Repair',
                icon: 'fas fa-snowflake',
                description: 'Expert repair services for all types of air conditioners including split, window, and central AC units.',
                details: [
                    'AC not cooling or heating issues',
                    'Compressor and motor repair',
                    'Gas charging and leak detection',
                    'PCB and control panel repair',
                    'Indoor and outdoor unit servicing'
                ],
                duration: '1-3 hours',

                price: 'Starting from ₹400'
            },
            'refrigerator-repair': {
                title: 'Refrigerator Repair & Gas Charging',
                icon: 'fas fa-thermometer-empty',
                description: 'Complete refrigerator repair services including cooling issues, gas charging, and component replacement.',
                details: [
                    'Cooling and freezing problems',
                    'Gas charging and leak repair',
                    'Compressor and thermostat repair',
                    'Door seal and gasket replacement',
                    'Ice maker and water dispenser repair'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹500'
            },
            'washing-machine-repair': {
                title: 'Washing Machine Repair',
                icon: 'fas fa-tshirt',
                description: 'Professional repair services for all types of washing machines including semi-automatic, fully automatic, front load, and top load.',
                details: [
                    'Water filling and draining issues',
                    'Motor and belt replacement',
                    'Control panel and timer repair',
                    'Drum and agitator problems',
                    'Spin and wash cycle issues'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹350'
            },
            'microwave-repair': {
                title: 'Microwave Oven Repair',
                icon: 'fas fa-microchip',
                description: 'Expert repair services for microwave ovens including heating issues, control panel problems, and component replacement.',
                details: [
                    'Heating and power issues',
                    'Turntable and door problems',
                    'Control panel and display repair',
                    'Magnetron and transformer repair',
                    'Safety interlock system repair'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹400'
            },
            'geyser-repair': {
                title: 'Geyser (Water Heater) Repair',
                icon: 'fas fa-fire',
                description: 'Complete repair services for electric and gas geysers including heating element replacement and thermostat repair.',
                details: [
                    'Heating element replacement',
                    'Thermostat and temperature control repair',
                    'Tank leakage and pressure issues',
                    'Safety valve and pipe connections',
                    'Gas geyser burner and ignition repair'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹300'
            },
            'fan-repair': {
                title: 'Fan Repair Service',
                icon: 'fas fa-fan',
                description: 'Professional repair services for all types of fans including ceiling fans, table fans, and exhaust fans.',
                details: [
                    'Motor and bearing replacement',
                    'Blade balancing and alignment',
                    'Speed regulator and capacitor repair',
                    'Wiring and connection issues',
                    'Remote control and timer repair'
                ],
                duration: '30 minutes - 1 hour',
                price: 'Starting from ₹200'
            },
            'tv-repair': {
                title: 'Television Repair & Troubleshooting',
                icon: 'fas fa-tv',
                description: 'Expert repair services for LED, LCD, Smart TVs, and traditional television sets.',
                details: [
                    'Display and screen issues',
                    'Audio and sound problems',
                    'Power and startup issues',
                    'Remote control and connectivity',
                    'Smart TV software troubleshooting'
                ],
                duration: '1-3 hours',
                price: 'Starting from ₹400'
            },
            'iron-repair': {
                title: 'Electric Iron/Press Repair',
                icon: 'fas fa-tshirt',
                description: 'Complete repair services for electric irons, steam irons, and pressing equipment.',
                details: [
                    'Heating element replacement',
                    'Temperature control and thermostat repair',
                    'Steam function and water tank issues',
                    'Cord and plug replacement',
                    'Safety and auto-shutoff repair'
                ],
                duration: '30 minutes - 1 hour',
                price: 'Starting from ₹150'
            },
            'music-system-repair': {
                title: 'Music System & Home Theatre Repair',
                icon: 'fas fa-music',
                description: 'Professional repair services for music systems, home theatres, and audio equipment.',
                details: [
                    'Speaker and audio output issues',
                    'CD/DVD player repair',
                    'Bluetooth and connectivity problems',
                    'Amplifier and power issues',
                    'Remote control and display repair'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹300'
            },
            'heater-repair': {
                title: 'Electric Heater Repair',
                icon: 'fas fa-thermometer-half',
                description: 'Repair services for room heaters, heating rods, and electric heating appliances.',
                details: [
                    'Heating element replacement',
                    'Thermostat and temperature control',
                    'Fan and blower motor repair',
                    'Safety switch and auto-cutoff',
                    'Cord and electrical connections'
                ],
                duration: '30 minutes - 1 hour',
                price: 'Starting from ₹200'
            },
            'induction-repair': {
                title: 'Induction Cooktop & Electric Stove Repair',
                icon: 'fas fa-fire-alt',
                description: 'Expert repair services for induction cooktops, electric stoves, and cooking appliances.',
                details: [
                    'Heating coil and element repair',
                    'Control panel and touch screen issues',
                    'Power and electrical problems',
                    'Temperature sensor repair',
                    'Safety features and auto-shutoff'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹300'
            },
            'cooler-repair': {
                title: 'Air Cooler Repair',
                icon: 'fas fa-wind',
                description: 'Complete repair services for desert coolers, personal coolers, and evaporative cooling systems.',
                details: [
                    'Motor and pump repair',
                    'Water circulation and leakage issues',
                    'Cooling pad replacement',
                    'Fan and blade problems',
                    'Control panel and timer repair'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹250'
            },
            'power-tools-repair': {
                title: 'Power Tools Repair',
                icon: 'fas fa-tools',
                description: 'Professional repair services for electric drills, cutters, grinders, and other power tools.',
                details: [
                    'Motor and gear repair',
                    'Chuck and bit holder issues',
                    'Power cord and switch problems',
                    'Speed control and variable settings',
                    'Safety features and guards'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹200'
            },
            'water-filter-repair': {
                title: 'Water Filter/Purifier Repair',
                icon: 'fas fa-tint',
                description: 'Expert repair and maintenance services for water filters, RO systems, and water purifiers.',
                details: [
                    'Filter cartridge replacement',
                    'RO membrane and UV lamp replacement',
                    'Pump and motor repair',
                    'Leakage and pipe connection issues',
                    'TDS and water quality testing'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹300'
            },
            'mixer-grinder-repair': {
                title: 'Mixer Grinder & Juicer Repair',
                icon: 'fas fa-blender',
                description: 'Complete repair and cleaning services for mixer grinders, juicers, and food processing equipment.',
                details: [
                    'Motor and coupling repair',
                    'Jar and blade replacement',
                    'Speed control and switch issues',
                    'Deep cleaning and maintenance',
                    'Safety lock and overload protection'
                ],
                duration: '1 hour',
                price: 'Starting from ₹200'
            },
            // Installation Services - Appliance Setup
            'tv-dish-installation': {
                title: 'TV/DTH Dish Installation and Tuning',
                icon: 'fas fa-satellite-dish',
                description: 'Professional installation and setup of TV, DTH dish, and satellite systems with proper tuning and signal optimization.',
                details: [
                    'LED/LCD/Smart TV wall mounting and setup',
                    'DTH dish installation and alignment',
                    'Cable management and wiring',
                    'Channel tuning and signal optimization',
                    'Remote programming and setup'
                ],
                duration: '2-3 hours',
                price: 'Starting from ₹500'
            },
            'chimney-installation': {
                title: 'Electric Chimney Installation',
                icon: 'fas fa-wind',
                description: 'Complete installation of electric kitchen chimneys with proper ducting and ventilation setup.',
                details: [
                    'Wall mounting and bracket installation',
                    'Ducting and pipe connection',
                    'Electrical wiring and switch setup',
                    'Filter installation and setup',
                    'Testing and performance check'
                ],
                duration: '2-4 hours',
                price: 'Starting from ₹800'
            },
            'fan-installation': {
                title: 'Ceiling and Wall Fan Installation',
                icon: 'fas fa-fan',
                description: 'Professional installation of ceiling fans, wall fans, and exhaust fans with proper wiring and mounting.',
                details: [
                    'Ceiling fan mounting and balancing',
                    'Wall fan bracket installation',
                    'Electrical wiring and regulator setup',
                    'Speed control and remote setup',
                    'Safety testing and operation check'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹300'
            },
            'washing-machine-installation': {
                title: 'Washing Machine Installation',
                icon: 'fas fa-tshirt',
                description: 'Complete installation and setup of washing machines including plumbing connections and testing.',
                details: [
                    'Machine positioning and leveling',
                    'Water inlet and outlet connections',
                    'Drain pipe installation',
                    'Electrical connection and testing',
                    'Demo run and operation guidance'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹400'
            },
            'cooler-installation': {
                title: 'Air Cooler Installation',
                icon: 'fas fa-wind',
                description: 'Professional installation of desert coolers and air coolers with water connections and setup.',
                details: [
                    'Cooler positioning and mounting',
                    'Water connection and float setup',
                    'Electrical wiring and switch installation',
                    'Cooling pad installation',
                    'Testing and performance check'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹350'
            },
            'water-filter-installation': {
                title: 'Water Filter/Purifier Installation',
                icon: 'fas fa-tint',
                description: 'Complete installation of water filters, RO systems, and water purifiers with plumbing connections.',
                details: [
                    'Wall mounting and bracket setup',
                    'Input and output pipe connections',
                    'Electrical connections for UV/RO',
                    'Filter cartridge installation',
                    'Water quality testing and setup'
                ],
                duration: '2-3 hours',
                price: 'Starting from ₹600'
            },
            'geyser-installation': {
                title: 'Geyser/Water Heater Installation',
                icon: 'fas fa-fire',
                description: 'Professional installation of electric and gas geysers with proper plumbing and safety connections.',
                details: [
                    'Wall mounting and bracket installation',
                    'Hot and cold water pipe connections',
                    'Electrical wiring and safety switch',
                    'Pressure relief valve installation',
                    'Testing and safety checks'
                ],
                duration: '2-3 hours',
                price: 'Starting from ₹700'
            },
            'light-installation': {
                title: 'Light Fixture Installation',
                icon: 'fas fa-lightbulb',
                description: 'Installation of various light fixtures including LED panels, chandeliers, and decorative lighting.',
                details: [
                    'Ceiling and wall light mounting',
                    'Electrical wiring and switch connections',
                    'LED panel and tube light installation',
                    'Chandelier and decorative light setup',
                    'Dimmer and smart switch installation'
                ],
                duration: '1-3 hours',
                price: 'Starting from ₹250'
            },
            // Installation Services - Tech & Security
            'cctv-installation': {
                title: 'CCTV and Security Camera Installation',
                icon: 'fas fa-video',
                description: 'Complete CCTV system installation with cameras, DVR setup, and remote monitoring configuration.',
                details: [
                    'Camera mounting and positioning',
                    'DVR/NVR setup and configuration',
                    'Cable routing and connections',
                    'Mobile app setup for remote viewing',
                    'System testing and training'
                ],
                duration: '3-6 hours',
                price: 'Starting from ₹1500'
            },
            'wifi-setup': {
                title: 'Wi-Fi Router and Modem Setup',
                icon: 'fas fa-wifi',
                description: 'Professional setup and configuration of Wi-Fi routers, modems, and network equipment.',
                details: [
                    'Router and modem installation',
                    'Internet connection setup',
                    'Wi-Fi network configuration',
                    'Password and security setup',
                    'Range extender installation'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹300'
            },
            'smart-home-installation': {
                title: 'Smart Home Device Installation',
                icon: 'fas fa-home',
                description: 'Installation and setup of smart home devices including smart switches, lights, and automation systems.',
                details: [
                    'Smart switch and socket installation',
                    'Smart bulb and light setup',
                    'Home automation hub configuration',
                    'Mobile app setup and pairing',
                    'Voice control integration'
                ],
                duration: '2-4 hours',
                price: 'Starting from ₹800'
            },
            // Maintenance Services - Routine Care
            'ac-servicing': {
                title: 'AC Wet and Dry Servicing',
                icon: 'fas fa-snowflake',
                description: 'Complete air conditioner servicing including cleaning, gas checking, and performance optimization.',
                details: [
                    'Indoor and outdoor unit cleaning',
                    'Filter cleaning and replacement',
                    'Gas pressure checking and top-up',
                    'Coil cleaning and maintenance',
                    'Performance testing and optimization'
                ],
                duration: '2-3 hours',
                price: 'Starting from ₹600'
            },
            'washing-machine-maintenance': {
                title: 'Washing Machine Maintenance',
                icon: 'fas fa-tshirt',
                description: 'Comprehensive washing machine cleaning and maintenance service for optimal performance.',
                details: [
                    'Drum and tub deep cleaning',
                    'Filter cleaning and maintenance',
                    'Pipe and drain cleaning',
                    'Motor and belt inspection',
                    'Performance testing and calibration'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹400'
            },
            'geyser-servicing': {
                title: 'Geyser Descaling and Service',
                icon: 'fas fa-fire',
                description: 'Professional geyser servicing including descaling, element checking, and safety inspection.',
                details: [
                    'Tank descaling and cleaning',
                    'Heating element inspection',
                    'Thermostat calibration',
                    'Safety valve checking',
                    'Pipe and connection inspection'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹500'
            },
            'water-filter-servicing': {
                title: 'Water Filter Cartridge Replacement',
                icon: 'fas fa-tint',
                description: 'Regular maintenance and cartridge replacement for water filters and RO systems.',
                details: [
                    'Filter cartridge replacement',
                    'RO membrane replacement',
                    'UV lamp replacement',
                    'System sanitization',
                    'Water quality testing'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹400'
            },
            'water-tank-cleaning': {
                title: 'Water Tank Cleaning Service',
                icon: 'fas fa-tint',
                description: 'Professional cleaning of overhead and underground water tanks for safe and clean water supply.',
                details: [
                    'Tank draining and scrubbing',
                    'Disinfection and sanitization',
                    'Pipe and valve cleaning',
                    'Quality testing after cleaning',
                    'Preventive maintenance tips'
                ],
                duration: '2-4 hours',
                price: 'Starting from ₹800'
            },
            // Plumbing Services - Fixtures & Taps
            'tap-repair': {
                title: 'Tap, Faucet, and Shower Repair',
                icon: 'fas fa-faucet',
                description: 'Professional repair and installation services for taps, faucets, and shower systems.',
                details: [
                    'Leaky tap and faucet repair',
                    'Shower head installation and repair',
                    'Mixer and diverter repair',
                    'Pipe connection and sealing',
                    'Water pressure optimization'
                ],
                duration: '1-2 hours',
                price: 'Starting from ₹250'
            },
            'basin-installation': {
                title: 'Washbasin and Sink Installation',
                icon: 'fas fa-sink',
                description: 'Complete installation and repair services for washbasins, kitchen sinks, and related plumbing.',
                details: [
                    'Basin and sink mounting',
                    'Drain pipe connections',
                    'Tap and faucet installation',
                    'Sealant and waterproofing',
                    'Plumbing leak testing'
                ],
                duration: '2-3 hours',
                price: 'Starting from ₹600'
            },
            'toilet-installation': {
                title: 'Toilet and Flush Tank Installation',
                icon: 'fas fa-toilet',
                description: 'Professional installation and repair of toilets, commodes, and flush tank systems.',
                details: [
                    'Toilet and commode installation',
                    'Flush tank setup and adjustment',
                    'Pipe connections and sealing',
                    'Seat and cover installation',
                    'Flush mechanism testing'
                ],
                duration: '2-4 hours',
                price: 'Starting from ₹800'
            }
        };

        // Add click event listeners to all service items
        document.addEventListener('DOMContentLoaded', function() {
            const serviceItems = document.querySelectorAll('.service-item');
            
            serviceItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const serviceKey = this.getAttribute('data-service');
                    const service = serviceDetails[serviceKey];
                    
                    if (service) {
                        showServiceDetails(service);
                    }
                });
                
                // Add hover effect
                item.addEventListener('mouseenter', function() {
                    this.style.color = '#EC4899';
                    this.style.paddingLeft = '10px';
                    this.style.backgroundColor = 'rgba(236, 72, 153, 0.1)';
                    this.style.borderRadius = '5px';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.style.color = '#495057';
                    this.style.paddingLeft = '0px';
                    this.style.backgroundColor = 'transparent';
                });
            });
        });

        function showServiceDetails(service) {
            const modalTitle = document.getElementById('serviceDetailsModalLabel');
            const modalContent = document.getElementById('serviceDetailsContent');
            
            modalTitle.innerHTML = `<i class="${service.icon}"></i> ${service.title}`;
            
            modalContent.innerHTML = `
                <div class="service-detail-card">
                    <div class="service-description" style="margin-bottom: 20px;">
                        <h6 style="color: #EC4899; font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">Service Description</h6>
                        <p style="color: #6c757d; line-height: 1.5; margin-bottom: 15px; font-size: 0.85rem;">${service.description}</p>
                    </div>
                    
                    <div class="service-highlights" style="margin-bottom: 20px;">
                        <h6 style="color: #EC4899; font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">What We Do</h6>
                        <div class="highlights-grid" style="display: grid; grid-template-columns: 1fr; gap: 5px;">
                            ${service.details.map(detail => `
                                <div style="display: flex; align-items: center; font-size: 0.8rem; color: #495057; padding: 3px 0;">
                                    <i class="fas fa-check-circle" style="color: #10B981; margin-right: 8px; font-size: 0.7rem;"></i>
                                    <span>${detail}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div class="service-info-compact" style="background: linear-gradient(135deg, #FDF2F8 0%, #FCE7F3 100%); padding: 15px; border-radius: 10px; text-align: center;">
                        <div style="display: flex; justify-content: center; align-items: center; gap: 20px;">
                            <div>
                                <i class="fas fa-clock" style="color: #EC4899; font-size: 1rem;"></i>
                                <div style="font-size: 0.75rem; color: #495057; margin-top: 3px;">Duration</div>
                                <div style="font-size: 0.8rem; color: #6c757d; font-weight: 600;">${service.duration}</div>
                            </div>
                            <div style="border-left: 1px solid rgba(236, 72, 153, 0.3); height: 40px;"></div>
                            <div>
                                <i class="fas fa-shield-alt" style="color: #10B981; font-size: 1rem;"></i>
                                <div style="font-size: 0.75rem; color: #495057; margin-top: 3px;">Quality</div>
                                <div style="font-size: 0.8rem; color: #6c757d; font-weight: 600;">Certified</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#serviceDetailsModal').modal('show');
        }
    </script>

    <!-- Bottom Navigation Bar -->
    <?php include("vendor/inc/bottom-nav-home.php"); ?>

</body>

</html>