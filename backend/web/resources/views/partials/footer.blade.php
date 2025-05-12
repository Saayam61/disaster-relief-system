<footer class="text-center py-4">
    <div class="container">
        @if(Auth::check() && Auth::user()->role !== 'Administrator')
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5>Disaster Relief System</h5>
                <p class="text-light">Providing hope and support during disasters.</p>
                <img src="/logo.png" alt="Logo" class="logo mb-2">
            </div>
            <div class="col-md-4 mb-3">
                <h5>Quick Links</h5>
                <ul class="list-unstyled quick-links">
                    <li><a href="#"><i class="fas fa-home me-2"></i> Home</a></li>
                    <li><a href="#"><i class="fas fa-info-circle me-2"></i> About Us</a></li>
                    <li><a href="#"><i class="fas fa-envelope me-2"></i> Contact</a></li>
                    <li><a href="#"><i class="fas fa-hand-holding-heart me-2"></i> Donate</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Connect With Us</h5>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="mailto:saayamgautam61@gmail.com">
                        <i class="fas fa-envelope email-icon"></i>
                    </a>
                </div>
                <p class="mt-3">
                    <span class="text-light"><i class="fa fa-phone"></i>  Phone: +977 9807000038</span>
                </p>
            </div>
        </div>
        <hr class="bg-light">
        @endif
        <small class="d-block mt-2">© {{ date('Y') }} Disaster Relief System — Together we rise</small>
    </div>
</footer>
