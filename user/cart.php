<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/user/login.php");
    exit;
}

include '../config/database.php';

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "
    SELECT cart.*, products.name, products.price, products.image
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.user_id = $user_id
");

$count = mysqli_num_rows($query);
$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart - Orderly</title>
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

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-gray);
            font-weight: 500;
            transition: var(--transition);
            position: relative;
        }

        .nav-links a:hover, .nav-links a.active {
            color: var(--primary);
            font-weight: 600;
        }

        .nav-links a.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--secondary);
            border-radius: 2px;
        }

        .nav-right a {
            padding: 10px 24px;
            background: var(--primary);
            color: var(--white);
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
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(73, 136, 196, 0.2);
        }

        /* CONTAINER */
        .container {
            max-width: 1000px;
            width: 95%;
            margin: 40px auto;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .page-title {
            font-size: 1.8rem;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cart-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* CART ITEM CARD */
        .cart-item {
            background: var(--white);
            border-radius: 20px;
            padding: 20px;
            display: flex;
            gap: 25px;
            align-items: center;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            border: 1px solid rgba(0,0,0,0.02);
            position: relative;
        }

        .cart-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.08);
            border-color: rgba(73, 136, 196, 0.1);
        }

        .product-img {
            width: 110px;
            height: 110px;
            border-radius: 14px;
            overflow: hidden;
            background: var(--bg-color);
            flex-shrink: 0;
        }

        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info {
            flex: 1;
        }

        .product-info h3 {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .price {
            color: var(--secondary);
            font-weight: 600;
            font-size: 1.05rem;
        }

        /* QTY CONTROL */
        .qty-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .qty-control {
            display: flex;
            align-items: center;
            background: var(--bg-color);
            border-radius: 99px;
            padding: 5px;
        }

        .qty-btn {
            width: 34px;
            height: 34px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            background: var(--white);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            text-decoration: none;
        }

        .qty-btn:hover {
            background: var(--primary);
            color: var(--white);
        }

        .qty-control input {
            width: 45px;
            text-align: center;
            border: none;
            background: none;
            font-weight: 600;
            color: var(--primary);
            font-size: 1rem;
            pointer-events: none;
        }

        /* DELETE */
        .delete-btn {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: rgba(239, 68, 68, 0.08);
            color: var(--danger);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: var(--transition);
        }

        .delete-btn:hover {
            background: var(--danger);
            color: var(--white);
            transform: scale(1.05);
        }

        /* FOOTER */
        .cart-footer {
            margin-top: 40px;
            background: var(--white);
            padding: 30px;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-box {
            display: flex;
            flex-direction: column;
        }
        
        .total-label {
            font-size: 0.9rem;
            color: var(--text-gray);
            font-weight: 500;
        }

        .total-amount {
            font-size: 1.5rem;
            color: var(--primary);
            font-weight: 700;
        }

        .save-btn {
            padding: 16px 35px;
            border: none;
            border-radius: 12px;
            background: var(--primary);
            color: var(--white);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(15, 40, 84, 0.15);
        }

        .save-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(73, 136, 196, 0.25);
        }

        /* EMPTY CART */
        .empty-cart {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: var(--white);
            border-radius: 24px;
            padding: 60px 20px;
            box-shadow: var(--card-shadow);
            margin-top: 20px;
        }

        .empty-icon {
            width: 100px;
            height: 100px;
            background: rgba(73, 136, 196, 0.1);
            color: var(--secondary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
        }

        .empty-cart h2 {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .empty-cart p {
            color: var(--text-gray);
            margin-bottom: 30px;
            font-size: 1rem;
        }

        .empty-cart button {
            padding: 14px 30px;
            border: none;
            border-radius: 12px;
            background: var(--primary);
            color: var(--white);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .empty-cart button:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(73, 136, 196, 0.2);
        }

        @media (max-width: 600px) {
            .cart-item {
                flex-direction: column;
                align-items: flex-start;
                position: relative;
            }
            .delete-btn {
                position: absolute;
                top: 20px;
                right: 20px;
            }
            .qty-wrapper {
                width: 100%;
                justify-content: space-between;
                margin-top: 10px;
            }
            .cart-footer {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            .save-btn { width: 100%; justify-content: center; }
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

        <div class="nav-links">
            <a href="cart.php" class="active">Cart</a>
            <a href="checkout.php">Checkout</a>
            <a href="history.php">History</a>
        </div>

        <div class="nav-right">
            <a href="dashboard.php">
                <i data-lucide="arrow-left" style="width: 18px;"></i> Back
            </a>
        </div>
    </nav>

    <!-- CART -->
    <div class="container">

        <?php if($count > 0): ?>
            
        <h1 class="page-title"><i data-lucide="shopping-cart"></i> Your Cart</h1>

        <div class="cart-list">
            <?php while($row = mysqli_fetch_assoc($query)) { 
                $total += $row['price'] * $row['qty'];
            ?>
            <div class="cart-item">
                <div class="product-img">
                    <img src="../uploads/products/<?= htmlspecialchars($row['image']); ?>" alt="Product">
                </div>

                <div class="product-info">
                    <h3><?= htmlspecialchars($row['name']); ?></h3>
                    <div class="price">Rp <?= number_format($row['price'], 0, ',', '.'); ?></div>
                </div>

                <div class="qty-wrapper">
                    <div class="qty-control">
                        <a href="update_qty.php?action=decrease&id=<?= $row['id']; ?>" class="qty-btn" title="Decrease">
                            <i data-lucide="minus" style="width: 16px;"></i>
                        </a>

                        <input type="number" value="<?= $row['qty']; ?>" readonly>

                        <a href="update_qty.php?action=increase&id=<?= $row['id']; ?>" class="qty-btn" title="Increase">
                            <i data-lucide="plus" style="width: 16px;"></i>
                        </a>
                    </div>
                    
                    <a href="delete_cart.php?id=<?= $row['id']; ?>" class="delete-btn" title="Remove Item">
                        <i data-lucide="trash-2" style="width: 20px;"></i>
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>

        <div class="cart-footer">
            <div class="total-box">
                <span class="total-label">Estimated Total</span>
                <span class="total-amount">Rp <?= number_format($total, 0, ',', '.'); ?></span>
            </div>

            <button class="save-btn" onclick="window.location.href='checkout.php'">
                Proceed to Checkout <i data-lucide="arrow-right" style="width: 18px;"></i>
            </button>
        </div>

        <?php else: ?>

        <div class="empty-cart">
            <div class="empty-icon">
                <i data-lucide="shopping-cart" style="width: 45px; height: 45px;"></i>
            </div>
            <h2>Your Cart is Empty</h2>
            <p>Looks like you haven't added anything yet. Let's find something you'll love!</p>
            <button onclick="window.location.href='dashboard.php'">
                <i data-lucide="shopping-bag" style="width: 18px;"></i> Start Shopping
            </button>
        </div>

        <?php endif; ?>

    </div>

    <script>
        lucide.createIcons();
    </script>

</body>
</html>