<?php
session_start();
include('includes/config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result Sheet</title>
    <link rel="stylesheet" href="css/modern.css">
</head>
<body style="background:var(--bg-body); padding:2rem;">
    <?php
    $rollid = $_POST['rollid']; 
    $classid = $_POST['class'];
    
    // Fetch Student Info with Image
    $q = "SELECT tblstudents.StudentName,tblstudents.RollId,tblstudents.StudentImage,tblstudents.RegDate,tblstudents.StudentId,tblstudents.Status,tblclasses.ClassName,tblclasses.Section 
          FROM tblstudents 
          JOIN tblclasses ON tblclasses.id=tblstudents.ClassId 
          WHERE tblstudents.RollId=:rollid AND tblstudents.ClassId=:classid";
    $stmt = $dbh->prepare($q); 
    $stmt->bindParam(':rollid',$rollid,PDO::PARAM_STR); 
    $stmt->bindParam(':classid',$classid,PDO::PARAM_STR); 
    $stmt->execute();
    $studentDetails = $stmt->fetch(PDO::FETCH_OBJ);
    
    if($stmt->rowCount() > 0) {
        // logic to check if image exists or use placeholder
        $profile_pic = !empty($studentDetails->StudentImage) ? "images/".$studentDetails->StudentImage : "https://via.placeholder.com/100";
    ?>
    <div class="card" style="max-width:800px; margin:0 auto;">
        <h2 style="text-align:center; margin-bottom:20px;">Student Result Sheet</h2>
        
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; padding-bottom:15px; border-bottom:1px solid #eee;">
            <div style="display:flex; gap:15px; align-items:center;">
                <!-- Profile Image Display -->
                <img src="<?php echo $profile_pic; ?>" style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:2px solid var(--primary);">
                <div>
                    <h3 style="margin:0;"><?php echo htmlentities($studentDetails->StudentName);?></h3>
                    <p style="margin:5px 0 0 0; color:var(--text-muted);">Roll ID: <?php echo htmlentities($studentDetails->RollId);?></p>
                </div>
            </div>
            <div style="text-align:right;">
                <p><strong>Class:</strong> <?php echo htmlentities($studentDetails->ClassName);?> (<?php echo htmlentities($studentDetails->Section);?>)</p>
                <p><strong>Date:</strong> <?php echo date("d-m-Y");?></p>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Marks Obtained</th>
                    <th>Total Marks</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $q2 = "SELECT tblsubjects.SubjectName, tblresult.marks, tblresult.TotalMarks 
                       FROM tblresult 
                       JOIN tblsubjects ON tblsubjects.id=tblresult.SubjectId 
                       WHERE tblresult.StudentId=:sid";
                $stmt2 = $dbh->prepare($q2); 
                $stmt2->bindParam(':sid',$studentDetails->StudentId,PDO::PARAM_STR); 
                $stmt2->execute();
                $results = $stmt2->fetchAll(PDO::FETCH_OBJ); 
                
                $cnt=1; 
                $totalObtained=0; 
                $totalMax=0;
                
                foreach($results as $row) {
                    $totalObtained += $row->marks;
                    $totalMax += $row->TotalMarks;
                ?>
                <tr>
                    <td><?php echo htmlentities($cnt);?></td>
                    <td><?php echo htmlentities($row->SubjectName);?></td>
                    <td><?php echo htmlentities($row->marks);?></td>
                    <td><?php echo htmlentities($row->TotalMarks);?></td>
                </tr>
                <?php $cnt++; } ?>

                <!-- GRADE CALCULATIONS -->
                <?php 
                    $percentage = ($totalMax > 0) ? round(($totalObtained/$totalMax)*100, 2) : 0;
                    
                    // Grade Logic
                    $grade = "";
                    $color = "black";
                    if($percentage >= 90){ $grade = "A+ (Outstanding)"; $color = "green"; }
                    else if($percentage >= 80){ $grade = "A (Excellent)"; $color = "green"; }
                    else if($percentage >= 70){ $grade = "B (Good)"; $color = "blue"; }
                    else if($percentage >= 60){ $grade = "C (Average)"; $color = "orange"; }
                    else if($percentage >= 50){ $grade = "D (Pass)"; $color = "orange"; }
                    else { $grade = "F (Fail)"; $color = "red"; }
                ?>
                
                <tr style="font-weight:bold; background:var(--bg-body);">
                    <td colspan="2" style="text-align:right">Total</td>
                    <td><?php echo htmlentities($totalObtained); ?></td>
                    <td><?php echo htmlentities($totalMax); ?></td>
                </tr>

                <tr style="font-weight:bold;">
                    <td colspan="2" style="text-align:right">Percentage</td>
                    <td colspan="2"><?php echo $percentage . "%"; ?></td>
                </tr>

                <tr style="font-weight:bold; font-size: 1.2rem;">
                    <td colspan="2" style="text-align:right">Final Grade</td>
                    <td colspan="2" style="color: <?php echo $color; ?>;"><?php echo $grade; ?></td>
                </tr>

            </tbody>
        </table>
        
        <div style="margin-top:20px; text-align:center;">
            <button onclick="window.print()" class="btn btn-primary">Print Result</button>
            <a href="find-result.php" class="btn">Back</a>
        </div>
    </div>
    <?php } else { echo "<div class='card' style='max-width:400px; margin:50px auto; text-align:center; color:red;'>Invalid Roll ID or Class<br><a href='find-result.php'>Try Again</a></div>"; } ?>
</body>
</html>