<?php
session_start();

include '../config/database.php';
$is_logged_in = isset($_SESSION['username_user']);

$brandFilter = isset($_GET['brand']) ? mysqli_real_escape_string($conn, $_GET['brand']) : '';

if($brandFilter){
    $query = mysqli_query($conn, "SELECT * FROM products WHERE brand='$brandFilter' ORDER BY created_at DESC");
} else {
    $query = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC");
}

$brands = mysqli_query($conn, "SELECT DISTINCT brand FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #0F2854;
            --secondary: #4988C4;
            --bg-color: #f8fafc;
            --white: #ffffff;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --danger: #ef4444;
            --success: #22c55e;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* NAVBAR */
        .navbar {
            background: var(--white);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo span {
            color: var(--secondary);
        }

        .nav-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .icon-btn {
            color: var(--text-gray);
            position: relative;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-color);
            width: 44px;
            height: 44px;
            border-radius: 50%;
        }

        .icon-btn:hover {
            color: var(--white);
            background: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(15, 40, 84, 0.2);
        }

        /* HEADER WELCOME */
        .header {
            font-size: 1.8rem;
            margin: 30px 5%;
            color: var(--text-dark);
            font-weight: 500;
        }

        .header span {
            color: var(--secondary);
            font-weight: 700;
        }

        /* CAROUSEL */
        .carousel {
            width: 90%;
            margin: 0 auto 50px auto;
            border-radius: 24px;
            overflow: hidden;
            position: relative;
            height: 450px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .slides {
            display: flex;
            width: 100%;
            height: 100%;
            transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .slides img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            flex-shrink: 0;
            object-position: center;
        }

        .carousel-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 80px 40px 40px;
            background: linear-gradient(to top, rgba(15, 40, 84, 0.9), transparent);
            color: var(--white);
        }

        .carousel-overlay h2 {
            font-size: 2.5rem;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .carousel-overlay p {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.8);
            font-weight: 300;
        }

        /* BRANDS */
        .brand-container {
            display: flex;
            gap: 15px;
            width: 90%;
            margin: 0 auto 40px auto;
            flex-wrap: wrap;
        }

        .brand-card {
            padding: 10px 24px;
            background: var(--white);
            color: var(--text-gray);
            border-radius: 99px;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
            border: 1px solid transparent;
        }

        .brand-card:hover, .brand-card.active {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(15, 40, 84, 0.15);
        }

        /* PRODUCTS */
        .product-container {
            width: 90%;
            margin: 0 auto 80px auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        .card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(0,0,0,0.02);
            position: relative;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.1);
            border-color: rgba(73, 136, 196, 0.1);
        }

        .card-img-wrapper {
            position: relative;
            overflow: hidden;
            background: var(--bg-color);
        }

        .card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .card:hover img {
            transform: scale(1.05);
        }

        .card-overlay {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .card-overlay h3 {
            color: var(--primary);
            margin-bottom: 8px;
            font-size: 1.15rem;
            font-weight: 600;
            line-height: 1.3;
        }

        .card-overlay p {
            font-size: 0.9rem;
            color: var(--text-gray);
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.5;
            min-height: 2.7rem;
        }

        .price-stock {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .price {
            font-weight: 700;
            color: var(--secondary);
            font-size: 1.1rem;
        }

        .badges {
            display: flex;
            gap: 8px;
        }

        .size, .stock {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 99px;
            font-weight: 500;
        }

        .size {
            background: rgba(73, 136, 196, 0.1);
            color: var(--secondary);
        }

        .stock {
            background: rgba(34, 197, 94, 0.1);
            color: var(--success);
        }
        .stock.out {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .card-actions {
            display: flex;
            gap: 10px;
        }

        .btn-detail, .btn-cart {
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 44px;
        }

        .btn-detail {
            flex: 1;
            background: var(--bg-color);
            color: var(--primary);
        }

        .btn-detail:hover {
            background: rgba(73, 136, 196, 0.1);
            color: var(--secondary);
        }

        .btn-cart {
            width: 100%;
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(15, 40, 84, 0.15);
        }

        .btn-cart:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(73, 136, 196, 0.25);
        }

        /* INFO SECTION */
        .info-section {
            width: 90%;
            margin: 0 auto 50px auto;
            padding: 50px;
            text-align: center;
            background: var(--white);
            box-shadow: var(--card-shadow);
            border-radius: 24px;
        }

        .info-section h2 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 2rem;
            font-weight: 700;
        }

        .info-section h2 span {
            color: var(--secondary);
        }

        .info-section p {
            color: var(--text-gray);
            line-height: 1.8;
            font-size: 1rem;
            max-width: 800px;
            margin: 0 auto;
        }

        /* FOOTER */
        .footer {
            background: var(--white);
            color: var(--text-gray);
            padding: 30px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #e2e8f0;
            margin-top: auto;
        }
        
        .footer-logo{
            font-weight: 700;
            color: var(--primary);
            font-size: 1.2rem;
        }

        .footer-logo span {
            color: var(--secondary);
        }

        /* FLYING IMG EFFECT */
        .flying-img {
            position: fixed;
            z-index: 9999;
            object-fit: cover;
            border-radius: 50%;
            pointer-events: none;
            transition: all 0.8s cubic-bezier(0.2, 1, 0.3, 1);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        /* POPUP */
        .popup {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 40, 84, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 10000;
        }

        .popup.active {
            opacity: 1;
            visibility: visible;
        }

        .popup-box {
            background: var(--white);
            padding: 40px;
            border-radius: 24px;
            text-align: center;
            transform: scale(0.9);
            transition: var(--transition);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 380px;
            width: 90%;
        }

        .popup.active .popup-box {
            transform: scale(1);
        }

        .success-icon {
            width: 70px;
            height: 70px;
            background: rgba(34, 197, 94, 0.1);
            color: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .popup-box h2 {
            color: var(--primary);
            margin-bottom: 8px;
            font-weight: 700;
        }

        .popup-box p {
            color: var(--text-gray);
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .carousel { height: 300px; }
            .carousel-overlay h2 { font-size: 1.8rem; }
            .header { font-size: 1.5rem; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="dashboard.php" class="logo">
            <i data-lucide="shopping-bag" style="width: 24px; color: var(--primary);"></i>
            Order<span>ly</span>
        </a>

        <div class="nav-right">
            <?php if($is_logged_in): ?>
                <a href="cart.php" class="icon-btn" id="cart-icon" title="Cart">
                    <i data-lucide="shopping-cart" style="width: 20px;"></i>
                </a>
                <a href="notifications.php" class="icon-btn" title="Notifications" style="position:relative;">
                    <i data-lucide="bell" style="width: 20px;"></i>
                    <span class="user-notif-badge" style="position:absolute; top:-2px; right:-2px; background:var(--danger); color:white; font-size:10px; padding:2px 5px; border-radius:50%; display:none; font-weight:bold;">0</span>
                </a>
                <a href="chat.php" class="icon-btn" title="Chat">
                    <i data-lucide="message-square" style="width: 20px;"></i>
                </a>
                <a href="profile.php" class="icon-btn" title="Profile">
                    <i data-lucide="user" style="width: 20px;"></i>
                </a>
                <a href="../auth/user/logout.php" class="icon-btn" title="Logout">
                    <i data-lucide="log-out" style="width: 20px;"></i>
                </a>
            <?php else: ?>
                <a href="../auth/user/register.php" style="color: var(--primary); font-weight: 500; text-decoration: none; font-size: 0.95rem; margin-right: 10px;">Register</a>
                <a href="../auth/user/login.php" class="icon-btn" style="width: auto; padding: 0 20px; border-radius: 99px; background: var(--primary); color: white; text-decoration: none; font-weight: 500;" title="Login">
                    Login
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="header">
        <?php if($is_logged_in): ?>
            Welcome back, <span><?= htmlspecialchars($_SESSION['username_user']); ?>!</span>
        <?php else: ?>
            Welcome to <span>Orderly!</span>
        <?php endif; ?>
    </div>

    <!-- CAROUSEL -->
    <div class="carousel">
        <div class="slides" id="slides">
            <img src="../assets/1.jpg" alt="Banner 1">
            <img src="../assets/2.jpg" alt="Banner 2">
            <img src="../assets/3.jpg" alt="Banner 3">
        </div>
        <div class="carousel-overlay">
            <h2>Discover Your Style</h2>
            <p>Premium Products Just For You</p>
        </div>
    </div>

    <!-- BRANDS -->
    <div class="brand-container">
        <div class="brand-card <?= $brandFilter == '' ? 'active' : '' ?>" onclick="filterBrand('')">All Collections</div>
        <?php while($b = mysqli_fetch_assoc($brands)) { ?>
            <div class="brand-card <?= $brandFilter == $b['brand'] ? 'active' : '' ?>" onclick="filterBrand('<?= htmlspecialchars($b['brand']); ?>')">
                <?= htmlspecialchars($b['brand']); ?>
            </div>
        <?php } ?>
    </div>

    <!-- PRODUCTS -->
    <div class="product-container">
        <?php while($row = mysqli_fetch_assoc($query)) { ?>
        <div class="card">
            <div class="card-img-wrapper">
                <img src="../uploads/products/<?= htmlspecialchars($row['image']); ?>" class="product-image" alt="Product">
            </div>

            <div class="card-overlay">
                <h3><?= htmlspecialchars($row['name']); ?></h3>
                <p><?= htmlspecialchars($row['description']); ?></p>

                <div class="price-stock">
                    <span class="price">Rp <?= number_format($row['price'], 0, ',', '.'); ?></span>
                    <div class="badges">
                        <span class="size"> <?= htmlspecialchars($row['size']); ?></span>
                        <?php if($row['stock'] > 0){ ?>
                            <span class="stock"><?= $row['stock']; ?> left</span>
                        <?php } else { ?>
                            <span class="stock out">Out of Stock</span>
                        <?php } ?>
                    </div>
                </div>

                <div class="card-actions">
                    <button type="button" class="btn-detail" onclick="window.location.href='product_detail.php?id=<?= $row['id']; ?>'">
                        <i data-lucide="eye" style="width: 18px;"></i>
                    </button>

                    <?php if($is_logged_in): ?>
                        <form action="add_to_cart.php" method="POST" class="cart-form" style="flex:2;">
                            <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                            <button type="submit" class="btn-cart" <?= $row['stock'] <= 0 ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' ?>>
                                <i data-lucide="plus" style="width: 18px; margin-right:5px;"></i> Add to Cart
                            </button>
                        </form>
                    <?php else: ?>
                        <button type="button" class="btn-cart" style="flex:2;" onclick="window.location.href='../auth/user/login.php'">
                            <i data-lucide="plus" style="width: 18px; margin-right:5px;"></i> Add to Cart
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php if(mysqli_num_rows($query) == 0): ?>
            <div style="grid-column: 1/-1; text-align:center; padding: 50px; color: var(--text-gray);">
                No products found.
            </div>
        <?php endif; ?>
    </div>

    <!-- INFO SECTION -->
    <div class="info-section">
        <h2>About Order<span>ly</span></h2>
        <p>Orderly is a modern e-commerce platform dedicated to providing high-quality footwear for every lifestyle. From casual sneakers and stylish streetwear to formal shoes and performance sports footwear, we carefully curate our collection to meet the needs of every customer. We believe that the right pair of shoes can boost confidence and express personality. That’s why Orderly focuses not only on style, but also on comfort, durability, and affordability.</p>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-logo">Order<span>ly</span></div>
        <div>© <?= date('Y'); ?> All Rights Reserved</div>
    </div>

    <!-- POPUP THANK YOU -->
    <div id="thankyou-popup" class="popup">
        <div class="popup-box">
            <div class="success-icon">
                <i data-lucide="check" style="width: 35px; height: 35px;"></i>
            </div>
            <h2>Added to Cart!</h2>
            <p>Product has been successfully added to your shopping cart.</p>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Flying Cart Animation
        document.querySelectorAll(".cart-form").forEach(form => {
            form.addEventListener("submit", function(e){
                e.preventDefault();

                // If disabled, don't execute
                if(form.querySelector('button').disabled) return;

                let card = form.closest(".card");
                let img = card.querySelector(".product-image");
                let cartIcon = document.getElementById("cart-icon");
                let popup = document.getElementById("thankyou-popup");

                let imgRect = img.getBoundingClientRect();
                let cartRect = cartIcon.getBoundingClientRect();

                let flyingImg = img.cloneNode(true);
                flyingImg.classList.add("flying-img");

                flyingImg.style.top = imgRect.top + "px";
                flyingImg.style.left = imgRect.left + "px";
                flyingImg.style.width = imgRect.width + "px";
                flyingImg.style.height = imgRect.height + "px";

                document.body.appendChild(flyingImg);

                setTimeout(() => {
                    flyingImg.style.top = cartRect.top + "px";
                    flyingImg.style.left = cartRect.left + "px";
                    flyingImg.style.width = "20px";
                    flyingImg.style.height = "20px";
                    flyingImg.style.opacity = "0.2";
                }, 10);

                setTimeout(() => {
                    flyingImg.remove();
                    popup.classList.add("active");
                }, 800);

                setTimeout(() => {
                    form.submit();
                }, 1800);
            });
        });

        // Carousel Auto Slide
        const slides = document.getElementById("slides");
        const totalSlides = slides ? slides.children.length : 0;
        let index = 0;

        function nextSlide(){
            if(totalSlides === 0) return;
            index++;
            if(index >= totalSlides) index = 0;
            slides.style.transform = `translateX(-${index * 100}%)`;
        }
        if(totalSlides > 0) setInterval(nextSlide, 4000);

        // Filter
        function filterBrand(brand){
            window.location.href = brand === '' ? 'dashboard.php' : 'dashboard.php?brand=' + encodeURIComponent(brand);
        }
    </script>

    <!-- NOTIF LOGIC USER -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('api_notif.php')
            .then(r => r.json())
            .then(data => {
                let badges = document.querySelectorAll('.user-notif-badge');
                badges.forEach(b => {
                    if(data.count > 0) {
                        b.style.display = 'flex';
                        b.textContent = data.count;
                    }
                });
            }).catch(e=>console.log(e));
        });
    </script>
</body>
</html>