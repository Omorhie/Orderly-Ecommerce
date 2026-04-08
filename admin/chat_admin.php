<?php
session_start();

if (!isset($_SESSION['officer_role'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";

// ambil semua user yang pernah chat
$users = $conn->query("
    SELECT DISTINCT users.id, users.username
    FROM chats
    JOIN users ON chats.user_id = users.id
");

$selected_user = $_GET['user_id'] ?? null;

// kirim balasan
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])){
    $msg = $_POST['message'];

    if(trim($msg) !== ''){
        mysqli_query($conn, "
            INSERT INTO chats (user_id, message, sender)
            VALUES ('$selected_user', '$msg', 'admin')
        ");
    }
    header("Location: chat_admin.php?user_id=" . $selected_user);
    exit;
}

// ambil chat user tertentu
$chat = [];
if($selected_user){
    $chatUserQuery = mysqli_query($conn, "SELECT username FROM users WHERE id='$selected_user'");
    $chatUser = false;
    if(mysqli_num_rows($chatUserQuery) > 0){
        $chatUser = mysqli_fetch_assoc($chatUserQuery)['username'];
    }

    $chat = mysqli_query($conn, "
        SELECT * FROM chats
        WHERE user_id='$selected_user'
        ORDER BY id ASC
    ");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Customer Support - Orderly Admin</title>
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
            --card-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
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
            height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* CUSTOMER LIST SIDEBAR */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0,0,0,0.02);
            z-index: 10;
        }

        .sidebar-header {
            padding: 24px 20px;
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-list {
            flex: 1;
            overflow-y: auto;
            padding: 15px 10px;
        }

        /* Scrollbar custom */
        .user-list::-webkit-scrollbar, .chat-box::-webkit-scrollbar { width: 6px; }
        .user-list::-webkit-scrollbar-thumb, .chat-box::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .user {
            margin-bottom: 5px;
            border-radius: 12px;
            transition: var(--transition);
        }

        .user a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text-gray);
            font-weight: 500;
            padding: 12px 15px;
            border-radius: 12px;
        }

        .user:hover {
            background: #f1f5f9;
        }

        .user.active a {
            background: rgba(59, 130, 246, 0.1);
            color: var(--accent);
            font-weight: 600;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-gray);
        }

        .user.active .user-avatar {
            background: var(--accent);
            color: white;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            border-radius: 12px;
            background: var(--bg-color);
            color: var(--text-gray);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-back:hover {
            background: #e2e8f0;
            color: var(--primary);
        }

        /* CHAT AREA */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--bg-color);
            position: relative;
        }

        .chat-header {
            padding: 20px 30px;
            background: var(--sidebar-bg);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: var(--card-shadow);
            z-index: 5;
        }

        .chat-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
        }

        .chat-header .status {
            font-size: 13px;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .chat-box {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .msg {
            max-width: 65%;
            padding: 15px 20px;
            border-radius: 18px;
            font-size: 14.5px;
            line-height: 1.5;
            position: relative;
            word-wrap: break-word;
        }

        /* ADMIN BUBBLE */
        .msg.admin {
            align-self: flex-end;
            background: var(--primary);
            color: white;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 10px rgba(30, 41, 59, 0.15);
        }

        /* USER BUBBLE */
        .msg.user {
            align-self: flex-start;
            background: var(--sidebar-bg);
            color: var(--text-dark);
            border-bottom-left-radius: 4px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        }

        .chat-form {
            padding: 20px 30px;
            background: var(--sidebar-bg);
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: flex-end;
            gap: 15px;
        }

        .chat-form textarea {
            flex: 1;
            padding: 16px 20px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            outline: none;
            background: var(--bg-color);
            font-size: 15px;
            resize: none;
            height: 54px;
            min-height: 54px;
            max-height: 150px;
            overflow-y: auto;
            transition: var(--transition);
        }

        .chat-form textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            background: white;
        }

        .btn-send {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: var(--accent);
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .btn-send:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);
        }

        /* EMPTY STATE */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--text-gray);
            gap: 15px;
            text-align: center;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            background: rgba(100, 116, 139, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i data-lucide="users"></i> Active Chats
        </div>

        <div class="user-list">
            <?php while($u = $users->fetch_assoc()){ ?>
                <div class="user <?= ($selected_user == $u['id']) ? 'active' : '' ?>">
                    <a href="?user_id=<?= $u['id'] ?>">
                        <div class="user-avatar">
                            <i data-lucide="user" style="width: 18px; height: 18px;"></i>
                        </div>
                        <span><?= htmlspecialchars($u['username']) ?></span>
                    </a>
                </div>
            <?php } ?>
        </div>

        <div class="sidebar-footer">
            <a href="dashboard.php" class="btn-back">
                <i data-lucide="arrow-left" style="width: 18px;"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- CHAT AREA -->
    <div class="chat-area">

        <?php if($selected_user && $chatUser){ ?>
            
            <div class="chat-header">
                <div>
                    <h2><?= htmlspecialchars($chatUser) ?></h2>
                    <div class="status"><div class="status-dot"></div> Active now</div>
                </div>
            </div>

            <div class="chat-box" id="chatBox">
                <?php while($c = mysqli_fetch_assoc($chat)){ ?>
                    <div class="msg <?= $c['sender'] ?>">
                        <?= nl2br(htmlspecialchars($c['message'])) ?>
                    </div>
                <?php } ?>
            </div>

            <form method="POST" class="chat-form">
                <textarea name="message" id="msgInput" placeholder="Write a reply..." required></textarea>
                <button type="submit" class="btn-send">
                    <i data-lucide="send" style="width: 22px; margin-left: -2px;"></i>
                </button>
            </form>

        <?php } else { ?>
            <!-- EMPTY STATE -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i data-lucide="message-square" style="width: 40px; height: 40px; color: #64748b;"></i>
                </div>
                <h3>Select a conversation</h3>
                <p>Choose a customer from the sidebar to view their messages<br>and respond to their inquiries.</p>
            </div>
        <?php } ?>

    </div>

    <script>
        lucide.createIcons();

        // Auto-scroll to bottom of chat
        const chatBox = document.getElementById('chatBox');
        if(chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        // Handle Textarea Auto-resize and Enter to Send
        const textarea = document.getElementById('msgInput');
        if(textarea) {
            textarea.addEventListener("input", function() {
                this.style.height = "54px";
                this.style.height = (this.scrollHeight) + "px";
            });

            textarea.addEventListener("keydown", function(e) {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    if(this.value.trim() !== "") {
                        this.closest('form').submit();
                    }
                }
            });
        }
    </script>
</body>
</html>