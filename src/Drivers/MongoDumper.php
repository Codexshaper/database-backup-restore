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
    protected $authenticationDatabase = "";
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
        $destinationPath = !empty($destinationPath) ? '"' . $destinationPath . '"' : '"' . $this->destinationPath . '"';
        $dumpCommand     = $this->prepareDumpCommand($destinationPath);
        $this->command   = $this->removeExtraSpaces($dumpCommand);
        $this->run();
    }

    public function restore(string $restorePath = "")
    {
        $restorePath    = !empty($restorePath) ? '"' . $restorePath . '"' : '"' . $this->restorePath . '"';
        $restoreCommand = $this->prepareRestoreCommand($restorePath);
        $this->command  = $this->removeExtraSpaces($restoreCommand);
        $this->run();
    }

    public function getDumpCommand($destinationPath = '')
    {
        $destinationPath = !empty($destinationPath) ? $destinationPath : $this->destinationPath;
        $dumpCommand     = $this->prepareDumpCommand($destinationPath);

        return $this->removeExtraSpaces($dumpCommand);
    }

    public function getRestoreCommand(string $filePath = '')
    {
        $filePath       = !empty($filePath) ? '"' . $filePath : $this->restorePath;
        $restoreCommand = $this->prepareRestoreCommand($filePath);

        return $this->removeExtraSpaces($restoreCommand);
    }

    protected function prepareDumpCommand(string $destinationPath): string
    {
        $archive = $this->isCompress ? "--archive --gzip" : "";

        $dumpCommand = sprintf(
            '%s %s %s %s %s %s %s %s %s',
            $this->quoteCommand($this->commandBinaryPath . 'mongodump'),
            $archive,
            $this->prepareDatabase(),
            $this->prepareUserName(),
            $this->preparePassword(),
            $this->prepareHost(),
            $this->preparePort(),
            $this->prepareCollection(),
            $this->prepareAuthenticateDatabase()
        );

        if ($this->uri) {
            $dumpCommand = sprintf(
                '%s %s --uri %s %s',
                $this->quoteCommand($this->commandBinaryPath . 'mongodump'),
                $archive,
                $this->uri,
                $this->prepareCollection()
            );
        }

        if ($this->isCompress) {
            return "{$dumpCommand} > \"{$destinationPath}{$this->compressExtension}\"";
        }

        return "{$dumpCommand} --out \"{$destinationPath}\"";
    }

    protected function prepareRestoreCommand(string $filePath): string
    {

        $archive = $this->isCompress ? "--gzip --archive" : "";

        $restoreCommand = sprintf("%s %s %s %s %s %s",
            $this->quoteCommand($this->commandBinaryPath . 'mongorestore'),
            $archive,
            $this->prepareHost(),
            $this->preparePort(),
            $this->prepareUserName(),
            $this->prepareAuthenticateDatabase()
        );

        if ($this->uri) {
            $restoreCommand = sprintf(
                '%smongorestore %s --uri %s',
                $this->commandBinaryPath,
                $archive,
                $this->uri
            );
        }

        if ($this->isCompress) {
            return "{$restoreCommand} < \"{$filePath}\"";
        }

        return "{$restoreCommand} \"{$filePath}\"";
    }

    public function preparePassword()
    {
        return !empty($this->password) ? "--password {$this->password}" : "";
    }

    public function prepareAuthenticateDatabase()
    {
        return !empty($this->authenticationDatabase) ? "--authenticationDatabase {$this->authenticationDatabase}" : "";
    }

    public function prepareCollection()
    {
        return !empty($this->collection) ? "--collection {$this->collection}" : "";
    }
}
