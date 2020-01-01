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

    public function prepareHost()
    {
        switch (strtolower($this->getDumperClassName())) {
            case 'pgsqldumper':
                return ($this->socket !== '') ? $this->socket : $this->host;
                break;
            case 'mongodumper';
                return !empty($this->host) ? "--host {$this->host}" : "";
                break;
        }
        return $host;
    }

    public function preparePort()
    {
        switch (strtolower($this->getDumperClassName())) {
            case 'pgsqldumper':
                return !empty($this->port) ? '-p ' . $this->port : '';
                break;
            case 'mongodumper':
                return !empty($this->port) ? "--port {$this->port}" : "";
                break;
        }
    }

    public function prepareSocket()
    {
        switch (strtolower($this->getDumperClassName())) {
            case 'mysqldumper':
                return ($this->socket !== '') ? "--socket={$this->socket}" : '';
                break;
        }
    }

    public function prepareDatabase()
    {
        switch (strtolower($this->getDumperClassName())) {
            case 'mysqldumper':
            case 'pgsqldumper':
                return !empty($this->dbName) ? $this->dbName : "";
                break;
            case 'mongodumper';
                return !empty($this->dbName) ? "--db {$this->dbName}" : "";
                break;
        }
    }

    public function prepareUserName()
    {
        switch (strtolower($this->getDumperClassName())) {
            case 'pgsqldumper':
                return !empty($this->username) ? $this->username : "";
                break;
            case 'mongodumper';
                return !empty($this->username) ? "--username {$this->username}" : "";
                break;
        }
    }

    public function prepareIncludeTables()
    {
        switch (strtolower($this->getDumperClassName())) {
            case 'mysqldumper':
                $includeTables = (count($this->tables) > 0) ? implode(' ', $this->tables) : '';
                return !empty($includeTables) ? "--tables {$includeTables}" : '';
                break;
            case 'pgsqldumper':
                return (count($this->tables) > 0) ? '-t ' . implode(' -t ', $this->tables) : "";
                break;
        }
    }

    public function prepareIgnoreTables()
    {
        switch (strtolower($this->getDumperClassName())) {
            case 'mysqldumper':
                $ignoreTablesArgs = [];
                foreach ($this->ignoreTables as $tableName) {
                    $ignoreTablesArgs[] = "--ignore-table={$this->dbName}.{$tableName}";
                }
                return (count($ignoreTablesArgs) > 0) ? implode(' ', $ignoreTablesArgs) : '';
                break;
            case 'pgsqldumper';
                return (count($this->ignoreTables) > 0) ? '-T ' . implode(' -T ', $this->ignoreTables) : '';
                break;
        }
    }

    public function prepareCreateTables()
    {
        switch (strtolower($this->getDumperClassName())) {
            case 'mysqldumper':
                return !$this->createTables ? '--no-create-info' : '';
                break;
            case 'pgsqldumper':
                return (!$this->createTables) ? '--data-only' : '';
                break;
        }
        return $createTables;
    }

    public function getDumperClassName()
    {
        $classWithNamespace = static::class;
        $partials           = explode("\\", $classWithNamespace);
        $className          = end($partials);
        return $className;
    }
}
