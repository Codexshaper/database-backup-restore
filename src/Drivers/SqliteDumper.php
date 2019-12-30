<?php

namespace CodexShaper\Dumper\Drivers;

use CodexShaper\Dumper\Dumper;

class SqliteDumper extends Dumper
{
    public function dump(string $destinationPath = "")
    {
        $destinationPath = !empty($destinationPath) ? $destinationPath : $this->destinationPath;
        $dumpCommand     = $this->prepareDumpCommand($destinationPath);
        $this->command   = $this->removeExtraSpaces($dumpCommand);
        $this->run();
    }

    public function restore(string $restorePath = "")
    {
        $restorePath    = !empty($restorePath) ? $restorePath : $this->restorePath;
        $restoreCommand = $this->prepareRestoreCommand($restorePath);
        $this->command  = $this->removeExtraSpaces($restoreCommand);
        $this->run();
    }

    protected function prepareDumpCommand(string $destinationPath): string
    {
        $dumpCommand = sprintf(
            "%ssqlite3 %s .dump",
            $this->commandBinaryPath,
            $this->dbName
        );

        if ($this->isCompress) {

            return "{$dumpCommand} | {$this->compressBinaryPath}{$this->compressCommand} > {$destinationPath}{$this->compressExtension}";
        }

        return "{$dumpCommand} > {$destinationPath}";
    }

    protected function prepareRestoreCommand(string $filePath): string
    {
        $restoreCommand = sprintf("%ssqlite3 %s",
            $this->commandBinaryPath,
            $this->dbName
        );

        if ($this->isCompress) {
            return "{$this->compressBinaryPath}{$this->compressCommand} < {$filePath} | {$restoreCommand}";
        }

        return "{$restoreCommand} < {$filePath}";
    }
}
