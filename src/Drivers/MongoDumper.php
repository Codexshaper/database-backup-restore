<?php

namespace CodexShaper\Dumper\Drivers;

use CodexShaper\Dumper\Dumper;

class MongoDumper extends Dumper
{
    /*@var int*/
    protected $port = 27017;
    /*@var string*/
    protected $collection = "";
    /*@var string*/
    protected $authenticationDatabase = "admin";
    /*@var string*/
    protected $uri = "";

    public function setUri(string $uri)
    {
        $this->uri = $uri;
        return $this;
    }

    public function setCollection(string $collection)
    {
        $this->collection = $collection;
        return $this;
    }
    public function setAuthenticationDatabase(string $authenticationDatabase)
    {
        $this->authenticationDatabase = $authenticationDatabase;
        return $this;
    }

    public function dump(string $destinationPath = "")
    {
        $destinationPath = !empty($destinationPath) ? $destinationPath : $this->destinationPath;
        $this->command   = $this->prepareDumpCommand($destinationPath);
        $this->run();
    }

    public function restore(string $restorePath = "")
    {
        $restorePath   = !empty($restorePath) ? $restorePath : $this->restorePath;
        $this->command = $this->prepareRestoreCommand($restorePath);
        $this->run();
    }

    protected function prepareDumpCommand(string $destinationPath): string
    {
        $options = [
            'archive' => '',
            'database' => '',
            'username' => '',
            'password' => '',
            'host' => '',
            'port' => '',
            'collection' => '',
            'authenticationDatabase' => '',
        ];
        // Database
        if(!empty($this->dbName)) {
            $options['database'] = "--db {$this->dbName}";
        }
        // Username
        if(!empty($this->username)) {
            $options['username'] = "--username {$this->username}";
        }
        //Password
        if(!empty($this->password)) {
            $options['password'] = "--password {$this->password}";
        }
        // Host
        if(!empty($this->host)) {
           $options['host'] "--host {$this->host}";
        }
        // Port
        if(!empty($this->port)) {
            $options['port'] = "--port {$this->port}";
        }
        // Collection
        if(!empty($this->collection)) {
            $options['collection'] = "--collection {$this->collection}";
        }
        // Authentication Database
        if(!empty($this->authenticationDatabase)) {
            $options[] = "--authenticationDatabase {$this->authenticationDatabase}";
        } 
        // Archive
        if ($this->isCompress) {
            $options['archive'] = "--archive --gzip";
        }
        // Dump Command
        $dumpCommand = sprintf(
            '%smongodump %s %s %s %s %s %s %s %s',
            $this->dumpCommandPath,
            $archive,
            $databaseArg,
            $username,
            $password,
            $host,
            $port,
            $collection,
            $authenticationDatabase
        );
        // Generate dump command from uri
        if ($this->uri) {
            $dumpCommand = sprintf(
                '%smongodump %s --uri %s %s',
                $this->dumpCommandPath,
                $archive,
                $this->uri,
                $collection
            );
        }

        if ($this->isCompress) {
            return "{$dumpCommand} > {$destinationPath}{$this->compressExtension}";
        }

        return "{$dumpCommand} --out {$destinationPath}";
    }

    protected function prepareRestoreCommand(string $filePath): string
    {
        // Username
        $username = !empty($this->username) ? "--username " . escapeshellarg($this->username) : "";
        // Host
        $host = !empty($this->host) ? "--host " . escapeshellarg($this->host) : "";
        // Port
        $port = !empty($this->port) ? "--port " . escapeshellarg($this->port) : "";
        // Authentication Database
        $authenticationDatabase = !empty($this->authenticationDatabase) ? "--authenticationDatabase " . escapeshellarg($this->authenticationDatabase) : "";
        // Archive
        $archive = "";

        if ($this->isCompress) {

            $archive = "--gzip --archive";
        }
        // Restore Command
        $restoreCommand = sprintf("%smongorestore %s %s %s %s %s",
            $this->dumpCommandPath,
            $archive,
            $host,
            $port,
            $username,
            $authenticationDatabase
        );
        // Generate restore command for uri
        if ($this->uri) {
            $restoreCommand = sprintf(
                '%smongorestore %s --uri %s',
                $this->dumpCommandPath,
                $archive,
                $this->uri
            );
        }
        // Check compress is enable
        if ($this->isCompress) {
            return "{$restoreCommand} < {$filePath}";
        }

        return "{$restoreCommand} {$filePath}";
    }
}
