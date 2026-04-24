<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) { header('location:index.php'); }

if(isset($_POST['import'])){
    // Check if file is uploaded
    if($_FILES['csvfile']['size'] > 0){
        $file = $_FILES['csvfile']['tmp_name'];
        $handle = fopen($file, "r");
        
        // Skip the first line (Header)
        fgetcsv($handle);
        
        $count = 0;
        while(($filesop = fgetcsv($handle, 1000, ",")) !== false){
            $name = $filesop[0];
            $roll = $filesop[1];
            $email = $filesop[2];
            $gender = $filesop[3];
            $class = $filesop[4]; // Needs to be the numeric Class ID
            $dob = $filesop[5];
            
            // Insert into DB
            $sql = "INSERT INTO tblstudents(StudentName,RollId,StudentEmail,Gender,ClassId,DOB) VALUES(:name,:roll,:email,:gender,:class,:dob)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':name', $name, PDO::PARAM_STR);
            $query->bindParam(':roll', $roll, PDO::PARAM_STR);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->bindParam(':gender', $gender, PDO::PARAM_STR);
            $query->bindParam(':class', $class, PDO::PARAM_STR);
            $query->bindParam(':dob', $dob, PDO::PARAM_STR);
            
            try {
                $query->execute();
                $count++;
            } catch(Exception $e) {
                // Skip duplicates silently or log errors
            }
        }
        $msg = "$count Students Imported Successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Import Students</title>
    <link rel="stylesheet" href="css/modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="brand">SRMS Pro</div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="manage-students.php" class="nav-link"><i class="fas fa-arrow-left"></i> Back to List</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h1 class="page-title">Bulk Import Students</h1>
            
            <div class="card" style="max-width:600px;">
                <?php if(isset($msg)){ echo "<div style='color:green; margin-bottom:10px;'>$msg</div>"; } ?>
                
                <div style="background:#eef2ff; padding:15px; border-radius:8px; margin-bottom:20px; font-size:0.9rem;">
                    <strong>Instructions:</strong><br>
                    1. Create an Excel file and Save As <strong>.csv</strong> (Comma Separated Values).<br>
                    2. The columns must be in this order:<br>
                    <code>Name, RollID, Email, Gender, ClassID, DOB(yyyy-mm-dd)</code><br>
                    3. <strong>ClassID</strong> must be the number ID of the class (check Manage Classes page).
                </div>

                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label">Upload CSV File</label>
                        <input type="file" name="csvfile" class="form-control" accept=".csv" required>
                    </div>
                    <button type="submit" name="import" class="btn btn-primary">Import Now</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>