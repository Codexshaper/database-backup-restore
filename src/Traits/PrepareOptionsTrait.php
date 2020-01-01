<?php

namespace CodexShaper\Dumper\Traits;

trait PrepareOptionsTrait
{
    public function prepareHost()
    {
        switch (strtolower($this->getDumperClassName())) {
            case 'pgsqldumper':
                return ($this->socket !== '') ? $this->socket : $this->host;
            case 'mongodumper';
                return !empty($this->host) ? "--host {$this->host}" : "";
        }
    }

    public function preparePort()
    {
        switch (strtolower($this->getDumperClassName())) {
            case 'pgsqldumper':
                return !empty($this->port) ? '-p ' . $this->port : '';
            case 'mongodumper':
                return !empty($this->port) ? "--port {$this->port}" : "";
        }
    }

    public function prepareSocket()
    {
        switch (strtolower($this->getDumperClassName())) {
            case 'mysqldumper':
                return ($this->socket !== '') ? "--socket={$this->socket}" : '';
        }
    }

    public function prepareDatabase()
    {
        switch (strtolower($this->getDumperClassName())) {
            case 'mysqldumper':
            case 'pgsqldumper':
                return !empty($this->dbName) ? $this->dbName : "";
            case 'mongodumper';
                return !empty($this->dbName) ? "--db {$this->dbName}" : "";
        }
    }

    public function prepareUserName()
    {
        switch (strtolower($this->getDumperClassName())) {
            case 'pgsqldumper':
                return !empty($this->username) ? $this->username : "";
            case 'mongodumper';
                return !empty($this->username) ? "--username {$this->username}" : "";
        }
    }

    public function prepareIncludeTables()
    {
        switch (strtolower($this->getDumperClassName())) {
            case 'mysqldumper':
                $includeTables = (count($this->tables) > 0) ? implode(' ', $this->tables) : '';
                return !empty($includeTables) ? "--tables {$includeTables}" : '';
            case 'pgsqldumper':
                return (count($this->tables) > 0) ? '-t ' . implode(' -t ', $this->tables) : "";
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
            case 'pgsqldumper';
                return (count($this->ignoreTables) > 0) ? '-T ' . implode(' -T ', $this->ignoreTables) : '';
        }
    }

    public function getDumperClassName()
    {
        $classWithNamespace = static::class;
        $partials           = explode("\\", $classWithNamespace);
        $className          = end($partials);
        return $className;
    }
}
