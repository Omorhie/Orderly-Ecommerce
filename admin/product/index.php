    <?php
    session_start();
if (!isset($_SESSION['officer_role']) || 
   !in_array($_SESSION['officer_role'], ['admin','petugas'])) {

    header("Location: ../auth/login.php");
    exit;
}

    require_once "../../config/database.php";
    $result = $conn->query("SELECT * FROM products ORDER BY id DESC");
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Manajemen Produk</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: Arial, sans-serif;
            }

            body {
                display: flex;
                min-height: 100vh;
                background-color: #27374D;
            }

            /* SIDEBAR */
            .sidebar {
                width: 250px;
                background-color: #D9D9D9;
                color: #fff;
                padding-top: 50px;
                padding-bottom: 20px;
                border-radius: 0px 16px 16px 0px;
            }

            .sidebar h2 {
                text-align: center;
                margin-bottom: 30px;
                font-size: 30px;
                font-weight: 550;
                color: #27374D;
            }

            .sidebar ul {
                list-style: none;
                
            }

            .sidebar ul li {
                margin-bottom: 15px;
            }

            .sidebar ul li a {
                text-decoration: none;
                color: #27374D;
                display: block;
                padding: 20px;
                transition: 0.3s;
                text-align: center;
                font-size: 20px;
                height: 60px;
                font-weight: 540;
            }

            .sidebar a {
                width: 100%;
            }

            .sidebar ul li a:hover {
                background-color: #27374D;
                color: #fff;
                height: 100px;
                text-align: center;
                    font-weight: bold;
                padding-top: 40px;
            }

            /* ACTIVE SIDEBAR */
.sidebar ul li a.active {
    background-color: #27374D;
    color: white;   
    font-weight: bold;
    height: 100px;
    text-align: center;
    padding-top: 40px
}



            /* CONTENT */
            .content {
                flex: 1;
                padding: 30px;
            }

            .header {
                padding-top: 10px;
                margin-bottom: 20px;
                font-size: 25px;
                color: #fff;
                justify-content: space-between;
                display: flex;
            }

            .header span {
                color: #526D82;
            }

            .header h2  {
                padding-top: 10px;
                font-weight: 540;
            }

            .btn {
                padding: 15px 12px;
                text-decoration: none;
                border-radius: 16px;
                font-size: 18px;
                display: inline-block;
                width: 220px;
                text-align: center;
                font-weight: bold;
                height: 55px;
                border: none;
            }

            .btn-add {
                background: #D9D9D9;
                color: #526D82;
                transition: all 300ms;
            }

            .btn-add:hover{
                background-color: #526D82;
                color: #D9D9D9;
                width: 250px;
            }
            .btn-edit {
                background: #2563eb;
                color: white;
            }

            .btn-delete {
                background: #dc2626;
                color: white;
            } 

            table {
                width: 100%;
                border-collapse: collapse;
                background: white;
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            }


            th, td {
                padding: 12px;
                border-bottom: 1px solid #bdbdbd;
                text-align: center;
                background-color: #D9D9D9;
                color: #526D82;
            }

            td {
                border-right: 1px solid #bdbdbd;
                font-weight: bold;
            }

            th {
                background: #D9D9D9;
                color: #526D82;
                height: 50px;
                font-weight: bold;
                text-align: center;
                font-size: 19px;

            }

            img {
                width: 60px;
                height: 60px;
                object-fit: cover;
                border-radius: 5px;
            }

            .actions a {
                margin-right: 5px;
            }

            /* MODAL */
    .modal {
        display: none;
        position: fixed;
        z-index: 999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
    }

    .modal-content {
        background: #27374D;
        width: 1000px;
        margin: 3% auto;
        padding: 0px 0px 30px 0px;
        border-radius: 16px;
        position: relative;
        animation: fadeIn 0.3s ease;
    }

    .close {
        position: absolute;
        right: 20px;
        top: 15px;
        font-size: 30px;
        cursor: pointer;
        color: #526D82;
    }

    /* FORM GROUP */
    .form-group {
        margin-bottom: 20px;
        position: relative;
    }

    /* INPUT STYLE */
    .form-group input,
    .form-group textarea {
        width: 80%; /* Dipendekkan */
        padding: 15px 20px;
        border-radius: 14px;
        border: 1px solid #ccc;
        font-size: 15px;
        background-color: #d9d9d9;
        color: #526D82; /* Warna teks */
        transition: all 0.3s ease;
    }

    /* FLOATING EFFECT */
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        border-color: #27374D;
    }

    /* Placeholder style */
    .form-group input::placeholder,
    .form-group textarea::placeholder {
        color: #526D82;
        opacity: 0.7;
    }

    /* HILANGKAN SCROLL INPUT */
    .form-group input {
        overflow: hidden;
    }

    /* KHUSUS TEXTAREA */
    .form-group textarea {
        resize: none;         
        /* overflow: hidden;     */
        min-height: 120px;     /* Tinggi tetap */
    }



    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .modal-grid {
        display: flex;
        gap: 30px;
    }

    .left-form {
        flex: 2;
    }

    .right-form {
        flex: 1;
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }

    .right-form input[type="file"] {
        background: #D9D9D9;
        padding: 10px 20px;
        border-radius: 16px;
        cursor: pointer;
    }

    /* NAVBAR MODAL */
    .modal-navbar {
        background: #D9D9D9;
        color: white;
        padding: 15px;
        border-radius: 16px 16px 0px 0px;
        width: 100%;
        margin-bottom: 50px;
        height: 60px;
    }

    .modal-navbar h3 {
        margin: 0;
        color: #526D82;
        padding-top: 2px;
        padding-left: 15px;
        font-size: 25px;
    }

    //* FORM GROUP */
    .form-group {
        position: relative;
        margin-bottom: 30px;
    }

    /* GLASS INPUT */
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 20px 15px;
        border-radius: 14px;
        border: 1px solid rgba(255,255,255,0.2);
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        font-size: 15px;
        color: #D9D9D9;
        outline: none;
        transition: all 0.3s ease;
    }

    /* FLOAT EFFECT */
    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #526D82;
        box-shadow: 0 0 15px #526D82;
    }

    /* FLOATING LABEL */
    .form-group label {
        position: absolute;
        left: 15px;
        top: 18px;
        color: #526D82;
        font-size: 14px;
        pointer-events: none;
        transition: 0.3s ease;
        background: transparent;
    }

    /* Label naik saat focus / ada isi */
    .form-group input:focus + label,
    .form-group input:valid + label,
    .form-group textarea:focus + label,
    .form-group textarea:valid + label {
        top: -10px;
        left: 12px;
        font-size: 12px;
        color: #D9D9D9;
        background: #526D82;
        padding: 0 6px;
        border-radius: 6px;
    }


    /* UPLOAD BOX */
    .upload-box {
        width: 100%;
        height: 200px;
        border-radius: 20px;
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.3s ease;
    }

    .upload-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    }


    .left-form{
        padding-left: 30px;
    }

    .right-form{
        padding-right: 30px;
    }

    /* MODAL FOOTER */
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0;
        padding: 20px 30px;
    }


    /* SAVE BUTTON STYLE */
    .btn-save {
        background: none;
        color: #77D42F;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    /* FLOATING ANIMATION */
    .btn-save:hover {
        color: #D9D9D9;
    }

    .btn-save {
        animation: floatIn 0.4s ease;
    }

    @keyframes floatIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* HILANGKAN SPINNER NUMBER INPUT */

    /* Chrome, Safari, Edge */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }

    /* DROPDOWN TRIPLE DOT */
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dot-btn {
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: #526D82;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background: white;
        min-width: 120px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-radius: 10px;
        overflow: hidden;
        z-index: 999;
    }

    .dropdown-content button,
    .dropdown-content a {
        width: 100%;
        padding: 12px;
        border: none;
        background: none;
        text-align: left;
        cursor: pointer;
        display: block;
        color: #526D82;
        text-decoration: none;
    }

    .dropdown-content button:hover,
    .dropdown-content a:hover {
        background: #f1f5f9;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dot-btn {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #526D82;
        transition: 0.2s;
    }

    .btn-delete {
        background: none;
        color: #B61E1E;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-delete:hover {
        color: #d9d9d9;
    }

    .modal-footer .btn {
        width: auto;
        padding: 12px 25px;
    }

    .upload-box {
    width: 100%;
    height: 200px;
    border-radius: 20px;
    border: 2px dashed rgba(255,255,255,0.3);
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(12px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: 0.3s ease;
    color: #D9D9D9;
    text-align: center;
}

.upload-box:hover {
    border-color: #526D82;
    transform: translateY(-5px);
}

.upload-box.dragover {
    border-color: #77D42F;
    background: rgba(119,212,47,0.1);
}


.desc-col{
    max-width: 250px;          /* batasi lebar */
    white-space: nowrap;       /* jangan turun baris */
    overflow: hidden;          
    text-overflow: ellipsis;   /* munculkan ... */
}

        </style>
    </head>
    <body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>Ordely</h2>
<ul>
    <li><a href="../dashboard.php">Home</a></li>

    <li>
        <a href="index.php" class="active">
            Product Management
        </a>
    </li>

    <li><a href="../user/index.php">Officer Management</a></li>
    <li><a href="../transaksi/index.php">Transactions</a></li>
    <li><a href="../laporan/index.php">Report</a></li>
    <li><a href="../backup/backup.php">Backup/Restore</a></li>
    <li><a href="../../auth/admin/logout.php">Logout</a></li>
</ul>

    </div>

    <!-- CONTENT -->
    <div class="content">

        <div class="header">
            <h2>Product <span>Management</span></h2>
            <br>
            <button class="btn btn-add" onclick="openModal()">+ Add New Product</button>

        </div>

        <table>
            <tr>
                <th>Name Product</th>
                <th class="desc-col">Description</th>
                <th>Price</th>
                <th>Stocks</th>
                <th>Picture</th>
                <th>Action</th>
            </tr>

            <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td class="desc-col" title="<?= htmlspecialchars($row['description']) ?>">
                 <?= htmlspecialchars($row['description']) ?>
                </td>
                <td>Rp <?= number_format($row['price']) ?></td>
                <td><?= $row['stock'] ?></td>
                <td>
                    <?php if($row['image']) { ?>
                        <img src="../../uploads/products/<?= $row['image'] ?>">
                    <?php } else { ?>
                        -
                    <?php } ?>
                </td>
    <td class="actions">
        <button class="dot-btn"
            onclick="openEditModal(
                '<?= $row['id'] ?>',
                '<?= htmlspecialchars($row['name']) ?>',
                '<?= $row['price'] ?>',
                '<?= $row['stock'] ?>',
                `<?= htmlspecialchars($row['description']) ?>`
            )">
            ⋮
        </button>
    </td>



            </tr>
            <?php } ?>
        </table>

    </div>

    <!-- MODAL -->
    <div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>

        <div class="modal-navbar">
            <h3>Create</h3>
        </div>

            <span class="close" onclick="closeModal()">&times;</span>

            <form action="store.php" method="POST" enctype="multipart/form-data">
        
        <div class="modal-grid">
            
            <!-- LEFT SIDE -->
    <div class="left-form">
    <div class="form-group">
        <input type="text" name="name" required>
        <label>Product Name</label>
    </div>

    <div class="form-group">
        <input type="number" name="price" required>
        <label>Price</label>
    </div>

    <div class="form-group">
        <input type="number" name="stock" required>
        <label>Stock</label>
    </div>

    <div class="form-group">
        <textarea name="description" rows="4" required></textarea>
        <label>Description</label>  
    </div>

    </div>


            <!-- RIGHT SIDE -->
    <div class="right-form">
<div class="upload-box" id="drop-area">
    <p>Drag & Drop Image Here</p>
    <span>or click to choose file</span>
    <input type="file" name="image" id="fileElem" hidden required>
</div>

    </div>

        </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-save">Create Product</button>
    </div>

    </form>

        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="modal">
    <div class="modal-content">

        <span class="close" onclick="closeEditModal()">&times;</span>

        <div class="modal-navbar">
            <h3>Edit Product</h3>
        </div>

    <form action="update.php" method="POST" enctype="multipart/form-data">

    <input type="hidden" name="id" id="edit_id">

    <div class="modal-grid">

    <div class="left-form">

    <div class="form-group">
        <input type="text" name="name" id="edit_name" required>
        <label>Product Name</label>
    </div>

    <div class="form-group">
        <input type="number" name="price" id="edit_price" required>
        <label>Price</label>
    </div>

    <div class="form-group">
        <input type="number" name="stock" id="edit_stock" required>
        <label>Stock</label>
    </div>

    <div class="form-group">
        <textarea name="description" id="edit_description" required></textarea>
        <label>Description</label>
    </div>

    </div>

<div class="right-form">
<div class="upload-box" id="drop-area-edit">
    <p>Drag & Drop Image Here</p>
    <span>or click to choose file</span>
    <input type="file" name="image" id="fileElemEdit" hidden>
</div>
</div>

    </div>

    <div class="modal-footer">

    <button type="submit" class="btn btn-save">
    Update Product
    </button>

    <button type="button" class="btn btn-delete"
    onclick="deleteProduct()">
    Delete Product
    </button>

    </div>


    </form>
    </div>
    </div>




    </body>

    <script>
    function openModal() {
        document.getElementById("productModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("productModal").style.display = "none";
    }

    window.onclick = function(event) {
        const modal = document.getElementById("productModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }

        
    }

    const textarea = document.querySelector("textarea");

    textarea.addEventListener("input", function () {
        this.style.height = "auto";
        this.style.height = this.scrollHeight + "px";
    });

    function openEditModal(id, name, price, stock, description) {
        document.getElementById("edit_id").value = id;
        document.getElementById("edit_name").value = name;
        document.getElementById("edit_price").value = price;
        document.getElementById("edit_stock").value = stock;
        document.getElementById("edit_description").value = description;

        document.getElementById("editModal").style.display = "block";
    }

    function closeEditModal() {
        document.getElementById("editModal").style.display = "none";
    }

    function deleteProduct() {
        const id = document.getElementById("edit_id").value;

        if (confirm("Yakin ingin menghapus produk ini?")) {
            window.location.href = "delete.php?id=" + id;
        }
    }


// ===== CREATE MODAL DRAG DROP =====
const dropArea = document.getElementById("drop-area");
const fileInput = document.getElementById("fileElem");

if(dropArea){
    dropArea.addEventListener("click", () => fileInput.click());

    dropArea.addEventListener("dragover", e => {
        e.preventDefault();
        dropArea.classList.add("dragover");
    });

    dropArea.addEventListener("dragleave", () => {
        dropArea.classList.remove("dragover");
    });

    dropArea.addEventListener("drop", e => {
        e.preventDefault();
        dropArea.classList.remove("dragover");
        fileInput.files = e.dataTransfer.files;
        dropArea.querySelector("p").innerText = e.dataTransfer.files[0].name;
    });

    fileInput.addEventListener("change", () => {
        dropArea.querySelector("p").innerText = fileInput.files[0].name;
    });
}

// ===== EDIT MODAL DRAG DROP =====
const dropAreaEdit = document.getElementById("drop-area-edit");
const fileInputEdit = document.getElementById("fileElemEdit");

if(dropAreaEdit){
    dropAreaEdit.addEventListener("click", () => fileInputEdit.click());

    dropAreaEdit.addEventListener("dragover", e => {
        e.preventDefault();
        dropAreaEdit.classList.add("dragover");
    });

    dropAreaEdit.addEventListener("dragleave", () => {
        dropAreaEdit.classList.remove("dragover");
    });

    dropAreaEdit.addEventListener("drop", e => {
        e.preventDefault();
        dropAreaEdit.classList.remove("dragover");
        fileInputEdit.files = e.dataTransfer.files;
        dropAreaEdit.querySelector("p").innerText = e.dataTransfer.files[0].name;
    });

    fileInputEdit.addEventListener("change", () => {
        dropAreaEdit.querySelector("p").innerText = fileInputEdit.files[0].name;
    });
}





    </script>

    </html>