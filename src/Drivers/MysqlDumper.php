<?php

namespace CodexShaper\Dumper\Drivers;

use CodexShaper\Dumper\Dumper;
use Symfony\Component\Process\Exception\ProcessFailedException;

class MysqlDumper extends Dumper
{
    /*@var bool*/
    protected $singleTransaction = false;
    /*@var bool*/
    protected $skipLockTables = false;
    /*@var bool*/
    protected $quick = false;
    /*@var bool*/
    protected $skipComments = true;
    /*@var string*/
    protected $defaultCharacterSet = '';
    /*@var bool*/
    protected $createTables = true;

    public function useSingleTransaction()
    {
        $this->singleTransaction = true;
        return $this;
    }
    public function useSkipLockTables()
    {
        $this->skipLockTables = true;
        return $this;
    }
    public function useQuick()
    {
        $this->quick = true;
        return $this;
    }
    public function doNotUseSkipComments()
    {
        $this->skipComments = false;
        return $this;
    }
    public function doNotUseCreateTables()
    {
        $this->createTables = false;
        return $this;
    }
    public function setDefaultCharacterSet(string $charecterSet)
    {
        $this->defaultCharacterSet = $charecterSet;
        return $this;
    }

    public function dump(string $destinationPath = "")
    {
        $destinationPath = !empty($destinationPath) ? $destinationPath : $this->destinationPath;
        $this->runCommand($destinationPath, "dump");
        return $this;
    }

    public function restore(string $restorePath = "")
    {
        $restorePath = !empty($restorePath) ? $restorePath : $this->restorePath;
        $this->runCommand($restorePath, 'restore');
        return $this;
    }

    protected function prepareDumpCommand(string $credentialFile, string $destinationPath): string
    {
        $command = "{$this->dumpCommandPath}mysqldump --defaults-extra-file={$credentialFile} ";

        if (!empty($this->dbName)) {
            $command .= $this->dbName . " ";
        }
        if ($this->socket !== '') {
            $command .= "--socket={$this->socket} ";
        }
        if ($this->skipComments) {
            $command .= '--skip-comments ';
        }
        if (!$this->createTables) {
            $command .= '--no-create-info ';
        }
        if ($this->singleTransaction) {
            $command .= '--single-transaction ';
        }
        if ($this->skipLockTables) {
            $command .= '--skip-lock-tables ';
        }
        if ($this->quick) {
            $command .= '--quick ';
        }
        if ($this->defaultCharacterSet) {
            $command .= "--default-character-set={$this->defaultCharacterSet} ";
        }
        if (count($this->tables) > 0) {
            $includetables = implode(' ', $this->tables);
            $command .= "--tables {$includetables} ";
        }
        // Ignore Tables
        foreach ($this->ignoreTables as $tableName) {
            $command .= "--ignore-table={$this->dbName}.{$tableName} ";
        }
        // Add compressor if compress is enable
        if ($this->isCompress) {
            return "{$command} | {$this->compressBinaryPath}{$this->compressCommand} > {$destinationPath}{$this->compressExtension}";
        }
        return "{$command} > {$destinationPath}";
    }

    protected function prepareRestoreCommand(string $credentialFile, string $filePath): string
    {
        // Database
        $database = $this->dbName;
        // Authentication File
        $authenticate = "--defaults-extra-file=" . $credentialFile;
        // Restore command
        $restoreCommand = sprintf("%smysql %s %s",
            $this->dumpCommandPath,
            $authenticate,
            $database
        );
        // Add compressor if compress is enable
        if ($this->isCompress) {
            return "{$this->compressBinaryPath}{$this->compressCommand} < {$filePath} | {$restoreCommand}";
        }

        return "{$restoreCommand} < {$filePath}";
    }

    protected function runCommand($filePath, $action)
    {
        try {
            // Get Credentials
            $credentials = $this->getCredentials();
            // Create a temporary file
            $this->tempFile = tempnam(sys_get_temp_dir(), 'mysqlpass');
            // Create file handler
            $handler = fopen($this->tempFile, 'r+');
            // Write credentials into temporary file
            fwrite($handler, $credentials);

            if ($action == 'dump') {
                $this->command = preg_replace('/\s+/', ' ', $this->prepareDumpCommand($this->tempFile, $filePath));
            } else if ($action == 'restore') {
                $this->command = preg_replace('/\s+/', ' ', $this->prepareRestoreCommand($this->tempFile, $filePath));
            }
            // Get Symfony process with prepared command
            $process = $this->prepareProcessCommand();

            if ($this->debug) {
                $process->mustRun();
            } else {
                $process->run();
            }
            // close handler
            fclose($handler);
            // Remove temporary file
            unlink($this->tempFile);

        } catch (ProcessFailedException $e) {
            throw new \Exception($e->getMessage());

        }
    }

    protected function getCredentials()
    {
        $contents = [
            '[client]',
            "user = '{$this->username}'",
            "password = '{$this->password}'",
            "host = '{$this->host}'",
            "port = '{$this->port}'",
        ];
        return implode(PHP_EOL, $contents);
    }
}
