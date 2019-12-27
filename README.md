# database-backup-restore
Database Backup &amp; Restore

# MongoDB Dump

```
$options    = [
    'host'            => 'HOST',
    'port'            => 'PORT',
    'dbName'          => 'DATABASE_NAME',
    'username'        => 'DATABASE_USERNAME',
    'password'        => 'DATABASE_PASSWORD',
    'destinationPath' => 'STORAGE_PATH_WITH_FILE_NAME', // /path/to/backups/mongodb/dump
];
```

## Use constructor with options
```
$dumper = new \CodexShaper\Dumper\Drivers\MongoDumper($options);
$dumper->dump();
```
## Use create method
```
\CodexShaper\Dumper\Drivers\MongoDumper::create($options)->dump();
```

## Set Dynamically
```
\CodexShaper\Dumper\Drivers\MongoDumper::create()
  ->setHost($host)
  ->setPort($port)
  ->setDbName($database)
  ->setUserName($username)
  ->setPassword($password)
  ->setDestinationPath($destinationPath)
  ->dump();
```
## Use URI
```
$options = [
    'uri'             => $uri,
    "destinationPath" => $destinationPath, // /path/to/backups/mongodb/dump 
];
$dumper = new \CodexShaper\Dumper\Drivers\MongoDumper($options);
$dumper->dump();
```
OR
```
\CodexShaper\Dumper\Drivers\MongoDumper::create(['uri' => $uri])
  ->setDestinationPath($destinationPath)
  ->dump()
```
Archive
```
\CodexShaper\Dumper\Drivers\MongoDumper::create(['uri' => $uri])
  ->useCompress("gzip")
  ->setDestinationPath($destinationPath)
  ->dump();
```
## Archive
```
\CodexShaper\Dumper\Drivers\MongoDumper::create($options)
  ->useCompress("gzip") // This command will add --archive with --gzip
  ->dump();
```

# MongoDB Restore

## Restore from without archive
```
$dumper = new \CodexShaper\Dumper\Drivers\MongoDumper($options);
$dumper->setRestorePath($restorePath); // /path/to/backups/mongodb/dump 
$dumper->restore();
```

## Use URI

```
\CodexShaper\Dumper\Drivers\MongoDumper::create(['uri' => $uri])
    ->setRestorePath($restorePath)
    ->restore();
```

## Restore from archive
```
\CodexShaper\Dumper\Drivers\MongoDumper::create($options)
  ->useCompress("gzip")
  ->setRestorePath($restorePath) // /path/to/backups/mongodb/dump.gz
  ->restore();
```
## Restore from archive using URI
```
\CodexShaper\Dumper\Drivers\MongoDumper::create(['uri' => $uri])
  ->useCompress("gzip")
  ->setRestorePath($restorePath)
  ->restore();
```

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
