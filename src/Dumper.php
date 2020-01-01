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

    abstract public function dump();
    abstract public function restore();

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
