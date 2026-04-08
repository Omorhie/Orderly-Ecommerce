<?php
session_start();
if (!isset($_SESSION['officer_role']) || 
   !in_array($_SESSION['officer_role'], ['admin','petugas'])) {

    header("Location: ../../auth/login.php");
    exit;
}

require_once "../../config/database.php";
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
$role = $_SESSION['officer_role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Management - Orderly Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            /* Admin Slate Palette */
            --primary: #1e293b;
            --secondary: #334155;
            --accent: #3b82f6;
            --bg-color: #f8fafc;
            --sidebar-bg: #ffffff;
            --text-dark: #0f172a;
            --text-gray: #64748b;
            --card-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: var(--bg-color);
            color: var(--text-dark);
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            padding: 30px 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .sidebar h2 span {
            color: var(--accent);
        }

        .sidebar ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 500;
            transition: var(--transition);
        }

        .sidebar ul li a i {
            width: 20px;
            height: 20px;
        }

        .sidebar ul li a:hover, 
        .sidebar ul li a.active {
            background-color: var(--primary);
            color: #ffffff;
            transform: translateX(5px);
            box-shadow: 0 10px 15px -3px rgba(30, 41, 59, 0.2);
        }

        .sidebar ul li:last-child {
            margin-top: auto; 
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .sidebar ul li:last-child a:hover {
            background-color: #ef4444;
            box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3);
        }

        /* CONTENT */
        .content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
            position: relative;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: var(--sidebar-bg);
            padding: 20px 30px;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
        }

        .header h2 {
            font-size: 22px;
            font-weight: 600;
            color: var(--primary);
        }

        .btn-add {
            background: var(--accent);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2);
        }

        .btn-add:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        th, td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
        }

        th {
            background: rgba(248, 250, 252, 0.8);
            color: var(--text-gray);
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            color: var(--text-dark);
            font-size: 14.5px;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #f8fafc;
        }

        .desc-col {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: var(--text-gray);
        }

        img {
            width: 55px;
            height: 55px;
            object-fit: contain;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 4px;
        }

        .price-badge {
            font-weight: 600;
            color: var(--primary);
        }

        /* ACTION DOTS */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dot-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--text-gray);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .dot-btn:hover {
            background: #f1f5f9;
            color: var(--primary);
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            min-width: 150px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-radius: 12px;
            overflow: hidden;
            z-index: 50;
            border: 1px solid #e2e8f0;
        }

        .dropdown:hover .dropdown-content,
        .dropdown:focus-within .dropdown-content {
            display: block;
            animation: dropFade 0.2s ease;
        }

        @keyframes dropFade {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-item {
            width: 100%;
            padding: 12px 16px;
            border: none;
            background: none;
            text-align: left;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-dark);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: var(--transition);
        }

        .dropdown-item i { width: 16px; height: 16px; }
        
        .dropdown-item:hover {
            background: #f1f5f9;
            color: var(--accent);
        }
        
        .dropdown-item.delete:hover {
            color: #ef4444;
            background: #fef2f2;
        }

        /* MODAL FULL SCREEN REDESIGN */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0; top: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--sidebar-bg);
            width: 100%;
            max-width: 900px;
            max-height: 90vh;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            display: flex;
            flex-direction: column;
            animation: modalIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-header {
            padding: 24px 30px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }

        .modal-header h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .close-btn {
            background: none;
            border: none;
            color: var(--text-gray);
            cursor: pointer;
            transition: var(--transition);
        }

        .close-btn:hover {
            color: #ef4444;
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 30px;
            overflow-y: auto;
        }

        .modal-body::-webkit-scrollbar { width: 6px; }
        .modal-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .modal-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 30px;
        }

        /* Modern Form Inputs */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-gray);
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-size: 15px;
            color: var(--text-dark);
            background: var(--bg-color);
            transition: var(--transition);
            outline: none;
        }

        .form-control:focus {
            border-color: var(--accent);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        /* Clean Upload Box */
        .upload-area {
            height: 100%;
            min-height: 250px;
            border: 2px dashed #cbd5e1;
            border-radius: 20px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 15px;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .upload-area:hover, .upload-area.dragover {
            border-color: var(--accent);
            background: rgba(59, 130, 246, 0.05);
        }

        .upload-area i {
            color: var(--text-gray);
            transition: var(--transition);
        }

        .upload-area:hover i {
            color: var(--accent);
            transform: translateY(-5px);
        }

        .upload-area p {
            color: var(--text-gray);
            font-weight: 500;
            font-size: 15px;
        }

        .upload-area span {
            font-size: 13px;
            color: #94a3b8;
        }

        #filePreview, #editFilePreview {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            object-fit: contain;
            background: #ffffff;
            display: none;
            padding: 10px;
        }

        .modal-footer {
            padding: 24px 30px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            background: var(--bg-color);
        }

        .btn-cancel {
            background: white;
            color: var(--text-gray);
            border: 1px solid #e2e8f0;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-cancel:hover {
            background: #f1f5f9;
            color: var(--text-dark);
        }

        .btn-save {
            background: var(--accent);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2);
        }

        .btn-save:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);
        }

    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2><i data-lucide="shield-check"></i> Order<span>ly</span></h2>
        <ul>
            <li><a href="../dashboard.php"><i data-lucide="layout-dashboard"></i> Dashboard</a></li>
            <li><a href="index.php" class="active"><i data-lucide="package"></i> Products</a></li>
            <li><a href="../transaksi/index.php"><i data-lucide="shopping-cart"></i> Transactions</a></li>
            <li><a href="../laporan/index.php"><i data-lucide="file-bar-chart"></i> Reports</a></li>
            <?php if($role === 'admin') { ?>
                <li><a href="../user/index.php"><i data-lucide="users"></i> Officers</a></li>
                <li><a href="../backup/backup.php"><i data-lucide="database"></i> Backup</a></li>
            <?php } ?>
            <li><a href="../chat_admin.php"><i data-lucide="message-square"></i> Support</a></li>
            <li><a href="../../auth/admin/logout.php"><i data-lucide="log-out"></i> Logout</a></li>
        </ul>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <div class="header">
            <h2>Product Directory</h2>
            <button class="btn-add" onclick="openModal('createModal')">
                <i data-lucide="plus"></i> Add Product
            </button>
        </div>

        <table>
            <tr>
                <th>Product</th>
                <th>Brand</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Sizes</th>
                <th>Actions</th>
            </tr>

            <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <?php if($row['image']) { ?>
                            <img src="../../uploads/products/<?= htmlspecialchars($row['image']) ?>" alt="Product">
                        <?php } else { ?>
                            <div style="width: 55px; height: 55px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                <i data-lucide="image"></i>
                            </div>
                        <?php } ?>
                        <span style="font-weight: 600;"><?= htmlspecialchars($row['name']) ?></span>
                    </div>
                </td>
                <td><span style="background:#f1f5f9; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600; color:#64748b;"><?= htmlspecialchars($row['brand']) ?></span></td>
                <td><div class="desc-col" title="<?= htmlspecialchars($row['description']) ?>"><?= htmlspecialchars($row['description']) ?></div></td>
                <td class="price-badge">Rp <?= number_format($row['price']) ?></td>
                <td>
                    <?php if($row['stock'] > 10): ?>
                        <span style="color:#10b981; font-weight:600;"><i data-lucide="box" style="width:14px; margin-bottom:-2px;"></i> <?= $row['stock'] ?></span>
                    <?php else: ?>
                        <span style="color:#f59e0b; font-weight:600;"><i data-lucide="alert-circle" style="width:14px; margin-bottom:-2px;"></i> <?= $row['stock'] ?></span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['size']) ?></td>
                <td>
                    <div class="dropdown">
                        <button class="dot-btn"><i data-lucide="more-vertical"></i></button>
                        <div class="dropdown-content">
                            <button type="button" class="dropdown-item edit-btn"
                                data-id="<?= $row['id'] ?>"
                                data-name="<?= htmlspecialchars($row['name']) ?>"
                                data-brand="<?= htmlspecialchars($row['brand']) ?>"
                                data-price="<?= $row['price'] ?>"
                                data-stock="<?= $row['stock'] ?>"
                                data-size="<?= htmlspecialchars($row['size']) ?>"
                                data-description="<?= htmlspecialchars($row['description']) ?>"
                                data-img="../../uploads/products/<?= htmlspecialchars($row['image']) ?>">
                                <i data-lucide="edit-2"></i> Edit
                            </button>
                            <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?')" class="dropdown-item delete">
                                <i data-lucide="trash-2"></i> Delete
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>

    </div>

    <!-- CREATE MODAL -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i data-lucide="package-plus"></i> New Product</h3>
                <button class="close-btn" onclick="closeModal('createModal')"><i data-lucide="x"></i></button>
            </div>
            
            <form action="store.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="modal-grid">
                        
                        <div class="left-form">
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" name="name" class="form-control" required placeholder="e.g. Nike Air Max">
                            </div>

                            <div class="form-group">
                                <label>Brand Classification</label>
                                <input type="text" name="brand" class="form-control" required placeholder="e.g. Nike">
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div class="form-group">
                                    <label>Price (Rp)</label>
                                    <input type="number" name="price" class="form-control" required placeholder="0">
                                </div>
                                <div class="form-group">
                                    <label>Stock Quantity</label>
                                    <input type="number" name="stock" class="form-control" required placeholder="0">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Available Sizes</label>
                                <input type="text" name="size" class="form-control" required placeholder="e.g. 40, 41, 42">
                            </div>

                            <div class="form-group">
                                <label>Full Description</label>
                                <textarea name="description" class="form-control" required placeholder="Describe the product details and specifications..."></textarea>
                            </div>
                        </div>

                        <div class="right-form">
                            <label style="display:block; font-size:14px; font-weight:600; color:var(--text-gray); margin-bottom:8px;">Product Image</label>
                            <label class="upload-area" id="uploadArea">
                                <i data-lucide="image-plus" style="width: 48px; height: 48px;"></i>
                                <p>Click or drag image here</p>
                                <span>PNG, JPG up to 5MB</span>
                                <input type="file" name="image" id="fileElem" hidden required accept="image/*">
                                <img id="filePreview" alt="Preview">
                            </label>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('createModal')">Cancel</button>
                    <button type="submit" class="btn-save"><i data-lucide="check"></i> Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i data-lucide="edit"></i> Update Product</h3>
                <button class="close-btn" onclick="closeModal('editModal')"><i data-lucide="x"></i></button>
            </div>
            
            <form action="update.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="modal-body">
                    <div class="modal-grid">
                        
                        <div class="left-form">
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Brand Classification</label>
                                <input type="text" name="brand" id="edit_brand" class="form-control" required>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div class="form-group">
                                    <label>Price (Rp)</label>
                                    <input type="number" name="price" id="edit_price" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Stock Quantity</label>
                                    <input type="number" name="stock" id="edit_stock" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Available Sizes</label>
                                <input type="text" name="size" id="edit_size" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Full Description</label>
                                <textarea name="description" id="edit_description" class="form-control" required></textarea>
                            </div>
                        </div>

                        <div class="right-form">
                            <label style="display:block; font-size:14px; font-weight:600; color:var(--text-gray); margin-bottom:8px;">Update Image (Optional)</label>
                            <label class="upload-area" id="editUploadArea">
                                <i data-lucide="image-plus" style="width: 48px; height: 48px;"></i>
                                <p>Click to change image</p>
                                <span>Leave empty to keep existing</span>
                                <input type="file" name="image" id="editFileElem" hidden accept="image/*">
                                <img id="editFilePreview" style="display:block;" alt="Preview">
                            </label>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" class="btn-save"><i data-lucide="save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        lucide.createIcons();

        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = "none";
            }
        }

        // Image Preview logic
        function setupImagePreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        setupImagePreview('fileElem', 'filePreview');
        setupImagePreview('editFileElem', 'editFilePreview');

        // Edit Modal Population
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_id').value = this.dataset.id;
                document.getElementById('edit_name').value = this.dataset.name;
                document.getElementById('edit_brand').value = this.dataset.brand;
                document.getElementById('edit_price').value = this.dataset.price;
                document.getElementById('edit_stock').value = this.dataset.stock;
                document.getElementById('edit_size').value = this.dataset.size;
                document.getElementById('edit_description').value = this.dataset.description;
                
                const imgSrc = this.dataset.img;
                const preview = document.getElementById('editFilePreview');
                if(imgSrc && !imgSrc.endsWith('/')) {
                    preview.src = imgSrc;
                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }

                openModal('editModal');
            });
        });
    </script>
</body>
</html>