<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) { header('location:index.php'); }
$id=intval($_GET['classid']);
if(isset($_POST['update'])){
    $classname=$_POST['classname']; $section=$_POST['section'];
    $sql="UPDATE tblclasses SET ClassName=:classname, Section=:section WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':classname',$classname,PDO::PARAM_STR);
    $query->bindParam(':section',$section,PDO::PARAM_STR);
    $query->bindParam(':id',$id,PDO::PARAM_STR);
    $query->execute();
    echo "<script>alert('Updated!');window.location.href='manage-classes.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Edit Class</title><link rel="stylesheet" href="css/modern.css"></head>
<body>
    <div style="display:flex; height:100vh; align-items:center; justify-content:center;">
        <div class="card" style="width:400px;">
            <h2>Edit Class</h2>
            <?php 
            $sql="SELECT * from tblclasses where id=:id"; $query=$dbh->prepare($sql);
            $query->bindParam(':id',$id,PDO::PARAM_STR); $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            foreach($results as $result){ ?>
            <form method="post">
                <div class="form-group"><label>Name</label><input type="text" name="classname" value="<?php echo htmlentities($result->ClassName);?>" class="form-control"></div>
                <div class="form-group"><label>Section</label><input type="text" name="section" value="<?php echo htmlentities($result->Section);?>" class="form-control"></div>
                <button type="submit" name="update" class="btn btn-primary">Update</button>
                <a href="manage-classes.php" class="btn">Cancel</a>
            </form>
            <?php } ?>
        </div>
    </div>
</body>
</html>