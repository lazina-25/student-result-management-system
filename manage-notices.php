<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) { header('location:index.php'); }

// Add Notice
if(isset($_POST['add'])){
    $title = $_POST['title'];
    $details = $_POST['details'];
    $sql = "INSERT INTO tblnotices(NoticeTitle,NoticeDetails) VALUES(:title,:details)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':title',$title,PDO::PARAM_STR);
    $query->bindParam(':details',$details,PDO::PARAM_STR);
    $query->execute();
    $msg = "Notice Posted Successfully!";
}

// Delete Notice
if(isset($_GET['del'])){
    $id = $_GET['del'];
    $sql = "DELETE FROM tblnotices WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id',$id,PDO::PARAM_STR);
    $query->execute();
    header("Location: manage-notices.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Notices</title>
    <link rel="stylesheet" href="css/modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="brand">SRMS Pro</div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a href="manage-students.php" class="nav-link"><i class="fas fa-users"></i> Students</a></li>
                <li class="nav-item"><a href="manage-results.php" class="nav-link"><i class="fas fa-poll"></i> Results</a></li>
                <!-- Active Link -->
                <li class="nav-item"><a href="manage-notices.php" class="nav-link active"><i class="fas fa-bullhorn"></i> Notices</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <h1 class="page-title">Digital Notice Board</h1>
            </header>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                
                <!-- Post Notice Form -->
                <div class="card">
                    <h3><i class="fas fa-plus-circle"></i> Post New Notice</h3>
                    <?php if(isset($msg)){ echo "<div style='color:green; margin-bottom:10px;'>$msg</div>"; } ?>
                    <form method="post">
                        <div class="form-group">
                            <label class="form-label">Notice Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Mid-Term Exam Schedule" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Details</label>
                            <textarea name="details" class="form-control" rows="5" placeholder="Write the full announcement here..." required></textarea>
                        </div>
                        <button type="submit" name="add" class="btn btn-primary">Post Notice</button>
                    </form>
                </div>

                <!-- Existing Notices List -->
                <div class="card">
                    <h3><i class="fas fa-list"></i> Active Notices</h3>
                    <div style="max-height: 400px; overflow-y: auto;">
                        <?php 
                        $sql = "SELECT * from tblnotices ORDER BY CreationDate DESC";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                        if($query->rowCount() > 0){
                            foreach($results as $result){ ?>
                            <div style="border-bottom:1px solid #eee; padding:15px 0;">
                                <div style="display:flex; justify-content:space-between;">
                                    <h4 style="margin:0; color:var(--primary);"><?php echo htmlentities($result->NoticeTitle);?></h4>
                                    <a href="manage-notices.php?del=<?php echo $result->id;?>" onclick="return confirm('Delete this notice?')" style="color:red;"><i class="fas fa-trash"></i></a>
                                </div>
                                <p style="font-size:0.9rem; color:var(--text-muted); margin:5px 0;">
                                    <?php echo htmlentities($result->NoticeDetails);?>
                                </p>
                                <small style="color:#aaa;"><i class="far fa-clock"></i> <?php echo htmlentities($result->CreationDate);?></small>
                            </div>
                        <?php }} else { echo "<p>No active notices.</p>"; } ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>