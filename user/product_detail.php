<?php
session_start();

include '../config/database.php';
$is_logged_in = isset($_SESSION['user_id']);

if(!isset($_GET['id'])){
    header("Location: dashboard.php");
    exit;
}

$id = intval($_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");

if(mysqli_num_rows($query) == 0){
    echo "<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>Product not found. <a href='dashboard.php'>Go back</a></div>";
    exit;
}

$product = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']); ?> - Orderly</title>
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
            --card-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
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

        .nav-right a {
            padding: 10px 24px;
            background: var(--bg-color);
            color: var(--primary);
            border-radius: 99px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .nav-right a:hover {
            background: rgba(73, 136, 196, 0.1);
            color: var(--secondary);
            transform: translateY(-2px);
        }

        /* CONTAINER */
        .detail-container {
            max-width: 1200px;
            width: 95%;
            margin: 60px auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            background: var(--white);
            padding: 40px;
            border-radius: 32px;
            box-shadow: var(--card-shadow);
        }

        /* IMAGE SECTION */
        .product-image {
            background: var(--bg-color);
            border-radius: 24px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 500px;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .product-image:hover img {
            transform: scale(1.08);
        }

        /* INFO SECTION */
        .product-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-label {
            font-size: 0.9rem;
            color: var(--secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .product-info h1 {
            font-size: 2.5rem;
            color: var(--primary);
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .product-info p {
            color: var(--text-gray);
            line-height: 1.8;
            font-size: 1.05rem;
            margin-bottom: 30px;
        }

        .price-wrapper {
            margin-bottom: 30px;
        }

        .price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .badges {
            display: flex;
            gap: 15px;
        }

        .badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .badge-size {
            background: rgba(73, 136, 196, 0.1);
            color: var(--secondary);
            border: 1px solid rgba(73, 136, 196, 0.2);
        }

        .badge-stock {
            background: rgba(34, 197, 94, 0.1);
            color: var(--success);
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .badge-stock.out {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* BUTTON */
        .btn-cart {
            width: 100%;
            padding: 18px;
            border: none;
            border-radius: 16px;
            background: var(--primary);
            color: var(--white);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 25px rgba(15, 40, 84, 0.2);
        }

        .btn-cart:hover:not(:disabled) {
            background: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(73, 136, 196, 0.3);
        }

        .btn-cart:disabled {
            background: var(--bg-color);
            color: var(--text-gray);
            cursor: not-allowed;
            box-shadow: none;
        }

        /* POPUP MODAL (Glassmorphism) */
        .popup-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 40, 84, 0.5);
            backdrop-filter: blur(5px);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 1000;
        }

        .popup-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .popup-content {
            background: var(--white);
            padding: 40px;
            border-radius: 24px;
            text-align: center;
            transform: scale(0.9);
            transition: var(--transition);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
        }

        .popup-overlay.active .popup-content {
            transform: scale(1);
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(34, 197, 94, 0.1);
            color: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .popup-content h2 {
            color: var(--primary);
            font-size: 1.6rem;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .popup-content p {
            color: var(--text-gray);
            font-size: 1rem;
        }

        @media (max-width: 850px) {
            .detail-container {
                grid-template-columns: 1fr;
                gap: 40px;
                padding: 30px 20px;
            }
            .product-image {
                height: 350px;
            }
            .product-info h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <div class="navbar">
        <a href="dashboard.php" class="logo">
            <i data-lucide="shopping-bag" style="width: 24px; color: var(--primary);"></i>
            Order<span>ly</span>
        </a>
        <div class="nav-right">
            <a href="dashboard.php">
                <i data-lucide="arrow-left" style="width: 18px;"></i> Back to Shop
            </a>
        </div>
    </div>

    <!-- DETAIL SECTION -->
    <div class="detail-container">

        <div class="product-image">
            <img src="../uploads/products/<?= htmlspecialchars($product['image']); ?>" alt="Product">
        </div>

        <div class="product-info">
            <div class="brand-label"><?= htmlspecialchars($product['brand']); ?></div>
            <h1><?= htmlspecialchars($product['name']); ?></h1>

            <p><?= nl2br(htmlspecialchars($product['description'])); ?></p>

            <div class="price-wrapper">
                <div class="price">
                    Rp <?= number_format($product['price'], 0, ',', '.') ?>
                </div>

                <div class="badges">
                    <div class="badge badge-size">
                        <i data-lucide="ruler" style="width: 18px;"></i> Size: <?= htmlspecialchars($product['size']); ?>
                    </div>

                    <?php if($product['stock'] > 0){ ?>
                        <div class="badge badge-stock">
                            <i data-lucide="check-circle-2" style="width: 18px;"></i> <?= $product['stock']; ?> in Stock
                        </div>
                    <?php } else { ?>
                        <div class="badge badge-stock out">
                            <i data-lucide="x-circle" style="width: 18px;"></i> Out of Stock
                        </div>
                    <?php } ?>
                </div>
            </div>

            <?php if($is_logged_in): ?>
                <form action="add_to_cart.php" method="POST" id="cart-form">
                    <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                    <button type="submit" class="btn-cart" <?= $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                        <i data-lucide="shopping-cart"></i> 
                        <?= $product['stock'] > 0 ? 'Add To Cart' : 'Out of Stock'; ?>
                    </button>
                </form>
            <?php else: ?>
                <button type="button" class="btn-cart" onclick="window.location.href='../auth/user/login.php'">
                    <i data-lucide="shopping-cart"></i> 
                    Login to Add to Cart
                </button>
            <?php endif; ?>
        </div>

    </div>

    <!-- POPUP -->
    <div id="popup" class="popup-overlay">
        <div class="popup-content">
            <div class="success-icon">
                <i data-lucide="check" style="width: 40px; height: 40px;"></i>
            </div>
            <h2>Added Successfully</h2>
            <p>Product has been added to your shopping cart.</p>
        </div>
    </div>

    <script>
        lucide.createIcons();

        document.getElementById("cart-form").addEventListener("submit", function(e){
            e.preventDefault();

            let popup = document.getElementById("popup");
            popup.classList.add("active");

            setTimeout(()=>{
                this.submit();
            },1500);
        });
    </script>

</body>
</html>