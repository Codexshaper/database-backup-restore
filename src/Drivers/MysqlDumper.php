<?php

namespace CodexShaper\Dumper\Drivers;

use CodexShaper\Dumper\Dumper;

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
    public function setDefaultCharacterSe(string $charecterSet)
    {
        $this->defaultCharacterSe = $charecterSet;
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
        $databaseArg = escapeshellarg($this->dbName);

        $includeTables    = (count($this->tables) > 0) ? implode(' ', $this->tables) : "";
        $includeTablesArg = !empty($includeTables) ? '--tables ' . escapeshellarg($includeTables) : '';

        $ignoreTablesArgs = [];
        foreach ($this->ignoreTables as $tableName) {
            $ignoreTablesArgs[] = "--ignore-table=" . $databaseArg . "." . escapeshellarg($tableName);
        }
        $ignoreTablesArg = (count($ignoreTablesArgs) > 0) ? implode(' ', $ignoreTablesArgs) : '';

        $singleTransaction   = ($this->singleTransaction) ? "--single-transaction" : "";
        $skipLockTable       = ($this->skipLockTables) ? "--skip-lock-tables" : "";
        $quick               = ($this->quick) ? "--quick" : "";
        $createTables        = (!$this->createTables) ? '--no-create-info' : '';
        $skipComments        = ($this->skipComments) ? '--skip-comments' : '';
        $socket              = ($this->socket !== '') ? "--socket={$this->socket}" : '';
        $defaultCharacterSet = ($this->defaultCharacterSet !== '') ? '--default-character-set=' . $this->defaultCharacterSet : '';

        $authenticate = "--defaults-extra-file=" . $credentialFile;

        $dumpCommand = sprintf(
            '%smysqldump %s %s %s %s %s %s %s %s %s %s %s',
            $this->dumpCommandPath,
            $authenticate,
            $databaseArg,
            $socket,
            $skipComments,
            $createTables,
            $singleTransaction,
            $skipLockTable,
            $quick,
            $defaultCharacterSet,
            $includeTablesArg,
            $ignoreTablesArg
        );

        if ($this->isCompress) {

            return "{$dumpCommand} | {$this->compressBinaryPath}{$this->compressCommand} > {$destinationPath}{$this->compressExtension}";
        }

        return "{$dumpCommand} > {$destinationPath}";
    }

    protected function prepareRestoreCommand(string $credentialFile, string $filePath): string
    {
        $database     = escapeshellarg($this->dbName);
        $authenticate = "--defaults-extra-file=" . $credentialFile;

        $restoreCommand = sprintf("%smysql %s %s",
            $this->dumpCommandPath,
            $authenticate,
            $database
        );

        if ($this->isCompress) {

            return "{$this->compressBinaryPath}{$this->compressCommand} < {$filePath} | {$restoreCommand}";
        }

        return "{$restoreCommand} < {$filePath}";
    }

    protected function runCommand($filePath, $action)
    {
        try {

            $credentials    = $this->getCredentials();
            $this->tempFile = tempnam(sys_get_temp_dir(), 'mysqlpass');
            $handler        = fopen($this->tempFile, 'r+');
            fwrite($handler, $credentials);

            if ($action == 'dump') {
                $this->command = preg_replace('/\s+/', ' ', $this->prepareDumpCommand($this->tempFile, $filePath));
            }

            if ($action == 'restore') {
                $this->command = preg_replace('/\s+/', ' ', $this->prepareRestoreCommand($this->tempFile, $filePath));
            }

            $process = $this->prepareProcessCommand();

            if ($this->debug) {
                $process->mustRun();
            } else {
                $process->run();
            }

            fclose($handler);
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
