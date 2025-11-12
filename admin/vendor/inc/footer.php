 <footer class="sticky-footer">
     <div class="container my-auto">
         <div class="copyright text-center my-auto">
             <span>Copyright &copy; <?php echo date('Y');?> Electrozot - Technician Booking System
             </span>
         </div>
     </div>
 </footer>

 <!-- WhatsApp Floating Button -->
 <a href="https://wa.me/917559606925?text=Hi%20Electrozot,%20I%20need%20assistance" target="_blank" class="whatsapp-chat-btn" aria-label="Chat on WhatsApp">
     <i class="fab fa-whatsapp"></i>
 </a>

 <style>
 .whatsapp-chat-btn {
     position: fixed;
     width: 60px;
     height: 60px;
     bottom: 30px;
     right: 30px;
     background-color: #25d366;
     color: #FFF;
     border-radius: 50px;
     text-align: center;
     font-size: 30px;
     box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
     z-index: 1000;
     display: flex;
     align-items: center;
     justify-content: center;
     transition: all 0.3s ease;
     animation: whatsapp-pulse 2s infinite;
 }

 .whatsapp-chat-btn:hover {
     background-color: #128c7e;
     color: #FFF;
     transform: scale(1.1);
     box-shadow: 2px 2px 20px rgba(37, 211, 102, 0.6);
     text-decoration: none;
 }

 .whatsapp-chat-btn i {
     margin-top: 3px;
 }

 @keyframes whatsapp-pulse {
     0% {
         box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
     }
     50% {
         box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
     }
     100% {
         box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
     }
 }

 @media (max-width: 768px) {
     .whatsapp-chat-btn {
         width: 50px;
         height: 50px;
         bottom: 20px;
         right: 20px;
         font-size: 26px;
     }
 }
 </style>