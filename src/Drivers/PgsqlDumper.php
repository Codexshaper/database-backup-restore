<?php

namespace CodexShaper\Dumper\Drivers;

use CodexShaper\Dumper\Dumper;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PgsqlDumper extends Dumper
{
    protected $useInserts   = false;
    protected $createTables = true;

    public function useInserts()
    {
        $this->useInserts = true;
        return $this;
    }

    public function doNotUseCreateTables()
    {
        $this->createTables = false;

        return $this;
    }

    public function dump(string $destinationPath = "")
    {
        $destinationPath = !empty($destinationPath) ? $destinationPath : $this->destinationPath;
        $dumpCommand     = $this->prepareDumpCommand($destinationPath);
        $this->command   = $this->removeExtraSpaces($dumpCommand);
        $this->runCommand();
    }

    public function restore(string $restorePath = "")
    {
        $restorePath    = !empty($restorePath) ? $restorePath : $this->restorePath;
        $restoreCommand = $this->prepareRestoreCommand($restorePath);
        $this->command  = $this->removeExtraSpaces($restoreCommand);
        $this->runCommand();
    }

    protected function prepareDumpCommand(string $destinationPath): string
    {
        $dumpCommand = sprintf(
            '%spg_dump -U %s -h %s %s %s %s %s %s %s',
            $this->dumpCommandPath,
            $this->prepareUsername(),
            $this->prepareHost(),
            $this->preparePort(),
            $this->prepareUseInserts(),
            $this->prepareCreateTables(),
            $this->prepareIncludeTables(),
            $this->prepareIgnoreTables(),
            $this->prepareDatabase()
        );

        if ($this->isCompress) {
            return "{$dumpCommand} | {$this->compressBinaryPath}{$this->compressCommand} > {$destinationPath}{$this->compressExtension}";
        }
        return "{$dumpCommand} > {$destinationPath}";
    }

    protected function prepareRestoreCommand(string $filePath): string
    {
        $restoreCommand = sprintf("%spsql -U %s -h %s %s %s",
            $this->dumpCommandPath,
            $this->prepareUsername(),
            $this->prepareHost(),
            $this->preparePort(),
            $this->prepareDatabase()
        );

        if ($this->isCompress) {
            return "{$this->compressBinaryPath}{$this->compressCommand} < {$filePath} | {$restoreCommand}";
        }

        return "{$restoreCommand} < {$filePath}";
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

    public function prepareDatabase()
    {
        return !empty($this->dbName) ? $this->dbName : "";
    }

    public function prepareUsername()
    {
        return !empty($this->username) ? $this->username : "";
    }

    public function prepareHost()
    {
        return ($this->socket !== '') ? $this->socket : $this->host;
    }

    public function preparePort()
    {
        return !empty($this->port) ? '-p ' . $this->port : '';
    }

    public function prepareIncludeTables()
    {
        return (count($this->tables) > 0) ? '-t ' . implode(' -t ', $this->tables) : "";
    }

    public function prepareIgnoreTables()
    {
        return (count($this->ignoreTables) > 0) ? '-T ' . implode(' -T ', $this->ignoreTables) : '';
    }

    public function prepareCreateTables()
    {
        return (!$this->createTables) ? '--data-only' : '';
    }

    public function prepareUseInserts()
    {
        return (!$this->useInserts) ? '--inserts' : '';
    }
}
