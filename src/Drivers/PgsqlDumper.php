<?php

namespace CodexShaper\Dumper\Drivers;

use CodexShaper\Dumper\Dumper;

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
        $command         = $this->prepareDumpCommand($destinationPath);
        $this->runCommand($command);
    }

    public function restore(string $restorePath = "")
    {
        $restorePath = !empty($restorePath) ? $restorePath : $this->restorePath;
        $command     = $this->prepareRestoreCommand($restorePath);
        $this->runCommand($command);
    }

    protected function prepareDumpCommand(string $destinationPath): string
    {
        $hostname         = ($this->socket !== '') ? $this->socket : escapeshellarg($this->host);
        $username         = escapeshellarg($this->username);
        $database         = escapeshellarg($this->dbName);
        $portArg          = !empty($this->port) ? '-p ' . escapeshellarg($this->port) : '';
        $includeTablesArg = (count($this->tables) > 0) ? '-t ' . implode(' -t ', $this->tables) : "";
        $ignoreTablesArg  = (count($this->ignoreTables) > 0) ? '-T ' . implode(' -T ', $this->ignoreTables) : '';
        $createTables     = (!$this->createTables) ? '--data-only' : '';
        $useInserts       = (!$this->useInserts) ? '--inserts' : '';

        $dumpCommand = sprintf(
            '%spg_dump -U %s -h %s %s %s %s %s %s %s',
            $this->dumpCommandPath,
            $username,
            $hostname,
            $portArg,
            $useInserts,
            $createTables,
            $includeTablesArg,
            $ignoreTablesArg,
            $database
        );

        if ($this->isCompress) {

            return "{$dumpCommand} | {$this->compressBinaryPath}{$this->compressCommand} > {$destinationPath}{$this->compressExtension}";
        }

        return "{$dumpCommand} > {$destinationPath}";
    }

    protected function prepareRestoreCommand(string $filePath): string
    {
        $hostname = ($this->socket !== '') ? $this->socket : escapeshellarg($this->host);
        $database = escapeshellarg($this->dbName);
        $username = escapeshellarg($this->username);
        $portArg  = !empty($this->port) ? '-p ' . escapeshellarg($this->port) : '';

        $restoreCommand = sprintf("%spsql -U %s -h %s %s %s",
            $this->dumpCommandPath,
            $username,
            $hostname,
            $portArg,
            $database
        );

        if ($this->isCompress) {

            return "{$this->compressBinaryPath}{$this->compressCommand} < {$filePath} | {$restoreCommand}";
        }

        return "{$restoreCommand} < {$filePath}";
    }

    protected function runCommand($command)
    {
        try {

            $credentials    = $this->host . ':' . $this->port . ':' . $this->dbName . ':' . $this->username . ':' . $this->password;
            $credentialFile = tempnam(sys_get_temp_dir(), 'pgsqlpass');
            $handler        = fopen($credentialFile, 'r+');
            fwrite($handler, $credentials);
            $process = $this->prepareProcessCommand($command);
            $process->run(null, [
                'PGPASSFILE' => $credentialFile,
            ]);
            fclose($handler);
            unlink($credentialFile);

        } catch (ProcessFailedException $e) {
            throw new \Exception($e->getMessage());

        }
    }
}
