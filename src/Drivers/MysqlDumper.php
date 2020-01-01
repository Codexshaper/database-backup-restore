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
    public function doNotCreateTables()
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

    public function getDumpCommand(string $credentialFile = '', $destinationPath = '')
    {
        $destinationPath = !empty($destinationPath) ? $destinationPath : $this->destinationPath;
        $dumpCommand     = $this->prepareDumpCommand($credentialFile, $destinationPath);

        return $this->removeExtraSpaces($dumpCommand);
    }

    public function getRestoreCommand(string $credentialFile = '', string $filePath = '')
    {
        $filePath       = !empty($filePath) ? '"' . $filePath : $this->restorePath;
        $restoreCommand = $this->prepareRestoreCommand($credentialFile, $filePath);

        return $this->removeExtraSpaces($restoreCommand);
    }

    protected function prepareDumpCommand(string $credentialFile, string $destinationPath): string
    {
        $dumpCommand = sprintf(
            '%s %s %s %s %s %s %s %s %s %s %s %s',
            $this->quoteCommand("{$this->commandBinaryPath}mysqldump"),
            $this->prepareAuthentication($credentialFile),
            $this->prepareSocket(),
            $this->prepareSkipComments(),
            $this->prepareCreateTables(),
            $this->prepareSingleTransaction(),
            $this->prepareSkipLockTables(),
            $this->prepareQuick(),
            $this->prepareDefaultCharSet(),
            $this->prepareDatabase(),
            $this->prepareIncludeTables(),
            $this->prepareIgnoreTables()
        );

        if ($this->isCompress) {
            $compressCommand = $this->quoteCommand("{$this->compressBinaryPath}{$this->compressCommand}");
            return "{$dumpCommand} | {$compressCommand} > \"{$destinationPath}{$this->compressExtension}\"";
        }

        return "{$dumpCommand} > \"{$destinationPath}\"";
    }

    protected function prepareRestoreCommand(string $credentialFile, string $filePath): string
    {
        $restoreCommand = sprintf("%s %s %s",
            $this->quoteCommand("{$this->commandBinaryPath}mysql"),
            $this->prepareAuthentication($credentialFile),
            $this->prepareDatabase()
        );

        if ($this->isCompress) {

            $compressCommand = $this->quoteCommand("{$this->compressBinaryPath}{$this->compressCommand}");

            return "{$compressCommand} < \"{$filePath}\" | {$restoreCommand}";
        }

        return "{$restoreCommand} < \"{$filePath}\"";
    }

    protected function runCommand($filePath, $action)
    {
        try {

            $credentials = $this->getCredentials();
            $tempFile    = tempnam(sys_get_temp_dir(), 'mysqlpass');
            $handler     = fopen($tempFile, 'r+');
            if ($handler !== false) {
                fwrite($handler, $credentials);

                if ($action == 'dump') {
                    $dumpCommand   = $this->prepareDumpCommand($tempFile, $filePath);
                    $this->command = $this->removeExtraSpaces($dumpCommand);
                } else if ($action == 'restore') {
                    $dumpCommand   = $this->prepareRestoreCommand($tempFile, $filePath);
                    $this->command = $this->removeExtraSpaces($dumpCommand);
                }

                $process = $this->prepareProcessCommand();

                if ($this->debug) {
                    $process->mustRun();
                } else {
                    $process->run();
                }

                fclose($handler);
                unlink($tempFile);
            }

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

    public function prepareSingleTransaction()
    {
        return $this->singleTransaction ? '--single-transaction' : '';
    }

    public function prepareSkipLockTables()
    {
        return $this->skipLockTables ? '--skip-lock-tables' : '';
    }

    public function prepareQuick()
    {
        return $this->quick ? '--quick' : '';
    }

    public function prepareCreateTables()
    {
        return !$this->createTables ? '--no-create-info' : '';
    }

    public function prepareSkipComments()
    {
        return $this->skipComments ? '--skip-comments' : '';
    }

    public function prepareDefaultCharSet()
    {
        return ($this->defaultCharacterSet !== '') ? "--default-character-set={$this->defaultCharacterSet}" : '';
    }

    public function prepareAuthentication(string $credentialFile)
    {
        return "--defaults-extra-file=\"{$credentialFile}\"";
    }
}
