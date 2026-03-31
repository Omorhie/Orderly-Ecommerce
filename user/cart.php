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
<html>
<head>
<title>Cart - Orderly</title>

<style>
body{
    min-height:100vh;
    display:flex;
    flex-direction:column;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

/* NAVBAR */
.navbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:30px;
    background:#0F2854;
    color:white;
}

.logo{
    font-size:24px;
}

.logo span{
    color:#4988C4;
}

.nav-center{
    display:flex;
    gap:40px;
}

.nav-center a{
    color:#4988C4;
    text-decoration:none;
    font-size:18px;
    transition:.3s;
}

.nav-center a:hover,
.nav-center a.active{
    color:#fff;
    font-weight:bold;
}

.nav-right a{
    color:white;
    text-decoration:none;
    font-size:18px;
    transition:.3s;
}

.nav-right a:hover{
    color:#4988C4;
}

/* CONTAINER */
.container{
    width:85%;
    margin:50px auto;
    flex:1;
    display:flex;
    flex-direction:column;
    gap:25px;
}

/* CART ITEM CARD */
.cart-item{
    display:flex;
    align-items:center;
    gap:20px;
    background:#ffffff;
    padding:20px;
    border-radius:20px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
    transition:0.3s;
}

.cart-item:hover{
    transform:translateY(-3px);
}

.product-img{
    width:120px;
    height:120px;
    border-radius:16px;
    overflow:hidden;
}

.product-img img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.product-card{
    flex:1;
    background:#4988C4;
    color:white;
    padding:25px;
    border-radius:16px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    height:120px;
}

.product-info h3{
    margin-bottom:10px;
}

.price{
    color:#0F2854;
    font-size:18px;
}

/* QTY CONTROL */
.qty-control{
    display:flex;
    align-items:center;
    gap:10px;
    background:#0F2854;
    border-radius:16px;
    padding:5px 10px;
}

.qty-control button{
    width:35px;
    height:35px;
    border:none;
    border-radius:16px;
    cursor:pointer;
    background:#0F2854;
    color:white;
    font-size:18px;
    transition:0.3s;
}

.qty-control button:hover{
    background:#fff;
    color:#0F2854;
}

.qty-control input{
    width:40px;
    text-align:center;
    border:none;
    background:none;
    color:#fff;
    font-size:16px;
}

/* DELETE */
.delete-btn a{
    color:#FF0000;
    transition:0.3s;
}

.delete-btn a:hover{
    color:#0F2854;
}

/* FOOTER */
.cart-footer{
    margin-top:auto;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.total{
    font-size:20px;
    color:#0F2854;
}

.save-btn{
    padding:15px 40px;
    border:none;
    border-radius:12px;
    background:#4988C4;
    color:white;
    font-size:16px;
    cursor:pointer;
    transition:0.3s;
}

.save-btn:hover{
    background:#0F2854;
}

/* EMPTY CART */
.empty-cart{
    flex:1;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
    gap:15px;
    color:#0F2854;
    margin-top:80px;
}

.empty-cart h2{
    font-size:30px;
}

.empty-cart p{
    color:#777;
}

.empty-cart button{
    padding:12px 30px;
    border:none;
    border-radius:12px;
    background:transparent;
    color:#4988C4;
    cursor:pointer;
    transition:0.3s;
    font-weight: bold;
}

.empty-cart button:hover{
    color: #0F2854;
}
</style>
</head>

<script src="https://unpkg.com/lucide@latest"></script>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">Order<span>ly</span></div>

    <div class="nav-center">
        <a href="#" class="active">Cart</a>
        <a href="checkout.php">Checkout</a>
        <a href="history.php">History</a>
    </div>

    <div class="nav-right">
        <a href="dashboard.php">Back</a>
    </div>
</div>

<!-- CART -->
<div class="container">

<?php if($count > 0): ?>

<?php while($row = mysqli_fetch_assoc($query)) { 
$total += $row['price'] * $row['qty'];
?>

<div class="cart-item">

    <div class="product-img">
        <img src="../uploads/products/<?= $row['image']; ?>">
    </div>

    <div class="product-card">
        <div class="product-info">
            <h3><?= htmlspecialchars($row['name']); ?></h3>
            <div class="price">
                Rp <?= number_format($row['price']); ?>
            </div>
        </div>

        <div class="qty-control">
            <a href="update_qty.php?action=decrease&id=<?= $row['id']; ?>">
                <button type="button">-</button>
            </a>

            <input type="number" value="<?= $row['qty']; ?>" readonly>

            <a href="update_qty.php?action=increase&id=<?= $row['id']; ?>">
                <button type="button">+</button>
            </a>
        </div>
    </div>

    <div class="delete-btn">
        <a href="delete_cart.php?id=<?= $row['id']; ?>">
            <i data-lucide="trash-2" width="32" height="32"></i>
        </a>
    </div>

</div>

<?php } ?>

<div class="cart-footer">
    <div class="total">
        Total: Rp <?= number_format($total); ?>
    </div>

    <button class="save-btn" onclick="window.location.href='checkout.php'">
        Confirm & Checkout
    </button>
</div>

<?php else: ?>

<div class="empty-cart">
    <i data-lucide="shopping-cart" width="80" height="80"></i>
    <h2>Cart is Empty</h2>
    <p>Cart Is Empty Like My Heart :(</p>
    <button onclick="window.location.href='dashboard.php'">
        Start Shopping
    </button>
</div>

<?php endif; ?>

</div>

<script>
lucide.createIcons();
</script>

</body>
</html>