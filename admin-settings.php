<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) { header('location:index.php'); }
if(isset($_POST['update'])){
    $newname = $_POST['fullname']; $newemail = $_POST['email']; $newpass = !empty($_POST['password']) ? md5($_POST['password']) : null;
    $uname = $_SESSION['alogin'];
    if($newpass){ $sql = "UPDATE admin SET FullName=:fn, Email=:em, Password=:pass WHERE UserName=:un"; $q=$dbh->prepare($sql); $q->bindParam(':pass',$newpass,PDO::PARAM_STR); } 
    else { $sql = "UPDATE admin SET FullName=:fn, Email=:em WHERE UserName=:un"; $q=$dbh->prepare($sql); }
    $q->bindParam(':fn',$newname,PDO::PARAM_STR); $q->bindParam(':em',$newemail,PDO::PARAM_STR); $q->bindParam(':un',$uname,PDO::PARAM_STR);
    $q->execute(); $msg="Profile Updated!";
}
$uname=$_SESSION['alogin']; $sql="SELECT * FROM admin WHERE UserName=:un"; $q=$dbh->prepare($sql); $q->bindParam(':un',$uname,PDO::PARAM_STR); $q->execute(); $data=$q->fetch(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Settings</title><link rel="stylesheet" href="css/modern.css"></head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="brand">SRMS Pro</div>
            <ul class="nav-menu"><li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="fas fa-arrow-left"></i> Back</a></li></ul>
        </aside>
        <main class="main-content">
            <h1 class="page-title">Admin Settings</h1>
            <div class="card" style="max-width:600px;">
                <?php if(isset($msg)) echo "<div style='color:green'>$msg</div>"; ?>
                <form method="post">
                    <div class="form-group"><label class="form-label">Full Name</label><input type="text" name="fullname" value="<?php echo $data->FullName;?>" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" value="<?php echo $data->Email;?>" class="form-control"></div>
                    <div class="form-group"><label class="form-label">New Password</label><input type="password" name="password" class="form-control"></div>
                    <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>