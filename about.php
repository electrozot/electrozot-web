<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About ElectroZot</title>
    <?php include("vendor/inc/head.php");?>
    </head>

<body style="background: linear-gradient(180deg, #f8f9fa 0%, #fff5f7 100%); min-height: 100vh;">

    <?php include("vendor/inc/nav.php");?>

    <!-- Hero Section -->
    <section class="about-hero" style="background: linear-gradient(135deg, #ffe5e8 0%, #fff0f2 50%, #ffe5e8 100%); padding: 100px 0 80px 0; position: relative; overflow: hidden;">
        <!-- Decorative Elements -->
        <div style="position: absolute; top: -50px; right: -50px; width: 300px; height: 300px; background: rgba(255, 71, 87, 0.1); border-radius: 50%; z-index: 1;"></div>
        <div style="position: absolute; bottom: -100px; left: -100px; width: 400px; height: 400px; background: rgba(255, 107, 157, 0.08); border-radius: 50%; z-index: 1;"></div>
        
        <div class="container" style="position: relative; z-index: 2;">
            <div class="text-center">
                <div class="mb-3" style="display: inline-block; background: rgba(255, 71, 87, 0.15); padding: 10px 24px; border-radius: 50px; border: 2px solid #ff4757;">
                    <span style="color: #ff4757; font-weight: 700; font-size: 0.95rem; letter-spacing: 2px;">ABOUT US</span>
                </div>
                <h1 class="about-title" style="font-size: 3rem; font-weight: 900; color: #2d3748; margin-bottom: 20px; text-shadow: 2px 2px 8px rgba(0,0,0,0.1);">
                    <i class="fas fa-bolt" style="color: #ff4757; margin-right: 15px;"></i>
                    ELECTROZOT
                </h1>
                <p class="about-subtitle" style="font-size: 1.2rem; color: #5e6570ff; max-width: 700px; margin: 0 auto; line-height: 1.8; font-weight: 400;">
                    Your trusted partner for excellence in electronic repair, electrical installation, and plumbing solutions
                </p>
            </div>
        </div>
    </section>

    <div class="container" style="padding-top: 60px; padding-bottom: 60px;">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-5">
            <ol class="breadcrumb about-breadcrumb" style="background: white; border-radius: 15px; padding: 15px 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-left: 4px solid #ff4757;">
                <li class="breadcrumb-item">
                    <a href="index.php" style="color: #ff4757; text-decoration: none; font-size: 1rem; font-weight: 600; transition: all 0.3s ease;">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item active" style="color: #6c757d; font-size: 1rem; font-weight: 500;">About</li>
            </ol>
        </nav>

        <!-- About Content Section -->
        <div class="row mb-5 align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-image-card border-0 shadow-lg" style="border-radius: 25px; overflow: hidden; background: white; border: 3px solid #ffd700;">
                    <img class="img-fluid about-image" src="vendor/img/about img.png" alt="Technician working on electrical panel" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content-card border-0 shadow-lg h-100" style="border-radius: 25px; padding: 40px; background: white;">
                    <div class="mb-4" style="display: inline-block; background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); padding: 8px 20px; border-radius: 50px;">
                        <span style="color: white; font-weight: 700; font-size: 0.85rem; letter-spacing: 1px;">WHO WE ARE</span>
                    </div>
                    <h2 class="about-heading mb-4" style="font-size: 2.2rem; font-weight: 800; color: #2d3748; line-height: 1.3;">
                        Our Commitment to <span style="color: #ff4757;">Excellence</span>
                    </h2>
                    <p style="font-size: 1.05rem; color: #4a5568; line-height: 1.9; margin-bottom: 25px;">
                        Welcome to <strong style="color: #fe90d2ff;">ElectroZot</strong>, your expert provider for Electronic Repair, Electrical Installation, and Plumbing Solutions. Our entire operation is centered on a single goal: delivering service defined by <strong>Trust, Quality, and Perfection</strong>.
                    </p>
                    <p style="font-size: 1.05rem; color: #4a5568; line-height: 1.9; margin-bottom: 25px;">
                        We firmly believe that exceptional service starts with superior materials. That is why we mandate the use of high-quality parts in every job—we never compromise on the components that ensure the longevity and safety of your home systems.
                    </p>
                    <p style="font-size: 1.05rem; color: #4a5568; line-height: 1.9; margin-bottom: 30px;">
                        Our team approaches every task with unwavering professionalism. We diagnose problems thoroughly, communicate clearly, and execute repairs and installations with meticulous attention to detail, ensuring perfection from start to finish.
                    </p>
                
                    <div class="warranty-card" data-toggle="modal" data-target="#warrantyModal" style="background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); border-radius: 15px; padding: 25px; box-shadow: 0 8px 25px rgba(255, 71, 87, 0.3); cursor: pointer; transition: all 0.3s ease;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="warranty-icon" style="width: 60px; height: 60px; border-radius: 50%; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; margin-right: 20px; backdrop-filter: blur(10px);">
                                    <i class="fas fa-shield-alt" style="font-size: 1.8rem; color: white;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1" style="font-size: 1.2rem; font-weight: 700; color: white;">Our Warranty</h5>
                                    <p class="mb-0" style="font-size: 1rem; color: rgba(255,255,255,0.95);">We stand by our work with a <strong style="color: #ffd700;">1-Month Service Warranty</strong> on all repairs.</p>
                                </div>
                            </div>
                            <div style="color: white; font-size: 1.5rem;">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                        <div class="mt-2 text-center">
                            <small style="color: rgba(255,255,255,0.9); font-size: 0.85rem;">
                                <i class="fas fa-info-circle"></i> Click to view Terms & Conditions
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expert Solutions Section -->
        <div class="row mb-5 mt-5">
            <div class="col-lg-12">
                <div class="text-center mb-5">
                    <div class="mb-3" style="display: inline-block; background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); padding: 8px 20px; border-radius: 50px;">
                        <span style="color: white; font-weight: 700; font-size: 0.85rem; letter-spacing: 1px;">WHAT WE OFFER</span>
                    </div>
                    <h3 class="section-heading" style="font-size: 2.5rem; font-weight: 800; color: #2d3748; margin-bottom: 15px;">
                        Our Expert <span style="color: #ff4757;">Solutions</span>
                    </h3>
                    <p style="font-size: 1.1rem; color: #6c757d; max-width: 600px; margin: 0 auto;">Comprehensive services tailored to your needs</p>
                </div>
                
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="solution-card border-0 shadow-lg h-100" style="border-radius: 20px; overflow: hidden; background: white; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                            <div class="solution-card-header" style="background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); padding: 30px; text-align: center;">
                                <div class="solution-icon-wrapper mb-3" style="width: 80px; height: 80px; margin: 0 auto; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                                    <i class="fas fa-microchip" style="font-size: 2.5rem; color: white;"></i>
                                </div>
                                <h4 class="mb-2" style="font-size: 1.3rem; font-weight: 700; color: white;">Electronic Repair</h4>
                                <small style="color: rgba(255,255,255,0.9); font-weight: 500; font-size: 0.95rem;">Diagnosis & Restoration</small>
                            </div>
                            <div class="solution-card-body" style="padding: 30px;">
                                <p style="font-size: 1rem; color: #4a5568; line-height: 1.8; margin: 0;">
                                    Professional service for diagnosing and fixing faults in your electronic devices, using reliable components and expert techniques.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="solution-card border-0 shadow-lg h-100" style="border-radius: 20px; overflow: hidden; background: white; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                            <div class="solution-card-header" style="background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); padding: 30px; text-align: center;">
                                <div class="solution-icon-wrapper mb-3" style="width: 80px; height: 80px; margin: 0 auto; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                                    <i class="fas fa-bolt" style="font-size: 2.5rem; color: white;"></i>
                                </div>
                                <h4 class="mb-2" style="font-size: 1.3rem; font-weight: 700; color: white;">Electrical Installation</h4>
                                <small style="color: rgba(255,255,255,0.9); font-weight: 500; font-size: 0.95rem;">Safety & Upgrade Specialists</small>
                            </div>
                            <div class="solution-card-body" style="padding: 30px;">
                                <p style="font-size: 1rem; color: #4a5568; line-height: 1.8; margin: 0;">
                                    Safe, code-compliant, and professional electrical installations and upgrades designed for peak performance and safety.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="solution-card border-0 shadow-lg h-100" style="border-radius: 20px; overflow: hidden; background: white; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                            <div class="solution-card-header" style="background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); padding: 30px; text-align: center;">
                                <div class="solution-icon-wrapper mb-3" style="width: 80px; height: 80px; margin: 0 auto; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                                    <i class="fas fa-tint" style="font-size: 2.5rem; color: white;"></i>
                                </div>
                                <h4 class="mb-2" style="font-size: 1.3rem; font-weight: 700; color: white;">Plumbing Solutions</h4>
                                <small style="color: rgba(255,255,255,0.9); font-weight: 500; font-size: 0.95rem;">Leak Repair & System Maintenance</small>
                            </div>
                            <div class="solution-card-body" style="padding: 30px;">
                                <p style="font-size: 1rem; color: #4a5568; line-height: 1.8; margin: 0;">
                                    Comprehensive plumbing services focused on durable fixes and precise installations for all your water system needs.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Values Section -->
        <div class="row mt-5 pt-5">
            <div class="col-lg-12">
                <div class="text-center mb-5">
                    <div class="mb-3" style="display: inline-block; background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); padding: 8px 20px; border-radius: 50px;">
                        <span style="color: white; font-weight: 700; font-size: 0.85rem; letter-spacing: 1px;">OUR VALUES</span>
                    </div>
                    <h3 class="section-heading" style="font-size: 2.5rem; font-weight: 800; color: #2d3748; margin-bottom: 15px;">
                        Our Core <span style="color: #ff4757;">Values</span>
                    </h3>
                    <p style="font-size: 1.1rem; color: #6c757d; max-width: 600px; margin: 0 auto;">The principles that guide everything we do</p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="value-card border-0 shadow-lg text-center" style="border-radius: 20px; padding: 40px 30px; background: white; transition: all 0.3s ease; border-top: 5px solid #ff4757;">
                            <div class="value-icon mb-4" style="width: 90px; height: 90px; margin: 0 auto; background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(255, 71, 87, 0.3);">
                                <i class="fas fa-handshake" style="font-size: 2.5rem; color: white;"></i>
                            </div>
                            <h5 style="font-size: 1.4rem; font-weight: 700; color: #2d3748; margin-bottom: 15px;">Trust</h5>
                            <p style="font-size: 1rem; color: #6c757d; margin: 0; line-height: 1.7;">Building lasting relationships through reliability and transparency</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="value-card border-0 shadow-lg text-center" style="border-radius: 20px; padding: 40px 30px; background: white; transition: all 0.3s ease; border-top: 5px solid #ff6b9d;">
                            <div class="value-icon mb-4" style="width: 90px; height: 90px; margin: 0 auto; background: linear-gradient(135deg, #ff6b9d 0%, #ffa8c5 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(255, 107, 157, 0.3);">
                                <i class="fas fa-award" style="font-size: 2.5rem; color: white;"></i>
                            </div>
                            <h5 style="font-size: 1.4rem; font-weight: 700; color: #2d3748; margin-bottom: 15px;">Quality</h5>
                            <p style="font-size: 1rem; color: #6c757d; margin: 0; line-height: 1.7;">Uncompromising standards in every service we provide</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="value-card border-0 shadow-lg text-center" style="border-radius: 20px; padding: 40px 30px; background: white; transition: all 0.3s ease; border-top: 5px solid #ff4757;">
                            <div class="value-icon mb-4" style="width: 90px; height: 90px; margin: 0 auto; background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(255, 71, 87, 0.3);">
                                <i class="fas fa-check-circle" style="font-size: 2.5rem; color: white;"></i>
                            </div>
                            <h5 style="font-size: 1.4rem; font-weight: 700; color: #2d3748; margin-bottom: 15px;">Perfection</h5>
                            <p style="font-size: 1rem; color: #6c757d; margin: 0; line-height: 1.7;">Meticulous attention to detail in every project</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Warranty Terms & Conditions Modal -->
    <div class="modal fade" id="warrantyModal" tabindex="-1" role="dialog" aria-labelledby="warrantyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); border: none; padding: 25px 30px;">
                    <div class="d-flex align-items-center">
                        <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; backdrop-filter: blur(10px);">
                            <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: white;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0" id="warrantyModalLabel" style="color: white; font-weight: 700; font-size: 1.5rem;">1-Month Service Warranty</h5>
                            <small style="color: rgba(255,255,255,0.9);">Terms & Conditions</small>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1; text-shadow: none;">
                        <span aria-hidden="true" style="font-size: 2rem;">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 35px 40px; background: #f8f9fa;">
                    <div class="warranty-intro mb-4" style="background: white; padding: 20px; border-radius: 15px; border-left: 4px solid #ff4757;">
                        <p style="font-size: 1rem; color: #2d3748; margin: 0; line-height: 1.7;">
                            <strong style="color: #ff4757;">Electrozot</strong> provides a comprehensive <strong>1-Month Service Warranty</strong> on all repair and installation services. This warranty ensures your peace of mind and demonstrates our commitment to quality workmanship.
                        </p>
                    </div>

                    <h6 style="font-size: 1.2rem; font-weight: 700; color: #2d3748; margin-bottom: 20px;">
                        <i class="fas fa-check-circle" style="color: #38ef7d; margin-right: 8px;"></i>
                        Warranty Coverage
                    </h6>
                    <div class="warranty-coverage mb-4" style="background: white; padding: 20px; border-radius: 15px;">
                        <ul style="margin: 0; padding-left: 20px; color: #4a5568; line-height: 2;">
                            <li>Free repair or replacement of defective parts installed by Electrozot</li>
                            <li>Complimentary service for workmanship-related issues</li>
                            <li>Priority scheduling for warranty claims</li>
                            <li>No additional labor charges for covered repairs</li>
                        </ul>
                    </div>

                    <h6 style="font-size: 1.2rem; font-weight: 700; color: #2d3748; margin-bottom: 20px;">
                        <i class="fas fa-exclamation-triangle" style="color: #ff4757; margin-right: 8px;"></i>
                        Warranty Void Conditions
                    </h6>
                    <div class="warranty-void mb-4">
                        <div class="void-item mb-3" style="background: white; padding: 18px; border-radius: 12px; border-left: 4px solid #ff4757;">
                            <div class="d-flex align-items-start">
                                <div style="min-width: 35px; height: 35px; background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                    <strong style="color: white; font-size: 0.9rem;">1</strong>
                                </div>
                                <div>
                                    <h6 style="font-size: 1rem; font-weight: 700; color: #2d3748; margin-bottom: 8px;">Seal Tampering or Breakage</h6>
                                    <p style="font-size: 0.95rem; color: #4a5568; margin: 0; line-height: 1.6;">Warranty becomes void if the official Electrozot security seal is tampered with, removed, or broken by unauthorized personnel.</p>
                                </div>
                            </div>
                        </div>

                        <div class="void-item mb-3" style="background: white; padding: 18px; border-radius: 12px; border-left: 4px solid #ff4757;">
                            <div class="d-flex align-items-start">
                                <div style="min-width: 35px; height: 35px; background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                    <strong style="color: white; font-size: 0.9rem;">2</strong>
                                </div>
                                <div>
                                    <h6 style="font-size: 1rem; font-weight: 700; color: #2d3748; margin-bottom: 8px;">Liquid or Water Damage</h6>
                                    <p style="font-size: 0.95rem; color: #4a5568; margin: 0; line-height: 1.6;">Any damage caused by exposure to liquids, water, moisture, or flooding is not covered under this warranty.</p>
                                </div>
                            </div>
                        </div>

                        <div class="void-item mb-3" style="background: white; padding: 18px; border-radius: 12px; border-left: 4px solid #ff4757;">
                            <div class="d-flex align-items-start">
                                <div style="min-width: 35px; height: 35px; background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                    <strong style="color: white; font-size: 0.9rem;">3</strong>
                                </div>
                                <div>
                                    <h6 style="font-size: 1rem; font-weight: 700; color: #2d3748; margin-bottom: 8px;">Physical Damage</h6>
                                    <p style="font-size: 0.95rem; color: #4a5568; margin: 0; line-height: 1.6;">Warranty does not cover damage from accidents, impacts, drops, mishandling, or any external physical force to internal or external components.</p>
                                </div>
                            </div>
                        </div>

                        <div class="void-item mb-3" style="background: white; padding: 18px; border-radius: 12px; border-left: 4px solid #ff4757;">
                            <div class="d-flex align-items-start">
                                <div style="min-width: 35px; height: 35px; background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                    <strong style="color: white; font-size: 0.9rem;">4</strong>
                                </div>
                                <div>
                                    <h6 style="font-size: 1rem; font-weight: 700; color: #2d3748; margin-bottom: 8px;">Unauthorized Repairs or Modifications</h6>
                                    <p style="font-size: 0.95rem; color: #4a5568; margin: 0; line-height: 1.6;">Any repairs, alterations, or modifications performed by non-Electrozot technicians will void the warranty immediately.</p>
                                </div>
                            </div>
                        </div>

                        <div class="void-item mb-3" style="background: white; padding: 18px; border-radius: 12px; border-left: 4px solid #ff4757;">
                            <div class="d-flex align-items-start">
                                <div style="min-width: 35px; height: 35px; background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                    <strong style="color: white; font-size: 0.9rem;">5</strong>
                                </div>
                                <div>
                                    <h6 style="font-size: 1rem; font-weight: 700; color: #2d3748; margin-bottom: 8px;">Improper Use or Negligence</h6>
                                    <p style="font-size: 0.95rem; color: #4a5568; margin: 0; line-height: 1.6;">Damage resulting from misuse, abuse, negligence, improper installation by others, or failure to follow operating instructions is not covered.</p>
                                </div>
                            </div>
                        </div>

                        <div class="void-item" style="background: white; padding: 18px; border-radius: 12px; border-left: 4px solid #ff4757;">
                            <div class="d-flex align-items-start">
                                <div style="min-width: 35px; height: 35px; background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                    <strong style="color: white; font-size: 0.9rem;">6</strong>
                                </div>
                                <div>
                                    <h6 style="font-size: 1rem; font-weight: 700; color: #2d3748; margin-bottom: 8px;">Acts of Nature & Power Surges</h6>
                                    <p style="font-size: 0.95rem; color: #4a5568; margin: 0; line-height: 1.6;">Damage caused by natural disasters, lightning, power surges, voltage fluctuations, or other environmental factors beyond our control is excluded.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="warranty-note" style="background: linear-gradient(135deg, #ffe5e8 0%, #fff0f2 100%); padding: 20px; border-radius: 15px; border: 2px solid #ff4757;">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-info-circle" style="color: #ff4757; font-size: 1.5rem; margin-right: 15px; margin-top: 3px;"></i>
                            <div>
                                <h6 style="font-size: 1rem; font-weight: 700; color: #2d3748; margin-bottom: 10px;">Important Note</h6>
                                <p style="font-size: 0.95rem; color: #4a5568; margin: 0; line-height: 1.7;">
                                    To claim warranty service, please retain your original service invoice and contact us within the warranty period. Our team will inspect the issue and determine coverage eligibility. For any questions or warranty claims, please call <strong style="color: #ff4757;">7559606925</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: white; border: none; padding: 20px 40px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="padding: 10px 25px; border-radius: 50px;">Close</button>
                    <a href="tel:7559606925" class="btn" style="background: linear-gradient(135deg, #ff4757 0%, #ff6b9d 100%); color: white; padding: 10px 25px; border-radius: 50px; border: none;">
                        <i class="fas fa-phone"></i> Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include("vendor/inc/footer.php");?>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <style>
        /* About Page Styles */
        .about-image-card {
            transition: all 0.4s ease;
        }

        .about-image-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(255, 71, 87, 0.2) !important;
        }

        .about-image-card:hover .about-image {
            transform: scale(1.05);
        }

        .about-content-card {
            transition: all 0.3s ease;
        }

        .about-content-card:hover {
            box-shadow: 0 15px 35px rgba(255, 71, 87, 0.15) !important;
        }

        .warranty-card {
            transition: all 0.3s ease;
        }

        .warranty-card:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 20px rgba(255, 71, 87, 0.2);
        }

        .warranty-icon {
            transition: all 0.3s ease;
        }

        .warranty-card:hover .warranty-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .solution-card {
            cursor: pointer;
        }

        .solution-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(255, 71, 87, 0.25) !important;
        }

        .solution-card-header {
            transition: all 0.3s ease;
        }

        .solution-icon-wrapper {
            transition: all 0.3s ease;
        }

        .solution-card:hover .solution-icon-wrapper {
            transform: scale(1.1) rotate(10deg);
        }

        .value-card {
            cursor: pointer;
        }

        .value-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(255, 71, 87, 0.2) !important;
        }

        .value-icon {
            transition: all 0.3s ease;
        }

        .value-card:hover .value-icon i {
            transform: scale(1.2) rotate(5deg);
        }

        .value-card:hover h5 {
            color: #ff4757;
        }

        .about-breadcrumb a:hover {
            color: #ff6b9d !important;
            transform: translateX(3px);
            transition: all 0.3s ease;
        }

        .about-heading {
            animation: fadeInUp 0.6s ease-out;
        }

        .section-heading {
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .about-title {
                font-size: 1.5rem !important;
            }
            
            .about-heading {
                font-size: 1.5rem !important;
            }
        }
    </style>

</body>
</html>