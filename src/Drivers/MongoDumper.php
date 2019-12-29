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
        // Database
        $databaseArg = !empty($this->dbName) ? "--db " . escapeshellarg($this->dbName) : "";
        // Username
        $username = !empty($this->username) ? "--username " . escapeshellarg($this->username) : "";
        //Password
        $password = !empty($this->password) ? "--password " . escapeshellarg($this->password) : "";
        // Host
        $host = !empty($this->host) ? "--host " . escapeshellarg($this->host) : "";
        // Port
        $port = !empty($this->port) ? "--port " . escapeshellarg($this->port) : "";
        // Collection
        $collection = !empty($this->collection) ? "--collection " . escapeshellarg($this->collection) : "";
        // Authentication Database
        $authenticationDatabase = !empty($this->authenticationDatabase) ? "--authenticationDatabase " . escapeshellarg($this->authenticationDatabase) : "";
        // Archive
        $archive = "";

        if ($this->isCompress) {

            $archive = "--archive --gzip";
        }

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
        $username               = !empty($this->username) ? "--username " . escapeshellarg($this->username) : "";
        $host                   = !empty($this->host) ? "--host " . escapeshellarg($this->host) : "";
        $port                   = !empty($this->port) ? "--port " . escapeshellarg($this->port) : "";
        $authenticationDatabase = !empty($this->authenticationDatabase) ? "--authenticationDatabase " . escapeshellarg($this->authenticationDatabase) : "";

        $archive = "";

        if ($this->isCompress) {

            $archive = "--gzip --archive";
        }

        $restoreCommand = sprintf("%smongorestore %s %s %s %s %s",
            $this->dumpCommandPath,
            $archive,
            $host,
            $port,
            $username,
            $authenticationDatabase
        );

        if ($this->uri) {
            $restoreCommand = sprintf(
                '%smongorestore %s --uri %s',
                $this->dumpCommandPath,
                $archive,
                $this->uri
            );
        }

        if ($this->isCompress) {

            return "{$restoreCommand} < {$filePath}";
        }

        return "{$restoreCommand} {$filePath}";
    }
}
