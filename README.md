# database-backup-restore
Database Backup &amp; Restore

# Sqlite Dump
## Use constructor
```
$options    = [
    'host'            => 'HOST',
    'port'            => 'PORT',
    'dbName'          => 'DATABASE_NAME',
    'username'        => 'DATABASE_USERNAME',
    'password'        => 'DATABASE_PASSWORD',
    'destinationPath' => 'STORAGE_PATH_WITH_FILE_NAME', //C:\\xampp\htdocs\\laravel-database-manager\\storage\\app\\backups\\sqlite\\dump.sql
];
$dumper = new \CodexShaper\Dumper\Drivers\SqliteDumper($options);
$dumper->dump();
```
## Use create method
```
$options    = [
    'host'            => 'HOST',
    'port'            => 'PORT',
    'dbName'          => 'DATABASE_NAME',
    'username'        => 'DATABASE_USERNAME',
    'password'        => 'DATABASE_PASSWORD',
    'destinationPath' => 'STORAGE_PATH_WITH_FILE_NAME', //C:\\xampp\htdocs\\laravel-database-manager\\storage\\app\\backups\\sqlite\\dump.sql
];
   \CodexShaper\Dumper\Drivers\SqliteDumper::create($options)->dump();
```

## Set Dynamically
```
\CodexShaper\Dumper\Drivers\SqliteDumper::create($options)
  ->setHost($host)
  ->setPort($port)
  ->setDbName($database)
  ->setUserName($username)
  ->setPassword($password)
  ->setDestinationPath($destinationPath)
  ->dump();
```
# Sqlite Restore
```
$options    = [
    'host'            => 'HOST',
    'port'            => 'PORT',
    'dbName'          => 'DATABASE_NAME',
    'username'        => 'DATABASE_USERNAME',
    'password'        => 'DATABASE_PASSWORD',
    'restorePath' => 'RESTORE_PATH_WITH_FILE_NAME', //C:\\xampp\htdocs\\laravel-database-manager\\storage\\app\\backups\\sqlite\\dump.sql
];
$dumper = new \CodexShaper\Dumper\Drivers\SqliteDumper($options);
$dumper->restore();
```
Or
```
\CodexShaper\Dumper\Drivers\SqliteDumper::create($options)
  ->setHost($host)
  ->setPort($port)
  ->setDbName($database)
  ->setUserName($username)
  ->setPassword($password)
  ->restore($restorePath);
```
