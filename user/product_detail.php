<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/user/login.php");
    exit;
}

include '../config/database.php';

if(!isset($_GET['id'])){
    header("Location: dashboard.php");
    exit;
}

$id = intval($_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");

if(mysqli_num_rows($query) == 0){
    echo "Product not found";
    exit;
}

$product = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Product Detail</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial, sans-serif;
}

body{
background:linear-gradient(135deg,#eef2f7,#d9e2ec);
min-height:100vh;
}

/* NAVBAR */
.navbar{
display:flex;
justify-content:space-between;
align-items:center;
padding:25px 50px;
background:#0F2854;
color:white;
box-shadow:0 5px 20px rgba(0,0,0,0.2);
}

.logo{
font-size:24px;
}

.logo span{
color:#4988C4;
}

.navbar a{
color:white;
text-decoration:none;
font-size:15px;
transition:.3s;
}

.navbar a:hover{
color:#4988C4;
}

/* CONTAINER */
.detail-container{
max-width:1200px;
margin:100px auto;
display:flex;
gap:80px;
align-items:center;
padding:0 40px;
}

/* IMAGE SECTION */
.product-image{
flex:1;
position:relative;
}

.product-image img{
width:100%;
border-radius:25px;
box-shadow:0 30px 60px rgba(0,0,0,0.2);
transition:.5s;
}

.product-image:hover img{
transform:scale(1.05);
}

/* INFO CARD */
.product-info{
flex:1;
background:#0F2854;
padding:60px;
border-radius:25px;
box-shadow:0 25px 50px rgba(0,0,0,0.15);
transition:.4s;
}

.product-info:hover{
transform:translateY(-10px);
}

.product-info h1{
font-size:32px;
color:#fff;
margin-bottom:20px;
}

.product-info p{
color:#fff;
line-height:1.7;
margin-bottom:30px;
}

.price{
font-size:32px;
font-weight:bold;
color:#4988C4;
margin-bottom:25px;
}

/* BUTTON */
.btn-cart{
width:100%;
padding:18px;
border:none;
border-radius:15px;
background:#4988C4;
color:white;
font-size:16px;
cursor:pointer;
transition:.3s;
box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

.btn-cart:hover{
transform:translateY(-3px);
box-shadow:0 15px 35px rgba(0,0,0,0.3);
background: #fff;
color: #4988C4;
}

/* POPUP */
.popup{
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.5);
display:flex;
justify-content:center;
align-items:center;
opacity:0;
visibility:hidden;
transition:.3s;
}

.popup.active{
opacity:1;
visibility:visible;
}

.popup-content{
background:#0F2854;
padding:70px 100px;
border-radius:25px;
text-align:center;
transform:scale(.8);
transition:.3s;
box-shadow:0 25px 60px rgba(0,0,0,0.3);
}

.popup.active .popup-content{
transform:scale(1);
}

.popup-content h2{
color:#fff;
margin-bottom:10px;
font-size:32px;
}

.popup-content p{
color:#4988C4;
font-size:18px;
}


.price-stock{
display:flex;
flex-direction:column; /* susun vertikal */
gap:1;
margin-bottom:7px;
}

.price{
font-size:32px;
font-weight:bold;
color:#4988C4;
}

.stock{
font-size:14px;
padding:6px 14px;
border-radius:20px;
background:rgba(255,255,255,0.1);
color:#fff;
width:fit-content; /* supaya badge tidak full lebar */
}

.btn-cart{
width:100%;
height:55px;
border:2px solid #4988C4;
border-radius:30px;
background:transparent;
color:white;
font-size:16px;
font-weight:600;
letter-spacing:.5px;
cursor:pointer;
transition:all .3s ease;
}

.btn-cart:hover:not(:disabled){
transform:translateY(-3px);
box-shadow:0 8px 20px rgba(73,136,196,0.4);
filter:brightness(1.1);
background: #4988C4;
color: #fff;
}

.btn-cart:disabled{
background:#B61E1E;
cursor:not-allowed;
border: none;
transition: all 300ms;
box-shadow:0 8px 20px rgba(225, 64, 46, 0.4);
}

.btn-cart:disabled:hover{
    color: #0F2854;

}
</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">Order<span>ly</span></div>
    <a href="dashboard.php">Back to Shop</a>
</div>

<!-- DETAIL SECTION -->
<div class="detail-container">

    <div class="product-image">
        <img src="../uploads/products/<?= $product['image']; ?>">
    </div>

    <div class="product-info">
        <h1><?= htmlspecialchars($product['name']); ?></h1>

        <p><?= htmlspecialchars($product['description']); ?></p>

<div class="price-stock">
<div class="price-stock">
    <div class="price">
        Rp <?= number_format($product['price']) ?>
    </div>

    <div class="stock">
        <?php if($product['stock'] > 0){ ?>
            Stock: <?= $product['stock']; ?>
        <?php } else { ?>
            Out of Stock
        <?php } ?>
    </div>
</div>
</div>

        <form action="add_to_cart.php" method="POST" id="cart-form">
            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
<button type="submit" class="btn-cart"
<?= $product['stock'] <= 0 ? 'disabled' : ''; ?>>
    <?= $product['stock'] > 0 ? 'Add To Cart' : 'Out of Stock'; ?>
</button>
        </form>
    </div>

</div>

<!-- POPUP -->
<div id="popup" class="popup">
    <div class="popup-content">
        <h2>Thank You!</h2>
        <p>Product added to your cart</p>
    </div>
</div>

<script>
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