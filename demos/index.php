<?php
require_once 'vendor/autoload.php';
$successMsg = false;
$errorMsg   = false;
$message    = "";
if (isset($_GET['backup']) && $_GET['backup'] == 'success') {
    $successMsg = true;
    $message    = "Database backedup successfully";
}
if (isset($_GET['backup']) && $_GET['backup'] == 'fail') {
    $errorMsg = true;
    $message  = "Database backedup failed";
}
if (isset($_GET['restore']) && $_GET['restore'] == 'success') {
    $successMsg = true;
    $message    = "Database restored successfully";
}
if (isset($_GET['restore']) && $_GET['restore'] == 'fail') {
    $errorMsg = true;
    $message  = "Database restored failed";
}
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>
<!DOCTYPE html>
<html>
<head>
 <title>Backup lists</title>
 <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body>
 <div class="container">
  <div class="card mt-5 mx-auto">
    <div class="card-body">
     <div class="success">
      <?php if ($successMsg): ?>
       <p class="alert alert-success"><?php echo $message; ?></p>
      <?php endif;?>
     </div>
     <div class="error">
      <?php if ($errorMsg): ?>
       <p class="alert alert-danger"><?php echo $message; ?></p>
      <?php endif;?>
     </div>
     <a href="backup.php" class="btn btn-success my-3">Create a new backup</a>
     <a href="index.php" class="btn btn-info my-3">Reload</a>
<?php $files = glob(__DIR__ . '/backups/*');?>
     <?php if (count($files)): ?>
<table class="table table-bordered">
     <thead>
       <tr>
         <th scope="col">#</th>
         <th scope="col">Name</th>
         <th scope="col">Directory</th>
         <th scope="col">Actions</th>
       </tr>
     </thead>
     <tbody>
   <?php foreach ($files as $key => $file): ?>
    <?php $info = pathinfo($file);?>
    <tr>
      <th scope="row"><?php echo $key + 1 ?></th>
      <td><?php echo $info['basename'] ?></td>
      <td><?php echo $info['dirname'] ?></td>
      <td class="d-flex">
       <form action="restore.php" method="GET" class="inline-flex mr-2">
        <input type="hidden" name="restore_path" value="<?php echo $file ?>">
        <input type="submit" name="restore" class="btn btn-success" value="Restore">
       </form>
       <form action="remove.php" method="POST" class="inline-flex">
        <input type="hidden" name="restore_path" value="<?php echo $file ?>">
        <input type="submit" name="remove" class="btn btn-danger" value="Remove">
       </form>
      </td>
    </tr>
    <?php endforeach;?>
     </tbody>
   </table>
   <?php else: ?>
    <p class="alert alert-danger">There is no backup</p>
   <?php endif;?>
    </div>
  </div>
 </div>
 <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
 <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>