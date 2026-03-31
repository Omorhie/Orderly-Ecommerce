<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

/* BACKGROUND */
body{
    height:100vh;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    background:#0F2854;
}

/* TEXT ATAS */
.welcome-text{
    color:white;
    font-size:32px;
    font-weight:bold;
    text-align: center;
}

.welcome-text h3 {
    font-size: 20px;
    font-weight: normal;
}


.welcome-text span{
    color:#4988C4;
}

/* CONTAINER */
.login-container{
    background:#E6E6E6;
    padding:40px;
    border-radius:16px;
    width:400px;
    height: 550px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
    margin-top: 30px;
    transition: all 300ms;
}


.login-container:hover{
    transform:translateY(-5px);
}

/* LOGIN TITLE */
.login-title{
    color:#4988C4   ;
    font-size:50px;
    margin-bottom:30px;
    text-align: center;
    margin-top: 30px;
}


/* INPUT */
input{
    width:100%;
    padding:12px;
    margin-top:8px;
    margin-bottom:20px;
    border-radius:16px;
    border:1px solid #ccc;
    outline:none;
}

/* INPUT FOCUS */
input:focus{
    border:1px solid #526D82;
}

/* BUTTON */
button{
    width:100%;
    padding:14px;
    border: 2px solid #4988C4;
    border-radius:16px;
    background:#4988C4;
    color:white;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:transparent;
    border: 2px solid #4988C4;
    color: #4988C4;
}

/* FLOATING LABEL WRAPPER */
.input-group{
    position:relative;
    margin-top:3px;
}

button{
    margin-top:30px; /* kasih jarak tombol dari input */
}

/* INPUT */
.input-group input{
    width:100%;
    padding:16px 12px;
    border-radius:16px;
    border:1px solid #ccc;
    outline:none;
    background:white;
    font-size:16px;
}

/* LABEL DEFAULT */
.input-group label{
    position:absolute;
    left:12px;
    top:42%;
    transform:translateY(-50%);
    padding:0 6px;
    color:#929292;
    font-size:12px;
    transition:0.25s ease;
    pointer-events:none;
    font-weight: 100;
}

/* LABEL NAIK SAAT FOCUS / ADA ISI */
.input-group input:focus + label,
.input-group input:not(:placeholder-shown) + label{
    top:0;
    font-size:12px;
    color:#526D82;
}

/* BORDER SAAT FOCUS */
.input-group input:focus{
    border:1px solid #526D82;
}

/* GARIS BAWAH BUTTON */
.divider{
    width:90%;
    height:4px;
    background:#4988C4;
    margin:30px auto 0 auto; /* center horizontal */
    border-radius:20px;
}

/* SIGN UP TEXT */
.signup-text{
    text-align:center;
    margin-top:25px;
    font-size:14px;
    color:#27374D;
}

.signup-text a{
    color:#4988C4;
    text-decoration:none;
    font-weight:bold;
    transition:0.3s;
}

.signup-text a:hover{
    color:#0F2854;
}
</style>
</head>

<body>

<div class="welcome-text">
    <h1>Hello!</h1>
    <h3>Welcome To <span>Orderly</span></h3>
</div>

<div class="login-container">

    <div class="login-title">
        Login
    </div>

    <form action="login_process.php" method="POST">

        <div class="input-group">
            <input type="text" name="username" placeholder=" " required>
            <label>Username</label>
        </div>

        <div class="input-group">
            <input type="password" name="password" placeholder=" " required>
            <label>Password</label>
        </div>

        <button type="submit">Login</button>

        <div class="divider"></div>

        <div class="signup-text">
    Don’t Have Account? 
    <a href="register.php">Sign Up</a>
</div>

    </form>

</div>

</body>
</html>