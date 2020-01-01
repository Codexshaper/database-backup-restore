<?php

namespace CodexShaper\Dumper;

use CodexShaper\Dumper\Contracts\Dumper as DumperContract;
use CodexShaper\Dumper\Traits\DumperTrait;
use CodexShaper\Dumper\Traits\PrepareOptionsTrait;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

abstract class Dumper implements DumperContract
{
    use DumperTrait, PrepareOptionsTrait;

    public function __construct(array $options = [])
    {
        foreach ($options as $option => $value) {
            if (property_exists($this, $option)) {
                $this->{$option} = $value;
            }
        }
    }
    /**
     * @return $this
     */
    public static function create(array $options = [])
    {
        return new static($options);
    }
    /**
     * @return \Symfony\Component\Process\Process
     */
    protected function prepareProcessCommand()
    {
        $process = Process::fromShellCommandline($this->command);
        $process->setTimeout($this->timeout);
        return $process;
    }
    /**
     * @return \Symfony\Component\Process\Process
     */
    protected function run()
    {
        try {

            $process = Process::fromShellCommandline($this->command);
            $process->setTimeout($this->timeout);

            if ($this->debug) {
                return $process->mustRun();
            }

            return $process->run();

        } catch (ProcessFailedException $e) {
            throw new \Exception($e->getMessage());

        }
    }

    abstract public function dump();
    abstract public function restore();

    public function getDumpCommand(string $credentialFile = '', $destinationPath = '')
    {
        $destinationPath = !empty($destinationPath) ? $destinationPath : $this->destinationPath;
        switch (strtolower($this->getDumperClassName())) {
            case 'mysqldumper':
                $dumpCommand = $this->prepareDumpCommand($credentialFile, $destinationPath);
                break;
            default:
                $dumpCommand = $this->prepareDumpCommand($destinationPath);
                break;
        }

        return $this->removeExtraSpaces($dumpCommand);
    }

    public function getRestoreCommand(string $credentialFile = '', string $filePath = '')
    {
        $filePath = !empty($filePath) ? '"' . $filePath : $this->restorePath;
        switch (strtolower($this->getDumperClassName())) {
            case 'mysqldumper':
                $restoreCommand = $this->prepareRestoreCommand($credentialFile, $filePath);
                break;
            default:
                $restoreCommand = $this->prepareRestoreCommand($filePath);
                break;
        }

        return $this->removeExtraSpaces($restoreCommand);
    }

    public function getDumperClassName()
    {
        $classWithNamespace = static::class;
        $partials           = explode("\\", $classWithNamespace);
        $className          = end($partials);
        return $className;
    }

    public function removeExtraSpaces(string $str)
    {
        return preg_replace('/\s+/', ' ', $str);
    }

    public static function isWindows()
    {
        return strcasecmp(substr(PHP_OS, 0, 3), 'WIN') == 0 ? true : false;
    }

    public function quoteCommand(string $command)
    {
        return static::isWindows() ? "\"{$command}\"" : "'{$command}'";
    }
}
