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



$total = 0;
    ?>

    <!DOCTYPE html>
    <html>
    <head>
    <title>Checkout - Orderly</title>

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
    padding:30px 30px;
    background:#0F2854;
    color:white;
    }

    .logo{
    font-size:24px;
    }

    .logo span{
    color:#4988C4;
    }

    /* NAV CENTER */
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

    .nav-center a:hover{
    color:#fff;
    }

    /* NAV RIGHT */
    .nav-right a{
    color:white;
    text-decoration:none;
    font-size:18px;
    transition:.3s;
    }

    .nav-right a:hover{
    color:#4988C4;
    }

    .nav-center a.active {
        color: #fff;
        font-weight: bold;
    }

    .container{
        width:85%;
        margin:50px auto;
        flex:1;
        display:flex;
        flex-direction:column;
    }

    /* CART ITEM */
    .cart-item{
    display:flex;
    align-items:center;
    margin-bottom:40px;
    gap:20px;
    }

    /* PRODUCT IMAGE */
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

    /* PRODUCT CARD */
    .product-card{
    flex:1;
    background:#4988C4;
    color:white;
    padding:25px;
    border-radius:16px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    height: 120px;
    }

    /* TITLE + PRICE */
    .product-info h3{
    margin-bottom:10px;
    }

    .price{
    color:#0F2854;
    font-size:18px;
    }

    /* QTY */
    .qty-control{
    display:flex;
    align-items:center;
    gap:10px;
    background: #0F2854;
    border-radius: 16px;
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
    transition: all 300ms;
    }

    .qty-control button:hover{
        background: #fff;
        color: #0F2854;
    }

    .qty-control input{
    width:60px; /* lebih kecil biar tidak overflow */
    text-align:center;
    padding:5px;
    border:none;
    outline:none;
    background:none;
    color:#fff;
    font-size:16px;

    /* hilangkan panah input number */
    appearance:none;
    -webkit-appearance:none;
    -moz-appearance:textfield;
    }

    /* hilangkan spinner di Chrome */
    .qty-control input::-webkit-outer-spin-button,
    .qty-control input::-webkit-inner-spin-button{
    -webkit-appearance:none;
    margin:0;
    }

    /* DELETE ICON */
    .delete-btn{
    cursor:pointer;
    color: #FF0000;
    transition: all 300ms;
    }

    .delete-btn:hover{
        color: #0F2854;
    }

    /* PAYMENT CARD */
    .payment-wrapper{
    margin-top:40px;
    }

    .payment-buttons{
    display:flex;
    }

    .payment-buttons button{
    padding:12px 30px;
    border:none;
    border-radius:16px 16px 0 0;
    cursor:pointer;
    background:#0F2854;
    color:white;
    transition:.3s;
    width: 150px;
    }

    .payment-buttons .btn-transfer{
        border-radius: 0 0 0 0;
        background: #0F2854;
    }

    .payment-buttons .btn-cod{
        border-radius: 0 16px 0 0;
        background: #4988C4;

    }

    .payment-buttons .btn-transfer:hover{
    background:#4988C4;
    }

    .payment-buttons .btn-cod:hover{
    background:#0F2854;
    }

    /* CARD */
    .payment-card{
        width:100%;
        padding:40px 30px;
        box-shadow:0 5px 20px rgba(0,0,0,0.1);
        display:flex;
        justify-content:space-between;
        gap:40px;
    }

    .transfer{
        background: #0F2854;
    }

    .cod{
        background: #4988C4;
    }

    /* LEFT & RIGHT */
    .left,
    .right{
    flex:1;
    }

    .left{
        color: #4988C4;
    }

    .left{
        padding-left:0;
    }

    .right{
        padding-right:8%;
        display:flex;
        justify-content:flex-end;
        align-items:flex-end;
    }

    .left p{
        font-style: italic;
    }

    .left input[type="text"]{
    width:100%;
    padding:8px 0;
    margin-top:10px;
    margin-bottom:25px;
    background:none;
    border:none;
    border-bottom:2px solid rgba(255,255,255,0.4);
    outline:none;
    color:white;
    font-size:15px;
    transition:0.3s;
    padding-left: 5px;
    }

    .payment-card{
        width:100%;
    }

    .payment-card > *{
        max-width:1200px;
    }

    .left input[type="text"]::placeholder{
    color:rgba(255,255,255,0.6);
    }

    .left input[type="text"]:focus{
    border-bottom:2px solid #fff;
    }

    .total{
    font-size:20px;
    margin-bottom:20px;
    color:#fff;
    }

    .confirm-btn{
    color:white;
    cursor:pointer;
    transition:.3s;
    background: none;
    border: none;
    font-size: 15px;
    }

    .confirm-btn:hover{
        color: #4988C4  ;
    }

    /* OUTER BOX (abu besar) */
    /* OUTER CONTAINER */
  /* FILE UPLOAD MODERN */
.file-upload{
    display:flex;
    align-items:center;
    gap:15px;
    margin-top:10px;
}

.file-upload input{
    display:none; /* hide original */
}

.upload-btn{
    padding:10px 25px;
    border-radius:999px;
    background:#4988C4;
    color:white;
    cursor:pointer;
    font-size:14px;
    transition:0.3s;
    white-space:nowrap;
}

.upload-btn:hover{
    background:#fff;
    color:#0F2854;
}

#fileName{
    font-size:14px;
    color:#fff;
    opacity:0.8;
}

    /* capsule button */
    .upload-wrapper{
    background:#0F2854;
    color:#4988C4;
    height:30px;
    border-radius:999px;
    font-weight:500;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:0 20px;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
    max-width:220px;
    transition:0.3s;
    }

    .file-upload:hover .upload-wrapper{
    background:#1f2b3a;
    color:white;
    }

    /* FORM ROW SEJAJAR */
    .form-row{
        display:flex;
        gap:30px;
        align-items:flex-end;
        margin-top:20px;
    }

    .payment-wrapper{
        margin-top:auto;   /* 🔥 ini yang bikin turun ke bawah */
    }

    .address-box{
        flex:1;
    }

    .upload-box{
        display:flex;
        flex-direction:column;
    }

    /* RIGHT SIDE FIX KE POJOK */
    .right{
        display:flex;
        justify-content:flex-end;
        align-items:flex-end;
    }

    .summary{
        text-align:right;
        margin-bottom:25px; /* naikkan sedikit */
    }

    /* Biar confirm button lebih proper */
    .confirm-btn{
        margin-top:10px;
    }

    .payment-wrapper{
        margin-top:auto;
    }


    /* POPUP OVERLAY */
.popup-overlay{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.6);
    display:flex;
    justify-content:center;
    align-items:center;
    opacity:0;
    visibility:hidden;
    transition:0.3s;
    z-index:999;
}

/* ACTIVE STATE */
.popup-overlay.active{
    opacity:1;
    visibility:visible;
}

/* POPUP BOX */
.popup-box{
    background:#0F2854;
    padding:50px 60px;
    border-radius:20px;
    text-align:center;
    animation:scaleIn 0.3s ease;
    width: 500px;
    height: 300px;
}

/* ICON */
.popup-icon{
    width:70px;
    height:70px;
    color:#22c55e;
    margin-bottom:20px;
}

/* TITLE */
.popup-box h2{
    margin-bottom:10px;
    color:#22c55e;
}

/* SUBTEXT */
.popup-box p{
    font-size:14px;
    color:#fff;
}

/* ANIMATION */
@keyframes scaleIn{
    from{
        transform:scale(0.8);
        opacity:0;
    }
    to{
        transform:scale(1);
        opacity:1;
    }
}
    </style>

    <script src="https://unpkg.com/lucide@latest"></script>

    </head>
    <body>

    <!-- NAVBAR -->
    <div class="navbar">
    <div class="logo">Order<span>ly</span></div>

    <div class="nav-center">
    <a href="cart.php">Cart</a>
    <a href="checkout.php" class="active">Checkout</a>
    <a href="history.php">History</a>
    </div>

    <div class="nav-right">
    <a href="dashboard.php">Back</a>
    </div>
    </div>

    <div class="container">

    <!-- ITEM -->
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
    <input 
        type="number" 
        value="<?= $row['qty']; ?>" 
        min="1"
        data-price="<?= $row['price']; ?>"
    >
</div>

</div>

</div>

<?php } ?>

    <!-- PAYMENT -->
    <div class="payment-wrapper">

        <div class="payment-buttons">
            <button onclick="showTransfer()" class="btn-transfer">Transfer</button>
            <button onclick="showCOD()" class="btn-cod">COD</button>
        </div>

        <form id="checkoutForm" action="process_order.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="method" id="paymentMethod">
    <div id="paymentContent"></div>
</form>

    </div>

    </div>

    <script>
let grandTotal = <?= $total ?>;
</script>

    <script>
    lucide.createIcons();

    function showTransfer(){
document.getElementById("paymentMethod").value = "Transfer";

document.getElementById("paymentContent").innerHTML = `
<div class="payment-card transfer">

    <div class="left">
        <p>Transfer Here : 0895383114323</p>

        <div class="form-row">
            <div class="address-box">
                <label>Address</label>
                <input type="text" name="address" required placeholder="Enter your address">
            </div>

            <div class="upload-box">
                <label>Upload Payment Proof</label>
                <div class="file-upload">
    <label for="proofFile" class="upload-btn">
        Choose File
    </label>
    <span id="fileName">No file chosen</span>
    <input 
        type="file" 
        name="proof_payment" 
        id="proofFile" 
        required 
        onchange="updateFileName(this)"
    >
</div>
            </div>
        </div>
    </div>

    <div class="right">
        <div class="summary">
            <div class="total">Total: Rp ${grandTotal.toLocaleString()}</div>
            <button type="submit" class="confirm-btn">Confirm Purchase</button>
        </div>
    </div>

</div>
`;
}

    function showCOD(){
document.getElementById("paymentMethod").value = "COD";

document.getElementById("paymentContent").innerHTML = `
<div class="payment-card cod">

    <div class="left">
        <label>Address</label>
        <input type="text" name="address" required placeholder="Enter your address">
    </div>

    <div class="right">
        <div class="summary">
            <div class="total">Total: Rp ${grandTotal.toLocaleString()}</div>
            <button type="submit" class="confirm-btn">Confirm Purchase</button>
        </div>
    </div>

</div>
`;
}

    lucide.createIcons();

    function increase(btn){
    let input = btn.parentElement.querySelector("input");
    input.value = parseInt(input.value) + 1;
    }

    function decrease(btn){
    let input = btn.parentElement.querySelector("input");
    if(input.value > 1){
    input.value = parseInt(input.value) - 1;
    }
    }

function updateFileName(input){
    const fileName = document.getElementById("fileName");
    if(input.files.length > 0){
        fileName.textContent = input.files[0].name;
    }else{
        fileName.textContent = "No file chosen";
    }
}

    window.onload = function(){
        showTransfer(); // otomatis tampil saat page dibuka
    };

    function showSuccessPopup(){
    const popup = document.getElementById("successPopup");
    popup.classList.add("active");
    lucide.createIcons();

    // auto close setelah 3 detik (opsional)
    setTimeout(()=>{
        popup.classList.remove("active");
    },3000);
}



    </script>


<!-- SUCCESS POPUP -->
<div id="successPopup" class="popup-overlay">
    <div class="popup-box">
        <i data-lucide="check-circle" class="popup-icon"></i>
        <h2>Successfully!</h2>
        <p>please wait, your order is being processed</p>
    </div>
</div>
    </body>
    </html>