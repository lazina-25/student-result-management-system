<?php
session_start();
include('includes/config.php');

if(isset($_POST['login'])){
    $uname=$_POST['username'];
    $password=md5($_POST['password']);
    
    // Check Admin Table
    $sql ="SELECT UserName,Password FROM admin WHERE UserName=:uname and Password=:password";
    $query= $dbh -> prepare($sql);
    $query-> bindParam(':uname', $uname, PDO::PARAM_STR);
    $query-> bindParam(':password', $password, PDO::PARAM_STR);
    $query-> execute();
    
    if($query->rowCount() > 0){
        $_SESSION['alogin']=$_POST['username'];
        echo "<script>window.location.href='dashboard.php';</script>";
    } else {
        $error="Invalid Admin Credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | SRMS Pro</title>
    <link rel="stylesheet" href="css/modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="login-wrapper">
    <div class="card login-card">
        <div style="text-align:center; margin-bottom:2rem;">
            <i class="fas fa-layer-group" style="font-size:3rem; color:var(--primary);"></i>
            <h2 style="margin-top:10px;">SRMS Pro</h2>
            <p style="color:var(--text-muted);">Admin Panel Login</p>
        </div>

        <?php if(isset($error)){ ?><div style="color:var(--danger); text-align:center; margin-bottom:1rem;"><?php echo $error; ?></div><?php } ?>

        <form method="post">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="admin" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary" style="width:100%">Admin Login</button>
        </form>

        <div style="margin-top:2rem; border-top:1px solid var(--border); padding-top:1rem; text-align:center;">
            <p style="color:var(--text-muted); margin-bottom:10px;">Are you a Student?</p>
            <div style="display:flex; gap:10px; justify-content:center;">
                <a href="student-login.php" class="btn" style="background:var(--secondary); color:white; flex:1;">
                    <i class="fas fa-user-graduate"></i> Student Login
                </a>
                <a href="find-result.php" class="btn" style="background:#e5e7eb; color:var(--text-main); flex:1;">
                    <i class="fas fa-search"></i> Search Result
                </a>
            </div>
        </div>
    </div>
</body>
</html>