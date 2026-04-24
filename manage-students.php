<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) { header('location:index.php'); }

// Delete Logic
if(isset($_GET['del'])){
    $id=$_GET['del'];
    $sql="DELETE FROM tblstudents WHERE StudentId=:id";
    $query = $dbh->prepare($sql); 
    $query->bindParam(':id',$id,PDO::PARAM_STR); 
    $query->execute();
    header("Location: manage-students.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Students List</title>
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
                <li class="nav-item"><a href="manage-students.php" class="nav-link active"><i class="fas fa-users"></i> Students</a></li>
                <li class="nav-item"><a href="manage-results.php" class="nav-link"><i class="fas fa-poll"></i> Results</a></li>
                <li class="nav-item"><a href="manage-notices.php" class="nav-link"><i class="fas fa-bullhorn"></i> Notices</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <h1 class="page-title">Students</h1>
                <div style="display:flex; gap:10px;">
                    <!-- Import Button -->
                    <a href="import-students.php" class="btn" style="background:#10b981; color:white;"><i class="fas fa-file-csv"></i> Import CSV</a>
                    <!-- Add Student Button -->
                    <a href="create-student.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Student</a>
                </div>
            </header>
            
            <div class="card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Roll ID</th>
                            <th>Class</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Query to fetch Student info + Image
                        $sql = "SELECT tblstudents.StudentName, tblstudents.RollId, tblstudents.StudentId, tblstudents.StudentImage, tblclasses.ClassName, tblclasses.Section 
                                FROM tblstudents 
                                JOIN tblclasses ON tblclasses.id=tblstudents.ClassId";
                        $query = $dbh->prepare($sql); 
                        $query->execute();
                        $results=$query->fetchAll(PDO::FETCH_OBJ); 
                        
                        if($query->rowCount() > 0){ 
                            foreach($results as $result){ 
                                // Check if image exists
                                $img = !empty($result->StudentImage) ? "images/".$result->StudentImage : "https://via.placeholder.com/40";
                        ?>
                        <tr>
                            <!-- Image Display -->
                            <td>
                                <img src="<?php echo $img;?>" style="width:40px; height:40px; border-radius:50%; object-fit:cover; border: 1px solid #ddd;">
                            </td>
                            <td><?php echo htmlentities($result->StudentName);?></td>
                            <td><?php echo htmlentities($result->RollId);?></td>
                            <td><?php echo htmlentities($result->ClassName);?> (<?php echo htmlentities($result->Section);?>)</td>
                            <td style="display: flex; gap: 5px;">
                                <!-- ID CARD BUTTON -->
                                <a href="generate-id.php?id=<?php echo $result->StudentId;?>" class="btn btn-sm" style="background:#6366f1; color:white;" title="Generate ID Card" target="_blank"><i class="fas fa-id-card"></i></a>
                                
                                <!-- DELETE BUTTON -->
                                <a href="javascript:void(0);" onclick="confirmDelete('manage-students.php?del=<?php echo $result->StudentId;?>')" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php }} ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="js/app.js"></script>
</body>
</html>