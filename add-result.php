<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) { header('location:index.php'); }
if(isset($_POST['submit'])){
    $class=$_POST['class']; $studentid=$_POST['studentid'];
    $marks=$_POST['marks']; $totalmarks=$_POST['totalmarks']; $subjectids=$_POST['sid'];
    for($i=0; $i<count($marks); $i++){
        $mrk = $marks[$i]; $tm = $totalmarks[$i]; $sid = $subjectids[$i];
        $chk="SELECT * FROM tblresult WHERE StudentId=:studentid AND SubjectId=:sid";
        $qchk=$dbh->prepare($chk);
        $qchk->bindParam(':studentid',$studentid,PDO::PARAM_STR); $qchk->bindParam(':sid',$sid,PDO::PARAM_STR);
        $qchk->execute();
        if($qchk->rowCount() > 0){
            $sql="UPDATE tblresult SET marks=:mrk, TotalMarks=:tm WHERE StudentId=:studentid AND SubjectId=:sid";
        } else {
            $sql="INSERT INTO tblresult(StudentId,ClassId,SubjectId,marks,TotalMarks) VALUES(:studentid,:class,:sid,:mrk,:tm)";
        }
        $query=$dbh->prepare($sql);
        $query->bindParam(':studentid',$studentid,PDO::PARAM_STR); $query->bindParam(':class',$class,PDO::PARAM_STR);
        $query->bindParam(':sid',$sid,PDO::PARAM_STR); $query->bindParam(':mrk',$mrk,PDO::PARAM_STR); $query->bindParam(':tm',$tm,PDO::PARAM_STR);
        $query->execute();
    }
    $msg="Result Declared Successfully";
}
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Add Result</title><link rel="stylesheet" href="css/modern.css"></head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="brand">SRMS Pro</div>
            <ul class="nav-menu"><li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="fas fa-arrow-left"></i> Back</a></li></ul>
        </aside>
        <main class="main-content">
            <h1 class="page-title">Declare Result</h1>
            <div class="card">
                <?php if(isset($msg)) echo "<div style='color:green'>$msg</div>"; ?>
                <form method="post"><div class="form-group"><label class="form-label">Select Class</label>
                    <select name="class" class="form-control" onchange="this.form.submit()" required>
                        <option value="">Select Class</option>
                        <?php $sql="SELECT * FROM tblclasses"; $q=$dbh->prepare($sql); $q->execute(); $res=$q->fetchAll(PDO::FETCH_OBJ);
                        foreach($res as $r){ ?><option value="<?php echo $r->id;?>" <?php if(isset($_POST['class']) && $_POST['class']==$r->id) echo "selected";?>><?php echo $r->ClassName;?> (<?php echo $r->Section;?>)</option><?php } ?>
                    </select></div>
                </form>
                <?php if(isset($_POST['class'])){ ?>
                <form method="post">
                    <input type="hidden" name="class" value="<?php echo $_POST['class'];?>">
                    <div class="form-group"><label class="form-label">Select Student</label>
                        <select name="studentid" class="form-control" required>
                            <?php $cid=$_POST['class']; $sql="SELECT * FROM tblstudents WHERE ClassId=:cid"; $q=$dbh->prepare($sql); $q->bindParam(':cid',$cid,PDO::PARAM_STR); $q->execute(); $res=$q->fetchAll(PDO::FETCH_OBJ);
                            foreach($res as $r){ ?><option value="<?php echo $r->StudentId;?>"><?php echo $r->StudentName;?></option><?php } ?>
                        </select>
                    </div>
                    <h3>Enter Marks</h3>
                    <?php $sql="SELECT * FROM tblsubjects"; $q=$dbh->prepare($sql); $q->execute(); $subs=$q->fetchAll(PDO::FETCH_OBJ);
                    foreach($subs as $s){ ?>
                        <div style="display:flex; gap:10px; margin-bottom:10px; align-items:center;">
                            <div style="width:200px; font-weight:600;"><?php echo $s->SubjectName;?></div>
                            <input type="hidden" name="sid[]" value="<?php echo $s->id;?>">
                            <input type="number" name="marks[]" placeholder="Obtained" class="form-control" required>
                            <input type="number" name="totalmarks[]" value="100" placeholder="Total" class="form-control" required>
                        </div>
                    <?php } ?>
                    <button type="submit" name="submit" class="btn btn-primary" style="margin-top:20px;">Declare Result</button>
                </form>
                <?php } ?>
            </div>
        </main>
    </div>
</body>
</html>