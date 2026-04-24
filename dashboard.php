<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) { header('location:index.php'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard | SRMS Pro</title>
    <link rel="stylesheet" href="css/modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand"><i class="fas fa-graduation-cap"></i> SRMS Pro</div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="dashboard.php" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a href="manage-classes.php" class="nav-link"><i class="fas fa-chalkboard"></i> Classes</a></li>
                <li class="nav-item"><a href="manage-subjects.php" class="nav-link"><i class="fas fa-book"></i> Subjects</a></li>
                <li class="nav-item"><a href="manage-students.php" class="nav-link"><i class="fas fa-users"></i> Students</a></li>
                <li class="nav-item"><a href="manage-results.php" class="nav-link"><i class="fas fa-poll"></i> Results</a></li>
                
                <!-- NEW LINKS -->
                <li class="nav-item"><a href="leaderboard.php" class="nav-link"><i class="fas fa-trophy"></i> Leaderboard</a></li>
                <li class="nav-item"><a href="manage-notices.php" class="nav-link"><i class="fas fa-bullhorn"></i> Notices</a></li>
                <li class="nav-item"><a href="admin-settings.php" class="nav-link"><i class="fas fa-cog"></i> Settings</a></li>
                <li class="nav-item"><a href="manage-admins.php" class="nav-link"><i class="fas fa-user-shield"></i> Admins</a></li>
                
                <li class="nav-item"><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <h1 class="page-title">Admin Dashboard</h1>
                <div class="user-controls">
                    <button class="theme-toggle" id="themeBtn" onclick="toggleTheme()"><i class="fas fa-moon"></i></button>
                    <div style="font-weight:600;">Welcome, Admin</div>
                </div>
            </header>

            <!-- Quick Stats -->
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:20px; margin-bottom: 30px;">
                <?php 
                $cnt1=$dbh->prepare("SELECT StudentId FROM tblstudents"); $cnt1->execute();
                $cnt2=$dbh->prepare("SELECT id FROM tblclasses"); $cnt2->execute();
                $cnt3=$dbh->prepare("SELECT id FROM tblsubjects"); $cnt3->execute();
                $cnt4=$dbh->prepare("SELECT DISTINCT StudentId FROM tblresult"); $cnt4->execute();
                ?>
                <div class="card" style="border-left:5px solid var(--primary); display:flex; justify-content:space-between; align-items:center;">
                    <div><div style="color:var(--text-muted); font-size:0.9rem;">Students</div><div style="font-size:2rem; font-weight:700;"><?php echo $cnt1->rowCount();?></div></div>
                    <i class="fas fa-users" style="font-size:2.5rem; opacity:0.1;"></i>
                </div>
                <div class="card" style="border-left:5px solid var(--secondary); display:flex; justify-content:space-between; align-items:center;">
                    <div><div style="color:var(--text-muted); font-size:0.9rem;">Classes</div><div style="font-size:2rem; font-weight:700;"><?php echo $cnt2->rowCount();?></div></div>
                    <i class="fas fa-chalkboard" style="font-size:2.5rem; opacity:0.1;"></i>
                </div>
                <div class="card" style="border-left:5px solid var(--success); display:flex; justify-content:space-between; align-items:center;">
                    <div><div style="color:var(--text-muted); font-size:0.9rem;">Subjects</div><div style="font-size:2rem; font-weight:700;"><?php echo $cnt3->rowCount();?></div></div>
                    <i class="fas fa-book" style="font-size:2.5rem; opacity:0.1;"></i>
                </div>
                <div class="card" style="border-left:5px solid var(--danger); display:flex; justify-content:space-between; align-items:center;">
                    <div><div style="color:var(--text-muted); font-size:0.9rem;">Results</div><div style="font-size:2rem; font-weight:700;"><?php echo $cnt4->rowCount();?></div></div>
                    <i class="fas fa-poll" style="font-size:2.5rem; opacity:0.1;"></i>
                </div>
            </div>

            <!-- Analytics Charts -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
                <div class="card">
                    <h3>Student Distribution</h3>
                    <canvas id="studentChart"></canvas>
                </div>
                <div class="card">
                    <h3>Subject Performance</h3>
                    <canvas id="marksChart"></canvas>
                </div>
            </div>

            <?php
            $q1 = "SELECT tblclasses.ClassName, tblclasses.Section, COUNT(tblstudents.StudentId) as count FROM tblstudents JOIN tblclasses ON tblstudents.ClassId = tblclasses.id GROUP BY tblclasses.id";
            $stmt1 = $dbh->prepare($q1); $stmt1->execute(); $classData = $stmt1->fetchAll(PDO::FETCH_ASSOC);

            $q2 = "SELECT tblsubjects.SubjectName, AVG(tblresult.marks) as avg_marks FROM tblresult JOIN tblsubjects ON tblresult.SubjectId = tblsubjects.id GROUP BY tblsubjects.id";
            $stmt2 = $dbh->prepare($q2); $stmt2->execute(); $subjectData = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            ?>

        </main>
    </div>
    <script src="js/app.js"></script>
    
    <script>
        const classLabels = <?php echo json_encode(array_map(function($i){ return $i['ClassName'].' ('.$i['Section'].')'; }, $classData)); ?>;
        const classCounts = <?php echo json_encode(array_column($classData, 'count')); ?>;
        const subjectLabels = <?php echo json_encode(array_column($subjectData, 'SubjectName')); ?>;
        const subjectAvg = <?php echo json_encode(array_column($subjectData, 'avg_marks')); ?>;

        new Chart(document.getElementById('studentChart'), {
            type: 'doughnut',
            data: { labels: classLabels, datasets: [{ label: 'Students', data: classCounts, backgroundColor: ['#6366f1', '#8b5cf6', '#ec4899', '#10b981', '#f59e0b'], borderWidth: 0 }] },
            options: { responsive: true }
        });

        new Chart(document.getElementById('marksChart'), {
            type: 'bar',
            data: { labels: subjectLabels, datasets: [{ label: 'Average Score', data: subjectAvg, backgroundColor: '#6366f1', borderRadius: 5 }] },
            options: { scales: { y: { beginAtZero: true, max: 100 } } }
        });
    </script>
</body>
</html>