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
        switch (strtolower($this->getClassName())) {
            case 'pgsqldumper':
                $host = ($this->socket !== '') ? $this->socket : $this->host;
                break;
            case 'mongodumper';
                $host = !empty($this->host) ? "--host {$this->host}" : "";
                break;
        }
        return $host;
    }

    public function preparePort()
    {
        switch (strtolower($this->getClassName())) {
            case 'pgsqldumper':
                $port = !empty($this->port) ? '-p ' . $this->port : '';
                break;
            case 'mongodumper':
                $port = !empty($this->port) ? "--port {$this->port}" : "";
                break;
        }
        return $port;
    }

    public function prepareSocket()
    {
        switch (strtolower($this->getClassName())) {
            case 'mysqldumper':
                $socket = ($this->socket !== '') ? "--socket={$this->socket}" : '';
                break;
        }

        return $socket;
    }

    public function prepareDatabase()
    {
        switch (strtolower($this->getClassName())) {
            case 'mysqldumper':
            case 'pgsqldumper':
                $databse = !empty($this->dbName) ? $this->dbName : "";
                break;
            case 'mongodumper';
                $databse = !empty($this->dbName) ? "--db {$this->dbName}" : "";
                break;
        }
        return $databse;
    }

    public function prepareUserName()
    {
        switch (strtolower($this->getClassName())) {
            case 'pgsqldumper':
                $username = !empty($this->username) ? $this->username : "";
                break;
            case 'mongodumper';
                $username = !empty($this->username) ? "--username {$this->username}" : "";
                break;
        }
        return $username;
    }

    public function prepareIncludeTables()
    {
        switch (strtolower($this->getClassName())) {
            case 'mysqldumper':
                $includeTables    = (count($this->tables) > 0) ? implode(' ', $this->tables) : '';
                $includeTablesArg = !empty($includeTables) ? "--tables {$includeTables}" : '';
                break;
            case 'pgsqldumper':
                $includeTablesArg = (count($this->tables) > 0) ? '-t ' . implode(' -t ', $this->tables) : "";
                break;
        }

        return $includeTablesArg;
    }

    public function prepareIgnoreTables()
    {
        switch (strtolower($this->getClassName())) {
            case 'mysqldumper':
                $ignoreTablesArgs = [];
                foreach ($this->ignoreTables as $tableName) {
                    $ignoreTablesArgs[] = "--ignore-table={$this->dbName}.{$tableName}";
                }
                $ignoreTablesArg = (count($ignoreTablesArgs) > 0) ? implode(' ', $ignoreTablesArgs) : '';
                break;
            case 'pgsqldumper';
                $ignoreTablesArg = (count($this->ignoreTables) > 0) ? '-T ' . implode(' -T ', $this->ignoreTables) : '';
                break;
        }

        return $ignoreTablesArg;
    }

    public function prepareCreateTables()
    {
        switch (strtolower($this->getClassName())) {
            case 'mysqldumper':
                $createTables = !$this->createTables ? '--no-create-info' : '';
                break;
            case 'pgsqldumper':
                $createTables = (!$this->createTables) ? '--data-only' : '';
                break;
        }
        return $createTables;
    }

    public function getClassName()
    {
        $classWithNamespace = static::class;
        $partials           = explode("\\", $classWithNamespace);
        $className          = end($partials);
        return $className;
    }
}
