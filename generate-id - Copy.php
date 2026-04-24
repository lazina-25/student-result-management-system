<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) { header('location:index.php'); }

$id = intval($_GET['id']);
$sql = "SELECT tblstudents.*, tblclasses.ClassName, tblclasses.Section FROM tblstudents JOIN tblclasses ON tblstudents.ClassId=tblclasses.id WHERE StudentId=:id";
$query = $dbh->prepare($sql);
$query->bindParam(':id',$id,PDO::PARAM_STR);
$query->execute();
$s = $query->fetch(PDO::FETCH_OBJ);

$img = !empty($s->StudentImage) ? "images/".$s->StudentImage : "https://via.placeholder.com/150";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student ID Card</title>
    <link rel="stylesheet" href="css/modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .id-card-container {
            width: 350px;
            height: 520px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            overflow: hidden;
            position: relative;
            text-align: center;
            border: 1px solid #ddd;
            margin: 50px auto;
        }
        .id-header {
            background: var(--primary);
            height: 100px;
            border-bottom-left-radius: 50% 20px;
            border-bottom-right-radius: 50% 20px;
        }
        .school-name {
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            padding-top: 20px;
            text-transform: uppercase;
        }
        .student-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
            margin-top: -60px;
            background: white;
        }
        .info-section { padding: 20px; }
        .st-name { font-size: 1.4rem; font-weight: bold; color: #333; margin: 10px 0 5px; }
        .st-role { color: var(--primary); font-weight: 600; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 1px; }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            font-size: 0.95rem;
            color: #555;
        }
        .id-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: #f8f9fa;
            padding: 15px;
            font-size: 0.8rem;
            color: #777;
            border-top: 1px solid #eee;
        }
        .barcode {
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 5px;
            font-weight: bold;
            font-size: 1.2rem;
            margin-top: 10px;
        }
    </style>
</head>
<body style="background:var(--bg-body);">

    <div class="id-card-container" id="printableArea">
        <div class="id-header">
            <div class="school-name"><i class="fas fa-graduation-cap"></i> SRMS PRO High School</div>
        </div>
        
        <img src="<?php echo $img; ?>" class="student-photo">
        
        <div class="info-section">
            <div class="st-name"><?php echo htmlentities($s->StudentName); ?></div>
            <div class="st-role">Student</div>

            <div style="margin-top: 20px; text-align: left;">
                <div class="detail-row">
                    <span>Roll ID:</span>
                    <span style="font-weight:bold;"><?php echo htmlentities($s->RollId); ?></span>
                </div>
                <div class="detail-row">
                    <span>Class:</span>
                    <span style="font-weight:bold;"><?php echo htmlentities($s->ClassName); ?> (<?php echo htmlentities($s->Section); ?>)</span>
                </div>
                <div class="detail-row">
                    <span>Gender:</span>
                    <span><?php echo htmlentities($s->Gender); ?></span>
                </div>
                <div class="detail-row">
                    <span>DOB:</span>
                    <span><?php echo htmlentities($s->DOB); ?></span>
                </div>
            </div>
            
            <div class="barcode">||| |||| || |||||</div>
        </div>

        <div class="id-footer">
            <div>Valid for Academic Year 2024-2025</div>
            <div>www.srms-pro.com</div>
        </div>
    </div>

    <div style="text-align:center; margin-bottom: 50px;">
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print ID Card</button>
        <a href="manage-students.php" class="btn" style="background:#ddd; margin-left:10px;">Back</a>
    </div>

</body>
</html>