<?php

namespace CodexShaper\Dumper\Drivers;

use CodexShaper\Dumper\Dumper;

class SqliteDumper extends Dumper
{
    public function dump(string $destinationPath = "")
    {
        $destinationPath = !empty($destinationPath) ? $destinationPath : $this->destinationPath;
        $command         = $this->prepareDumpCommand($destinationPath);
        $this->run($command);
    }

    public function restore(string $restorePath = "")
    {
        $restorePath = !empty($restorePath) ? $restorePath : $this->restorePath;
        $command     = $this->prepareRestoreCommand($restorePath);
        $this->run($command);
    }

    protected function prepareDumpCommand(string $destinationPath): string
    {
        $databaseArg = escapeshellarg($this->dbName);

        $dumpCommand = sprintf(
            "%ssqlite3 %s .dump",
            $this->dumpCommandPath,
            $databaseArg
        );

        if ($this->isCompress) {

            return "{$dumpCommand} | {$this->compressBinaryPath}{$this->compressCommand} > {$destinationPath}{$this->compressExtension}";
        }

        return "{$dumpCommand} > {$destinationPath}";
    }

    protected function prepareRestoreCommand(string $filePath): string
    {
        $database = escapeshellarg($this->dbName);

        $restoreCommand = sprintf("%ssqlite3 %s",
            $this->dumpCommandPath,
            $database
        );

        if ($this->isCompress) {

            return "{$this->compressBinaryPath}{$this->compressCommand} < {$filePath} | {$restoreCommand}";
        }

        return "{$restoreCommand} < {$filePath}";
    }
}
