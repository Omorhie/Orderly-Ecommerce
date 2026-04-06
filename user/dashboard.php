<?php
session_start();

if(!isset($_SESSION['username_user'])){
    header("Location: ../auth/user/login.php");
    exit;
}

include '../config/database.php';
$query = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard User</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

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

.nav-right{
display:flex;
gap:15px;
align-items:center;
}

.icon-btn{
color:white;
font-size:22px;
transition:.3s;
}

.icon-btn:hover{
color:#4988C4;
}

/* HEADER */
.header{
font-size:22px;
margin:30px 60px;
color:#0F2854;
}

.header span{
color:#4988C4;
}

/* ================= CAROUSEL ================= */

.carousel{
width:90%;
margin:20px auto 60px auto;
overflow:hidden;
border-radius:60px 60px 10px 10px;
position:relative;
height:500px;
box-shadow:0 20px 40px rgba(0,0,0,0.15);
}

.slides{
display:flex;
width:100%;
height:100%;
transition: transform 0.6s ease-in-out;
}

.slides img{
width:100%;
height:100%;
object-fit:cover;
flex-shrink:0;
object-position: center;
}

@keyframes slide{
0%, 25%{
    transform: translateX(0);
}

30%, 55%{
    transform: translateX(-100%);
}

60%, 85%{
    transform: translateX(-200%);
}

90%, 100%{
    transform: translateX(0);
}
}

.carousel-overlay{
position:absolute;
bottom:0;
left:0;
width:100%;
padding:40px;
background:linear-gradient(to top, rgba(0,0,0,0.7), transparent);
color:white;
}

.carousel-overlay h2{
font-size:36px;
margin-bottom:10px;
}

.carousel-overlay p{
font-size:18px;
color:#ddd;
}

/* ================= PRODUCT ================= */

.product-container{
max-width:1200px;
margin:0 auto 80px auto;
display:grid;
grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
gap:40px;
padding:0 40px;
}

.card{
border-radius:20px;
overflow:hidden;
background:#0F2854;
box-shadow:0 10px 25px rgba(0,0,0,0.1);
transition:.4s;
}

.card:hover{
transform:translateY(-12px);
box-shadow:0 25px 50px rgba(0,0,0,0.2);
}

.card img{
width:100%;
height:260px;
object-fit:cover;
transition:.5s;
}

.card:hover img{
transform:scale(1.08);
}

.card-overlay{
    padding:20px;
    }

    .card-overlay h3{
    color:#fff;
    margin-bottom:5px;
    font-size:18px;
    }

.card-overlay p{
font-size:14px;
color:#fff;
margin-top:5px;

/* MULTI LINE ELLIPSIS */
display:-webkit-box;
-webkit-line-clamp:2;
-webkit-box-orient:vertical;
overflow:hidden;
text-overflow:ellipsis;
line-height:1.5;
min-height:42px;
}

.price-tag{
font-weight:bold;
color:#4988C4;
margin:10px 0;
font-size:16px;
}

.size{
font-size:13px;
background:rgba(73,136,196,0.2);
padding:4px 10px;
border-radius:20px;
color:#fff;
}

.card-actions{
display:flex;
gap:10px;
margin-top:10px;
}

.btn-detail,
.btn-cart{
flex:2;
padding:12px;
border-radius:30px;
border:none;
cursor:pointer;
font-size:14px;
font-weight:600;
letter-spacing:.5px;
transition:all .3s ease;
position:relative;
overflow:hidden;
}

/* DETAIL BUTTON */
.btn-detail{
background:transparent;
color:#fff;
border:2px solid #4988C4;
}

.btn-detail:hover{
background:#4988C4;
box-shadow:0 8px 20px rgba(73,136,196,0.4);
}

/* CART BUTTON */
.btn-cart{
background:#77D42F;
color:white;
box-shadow:0 5px 15px rgba(0,0,0,0.2);
width: 100%;
}

.btn-cart:hover{
box-shadow:0 10px 25px rgba(0,0,0,0.3);
filter:brightness(1.1);
background: transparent;
border: 2px solid #77D42F;
}

/* FOOTER */
.footer{
background:#0F2854;
color:white;
padding:20px 40px;
display:flex;
justify-content:space-between;
align-items:center;
}

.footer span{
color:#4988C4;
}


.flying-img{
position:fixed;
z-index:9999;
width:150px;
height:150px;
object-fit:cover;
border-radius:20px;
pointer-events:none;
transition:all 0.8s cubic-bezier(.65,-0.2,.3,1.4);
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
transition:all .3s ease;
z-index:9999;
}

.popup.active{
opacity:1;
visibility:visible;
}

.popup-box{
background:#0F2854;
padding:60px 80px;
border-radius:20px;
text-align:center;
color:white;
transform:scale(.8);
transition:.3s;
}

.popup.active .popup-box{
transform:scale(1);
}

.popup-box h2{
margin-bottom:10px;
}

.popup-box p{
color:#4988C4;
}


.price-stock{
display:flex;
justify-content:space-between;
align-items:center;
margin:10px 0;
}

.price{
font-weight:bold;
color:#4988C4;
font-size:16px;
}

.stock{
font-size:13px;
background:rgba(255,255,255,0.15);
padding:4px 10px;
border-radius:20px;
color:#fff;
}

.info-section{
max-width: 90%;
margin:0 auto 30px auto;
padding:30px;
text-align:center;
background:#0F2854;
box-shadow:0 10px 30px rgba(0,0,0,0.1);
border-radius: 10px 10px 60px 60px;
}

.info-section h2{
color:#fff;
margin-bottom:10px;
}

.info-section p{
color:#fff;
line-height:1.6;
font-size:15px;
}   

.info-section span{
    color: #4988C4;
}
</style>
</head>

<script src="https://unpkg.com/lucide@latest"></script>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">Order<span>ly</span></div>

    <div class="nav-right">
<a href="cart.php" class="icon-btn" id="cart-icon">
    <i data-lucide="shopping-bag"></i>
</a>

        <a href="../auth/user/logout.php" class="icon-btn">
            <i data-lucide="log-out"></i>
        </a>

        <a href="profile.php" class="icon-btn">
            <i data-lucide="user-circle"></i>
        </a>
    </div>
</div>

<div class="header">
    Welcome, <span><?= $_SESSION['username_user']; ?>!</span>
</div>

<!-- CAROUSEL -->
<div class="carousel">
    <div class="slides" id="slides">
        <img src="../assets/1.jpg">
        <img src="../assets/2.jpg">
        <img src="../assets/3.jpg">
    </div>
    

    <div class="carousel-overlay">
        <h2>Discover Your Style</h2>
        <p>Premium Products Just For You</p>
    </div>
</div>

<!-- PRODUCTS -->
<div class="product-container">

<?php while($row = mysqli_fetch_assoc($query)) { ?>

<div class="card">
<img src="../uploads/products/<?= $row['image']; ?>" class="product-image" alt="">

    <div class="card-overlay">
        <h3><?= htmlspecialchars($row['name']); ?></h3>
        <p><?= htmlspecialchars($row['description']); ?></p>

<div class="price-stock">
    <span class="price">
        Rp <?= number_format($row['price']); ?>
    </span>

    <div style="display:flex; gap:8px; align-items:center;">
        <!-- SIZE -->
        <span class="size">
            Size: <?= htmlspecialchars($row['size']); ?>
        </span>

        <!-- STOCK -->
        <span class="stock">
            <?php if($row['stock'] > 0){ ?>
                Stock: <?= $row['stock']; ?>
            <?php } else { ?>
                <span style="color:#ff4d4d;">Out of Stock</span>
            <?php } ?>
        </span>
    </div>
</div>

        <div class="card-actions">
            <button type="button" class="btn-detail"
            onclick="window.location.href='product_detail.php?id=<?= $row['id']; ?>'">
                Detail
            </button>

            <form action="add_to_cart.php" method="POST" class="cart-form" style="flex:1;">
                <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                <button type="submit" class="btn-cart">
                    Cart
                </button>
            </form>
        </div>
    </div>
</div>

<?php } ?>

</div>

<div class="info-section">
    <h2>About Order<span>ly</span></h2>
    <p>
Orderly is a modern e-commerce platform dedicated to providing high-quality footwear for every lifestyle. From casual sneakers and stylish streetwear to formal shoes and performance sports footwear, we carefully curate our collection to meet the needs of every customer.
We believe that the right pair of shoes can boost confidence and express personality. That’s why Orderly focuses not only on style, but also on comfort, durability, and affordability. Every product is selected to ensure it meets our standards of quality and design.
Our mission is to deliver a seamless and enjoyable shopping experience. With an easy-to-use interface, secure transactions, and fast delivery, we make it simple for you to find and purchase your perfect pair of shoes anytime, anywhere.
At Orderly, customer satisfaction is our top priority. We continuously strive to improve our service, expand our product range, and bring you the latest trends in the world of footwear.
    </p>
</div>

<!-- FOOTER -->
<div class="footer">
    <div>Order<span>ly</span></div>
    <div>© <?= date('Y'); ?> All Rights Reserved</div>
</div>

<script>
lucide.createIcons();

document.querySelectorAll(".cart-form").forEach(form => {

    form.addEventListener("submit", function(e){

        e.preventDefault();

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
            flyingImg.style.width = "40px";
            flyingImg.style.height = "40px";
            flyingImg.style.opacity = "0.5";
        }, 10);

        setTimeout(() => {
            flyingImg.remove();
            popup.classList.add("active"); // tampilkan popup
        }, 800);

        setTimeout(() => {
            form.submit(); // baru submit setelah popup muncul
        }, 2000);

    });

});


const slides = document.getElementById("slides");
const totalSlides = slides.children.length;

let index = 0;

function showSlide(i){
    slides.style.transform = `translateX(-${i * 100}%)`;
}

function nextSlide(){
    index++;
    if(index >= totalSlides){
        index = 0;
    }
    showSlide(index);
}

// auto slide tiap 3 detik
setInterval(nextSlide, 3000);
</script>


<!-- POPUP THANK YOU -->
<div id="thankyou-popup" class="popup">
    <div class="popup-box">
        <h2>Thank You!</h2>
        <p>Product added to your cart</p>
    </div>
</div>

</body>
</html>