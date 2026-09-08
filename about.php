<?php
require 'config.php';
require 'includes/auth.php';
$activePage = 'about';
include 'includes/header.php';
?>

<section class="about-section">
    <div class="about-image">
        <img src="images/Gemini_Generated_Image_th39zcth39zcth39.jpg" alt="DON Vincent Coffee Shop">
    </div>
    <div class="about-text">
        <h2>About Us</h2>
        <h3>Welcome to Your New Favorite Escape</h3>
        <p>At DON Vincent, we believe that coffee is more than just a morning pick-me-up—it's an
        experience, a moment of pause, and a craft to be savored.</p>
        <p>We are dedicated to bringing you exceptional quality in every cup, combining artisanal
        expertise with a passion for community.</p>

        <h3 style="margin-top:20px;">Our Core Values</h3>
        <ul class="core-values">
            <li><strong>Ethically Sourced:</strong> We source the finest ethical beans from around the
            globe, partnering directly with smallholder farmers to ensure fair wages and sustainable
            farming practices.</li>
            <li><strong>Roasted to Perfection:</strong> Our beans are meticulously roasted in small
            batches—often within 48 hours of shipping—to guarantee absolute freshness, peak flavor
            profiles, and true craft quality.</li>
            <li><strong>Eco-Friendly Commitment:</strong> We care for the planet just as much as our
            craft, utilizing fully biodegradable packaging designed to lock in freshness without
            leaving a heavy footprint.</li>
            <li><strong>Prepared with Care:</strong> Whether you're grabbing a daily ritual espresso or
            joining our Coffee Club for exclusive perks, every order is crafted to offer you a moment
            of pure comfort.</li>
        </ul>
    </div>
</section>

<section class="why-section">
    <h2>Why Choose DON Vincent?</h2>
    <div class="why-grid">
        <div class="card">100% Ethical Sourcing: We partner directly with smallholder farmers,
        ensuring fair wages and sustainable farming practices.</div>
        <div class="card">Roasted to Order: Your beans are roasted within 48 hours of shipping to
        guarantee absolute freshness and peak flavor profiles.</div>
        <div class="card">Eco-Friendly Packaging: Our bags are fully biodegradable and crafted to
        lock in freshness without harming the planet.</div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>