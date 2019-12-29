<?php

namespace CodexShaper\Dumper;

use CodexShaper\Dumper\Contracts\Dumper as DumperContract;
use CodexShaper\Dumper\Traits\DumperTrait;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

abstract class Dumper implements DumperContract
{
    use DumperTrait;

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
}
