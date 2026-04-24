<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) { header('location:index.php'); }

if(isset($_POST['submit'])){
    $studentname=$_POST['fullanme'];
    $rollid=$_POST['rollid']; 
    $studentemail=$_POST['emailid']; 
    $gender=$_POST['gender']; 
    $classid=$_POST['class']; 
    $dob=$_POST['dob']; 
    
    // 1. Generate Default Password (MD5 of RollId)
    // Example: If Roll ID is "101", Password is "101"
    $password = md5($rollid);

    // Image Upload Logic
    $imgfile = $_FILES["studentimage"]["name"];
    $final_file = ""; 

    if(!empty($imgfile)){
        $extension = substr($imgfile,strlen($imgfile)-4,strlen($imgfile));
        $allowed_extensions = array(".jpg","jpeg",".png",".gif");
        if(in_array($extension,$allowed_extensions)) {
            $final_file = md5($imgfile).time().$extension;
            move_uploaded_file($_FILES["studentimage"]["tmp_name"],"images/".$final_file);
        }
    }

    // Insert Query - Now includes 'Password'
    $sql="INSERT INTO tblstudents(StudentName,RollId,StudentEmail,Gender,ClassId,DOB,StudentImage,Password) VALUES(:studentname,:rollid,:studentemail,:gender,:classid,:dob,:img,:pass)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':studentname',$studentname,PDO::PARAM_STR);
    $query->bindParam(':rollid',$rollid,PDO::PARAM_STR);
    $query->bindParam(':studentemail',$studentemail,PDO::PARAM_STR);
    $query->bindParam(':gender',$gender,PDO::PARAM_STR);
    $query->bindParam(':classid',$classid,PDO::PARAM_STR);
    $query->bindParam(':dob',$dob,PDO::PARAM_STR);
    $query->bindParam(':img',$final_file,PDO::PARAM_STR);
    $query->bindParam(':pass',$password,PDO::PARAM_STR); // Bind Password
    
    try{ 
        $query->execute(); 
        $msg="Student Added Successfully! Their default password is their Roll ID."; 
    } catch(Exception $e){ 
        $error="Error: Duplicate Roll ID or DB Error"; 
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Add Student</title><link rel="stylesheet" href="css/modern.css"></head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="brand">SRMS Pro</div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="manage-students.php" class="nav-link"><i class="fas fa-arrow-left"></i> Back to List</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h1 class="page-title">Register Student</h1>
            <div class="card" style="max-width:600px;">
                <?php if(isset($msg)){ echo "<div style='color:green'>$msg</div>"; } ?>
                <?php if(isset($error)){ echo "<div style='color:red'>$error</div>"; } ?>
                
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group"><label class="form-label">Full Name</label><input type="text" name="fullanme" class="form-control" required></div>
                    <div class="form-group"><label class="form-label">Roll ID (Will be default Password)</label><input type="text" name="rollid" class="form-control" required></div>
                    <div class="form-group"><label class="form-label">Email</label><input type="email" name="emailid" class="form-control" required></div>
                    <div class="form-group"><label class="form-label">Gender</label>
                        <input type="radio" name="gender" value="Male" checked> Male &nbsp; <input type="radio" name="gender" value="Female"> Female
                    </div>
                    <div class="form-group"><label class="form-label">Class</label>
                        <select name="class" class="form-control" required>
                            <option value="">Select Class</option>
                            <?php $sql = "SELECT * from tblclasses"; $query = $dbh->prepare($sql); $query->execute();
                            $results=$query->fetchAll(PDO::FETCH_OBJ); foreach($results as $result){ ?>
                            <option value="<?php echo $result->id;?>"><?php echo $result->ClassName;?> Section-<?php echo $result->Section;?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">DOB</label><input type="date" name="dob" class="form-control"></div>
                    <div class="form-group"><label class="form-label">Profile Photo</label><input type="file" name="studentimage" class="form-control" accept="image/*"></div>

                    <button type="submit" name="submit" class="btn btn-primary">Register Student</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>