# database-backup-restore
Database Backup &amp; Restore

# MySql Dump *(Note: Mustbe installed `mysqldump` in your system)*
```
$options    = [
    'host'            => 'HOST',
    'port'            => 'PORT',
    'dbName'          => 'DATABASE_NAME',
    'username'        => 'DATABASE_USERNAME',
    'password'        => 'DATABASE_PASSWORD',
    'destinationPath' => 'STORAGE_PATH_WITH_FILE_NAME', // /path/to/backups/mysql/dump
];
```
Use constructor with options
```
$dumper = new \CodexShaper\Dumper\Drivers\MysqlDumper($options);
$dumper->dump();
```
Use create method
```
\CodexShaper\Dumper\Drivers\MysqlDumper::create($options)->dump();
```

Dynamically
```
\CodexShaper\Dumper\Drivers\MysqlDumper::create()
  ->setHost($host)
  ->setPort($port)
  ->setDbName($database)
  ->setUserName($username)
  ->setPassword($password)
  ->setDestinationPath($destinationPath)
  ->dump();
```

Archive
```
\CodexShaper\Dumper\Drivers\MysqlDumper::create($options)
  ->useCompress("gzip") // This command apply gzip to zip
  ->dump();
```

# MySql Restore *(Note: Mustbe installed `mysql` in your system)*

Restore from without archive
```
$dumper = new \CodexShaper\Dumper\Drivers\MysqlDumper($options);
$dumper->setRestorePath($restorePath); // /path/to/backups/mysql/dump.sql 
$dumper->restore();
```

Restore from archive
```
\CodexShaper\Dumper\Drivers\MysqlDumper::create($options)
  ->useCompress("gunzip") // this command unzip the file
  ->setRestorePath($restorePath) // /path/to/backups/mysql/dump.sql.gz
  ->restore();
```

# PgSql Dump *(Note: Mustbe installed `pg_dump` in your system)*
```
$options    = [
    'host'            => 'HOST',
    'port'            => 'PORT',
    'dbName'          => 'DATABASE_NAME',
    'username'        => 'DATABASE_USERNAME',
    'password'        => 'DATABASE_PASSWORD',
    'destinationPath' => 'STORAGE_PATH_WITH_FILE_NAME', // /path/to/backups/pgsql/dump
];
```
Use constructor with options
```
$dumper = new \CodexShaper\Dumper\Drivers\PgsqlDumper($options);
$dumper->dump();
```
Use create method
```
\CodexShaper\Dumper\Drivers\PgsqlDumper::create($options)->dump();
```

Dynamically
```
\CodexShaper\Dumper\Drivers\PgsqlDumper::create()
  ->setHost($host)
  ->setPort($port)
  ->setDbName($database)
  ->setUserName($username)
  ->setPassword($password)
  ->setDestinationPath($destinationPath)
  ->dump();
```

Archive
```
\CodexShaper\Dumper\Drivers\PgsqlDumper::create($options)
  ->useCompress("gzip") // This command apply gzip to zip
  ->dump();
```

# PgSql Restore *(Note: Mustbe installed `psql` in your system)*
Restore from without archive
```
$dumper = new \CodexShaper\Dumper\Drivers\PgsqlDumper($options);
$dumper->setRestorePath($restorePath); // /path/to/backups/pgsql/dump.sql 
$dumper->restore();
```

Restore from archive
```
\CodexShaper\Dumper\Drivers\PgsqlDumper::create($options)
  ->useCompress("gunzip") // this command unzip the file
  ->setRestorePath($restorePath) // /path/to/backups/pgsql/dump.sql.gz
  ->restore();
```

# Sqlite Dump *(Note: Mustbe installed `sqlite3` in your system)*
```
$options    = [
    'dbName'          => 'DATABASE_PATH', // /path/to/database.sqlite
    'destinationPath' => 'STORAGE_PATH_WITH_FILE_NAME', // /path/to/backups/sqlite/dump.sql
];
```
Use constructor
```
$dumper = new \CodexShaper\Dumper\Drivers\SqliteDumper($options);
$dumper->dump();
```
Use create method
```
\CodexShaper\Dumper\Drivers\SqliteDumper::create($options)->dump();
```

Set Dynamically
```
\CodexShaper\Dumper\Drivers\SqliteDumper::create()
  ->setDbName($database)
  ->setDestinationPath($destinationPath)
  ->dump();
```
Archive
```
\CodexShaper\Dumper\Drivers\SqliteDumper::create()
  ->setDbName($database)
  ->setDestinationPath($destinationPath)
  ->useCompress("gzip") // This command apply gzip to zip
  ->dump();
```
# Sqlite Restore *(Note: Mustbe installed `sqlite3` in your system)*
```
$options    = [
    'dbName'          => 'DATABASE_PATH', // /path/to/database.sqlite
    'restorePath' => 'RESTORE_PATH_WITH_FILE_NAME', // /path/to/backups/sqlite/dump.sql
];
```
```
$dumper = new \CodexShaper\Dumper\Drivers\SqliteDumper($options);
$dumper->restore();
```
Or
```
\CodexShaper\Dumper\Drivers\SqliteDumper::create()
  ->setDbName($database)
  ->setRestorePath($restorePath)
  ->restore();
```
Restore From Archive
```
\CodexShaper\Dumper\Drivers\SqliteDumper::create()
  ->setDbName($database)
  ->setRestorePath($restorePath)
  ->useCompress("gunzip") // This command apply gzip to zip
  ->restore();
```

# MongoDB Dump *(Note: Mustbe installed `mongodump` in your system)*

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

Use constructor with options
```
$dumper = new \CodexShaper\Dumper\Drivers\MongoDumper($options);
$dumper->dump();
```
Use create method
```
\CodexShaper\Dumper\Drivers\MongoDumper::create($options)->dump();
```

Dynamically
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

Archive
```
\CodexShaper\Dumper\Drivers\MongoDumper::create($options)
  ->useCompress("gzip") // This command will add --archive with --gzip
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
Dynamic
```
\CodexShaper\Dumper\Drivers\MongoDumper::create()
  ->setUri($uri)
  ->setDestinationPath($destinationPath)
  ->dump()
```
Compress
```
\CodexShaper\Dumper\Drivers\MongoDumper::create(['uri' => $uri])
  ->useCompress("gzip")
  ->setDestinationPath($destinationPath)
  ->dump();
```

# MongoDB Restore *(Note: Mustbe installed `mongorestore` in your system)*

Restore from without archive
```
$dumper = new \CodexShaper\Dumper\Drivers\MongoDumper($options);
$dumper->setRestorePath($restorePath); // /path/to/backups/mongodb/dump 
$dumper->restore();
```

Use URI

```
\CodexShaper\Dumper\Drivers\MongoDumper::create(['uri' => $uri])
    ->setRestorePath($restorePath)
    ->restore();
```

Restore from archive
```
\CodexShaper\Dumper\Drivers\MongoDumper::create($options)
  ->useCompress("gzip")
  ->setRestorePath($restorePath) // /path/to/backups/mongodb/dump.gz
  ->restore();
```
Restore from archive using URI
```
\CodexShaper\Dumper\Drivers\MongoDumper::create(['uri' => $uri])
  ->useCompress("gzip")
  ->setRestorePath($restorePath)
  ->restore();
```

# Set Dump Binary Path
```
\CodexShaper\Dumper\Drivers\MysqlDumper::create($options)
  ->setDumpCommandPath($binaryPath) // /path/to/mysql/bin
  ->dump();
```

Same for other driver