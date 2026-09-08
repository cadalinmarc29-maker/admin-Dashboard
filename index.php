<?php
require 'config.php';

$activePage = 'home';
include 'includes/header.php';
?>

<section class="hero">
    <div class="hero-inner">
        <div class="hero-text">
            <span class="eyebrow">SINCE 2018</span>
            <h1>Welcome to<br>Your New Favorite Escape</h1>
            <p>At DON Vincent, we believe that coffee is more than just a morning pick-me-up—it's an
            experience, a moment of pause, and a craft to be savored. We source the finest ethical beans
            from around the globe, roast them in small batches to peak perfection, and deliver them
            straight to your doorstep.</p>
            <div class="hero-actions">
                <a href="menu.php" class="btn">VIEW MENU</a>
                <a href="join.php" class="btn btn-light">JOIN CLUB</a>
            </div>
        </div>

        <div class="hero-visual">
            <div class="coffee-card">   
                <div class="hero-image-wrap">
                    <img src="images/Gemini_Generated_Image_qgb53bqgb53bqgb5.jpg" alt="Signature coffee drink" class="hero-image">
                </div>
                <div class="card-details">
                    <strong>Signature Drink</strong>
                    <span>Don Vincent</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <div class="feature">
            <h4>PREMIUM QUALITY</h4>
            <p>We use freshly roasted 100% Arabica beans.</p>
        </div>
        <div class="feature">
            <h4>ARTISANAL ROAST</h4>
            <p>Crafted to perfection in every single cup.</p>
        </div>
        <div class="feature">
            <h4>SUSTAINABLY SOURCED</h4>
            <p>Ethically harvested, supporting local farms.</p>
        </div>
        <div class="feature">
            <h4>FRESHLY BREWED</h4>
            <p>Prepared to order every single time.</p>
        </div>
    </div>
</section>

<?php
// Pull a short preview of the menu straight from the database
$stmt = $pdo->query("
    SELECT c.category_name, i.item_name, i.price
    FROM menu_items i
    JOIN menu_categories c ON c.category_id = i.category_id
    ORDER BY c.category_id, i.item_id
    LIMIT 8
");
$previewItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="menu-section">
    <div class="menu-card">
        <h2>"The Don's Selection"</h2>
        <div class="menu-columns">
            <div>
                <h3>Menu Preview</h3>
                <ul>
                    <?php foreach ($previewItems as $row): ?>
                        <li>
                            <span><?= htmlspecialchars($row['item_name']) ?></span>
                            <span>&#8369;<?= number_format($row['price'], 2) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div style="display:flex; align-items:center; justify-content:center;">
                <a href="menu.php" class="btn">SEE FULL MENU</a>
            </div>
        </div>
    </div>
</section>

<section class="club-section">
    <h2>Join the Coffee Club</h2>
    <p class="sub">Exclusive access & savings at DON Vincent</p>
    <a href="join.php" class="btn">JOIN NOW</a>
</section>

<section class="about-home">
    <div class="about-home-inner">
        <div class="about-home-image">
            <img src="images/Gemini_Generated_Image_th39zcth39zcth39.jpg" alt="Coffee shop interior" class="about-home-img">
        </div>
        <div class="about-home-copy">
            <span class="eyebrow eyebrow-dark">ABOUT US</span>
            <h2>Crafted for the everyday ritual.</h2>
            <p>At DON Vincent, we bring together carefully sourced beans, warm hospitality, and slow-crafted coffee moments that make each visit worth savoring.</p>
            <p>From our classic espresso to handcrafted seasonal drinks, every cup is made to feel personal, comforting, and memorable.</p>
            <a href="about.php" class="btn btn-dark">LEARN MORE</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>