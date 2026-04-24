<?php
session_start();
include('includes/config.php');

if(isset($_POST['login'])){
    $rollid=$_POST['rollid'];
    $password=md5($_POST['password']); // Passwords are encrypted

    $sql ="SELECT StudentId,RollId,StudentName,ClassId FROM tblstudents WHERE RollId=:rollid and Password=:password";
    $query= $dbh -> prepare($sql);
    $query-> bindParam(':rollid', $rollid, PDO::PARAM_STR);
    $query-> bindParam(':password', $password, PDO::PARAM_STR);
    $query-> execute();
    $results=$query->fetchAll(PDO::FETCH_OBJ);

    if($query->rowCount() > 0){
        // Login Success
        foreach ($results as $result) {
            $_SESSION['slogin'] = $result->RollId;
            $_SESSION['student_id'] = $result->StudentId;
            $_SESSION['class_id'] = $result->ClassId;
            $_SESSION['student_name'] = $result->StudentName;
        }
        echo "<script>window.location.href='student-dashboard.php';</script>";
    } else {
        $error="Invalid Roll ID or Password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login | SRMS Pro</title>
    <link rel="stylesheet" href="css/modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="login-wrapper">
    <div class="card login-card">
        <div style="text-align:center; margin-bottom:2rem;">
            <i class="fas fa-user-graduate" style="font-size:3rem; color:var(--primary);"></i>
            <h2 style="margin-top:10px;">Student Portal</h2>
        </div>
        
        <?php if(isset($error)){ ?><div style="color:var(--danger); text-align:center; margin-bottom:1rem;"><?php echo $error; ?></div><?php } ?>
        
        <form method="post">
            <div class="form-group">
                <label class="form-label">Roll ID</label>
                <input type="text" name="rollid" class="form-control" placeholder="Enter your Roll ID" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Default is Roll ID" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary" style="width:100%">Login</button>
        </form>
        
        <div style="text-align:center; margin-top:1.5rem;">
            <a href="index.php" style="color:var(--text-muted); font-size:0.9rem;">Are you an Admin? Login here</a>
        </div>
    </div>
</body>
</html>