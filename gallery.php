<?php
  session_start();
  include('admin/vendor/inc/config.php');
?>
 <!DOCTYPE html>
 <html lang="en">


 
 <!--Head-->
 <?php include("vendor/inc/head.php");?>
 <!--End Head-->

<body style="background: linear-gradient(180deg, #f8f9fa 0%, #fff5f7 100%); min-height: 100vh;">

     <!-- Navigation -->
     <?php include("vendor/inc/nav.php");?>

    <!-- Hero Section -->
    <section class="gallery-hero" style="background: linear-gradient(135deg, #ffe5e8 0%, #fff0f2 50%, #ffe5e8 100%); padding: 116px 0 40px 0; margin-top: -56px;">
     <div class="container">
            <div class="text-center">
                <h1 class="gallery-title" style="font-size: 2rem; font-weight: 700; color: #2d3748; margin-bottom: 10px;">
                    <i class="fas fa-images" style="color: #ff4757;"></i> Our Gallery
         </h1>
                <p class="gallery-subtitle" style="font-size: 0.95rem; color: #6c757d; max-width: 600px; margin: 0 auto;">
                    Explore our work and services through our image gallery
                </p>
            </div>
        </div>
    </section>

    <!-- Page Content -->
    <div class="container" style="padding-top: 30px; padding-bottom: 40px;">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb gallery-breadcrumb" style="background: rgba(255,255,255,0.8); border-radius: 10px; padding: 10px 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 30px;">
             <li class="breadcrumb-item">
                    <a href="index.php" style="color: #ff4757; text-decoration: none; font-size: 0.9rem; font-weight: 500;">
                        <i class="fas fa-home"></i> Home
                    </a>
             </li>
                <li class="breadcrumb-item active" style="color: #6c757d; font-size: 0.9rem;">Gallery</li>
         </ol>
        </nav>

        <!-- Gallery Grid -->
        <div class="row gallery-grid">
        <?php
          // Load gallery items from DB
          $mysqli->query("CREATE TABLE IF NOT EXISTS tms_gallery (
            g_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            g_title VARCHAR(255) NOT NULL,
            g_image VARCHAR(255) NOT NULL,
            g_service_id INT NULL,
            g_description TEXT NULL,
            g_status VARCHAR(20) NOT NULL DEFAULT 'Active',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
          $q = $mysqli->prepare("SELECT g.*, s.s_name AS service_name FROM tms_gallery g LEFT JOIN tms_service s ON g.g_service_id=s.s_id WHERE g.g_status='Active' ORDER BY g.created_at DESC");
          $q->execute();
          $r = $q->get_result();
          while($item = $r->fetch_object()) {
        ?>
          <div class="col-lg-4 col-md-6 mb-4">
            <div class="gallery-item-card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden; background: white; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
              <div class="gallery-image-wrapper" style="position: relative; overflow: hidden; height: 250px; background: linear-gradient(135deg, #ffe5e8 0%, #fff0f2 100%);">
                <img class="gallery-image img-fluid" src="<?php echo htmlspecialchars($item->g_image); ?>" alt="<?php echo htmlspecialchars($item->g_title); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                <div class="gallery-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(255, 71, 87, 0.8) 0%, rgba(255, 107, 157, 0.8) 100%); opacity: 0; transition: opacity 0.4s ease; display: flex; align-items: center; justify-content: center;">
                  <div class="gallery-overlay-content text-white text-center" style="transform: translateY(20px); transition: transform 0.4s ease;">
                    <i class="fas fa-search-plus" style="font-size: 2.5rem; margin-bottom: 10px;"></i>
                    <h5 style="font-size: 1.1rem; font-weight: 600; margin: 0;"><?php echo htmlspecialchars($item->g_title); ?></h5>
                    <?php if(!empty($item->service_name)) { ?>
                      <p style="font-size: 0.85rem; margin: 5px 0 0 0; opacity: 0.9;">Service: <?php echo htmlspecialchars($item->service_name); ?></p>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="gallery-item-info" style="padding: 15px; background: white;">
                <h5 class="gallery-item-title" style="font-size: 0.95rem; font-weight: 600; color: #2d3748; margin-bottom: 5px;">
                  <?php echo htmlspecialchars($item->g_title); ?>
                </h5>
                <?php if(!empty($item->g_description)) { ?>
                  <span class="gallery-item-category" style="font-size: 0.75rem; color: #ff4757; font-weight: 500; display:block;">
                    <i class="fas fa-tag"></i> <?php echo htmlspecialchars($item->g_description); ?>
                  </span>
                <?php } ?>
              </div>
            </div>
          </div>
        <?php } ?>
        </div>

     </div>

     <?php include("vendor/inc/footer.php");?>

     <!-- Bootstrap core JavaScript -->
     <script src="vendor/jquery/jquery.min.js"></script>
     <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <style>
        /* Gallery Page Styles */
        .gallery-item-card {
            cursor: pointer;
        }

        .gallery-item-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(255, 71, 87, 0.25) !important;
        }

        .gallery-item-card:hover .gallery-image {
            transform: scale(1.1);
        }

        .gallery-item-card:hover .gallery-overlay {
            opacity: 1;
        }

        .gallery-item-card:hover .gallery-overlay-content {
            transform: translateY(0);
        }

        .gallery-image-wrapper {
            position: relative;
        }

        .gallery-breadcrumb a:hover {
            color: #ff6b9d !important;
            transform: translateX(3px);
            transition: all 0.3s ease;
        }

        .gallery-item-title {
            transition: color 0.3s ease;
        }

        .gallery-item-card:hover .gallery-item-title {
            color:rgb(253, 69, 85);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .gallery-image-wrapper {
                height: 200px !important;
            }
            
            .gallery-title {
                font-size: 1.5rem !important;
            }
        }

        /* Animation for gallery items */
        .gallery-item-card {
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
        }

        .gallery-item-card:nth-child(1) { animation-delay: 0.1s; }
        .gallery-item-card:nth-child(2) { animation-delay: 0.2s; }
        .gallery-item-card:nth-child(3) { animation-delay: 0.3s; }
        .gallery-item-card:nth-child(4) { animation-delay: 0.4s; }
        .gallery-item-card:nth-child(5) { animation-delay: 0.5s; }
        .gallery-item-card:nth-child(6) { animation-delay: 0.6s; }
        .gallery-item-card:nth-child(7) { animation-delay: 0.7s; }
        .gallery-item-card:nth-child(8) { animation-delay: 0.8s; }
        .gallery-item-card:nth-child(9) { animation-delay: 0.9s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

 </body>

 </html>
