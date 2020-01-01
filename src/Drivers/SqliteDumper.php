<?php

namespace CodexShaper\Dumper\Drivers;

use CodexShaper\Dumper\Dumper;

class SqliteDumper extends Dumper
{
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
        $dumpCommand = sprintf(
            "%s %s .dump",
            $this->quoteCommand($this->commandBinaryPath . 'sqlite3'),
            $this->dbName
        );

        if ($this->isCompress) {
            $compressCommand = $this->quoteCommand("{$this->compressBinaryPath}{$this->compressCommand}");
            return "{$dumpCommand} | $compressCommand > \"{$destinationPath}{$this->compressExtension}\"";
        }

        return "{$dumpCommand} > \"{$destinationPath}\"";
    }

    protected function prepareRestoreCommand(string $filePath): string
    {
        $restoreCommand = sprintf("%s %s",
            $this->quoteCommand($this->commandBinaryPath . 'sqlite3'),
            $this->dbName
        );

        if ($this->isCompress) {
            $compressCommand = $this->quoteCommand("{$this->compressBinaryPath}{$this->compressCommand}");
            return "$compressCommand < \"{$filePath}\" | {$restoreCommand}";
        }

        return "{$restoreCommand} < \"{$filePath}\"";
    }
}
