<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) { header('location:index.php'); }
if(isset($_POST['create'])){
    $classname=$_POST['classname'];
    $section=$_POST['section'];
    $sql="INSERT INTO tblclasses(ClassName,Section) VALUES(:classname,:section)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':classname',$classname,PDO::PARAM_STR);
    $query->bindParam(':section',$section,PDO::PARAM_STR);
    $query->execute();
    $msg="Class Created!";
}
if(isset($_GET['del'])){
    $id=$_GET['del'];
    $sql="DELETE FROM tblclasses WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id',$id,PDO::PARAM_STR);
    $query->execute();
    header("Location: manage-classes.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Classes</title>
    <link rel="stylesheet" href="css/modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="brand">SRMS Pro</div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a href="manage-classes.php" class="nav-link active"><i class="fas fa-chalkboard"></i> Classes</a></li>
                <li class="nav-item"><a href="manage-subjects.php" class="nav-link"><i class="fas fa-book"></i> Subjects</a></li>
                <li class="nav-item"><a href="manage-students.php" class="nav-link"><i class="fas fa-users"></i> Students</a></li>
                <li class="nav-item"><a href="manage-results.php" class="nav-link"><i class="fas fa-poll"></i> Results</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header class="top-header"><h1 class="page-title">Classes</h1><button class="theme-toggle" id="themeBtn" onclick="toggleTheme()"><i class="fas fa-moon"></i></button></header>
            <div class="card">
                <h3>Add Class</h3>
                <?php if(isset($msg)){ echo "<div style='color:green'>$msg</div>"; } ?>
                <form method="post" style="display:flex; gap:10px; align-items:flex-end;">
                    <div style="flex:1;"><label class="form-label">Name</label><input type="text" name="classname" class="form-control" required></div>
                    <div style="flex:1;"><label class="form-label">Section</label><input type="text" name="section" class="form-control" required></div>
                    <button type="submit" name="create" class="btn btn-primary">Add</button>
                </form>
            </div>
            <div class="card">
                <table class="table">
                    <thead><tr><th>#</th><th>Class</th><th>Section</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT * from tblclasses"; $query = $dbh->prepare($sql); $query->execute();
                        $results=$query->fetchAll(PDO::FETCH_OBJ); $cnt=1;
                        if($query->rowCount() > 0){ foreach($results as $result){ ?>
                        <tr>
                            <td><?php echo $cnt;?></td>
                            <td><?php echo htmlentities($result->ClassName);?></td>
                            <td><?php echo htmlentities($result->Section);?></td>
                            <td>
                                <a href="edit-class.php?classid=<?php echo $result->id;?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <a href="javascript:void(0);" onclick="confirmDelete('manage-classes.php?del=<?php echo $result->id;?>')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
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