<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) { header('location:index.php'); }
if(isset($_POST['create'])){
    $subname=$_POST['subname']; $subcode=$_POST['subcode'];
    $sql="INSERT INTO tblsubjects(SubjectName,SubjectCode) VALUES(:subname,:subcode)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':subname',$subname,PDO::PARAM_STR);
    $query->bindParam(':subcode',$subcode,PDO::PARAM_STR);
    $query->execute();
    $msg="Subject Added!";
}
if(isset($_GET['del'])){
    $id=$_GET['del'];
    $sql="DELETE FROM tblsubjects WHERE id=:id";
    $query = $dbh->prepare($sql); $query->bindParam(':id',$id,PDO::PARAM_STR); $query->execute();
    header("Location: manage-subjects.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Subjects</title>
    <link rel="stylesheet" href="css/modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="brand">SRMS Pro</div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a href="manage-classes.php" class="nav-link"><i class="fas fa-chalkboard"></i> Classes</a></li>
                <li class="nav-item"><a href="manage-subjects.php" class="nav-link active"><i class="fas fa-book"></i> Subjects</a></li>
                <li class="nav-item"><a href="manage-students.php" class="nav-link"><i class="fas fa-users"></i> Students</a></li>
                <li class="nav-item"><a href="manage-results.php" class="nav-link"><i class="fas fa-poll"></i> Results</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header class="top-header"><h1 class="page-title">Subjects</h1><button class="theme-toggle" id="themeBtn" onclick="toggleTheme()"><i class="fas fa-moon"></i></button></header>
            <div class="card">
                <h3>Add Subject</h3>
                <?php if(isset($msg)){ echo "<div style='color:green'>$msg</div>"; } ?>
                <form method="post" style="display:flex; gap:10px; align-items:flex-end;">
                    <div style="flex:1;"><label class="form-label">Subject Name</label><input type="text" name="subname" class="form-control" required></div>
                    <div style="flex:1;"><label class="form-label">Subject Code</label><input type="text" name="subcode" class="form-control" required></div>
                    <button type="submit" name="create" class="btn btn-primary">Add</button>
                </form>
            </div>
            <div class="card">
                <table class="table">
                    <thead><tr><th>#</th><th>Subject Name</th><th>Code</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT * from tblsubjects"; $query = $dbh->prepare($sql); $query->execute();
                        $results=$query->fetchAll(PDO::FETCH_OBJ); $cnt=1;
                        if($query->rowCount() > 0){ foreach($results as $result){ ?>
                        <tr>
                            <td><?php echo $cnt;?></td>
                            <td><?php echo htmlentities($result->SubjectName);?></td>
                            <td><?php echo htmlentities($result->SubjectCode);?></td>
                            <td>
                                <a href="javascript:void(0);" onclick="confirmDelete('manage-subjects.php?del=<?php echo $result->id;?>')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
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