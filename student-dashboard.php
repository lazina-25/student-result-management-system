<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['slogin'])==0) { header('location:student-login.php'); }

$sid = $_SESSION['student_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Dashboard</title>
    <link rel="stylesheet" href="css/modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">Student Panel</div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="student-dashboard.php" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a href="student-change-password.php" class="nav-link"><i class="fas fa-key"></i> Change Password</a></li>
                <li class="nav-item"><a href="student-logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <h1 class="page-title">My Dashboard</h1>
                <div class="user-controls">
                    <button class="theme-toggle" id="themeBtn" onclick="toggleTheme()"><i class="fas fa-moon"></i></button>
                    <div style="font-weight:600;"><?php echo $_SESSION['student_name']; ?></div>
                </div>
            </header>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                
                <!-- LEFT COLUMN: Profile & Results -->
                <div>
                    <!-- Profile Card -->
                    <?php 
                    $sql = "SELECT tblstudents.*, tblclasses.ClassName, tblclasses.Section 
                            FROM tblstudents 
                            JOIN tblclasses ON tblstudents.ClassId = tblclasses.id 
                            WHERE StudentId=:sid";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':sid', $sid, PDO::PARAM_STR);
                    $query->execute();
                    $student = $query->fetch(PDO::FETCH_OBJ);
                    $img = !empty($student->StudentImage) ? "images/".$student->StudentImage : "https://via.placeholder.com/150";
                    ?>
                    
                    <div class="card" style="display:flex; gap:30px; align-items:center;">
                        <img src="<?php echo $img; ?>" style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:4px solid var(--primary);">
                        <div>
                            <h2 style="margin-bottom:5px;"><?php echo htmlentities($student->StudentName); ?></h2>
                            <p style="color:var(--text-muted); margin-bottom:5px;">Roll ID: <strong><?php echo htmlentities($student->RollId); ?></strong></p>
                            <p style="color:var(--text-muted);">Class: <strong><?php echo htmlentities($student->ClassName); ?> (<?php echo htmlentities($student->Section); ?>)</strong></p>
                        </div>
                    </div>

                    <!-- Result Table -->
                    <div class="card">
                        <h3><i class="fas fa-poll"></i> Exam Results</h3>
                        <table class="table">
                            <thead>
                                <tr><th>Subject</th><th>Marks</th><th>Total</th><th>Grade</th></tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sql = "SELECT tblsubjects.SubjectName, tblresult.marks, tblresult.TotalMarks 
                                        FROM tblresult 
                                        JOIN tblsubjects ON tblresult.SubjectId = tblsubjects.id 
                                        WHERE tblresult.StudentId=:sid";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':sid', $sid, PDO::PARAM_STR);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                
                                $totalObtained = 0; $totalMax = 0;
                                
                                if($query->rowCount() > 0) {
                                    foreach($results as $row) {
                                        $totalObtained += $row->marks;
                                        $totalMax += $row->TotalMarks;
                                        $pct = ($row->TotalMarks > 0) ? ($row->marks/$row->TotalMarks)*100 : 0;
                                        
                                        $grade = "F"; $color="red";
                                        if($pct >= 90) { $grade="A+"; $color="green"; }
                                        elseif($pct >= 80) { $grade="A"; $color="green"; }
                                        elseif($pct >= 70) { $grade="B"; $color="blue"; }
                                        elseif($pct >= 60) { $grade="C"; $color="orange"; }
                                        elseif($pct >= 50) { $grade="D"; $color="orange"; }
                                ?>
                                <tr>
                                    <td><?php echo htmlentities($row->SubjectName); ?></td>
                                    <td><?php echo htmlentities($row->marks); ?></td>
                                    <td><?php echo htmlentities($row->TotalMarks); ?></td>
                                    <td style="color:<?php echo $color; ?>; font-weight:bold;"><?php echo $grade; ?></td>
                                </tr>
                                <?php } } else { ?>
                                    <tr><td colspan="4" style="text-align:center;">No results declared yet.</td></tr>
                                <?php } ?>
                                
                                <!-- Summary Row -->
                                <?php if($totalMax > 0) { 
                                    $finalPct = round(($totalObtained/$totalMax)*100, 2);
                                ?>
                                <tr style="background:var(--bg-body); font-weight:bold;">
                                    <td style="text-align:right;">Total:</td>
                                    <td><?php echo $totalObtained; ?> / <?php echo $totalMax; ?></td>
                                    <td style="text-align:right;">Percentage:</td>
                                    <td><?php echo $finalPct; ?>%</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Notice Board -->
                <div>
                    <div class="card" style="background:#fff8e1; border-color:#ffe082;">
                        <h3 style="color:#f57f17;"><i class="fas fa-bullhorn"></i> Notice Board</h3>
                        <div style="max-height: 400px; overflow-y: auto;">
                            <?php 
                            $sql = "SELECT * from tblnotices ORDER BY CreationDate DESC LIMIT 5";
                            $query = $dbh->prepare($sql);
                            $query->execute();
                            $notices=$query->fetchAll(PDO::FETCH_OBJ);
                            if($query->rowCount() > 0){
                                foreach($notices as $notice){ ?>
                                <div style="background:white; padding:15px; border-radius:8px; margin-bottom:15px; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
                                    <div style="font-weight:bold; color:#333;"><?php echo htmlentities($notice->NoticeTitle);?></div>
                                    <p style="font-size:0.85rem; color:#666; margin:5px 0;"><?php echo htmlentities($notice->NoticeDetails);?></p>
                                    <small style="color:#999; font-size:0.75rem;"><i class="far fa-clock"></i> <?php echo htmlentities($notice->CreationDate);?></small>
                                </div>
                            <?php }} else { echo "No recent notices."; } ?>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
    <script src="js/app.js"></script>
</body>
</html>