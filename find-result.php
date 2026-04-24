<?php
session_start();
include('includes/config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Result</title>
    <link rel="stylesheet" href="css/modern.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="login-wrapper">
    <div class="card login-card">
        <div style="text-align:center;"><h2>Check Your Result</h2></div>
        <form action="result.php" method="post" style="margin-top:20px;">
            <div class="form-group"><label class="form-label">Roll Id</label><input type="text" name="rollid" class="form-control" required></div>
            <div class="form-group"><label class="form-label">Class</label>
                <select name="class" class="form-control" required>
                    <option value="">Select Class</option>
                    <?php $sql = "SELECT * from tblclasses"; $query = $dbh->prepare($sql); $query->execute();
                    $results=$query->fetchAll(PDO::FETCH_OBJ); foreach($results as $result){ ?>
                    <option value="<?php echo $result->id;?>"><?php echo $result->ClassName;?> (<?php echo $result->Section;?>)</option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Search</button>
            <div style="text-align:center; margin-top:15px;"><a href="index.php">Admin Login</a></div>
        </form>
    </div>
</body>
</html>