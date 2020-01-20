<?php
require_once 'vendor/autoload.php';
if (isset($_POST['backup'])) {
    $filename        = 'backup_' . date('G_a_m_d_y_h_i_s') . ".sql";
    $directory       = $_POST['destination'] ?? '';
    $destinationPath = $directory . DIRECTORY_SEPARATOR . $filename;
    $options         = [
        'host'            => $_POST['host'],
        'port'            => $_POST['port'],
        'dbName'          => $_POST['dbname'],
        'username'        => $_POST['username'],
        'password'        => $_POST['password'],
        'destinationPath' => $destinationPath, // /path/to/backups/mysql/dump
    ];
    try {
        $dumper = new \CodexShaper\Dumper\Drivers\MysqlDumper($options);
        $dumper->dump();
        header('Location: index.php?backup=success&message=Database backedup successfully');
    } catch (Exception $ex) {
        header('Location: index.php?backup=fail&message=' . $ex->getMessage());
    }
}
?>
<!DOCTYPE html>
<html>
<head>
 <title>Database Backup and Restore</title>
 <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body>
 <div class="container">
  <div class="card mt-5 mx-auto" style="width: 50rem;">
   <div class="card-body">
    <h3 class="card-title">Welcome to Database backup and restore</h3>
    <a href="index.php" class="btn btn-info">Home</a>
    <form method="POST" action="backup.php">
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
      <label for="destination">Destination path</label>
      <input type="text" name="destination" id="destination" class="form-control">
     </div>
     <div class="form-group">
      <input type="submit" name="backup" value="Backup" class="btn btn-success">
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
