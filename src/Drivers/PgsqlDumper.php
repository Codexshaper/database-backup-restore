<?php

namespace CodexShaper\Dumper\Drivers;

use CodexShaper\Dumper\Dumper;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PgsqlDumper extends Dumper
{
    /*@var int*/
    protected $port = 5432;
    /*@var bool*/
    protected $useInserts = false;
    /*@var bool*/
    protected $createTables = true;

    public function useInserts()
    {
        $this->useInserts = true;
        return $this;
    }

    public function doNotCreateTables()
    {
        $this->createTables = false;

        return $this;
    }

    public function dump(string $destinationPath = "")
    {
        $destinationPath = !empty($destinationPath) ? '"' . $destinationPath . '"' : '"' . $this->destinationPath . '"';
        $dumpCommand     = $this->prepareDumpCommand($destinationPath);
        $this->command   = $this->removeExtraSpaces($dumpCommand);
        $this->runCommand();
    }

    public function restore(string $restorePath = "")
    {
        $restorePath    = !empty($restorePath) ? '"' . $restorePath . '"' : '"' . $this->restorePath . '"';
        $restoreCommand = $this->prepareRestoreCommand($restorePath);
        $this->command  = $this->removeExtraSpaces($restoreCommand);
        $this->runCommand();
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
        $dumpCommand = sprintf(
            '%s -U %s -h %s %s %s %s %s %s %s',
            $this->quoteCommand($this->commandBinaryPath . 'pg_dump'),
            $this->prepareUserName(),
            $this->prepareHost(),
            $this->preparePort(),
            $this->prepareUseInserts(),
            $this->prepareCreateTables(),
            $this->prepareIncludeTables(),
            $this->prepareIgnoreTables(),
            $this->prepareDatabase()
        );

        if ($this->isCompress) {
            $compressCommand = $this->quoteCommand("{$this->compressBinaryPath}{$this->compressCommand}");
            return "{$dumpCommand} | {$compressCommand} > \"{$destinationPath}{$this->compressExtension}\"";
        }
        return "{$dumpCommand} > \"{$destinationPath}\"";
    }

    protected function prepareRestoreCommand(string $filePath): string
    {
        $restoreCommand = sprintf("%s -U %s -h %s %s %s",
            $this->quoteCommand($this->commandBinaryPath . 'psql'),
            $this->prepareUserName(),
            $this->prepareHost(),
            $this->preparePort(),
            $this->prepareDatabase()
        );

        if ($this->isCompress) {
            $compressCommand = $this->quoteCommand("{$this->compressBinaryPath}{$this->compressCommand}");
            return "{$compressCommand} < \"{$filePath}\" | {$restoreCommand}";
        }

        return "{$restoreCommand} < \"{$filePath}\"";
    }

    protected function runCommand()
    {
        try {

            $credentials    = $this->host . ':' . $this->port . ':' . $this->dbName . ':' . $this->username . ':' . $this->password;
            $this->tempFile = tempnam(sys_get_temp_dir(), 'pgsqlpass');
            $handler        = fopen($this->tempFile, 'r+');
            fwrite($handler, $credentials);
            $env     = ['PGPASSFILE' => $this->tempFile];
            $process = $this->prepareProcessCommand($this->command);
            if ($this->debug) {
                $process->mustRun(null, $env);
            } else {
                $process->run(null, $env);
            }

            fclose($handler);
            unlink($this->tempFile);

        } catch (ProcessFailedException $e) {
            throw new \Exception($e->getMessage());

        }
    }

    public function prepareCreateTables()
    {
        return (!$this->createTables) ? '--data-only' : '';
    }

    public function prepareUseInserts()
    {
        return ($this->useInserts) ? '--inserts' : '';
    }
}
