<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) { header('location:index.php'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Class Leaderboard</title>
    <link rel="stylesheet" href="css/modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .rank-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            border: 1px solid #eee;
            transition: transform 0.3s;
        }
        .rank-card:hover { transform: translateY(-5px); }
        
        .rank-1 { border-top: 5px solid #FFD700; order: 2; transform: scale(1.1); z-index: 10; } /* Gold */
        .rank-2 { border-top: 5px solid #C0C0C0; order: 1; margin-top: 20px; } /* Silver */
        .rank-3 { border-top: 5px solid #CD7F32; order: 3; margin-top: 20px; } /* Bronze */
        
        .medal-icon { font-size: 3rem; margin-bottom: 10px; display: block; }
        .rank-1 .medal-icon { color: #FFD700; text-shadow: 0 2px 10px rgba(255, 215, 0, 0.4); }
        .rank-2 .medal-icon { color: #C0C0C0; }
        .rank-3 .medal-icon { color: #CD7F32; }

        .winner-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 10px;
        }
        
        .score-badge {
            background: var(--primary);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
            display: inline-block;
            margin-top: 10px;
        }

        .podium-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 20px;
            margin-top: 40px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand">SRMS Pro</div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a href="manage-students.php" class="nav-link"><i class="fas fa-users"></i> Students</a></li>
                <li class="nav-item"><a href="manage-results.php" class="nav-link"><i class="fas fa-poll"></i> Results</a></li>
                <li class="nav-item"><a href="leaderboard.php" class="nav-link active"><i class="fas fa-trophy"></i> Leaderboard</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <h1 class="page-title">Top Performers</h1>
            </header>

            <div class="card">
                <form method="post" style="display:flex; gap:10px; align-items:center;">
                    <label style="font-weight:bold;">Select Class to View Rankings:</label>
                    <select name="classid" class="form-control" style="width:auto; flex:1;" required>
                        <option value="">Select Class</option>
                        <?php 
                        $sql = "SELECT * from tblclasses";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                        foreach($results as $result){ ?>
                        <option value="<?php echo $result->id;?>" <?php if(isset($_POST['classid']) && $_POST['classid']==$result->id){ echo "selected";} ?>>
                            <?php echo $result->ClassName;?> Section-<?php echo $result->Section;?>
                        </option>
                        <?php } ?>
                    </select>
                    <button type="submit" name="search" class="btn btn-primary">Show Leaders</button>
                </form>
            </div>

            <?php if(isset($_POST['search'])) { 
                $cid = $_POST['classid'];
                
                // COMPLEX QUERY: Sum marks for each student in the class, Order by Total DESC, Limit 3
$sql = "SELECT 
    s.StudentId,
    s.StudentName,
    s.RollId,
    s.StudentImage,
    SUM(r.marks) AS total_obtained,
    COUNT(r.marks) * 100 AS total_max
FROM tblresult r
JOIN tblstudents s ON r.StudentId = s.StudentId
WHERE r.ClassId = :cid
GROUP BY s.StudentId, s.StudentName, s.RollId, s.StudentImage
ORDER BY total_obtained DESC
LIMIT 3";

                        
           


                $query = $dbh->prepare($sql);
                $query->bindParam(':cid',$cid,PDO::PARAM_STR);
                $query->execute();
                $leaders = $query->fetchAll(PDO::FETCH_OBJ);
                
                if(count($leaders) > 0) {
            ?>
                <div class="podium-container">
                    <?php 
                    $rank = 1;
                    foreach($leaders as $leader) {
                        $img = !empty($leader->StudentImage) ? "images/".$leader->StudentImage : "https://via.placeholder.com/150";
                        
                        // Assign Class based on rank
                        $rankClass = "rank-1"; // Default Gold
                        $medal = "fa-medal";
                        if($rank == 2) { $rankClass = "rank-2"; }
                        if($rank == 3) { $rankClass = "rank-3"; }
                        
                        // Calc %
                        $pct = ($leader->total_max > 0) ? round(($leader->total_obtained/$leader->total_max)*100, 1) : 0;
                    ?>
                    
                    <div class="rank-card <?php echo $rankClass; ?>" style="width: 250px;">
                        <i class="fas <?php echo $medal; ?> medal-icon"></i>
                        <img src="<?php echo $img; ?>" class="winner-img">
                        <h2 style="font-size:1.2rem; margin:10px 0 5px;"><?php echo htmlentities($leader->StudentName); ?></h2>
                        <div style="color:#777; font-size:0.9rem;"><?php echo htmlentities($leader->RollId); ?></div>
                        
                        <div class="score-badge">
                            <?php echo $leader->total_obtained; ?> / <?php echo $leader->total_max; ?>
                        </div>
                        <div style="margin-top:5px; font-weight:bold; color:var(--text-muted);">
                            <?php echo $pct; ?>%
                        </div>
                        <div style="margin-top:15px; font-weight:bold; font-size:1.5rem; opacity:0.1;">
                            #<?php echo $rank; ?>
                        </div>
                    </div>
                    
                    <?php $rank++; } ?>
                </div>
            <?php } else { echo "<div style='text-align:center; margin-top:50px; color:#888;'>No results found for this class yet.</div>"; } } ?>
        </main>
    </div>
    <script src="js/app.js"></script>
</body>
</html>