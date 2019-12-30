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
        $archive = $this->isCompress ? "--archive --gzip" : "";

        $dumpCommand = sprintf(
            '%smongodump %s %s %s %s %s %s %s %s',
            $this->dumpCommandPath,
            $archive,
            $this->getDatabaseOption(),
            $this->getUsernameOption(),
            $this->getPasswordOption(),
            $this->getHostOption(),
            $this->getPortOption(),
            $this->getCollectionOption(),
            $this->getAuthenticateDatabase()
        );

        if ($this->uri) {
            $dumpCommand = sprintf(
                '%smongodump %s --uri %s %s',
                $this->dumpCommandPath,
                $archive,
                $this->uri,
                $this->getCollectionOption()
            );
        }

        if ($this->isCompress) {
            return "{$dumpCommand} > {$destinationPath}{$this->compressExtension}";
        }

        return "{$dumpCommand} --out {$destinationPath}";
    }

    public function getDatabaseOption()
    {
        return !empty($this->dbName) ? "--db {$this->dbName}" : "";
    }

    public function getUsernameOption()
    {
        return !empty($this->username) ? "--username {$this->username}" : "";
    }

    public function getPasswordOption()
    {
        return !empty($this->password) ? "--password {$this->password}" : "";
    }

    public function getHostOption()
    {
        return !empty($this->host) ? "--host {$this->host}" : "";
    }

    public function getPortOption()
    {
        return !empty($this->port) ? "--port {$this->port}" : "";
    }

    public function getAuthenticateDatabase()
    {
        return !empty($this->authenticationDatabase) ? "--authenticationDatabase {$this->authenticationDatabase}" : "";
    }

    public function getCollectionOption()
    {
        return !empty($this->collection) ? "--collection {$this->collection}" : "";
    }

    public function getArchiveOption()
    {
        return 
    }

    protected function prepareRestoreCommand(string $filePath): string
    {

        $archive = $this->isCompress ?  "--gzip --archive" : "";

        $restoreCommand = sprintf("%smongorestore %s %s %s %s %s",
            $this->dumpCommandPath,
            $archive,
            $this->getHostOption(),
            $this->getPortOption(),
            $this->getUsernameOption(),
            $this->getAuthenticateDatabase()
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
