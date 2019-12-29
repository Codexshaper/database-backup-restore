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
        $command = $this->prepareDumpOptions();

        if ($this->isCompress) {
            return "{$command} > {$destinationPath}{$this->compressExtension}";
        }

        return "{$command} --out {$destinationPath}";
    }

    protected function prepareDumpOptions()
    {
        $options = [
            "{$this->dumpCommandPath}mongodump",
        ];
        if ($this->isCompress) {
            $options[] = "--archive --gzip";
        }
        if ($this->uri) {
            $options[] = $this->uri;
        }
        // Database
        if ($this->dbName && !$this->uri) {
            $options[] = "--db {$this->dbName}";
        }
        // Username
        if ($this->username && !$this->uri) {
            $options[] = "--username {$this->username}";
        }
        //Password
        if ($this->password && !$this->uri) {
            $options[] = "--password {$this->password}";
        }
        // Host
        if ($this->host && !$this->uri) {
            $options[] = "--host {$this->host}";
        }
        // Port
        if ($this->port && !$this->uri) {
            $options[] = "--port {$this->port}";
        }
        // Collection
        if ($this->collection) {
            $options[] = "--collection {$this->collection}";
        }
        // Authentication Database
        if ($this->authenticationDatabase && !$this->uri) {
            $options[] = "--authenticationDatabase {$this->authenticationDatabase}";
        }
        return implode(' ', $options);
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
