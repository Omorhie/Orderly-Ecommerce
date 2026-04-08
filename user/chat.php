<?php
session_start();

if(!isset($_SESSION['username_user'])){
    header("Location: ../auth/user/login.php");
    exit;
}

require_once "../config/database.php";
$user_id = $_SESSION['user_id'];

// kirim pesan
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])){
    $msg = mysqli_real_escape_string($conn, $_POST['message']);

    if(trim($msg) !== ''){
        mysqli_query($conn, "
            INSERT INTO chats (user_id, message, sender)
            VALUES ('$user_id', '$msg', 'user')
        ");
    }
    
    // Redirect to prevent form resubmission
    header("Location: chat.php");
    exit;
}

// ambil chat
$chat = mysqli_query($conn, "
    SELECT * FROM chats 
    WHERE user_id='$user_id'
    ORDER BY id ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Service - Orderly</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #0F2854;
            --secondary: #4988C4;
            --bg-color: #f1f5f9;
            --white: #ffffff;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bubble-user: #4988C4;
            --bubble-admin: #ffffff;
            --card-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.1);
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
            flex-direction: column;
            overflow: hidden;
        }

        /* NAVBAR */
        .navbar {
            background: var(--white);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
        }

        /* WRAPPER */
        .wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            height: calc(100vh - 70px);
        }

        /* CONTAINER */
        .container {
            width: 100%;
            max-width: 500px;
            height: 100%;
            max-height: 800px;
            background: var(--bg-color);
            border-radius: 24px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            border: 1px solid #e2e8f0;
        }

        /* HEADER */
        .header {
            background: var(--white);
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid #e2e8f0;
            z-index: 10;
        }

        .header-avatar {
            width: 45px;
            height: 45px;
            background: rgba(73, 136, 196, 0.1);
            color: var(--secondary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-info {
            display: flex;
            flex-direction: column;
        }

        .header-info h2 {
            font-size: 1.1rem;
            color: var(--primary);
            font-weight: 600;
        }

        .status {
            font-size: 0.8rem;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
        }

        /* CHAT BOX */
        .chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: #f8fafc;
        }

        .chat-box::-webkit-scrollbar {
            width: 6px;
        }

        .chat-box::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        /* MESSAGE BUBBLES */
        .msg {
            padding: 12px 18px;
            border-radius: 20px;
            max-width: 80%;
            font-size: 0.95rem;
            line-height: 1.5;
            animation: fadeIn 0.3s ease;
            position: relative;
            word-wrap: break-word;
        }

        .user {
            background: var(--bubble-user);
            color: var(--white);
            align-self: flex-end;
            border-bottom-right-radius: 4px;
            box-shadow: 0 2px 10px rgba(73, 136, 196, 0.2);
        }

        .admin {
            background: var(--bubble-admin);
            color: var(--text-dark);
            align-self: flex-start;
            border-bottom-left-radius: 4px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }

        /* INPUT AREA */
        .form {
            display: flex;
            padding: 15px 20px;
            background: var(--white);
            border-top: 1px solid #e2e8f0;
            gap: 12px;
            align-items: flex-end;
        }

        textarea {
            flex: 1;
            padding: 12px 18px;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            background: var(--bg-color);
            color: var(--text-dark);
            outline: none;
            resize: none;
            font-size: 0.95rem;
            font-family: inherit;
            min-height: 48px;
            max-height: 120px;
            overflow-y: auto;
            transition: var(--transition);
        }

        textarea:focus {
            border-color: var(--secondary);
            background: var(--white);
        }

        button {
            width: 48px;
            height: 48px;
            border: none;
            border-radius: 50%;
            background: var(--primary);
            color: var(--white);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: var(--transition);
            box-shadow: 0 4px 10px rgba(15, 40, 84, 0.2);
        }

        button:hover {
            background: var(--secondary);
            transform: scale(1.05);
        }

        /* ANIMATION */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 550px) {
            .wrapper { padding: 0; }
            .container { border-radius: 0; border: none; max-width: 100%; height: 100%; }
            .msg { max-width: 85%; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="dashboard.php" class="logo">
            <i data-lucide="shopping-bag" style="width: 24px; color: var(--primary);"></i>
            Order<span>ly</span>
        </a>
        <div class="nav-right">
            <a href="dashboard.php">
                <i data-lucide="arrow-left" style="width: 18px;"></i> Back 
            </a>
        </div>
    </nav>

    <div class="wrapper">
        <div class="container">
            <div class="header">
                <div class="header-avatar">
                    <i data-lucide="headphones" style="width: 24px;"></i>
                </div>
                <div class="header-info">
                    <h2>Customer Support</h2>
                    <div class="status"><div class="status-dot"></div> Online</div>
                </div>
            </div>

            <div class="chat-box" id="chatBox">
                <div class="msg admin">
                    Hello <?= htmlspecialchars($_SESSION['username_user']); ?>! How can we help you today?
                </div>
                <?php while($c = mysqli_fetch_assoc($chat)){ ?>
                    <div class="msg <?= htmlspecialchars($c['sender']); ?>">
                        <?= nl2br(htmlspecialchars($c['message'])); ?>
                    </div>
                <?php } ?>
            </div>

            <form method="POST" class="form">
                <textarea name="message" id="msgInput" placeholder="Type your message..." required rows="1"></textarea>
                <button type="submit" name="send">
                    <i data-lucide="send" style="width: 20px; margin-left: -2px;"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Scroll to bottom of chat
        const chatBox = document.getElementById('chatBox');
        chatBox.scrollTop = chatBox.scrollHeight;

        // Auto resize textarea and submit on Enter (without shift)
        const textarea = document.getElementById('msgInput');
        
        textarea.addEventListener("input", function() {
            this.style.height = "48px";
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
    </script>
</body>
</html>