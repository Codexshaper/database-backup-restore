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
        $dumpCommand = sprintf(
            '%smysqldump %s %s %s %s %s %s %s %s %s %s %s',
            $this->dumpCommandPath,
            $this->prepareAuthentication($credentialFile),
            $this->prepareDatabase(),
            $this->prepareSocket(),
            $this->prepareSkipComments(),
            $this->prepareCreateTables(),
            $this->prepareSingleTransaction(),
            $this->prepareSkipLockTables(),
            $this->prepareQuick(),
            $this->prepareDefaultCharSet(),
            $this->prepareIncludeTables(),
            $this->prepareIgnoreTables()
        );

        if ($this->isCompress) {

            return "{$dumpCommand} | {$this->compressBinaryPath}{$this->compressCommand} > {$destinationPath}{$this->compressExtension}";
        }

        return "{$dumpCommand} > {$destinationPath}";
    }

    protected function prepareRestoreCommand(string $credentialFile, string $filePath): string
    {
        $restoreCommand = sprintf("%smysql %s %s",
            $this->dumpCommandPath,
            $this->prepareAuthentication($credentialFile),
            $this->prepareDatabase()
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

    public function prepareDatabase()
    {
        return $this->dbName;
    }

    public function prepareIncludeTables()
    {
        $includeTables    = (count($this->tables) > 0) ? implode(' ', $this->tables) : "";
        $includeTablesArg = !empty($includeTables) ? '--tables ' . $includeTables : '';
        return $includeTablesArg;
    }

    public function prepareIgnoreTables()
    {
        $ignoreTablesArgs = [];
        foreach ($this->ignoreTables as $tableName) {
            $ignoreTablesArgs[] = "--ignore-table={$databaseArg}.{$tableName}";
        }
        $ignoreTablesArg = (count($ignoreTablesArgs) > 0) ? implode(' ', $ignoreTablesArgs) : '';
        return $ignoreTablesArg;
    }

    public function prepareSingleTransaction()
    {
        return $this->singleTransaction ? "--single-transaction" : "";
    }

    public function prepareSkipLockTables()
    {
        return $this->skipLockTables ? "--skip-lock-tables" : "";
    }

    public function prepareQuick()
    {
        return $this->quick ? "--quick" : "";
    }

    public function prepareCreateTables()
    {
        return !$this->createTables ? '--no-create-info' : '';
    }

    public function prepareSkipComments()
    {
        return $this->skipComments ? '--skip-comments' : '';
    }

    public function prepareSocket()
    {
        return ($this->socket !== '') ? "--socket={$this->socket}" : '';
    }

    public function prepareDefaultCharSet()
    {
        return ($this->defaultCharacterSet !== '') ? "--default-character-set={$this->defaultCharacterSet}" : '';
    }

    public function prepareAuthentication(string $credentialFile)
    {
        return "--defaults-extra-file={$credentialFile}";
    }
}
