<?php
require 'vendor/autoload.php';
if (isset($_POST['remove'])) {
    $file = $_POST['restore_path'] ?? "";
    try {
        if (file_exists($file)) {
            unlink($file);
            header('Location: index.php?backup=success&message=Backup removed successfully');
        } else {
            header('Location: index.php?backup=error&message=There is no file that you requested');
        }
    } catch (Exception $ex) {
        header('Location: index.php?backup=fail&message=' . $ex->getMessage());
    }
}
