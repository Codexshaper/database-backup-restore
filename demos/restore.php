<?php
require 'vendor/autoload.php';
$restore_path = "";
if (isset($_GET['restore_path'])) {
    $restore_path = $_GET['restore_path'];
}
if (isset($_POST['restore'])) {
    $options = [
        'host'        => $_POST['host'],
        'port'        => $_POST['port'],
        'dbName'      => $_POST['dbname'],
        'username'    => $_POST['username'],
        'password'    => $_POST['password'],
        'restorePath' => $_POST['restore_path'], // /path/to/restores/mysql/dump
    ];
    try {
        $dumper = new \CodexShaper\Dumper\Drivers\MysqlDumper($options);
        $dumper->restore();
        header('Location: index.php?restore=success&message=Database restored successfully');
    } catch (Exception $ex) {
        header('Location: index.php?restore=fail&message=' . $ex->getMessage());
    }
}
?>
<!DOCTYPE html>
<html>
<head>
 <title>Database restore and Restore</title>
 <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body>
 <div class="container">
  <div class="card mt-5 mx-auto" style="width: 50rem;">
   <div class="card-body">
    <h3 class="card-title">Welcome to Database restore and restore</h3>
    <form method="POST" action="restore.php">
     <div class="form-group">
      <label for="host">Host</label>
      <input type="text" name="host" id="host" class="form-control" value="localhost">
     </div>
     <div class="form-group">
      <label for="port">Port</label>
      <input type="text" name="port" id="port" class="form-control" value="3306">
     </div>
     <div class="form-group">
      <label for="dbname">Database Name</label>
      <input type="text" name="dbname" id="dbname" class="form-control" value="laravel">
     </div>
     <div class="form-group">
      <label for="username">User Name</label>
      <input type="text" name="username" id="username" class="form-control" value="root">
     </div>
     <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" class="form-control">
     </div>
     <div class="form-group">
      <label for="restore_path">Restore file</label>
      <input type="text" name="restore_path" id="restore_path" class="form-control" value="<?php echo $restore_path ?>">
     </div>
     <div class="form-group">
      <input type="submit" name="restore" value="Restore" class="btn btn-success">
      <a href="index.php" class="btn btn-info">Home</a>
     </div>
    </form>
   </div>
  </div>
 </div>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>