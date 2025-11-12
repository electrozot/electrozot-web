<footer class="py-4 bg-dark">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <p class="mb-2 mb-md-0 text-white">Copyright &copy; <?php echo date('Y');?> Electrozot - Technician Booking System</p>
            <a href="tech/index.php" class="btn btn-sm btn-success ml-md-auto">Technician Login</a>
        </div>
    </div>
</footer>
<!-- Floating WhatsApp Chat Button -->
<a
  href="https://wa.me/7559606925?text=hii%20electrozot%20i%20want%20to%20book%20your%20service"
  class="whatsapp-chat-btn"
  target="_blank"
  rel="noopener"
  aria-label="Chat on WhatsApp"
  title="Chat on WhatsApp"
>
  <i class="fab fa-whatsapp" aria-hidden="true"></i>
</a>
<!-- Floating Book Service Button -->
<?php if (basename($_SERVER['SCRIPT_NAME']) !== 'index.php'): ?>
<a
  href="index.php#booking-form"
  class="book-service-fab"
  aria-label="Book Service"
  title="Book Service"
>
  <i class="fas fa-bolt" aria-hidden="true"></i>
  <span>Book</span>
</a>
<?php endif; ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var bookFab = document.querySelector('.book-service-fab');
    var bookingAnchor = document.getElementById('booking-form');
    // If on home page and booking form exists, smooth scroll instead of hard navigation
    if (bookFab && bookingAnchor && /index\.php$/.test(window.location.pathname)) {
      bookFab.addEventListener('click', function(e) {
        e.preventDefault();
        bookingAnchor.scrollIntoView({ behavior: 'smooth', block: 'center' });
        var nameInput = document.querySelector('#booking-form input[name="customer_name"]');
        setTimeout(function() { if (nameInput) { nameInput.focus(); } }, 400);
      });
    }
  });
</script>