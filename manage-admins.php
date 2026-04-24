<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) { header('location:index.php'); }

// 1. Add New Admin Logic
if(isset($_POST['add_admin'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];
    $password = md5($_POST['password']); // Using MD5 encryption

    // Check if username already exists
    $sql = "SELECT UserName FROM admin WHERE UserName=:username";
    $query = $dbh->prepare($sql);
    $query->bindParam(':username',$username,PDO::PARAM_STR);
    $query->execute();

    if($query->rowCount() > 0){
        $error = "Username already exists. Please choose another.";
    } else {
        $sql = "INSERT INTO admin(UserName,Password,FullName,Email) VALUES(:username,:password,:fullname,:email)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':username',$username,PDO::PARAM_STR);
        $query->bindParam(':password',$password,PDO::PARAM_STR);
        $query->bindParam(':fullname',$fullname,PDO::PARAM_STR);
        $query->bindParam(':email',$email,PDO::PARAM_STR);
        $query->execute();
        $msg = "New Admin Added Successfully!";
    }
}

// 2. Delete Admin Logic
if(isset($_GET['del'])){
    $id = $_GET['del'];
    // Prevent deleting yourself
    $current_user = $_SESSION['alogin'];
    $sql = "SELECT * FROM admin WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id',$id,PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    if($result->UserName == $current_user){
        $error = "You cannot delete your own account while logged in!";
    } else {
        $sql = "DELETE FROM admin WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id',$id,PDO::PARAM_STR);
        $query->execute();
        $msg = "Admin deleted successfully.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Admins</title>
    <link rel="stylesheet" href="css/modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">SRMS Pro</div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a href="manage-classes.php" class="nav-link"><i class="fas fa-chalkboard"></i> Classes</a></li>
                <li class="nav-item"><a href="manage-subjects.php" class="nav-link"><i class="fas fa-book"></i> Subjects</a></li>
                <li class="nav-item"><a href="manage-students.php" class="nav-link"><i class="fas fa-users"></i> Students</a></li>
                <li class="nav-item"><a href="manage-results.php" class="nav-link"><i class="fas fa-poll"></i> Results</a></li>
                <li class="nav-item"><a href="manage-admins.php" class="nav-link active"><i class="fas fa-user-shield"></i> Admins</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <h1 class="page-title">Manage Staff/Admins</h1>
            </header>

            <!-- Add Admin Form -->
            <div class="card">
                <h3><i class="fas fa-user-plus"></i> Add New Admin</h3>
                <?php if(isset($msg)){ echo "<div style='color:green; margin-bottom:10px;'>$msg</div>"; } ?>
                <?php if(isset($error)){ echo "<div style='color:red; margin-bottom:10px;'>$error</div>"; } ?>
                
                <form method="post" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div style="grid-column: span 2;">
                        <button type="submit" name="add_admin" class="btn btn-primary">Create Admin</button>
                    </div>
                </form>
            </div>

            <!-- List Admins -->
            <div class="card">
                <h3>Existing Admins</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT * from admin";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                        $cnt=1;
                        if($query->rowCount() > 0){
                            foreach($results as $result){ ?>
                            <tr>
                                <td><?php echo $cnt;?></td>
                                <td><?php echo htmlentities($result->FullName);?></td>
                                <td><?php echo htmlentities($result->UserName);?></td>
                                <td><?php echo htmlentities($result->Email);?></td>
                                <td>
                                    <?php if($result->UserName !== $_SESSION['alogin']) { ?>
                                    <a href="javascript:void(0);" onclick="confirmDelete('manage-admins.php?del=<?php echo $result->id;?>')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                                    <?php } else { echo "<span style='color:#888; font-size:0.8rem;'>(You)</span>"; } ?>
                                </td>
                            </tr>
                        <?php $cnt++; }} ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="js/app.js"></script>
</body>
</html>