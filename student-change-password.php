<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['slogin'])==0) { header('location:student-login.php'); }

if(isset($_POST['update'])) {
    $oldpassword = md5($_POST['oldpassword']);
    $newpassword = $_POST['newpassword'];
    $confirmpassword = $_POST['confirmpassword'];
    $sid = $_SESSION['student_id'];

    // 1. Verify Old Password
    $sql = "SELECT Password FROM tblstudents WHERE StudentId=:sid AND Password=:oldpass";
    $query = $dbh->prepare($sql);
    $query->bindParam(':sid', $sid, PDO::PARAM_STR);
    $query->bindParam(':oldpass', $oldpassword, PDO::PARAM_STR);
    $query->execute();

    if($query->rowCount() > 0) {
        // 2. Check if New Passwords match
        if($newpassword == $confirmpassword) {
            $newmd5 = md5($newpassword);
            $sql = "UPDATE tblstudents SET Password=:pass WHERE StudentId=:sid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':pass', $newmd5, PDO::PARAM_STR);
            $query->bindParam(':sid', $sid, PDO::PARAM_STR);
            $query->execute();
            $msg = "Password Changed Successfully!";
        } else {
            $error = "New Password and Confirm Password do not match.";
        }
    } else {
        $error = "Current Password is incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="css/modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">Student Panel</div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="student-dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a href="student-change-password.php" class="nav-link active"><i class="fas fa-key"></i> Change Password</a></li>
                <li class="nav-item"><a href="student-logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <h1 class="page-title">Security Settings</h1>
                <div class="user-controls">
                    <button class="theme-toggle" id="themeBtn" onclick="toggleTheme()"><i class="fas fa-moon"></i></button>
                    <div style="font-weight:600;"><?php echo $_SESSION['student_name']; ?></div>
                </div>
            </header>

            <div class="card" style="max-width: 500px;">
                <h3><i class="fas fa-lock"></i> Update Password</h3>
                
                <?php if(isset($msg)) { echo "<div style='color:green; margin-bottom:15px;'>$msg</div>"; } ?>
                <?php if(isset($error)) { echo "<div style='color:red; margin-bottom:15px;'>$error</div>"; } ?>

                <form method="post">
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="oldpassword" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="newpassword" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirmpassword" class="form-control" required>
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </main>
    </div>
    <script src="js/app.js"></script>
</body>
</html>