<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) { header('location:index.php'); }

// --- FIXED DELETE LOGIC ---
if(isset($_GET['delId'])){
    $did = intval($_GET['delId']);
    
    // Check if ID is valid
    if($did > 0){
        try {
            $sql = "DELETE FROM tblresult WHERE id=:did";
            $query = $dbh->prepare($sql);
            $query->bindParam(':did', $did, PDO::PARAM_STR);
            
            if($query->execute()){
                $_SESSION['msg'] = "Result deleted successfully!";
            } else {
                $_SESSION['error'] = "Could not delete result. Database error.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    }
    
    // Redirect and EXIT to force the page update
    header("Location: manage-results.php");
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Results | SRMS Pro</title>
    <link rel="stylesheet" href="css/modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        // Internal Javascript for Delete Confirmation (Safe fallback)
        function confirmDelete(url) {
            if(confirm("Are you sure you want to delete this result? This cannot be undone.")) {
                window.location.href = url;
            }
        }
    </script>
</head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="brand">SRMS Pro</div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a href="manage-classes.php" class="nav-link"><i class="fas fa-chalkboard"></i> Classes</a></li>
                <li class="nav-item"><a href="manage-subjects.php" class="nav-link"><i class="fas fa-book"></i> Subjects</a></li>
                <li class="nav-item"><a href="manage-students.php" class="nav-link"><i class="fas fa-users"></i> Students</a></li>
                <li class="nav-item"><a href="manage-results.php" class="nav-link active"><i class="fas fa-poll"></i> Results</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <h1 class="page-title">Manage Results</h1>
                <a href="add-result.php" class="btn btn-primary"><i class="fas fa-plus"></i> Declare Result</a>
            </header>

            <div class="card">
                <?php if(isset($_SESSION['msg'])) { ?>
                    <div style="background:#d4edda; color:#155724; padding:10px; margin-bottom:15px; border-radius:5px;">
                        <?php echo htmlentities($_SESSION['msg']);?>
                        <?php unset($_SESSION['msg']);?>
                    </div>
                <?php } ?>
                
                <?php if(isset($_SESSION['error'])) { ?>
                    <div style="background:#f8d7da; color:#721c24; padding:10px; margin-bottom:15px; border-radius:5px;">
                        <?php echo htmlentities($_SESSION['error']);?>
                        <?php unset($_SESSION['error']);?>
                    </div>
                <?php } ?>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Marks</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT tblresult.id as rid, tblstudents.StudentName, tblclasses.ClassName, tblclasses.Section, tblsubjects.SubjectName, tblresult.marks, tblresult.TotalMarks 
                                FROM tblresult 
                                JOIN tblstudents ON tblresult.StudentId=tblstudents.StudentId 
                                JOIN tblclasses ON tblclasses.id=tblresult.ClassId 
                                JOIN tblsubjects ON tblsubjects.id=tblresult.SubjectId";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                        
                        if($query->rowCount() > 0){ 
                            foreach($results as $r){ ?>
                        <tr>
                            <td><?php echo htmlentities($r->StudentName);?></td>
                            <td><?php echo htmlentities($r->ClassName);?> (<?php echo htmlentities($r->Section);?>)</td>
                            <td><?php echo htmlentities($r->SubjectName);?></td>
                            <td><?php echo htmlentities($r->marks);?></td>
                            <td><?php echo htmlentities($r->TotalMarks);?></td>
                            <td>
                                <a href="javascript:void(0);" onclick="confirmDelete('manage-results.php?delId=<?php echo $r->rid;?>')" class="btn btn-sm btn-danger" title="Delete Result">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php }} else { ?>
                            <tr><td colspan="6" style="text-align:center;">No Results Found</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="js/app.js"></script>
</body>
</html>