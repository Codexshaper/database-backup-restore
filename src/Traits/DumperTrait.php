<?php

namespace CodexShaper\Dumper\Traits;

trait DumperTrait
{
    /*@var string*/
    protected $dbName;
    /*@var string*/
    protected $username;
    /*@var string*/
    protected $password;
    /*@var string*/
    protected $host = 'localhost';
    /*@var int*/
    protected $port = 3306;
    /*@var string*/
    protected $socket = '';
    /*@var string*/
    protected $commandBinaryPath = '';
    /*@var int*/
    protected $timeout = 0;
    /*@var array*/
    protected $tables = [];
    /*@var array*/
    protected $ignoreTables = [];
    /*@var string*/
    protected $destinationPath = 'dump.sql';
    /*@var string*/
    protected $restorePath = 'dump.sql';
    /*@var bool*/
    protected $isCompress = false;
    /*@var string*/
    protected $compressCommand = "gzip";
    /*@var string*/
    protected $compressBinaryPath = "";
    /*@var string*/
    protected $compressExtension = ".gz";
    /*@var bool*/
    protected $debug = false;
    /*@var string*/
    protected $command = "";

    public function setDbName(string $name)
    {
        $this->dbName = $name;
        return $this;
    }

    public function setUserName(string $username)
    {
        $this->username = $username;
        return $this;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
        return $this;
    }

    public function setHost(string $host)
    {
        $this->host = $host;
        return $this;
    }

    public function setPort(int $port)
    {
        $this->port = $port;
        return $this;
    }

    public function setSocket(string $socket)
    {
        $this->socket = $socket;
        return $this;
    }

    public function setTimeOut(int $timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }
    /**
     * @param string|array $tables
     * @throws \Exception
     */
    public function setTables($tables)
    {
        if (!empty($this->ignoreTables)) {
            throw new \Exception("You can choose only once between tables and ignoreTables at a time");
        }
        if (is_string($tables)) {
            $tables = [$tables];
        }
        $this->tables = $tables;
        return $this;
    }
    /**
     * @param string|array $tables
     * @throws \Exception
     */
    public function setIgnoreTables($tables)
    {
        if (!empty($this->tables)) {
            throw new \Exception("You can choose only once between tables and ignoreTables at a time");
        }
        if (is_string($tables)) {
            $tables = [$tables];
        }
        $this->ignoreTables = $tables;
        return $this;
    }
    public function setCommandBinaryPath(string $path)
    {
        $this->commandBinaryPath = $path;
        return $this;
    }
    public function setDestinationPath(string $path)
    {
        $this->destinationPath = $path;
        return $this;
    }
    public function setRestorePath(string $path)
    {
        $this->restorePath = $path;
        return $this;
    }
    // Compress
    public function setCompressBinaryPath(string $path)
    {
        $this->compressBinaryPath = $path;
        return $this;
    }
    public function setCompressCommand(string $command)
    {
        $this->compressCommand = $command;
        $this->isCompress      = true;
        return $this;
    }
    public function setCompressExtension(string $extension)
    {
        $this->compressExtension = $extension;
        return $this;
    }
    public function useCompress(string $command = "gzip", string $extension = ".gz", string $binary_path = "")
    {
        $this->compressCommand    = $command;
        $this->compressExtension  = $extension;
        $this->compressBinaryPath = $binary_path;
        $this->isCompress         = true;

        return $this;
    }
    public function enableDebug()
    {
        $this->debug = true;
        return $this;
    }
    public function getDbName()
    {
        return $this->dbName;
    }
    public function getUserName()
    {
        return $this->username;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function getHost()
    {
        return $this->host;
    }
    public function getPort()
    {
        return $this->port;
    }
    public function getSocket()
    {
        return $this->socket;
    }
    public function getTimeOut()
    {
        return $this->timeout;
    }
    public function getCommandBinaryPath()
    {
        return $this->commandBinaryPath;
    }
    public function getDestinationPath()
    {
        return $this->destinationPath;
    }
    public function getRestorePath()
    {
        return $this->restorePath;
    }
}
