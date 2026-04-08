<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/user/login.php");
    exit;
}

include '../config/database.php';

$user_id = $_SESSION['user_id'];

$userQuery = mysqli_query($conn, "SELECT address FROM users WHERE id = $user_id");
$userData = mysqli_fetch_assoc($userQuery);
$address = $userData['address'] ?? '';

$query = mysqli_query($conn, "
    SELECT cart.*, products.name, products.price, products.image
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.user_id = $user_id
");

$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Orderly</title>
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
            --border-radius: 16px;
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

        /* STEPPER */
        .stepper {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-gray);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .step.active, .step.completed {
            color: var(--primary);
            font-weight: 600;
        }

        .step.completed i {
            color: var(--white);
            background: var(--success);
            border-radius: 50%;
            padding: 3px;
            width: 22px; height: 22px;
        }

        .step.active i {
            color: var(--secondary);
        }

        .step-line {
            width: 50px;
            height: 3px;
            background: #e2e8f0;
            border-radius: 2px;
        }

        .step-line.active {
            background: var(--secondary);
        }

        /* MAIN LAYOUT */
        .main-container {
            max-width: 1100px;
            width: 95%;
            margin: 40px auto;
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 40px;
            flex: 1;
            align-items: start;
        }

        .page-title {
            grid-column: 1 / -1;
            font-size: 1.6rem;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: -10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* CART SECTION */
        .cart-section {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .cart-item {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 15px;
            display: flex;
            gap: 20px;
            align-items: center;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            border: 1px solid rgba(0,0,0,0.02);
            animation: slideUp 0.4s ease forwards;
            opacity: 0;
            transform: translateY(10px);
        }

        .cart-item:nth-child(1) { animation-delay: 0.1s; }
        .cart-item:nth-child(2) { animation-delay: 0.2s; }
        .cart-item:nth-child(3) { animation-delay: 0.3s; }

        @keyframes slideUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .cart-item:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.08);
            border-color: rgba(73, 136, 196, 0.1);
        }

        .item-img {
            width: 90px;
            height: 90px;
            border-radius: 12px;
            overflow: hidden;
            background: var(--bg-color);
            flex-shrink: 0;
        }

        .item-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
            line-height: 1.3;
        }

        .item-price {
            font-size: 1rem;
            color: var(--secondary);
            font-weight: 600;
        }

        .item-qty {
            background: var(--bg-color);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            color: var(--text-gray);
            font-weight: 500;
        }

        .item-qty span {
            color: var(--primary);
            font-weight: 700;
            font-size: 1rem;
            margin-left: 5px;
        }

        /* PAYMENT SECTION */
        .payment-section {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 100px;
            animation: slideUp 0.5s ease forwards;
        }

        .payment-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .payment-tabs {
            display: flex;
            gap: 10px;
            background: var(--bg-color);
            padding: 6px;
            border-radius: 12px;
            margin-bottom: 25px;
        }

        .tab-btn {
            flex: 1;
            padding: 12px 0;
            border: none;
            background: none;
            border-radius: 8px;
            font-weight: 600;
            color: var(--text-gray);
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .tab-btn.active {
            background: var(--white);
            color: var(--primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease-out;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-gray);
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.95rem;
            color: var(--primary);
            background: var(--white);
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 4px rgba(73, 136, 196, 0.1);
        }

        .info-box {
            background: rgba(73, 136, 196, 0.08);
            border: 1px solid rgba(73, 136, 196, 0.2);
            color: var(--primary);
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .info-box i {
            color: var(--secondary);
        }

        .info-box-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .info-title {
            font-size: 0.85rem;
            color: var(--text-gray);
            font-weight: 500;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary);
        }

        .file-upload-wrapper {
            position: relative;
        }

        .file-upload-input {
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 25px 15px;
            background: var(--bg-color);
            border: 2px dashed rgba(73, 136, 196, 0.3);
            border-radius: 12px;
            cursor: pointer;
            color: var(--text-gray);
            transition: var(--transition);
        }

        .file-upload-label i {
            color: var(--secondary);
            width: 28px;
            height: 28px;
            transition: var(--transition);
        }

        .file-upload-label:hover {
            border-color: var(--secondary);
            background: rgba(73, 136, 196, 0.04);
            color: var(--primary);
        }

        .file-upload-label:hover i {
            transform: translateY(-3px);
        }

        .file-name {
            display: block;
            margin-top: 8px;
            font-size: 0.85rem;
            color: var(--secondary);
            text-align: center;
            font-weight: 500;
        }

        .divider {
            height: 1px;
            background: #e2e8f0;
            margin: 25px 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: var(--text-gray);
            font-size: 0.95rem;
        }

        .summary-row.total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed #e2e8f0;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            margin-top: 25px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(15, 40, 84, 0.15);
        }

        .submit-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(73, 136, 196, 0.25);
        }

        /* POPUP MODAL */
        .popup-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 40, 84, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 1000;
        }

        .popup-overlay.active {
            opacity: 1; visibility: visible;
        }

        .popup-box {
            background: var(--white);
            padding: 40px;
            border-radius: 24px;
            text-align: center;
            max-width: 380px;
            width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.9);
            transition: var(--transition);
        }

        .popup-overlay.active .popup-box {
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

        .success-icon i {
            width: 35px;
            height: 35px;
        }

        .popup-box h2 {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 8px;
            font-weight: 700;
        }

        .popup-box p {
            color: var(--text-gray);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* Responsive */
        @media (max-width: 850px) {
            .main-container {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .payment-section {
                position: static;
            }
            .nav-links {
                display: none;
            }
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
            <a href="cart.php">Cart</a>
            <a href="checkout.php" class="active">Checkout</a>
            <a href="history.php">History</a>
        </div>

        <div class="nav-right">
            <a href="dashboard.php">
                <i data-lucide="arrow-left" style="width: 18px;"></i> Back
            </a>
        </div>
    </nav>

    <!-- MAIN APP CONT -->
    <div class="main-container">
        
        <div class="stepper">
            <div class="step completed"><i data-lucide="check"></i> <span>Cart</span></div>
            <div class="step-line active"></div>
            <div class="step active"><i data-lucide="credit-card"></i> <span>Checkout</span></div>
            <div class="step-line"></div>
            <div class="step"><i data-lucide="package"></i> <span>Done</span></div>
        </div>

        <h1 class="page-title"><i data-lucide="clipboard-list"></i> Order Summary</h1>

        <!-- LEFT COLUMN: CART ITEMS -->
        <div class="cart-section">
            <?php 
            $cartHasItems = false;
            while($row = mysqli_fetch_assoc($query)) { 
                $cartHasItems = true;
                $total += $row['price'] * $row['qty'];
            ?>
            <div class="cart-item">
                <div class="item-img">
                    <img src="../uploads/products/<?= htmlspecialchars($row['image']); ?>" alt="Product">
                </div>
                <div class="item-details">
                    <h3 class="item-name"><?= htmlspecialchars($row['name']); ?></h3>
                    <div class="item-price">Rp <?= number_format($row['price'], 0, ',', '.'); ?></div>
                </div>
                <div class="item-qty">
                    Qty: <span><?= $row['qty']; ?></span>
                </div>
            </div>
            <?php } ?>

            <?php if(!$cartHasItems): ?>
            <div class="info-box" style="justify-content: center; margin-top: 20px;">
                Your cart is empty.
            </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT COLUMN: PAYMENT -->
        <div class="payment-section">
            <h2 class="payment-title">
                <i data-lucide="credit-card" style="width: 22px;"></i> Payment Details
            </h2>

            <!-- TABS -->
            <div class="payment-tabs">
                <button type="button" class="tab-btn active" onclick="setMethod('Transfer', this)">Bank Transfer</button>
                <button type="button" class="tab-btn" onclick="setMethod('COD', this)">COD</button>
            </div>

            <!-- FORM -->
            <form id="checkoutForm" action="process_order.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="method" id="paymentMethod" value="Transfer">

                <div class="form-group">
                    <label for="address">Shipping Address</label>
                    <input type="text" name="address" id="address" class="form-control" required 
                           value="<?= htmlspecialchars($address); ?>" 
                           placeholder="Enter your full shipping address">
                </div>

                <!-- SHIPPING DETAILS -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="courier">Courier Partner</label>
                        <select name="courier" id="courier" class="form-control" style="cursor: pointer; appearance: menulist;" required>
                            <option value="JNE">JNE Express</option>
                            <option value="J&T">J&T Express</option>
                            <option value="SiCepat">SiCepat Halu</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="shipping_service">Shipping Service</label>
                        <select name="shipping_service" id="shipping_service" class="form-control" style="cursor: pointer; appearance: menulist;" required onchange="updateTotal()">
                            <option value="Regular" data-price="15000">Regular (Rp 15.000)</option>
                            <option value="Express" data-price="35000">Express (Rp 35.000)</option>
                        </select>
                    </div>
                </div>

                <!-- TRANSFER CONTENT -->
                <div id="transferContent" class="tab-content active">
                    <div class="info-box">
                        <i data-lucide="building-2"></i>
                        <div class="info-box-text">
                            <span class="info-title">Transfer Destination</span>
                            <span class="info-value">BCA 0895383114323</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Upload Payment Proof</label>
                        <div class="file-upload-wrapper">
                            <input type="file" name="proof_payment" id="proofFile" class="file-upload-input" accept="image/*" required onchange="updateFileName(this)">
                            <label for="proofFile" class="file-upload-label">
                                <i data-lucide="upload-cloud"></i>
                                <span style="font-weight: 500;">Click to browse or drag file</span>
                                <span style="font-size: 0.8rem; color: var(--text-gray);">JPG, PNG or PDF (max. 5MB)</span>
                            </label>
                            <span id="fileName" class="file-name"></span>
                        </div>
                    </div>
                </div>

                <!-- COD CONTENT -->
                <div id="codContent" class="tab-content">
                    <div class="info-box">
                        <i data-lucide="truck"></i>
                        <div class="info-box-text">
                            <span class="info-title">Payment Method</span>
                            <span class="info-value">Cash on Delivery</span>
                            <span style="font-size: 0.8rem; color: var(--text-gray); margin-top: 2px;">Pay directly to the courier when your order arrives.</span>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- SUMMARY -->
                <div class="summary-box">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>Rp <?= number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping Cost</span>
                        <span id="shippingDisplay" style="color: var(--success); font-weight: 600;">Rp 15.000</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total Payment</span>
                        <span id="totalDisplay">Rp <?= number_format($total + 15000, 0, ',', '.'); ?></span>
                    </div>
                </div>

                <button type="submit" class="submit-btn" <?= !$cartHasItems ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?>>
                    <i data-lucide="shield-check" style="width: 20px;"></i>
                    Place Order Now
                </button>
            </form>
        </div>

    </div>

    <!-- SUCCESS POPUP -->
    <div id="successPopup" class="popup-overlay">
        <div class="popup-box">
            <div class="success-icon">
                <i data-lucide="check"></i>
            </div>
            <h2>Order Placed!</h2>
            <p>Please wait, your order is being processed securely.</p>
        </div>
    </div>

    <script>
        // Form Validation flag to avoid submitting empty carts
        const cartHasItems = <?= $cartHasItems ? 'true' : 'false' ?>;
        const cartSubtotal = <?= $total ?>;

        // Initialize Lucide Icons
        lucide.createIcons();

        // Dynamic Shipping Cost Calculation
        function formatRupiah(number) {
            return "Rp " + new Intl.NumberFormat('id-ID').format(number).replace(/,/g, '.');
        }

        function updateTotal() {
            const serviceSelect = document.getElementById('shipping_service');
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            const shippingCost = parseInt(selectedOption.getAttribute('data-price')) || 0;
            
            document.getElementById('shippingDisplay').textContent = formatRupiah(shippingCost);
            
            const grandTotal = cartSubtotal + shippingCost;
            document.getElementById('totalDisplay').textContent = formatRupiah(grandTotal);
        }

        // Handle Tab Switching
        function setMethod(method, btn) {
            // Update Hidden Input Context
            document.getElementById('paymentMethod').value = method;
            
            // Update Active State on Buttons
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Switch Content Panels & Update Required fields
            if (method === 'Transfer') {
                document.getElementById('transferContent').classList.add('active');
                document.getElementById('codContent').classList.remove('active');
                document.getElementById('proofFile').setAttribute('required', 'required');
            } else {
                document.getElementById('codContent').classList.add('active');
                document.getElementById('transferContent').classList.remove('active');
                document.getElementById('proofFile').removeAttribute('required');
            }
        }

        // Handle File Input display
        function updateFileName(input) {
            const fileName = document.getElementById('fileName');
            if (input.files.length > 0) {
                fileName.textContent = "Selected: " + input.files[0].name;
            } else {
                fileName.textContent = "";
            }
        }

        // Handle Form Submission Animation
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            if(!cartHasItems) return; // double check

            // Show Beautiful Popup
            const popup = document.getElementById('successPopup');
            popup.classList.add('active');
            
            // Re-Initialize dynamic icons if any
            lucide.createIcons();

            // Delay submission for animation
            setTimeout(() => {
                this.submit();
            }, 1500);
        });
    </script>
</body>
</html>