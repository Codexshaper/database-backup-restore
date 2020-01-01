<?php

namespace CodexShaper\Dumper\Test;

use CodexShaper\Dumper\Drivers\MongoDumper;
use CodexShaper\Dumper\Dumper;
use PHPUnit\Framework\TestCase;

class MongoTest extends TestCase
{
    protected $quote = "'";

    public function __construct()
    {
        parent::__construct();
        $isWindows   = Dumper::isWindows();
        $this->quote = $isWindows ? '"' : "'";
    }

    /** @test */
    public function it_provides_a_factory_method()
    {
        $this->assertInstanceOf(MongoDumper::class, MongoDumper::create());
    }

    /** @test */
    public function it_will_make_a_dump_command()
    {
        $dumpCommand = MongoDumper::create()
            ->setDbName('dbname')
            ->setDestinationPath('dump')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}mongodump{$this->quote} --db dbname --host localhost --port 27017 --out \"dump\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_compression_enabled()
    {
        $dumpCommand = MongoDumper::create()
            ->setDbName('dbname')
            ->useCompress()
            ->setDestinationPath('dump')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}mongodump{$this->quote} --archive --gzip --db dbname --host localhost --port 27017 > \"dump.gz\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_absolute_path()
    {
        $dumpCommand = MongoDumper::create()
            ->setDbName('dbname')
            ->setDestinationPath('/path/to/directory/dump')
            ->getDumpCommand();
        $this->assertSame("{$this->quote}mongodump{$this->quote} --db dbname --host localhost --port 27017 --out \"/path/to/directory/dump\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_username_and_password()
    {
        $dumpCommand = MongoDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setDestinationPath('dump')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}mongodump{$this->quote} --db dbname --username username --password password --host localhost --port 27017 --out \"dump\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_custom_host_and_port()
    {
        $dumpCommand = MongoDumper::create()
            ->setDbName('dbname')
            ->setHost('test.mongodb.net')
            ->setPort(27000)
            ->setDestinationPath('dump')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}mongodump{$this->quote} --db dbname --host test.mongodb.net --port 27000 --out \"dump\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_for_a_single_collection()
    {
        $dumpCommand = MongoDumper::create()
            ->setDbName('dbname')
            ->setCollection('collection')
            ->setDestinationPath('dump')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}mongodump{$this->quote} --db dbname --host localhost --port 27017 --collection collection --out \"dump\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_custom_binary_path()
    {
        $dumpCommand = MongoDumper::create()
            ->setDbName('dbname')
            ->setCommandBinaryPath('/path/to/mongodb/bin/')
            ->setDestinationPath('dump')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}/path/to/mongodb/bin/mongodump{$this->quote} --db dbname --host localhost --port 27017 --out \"dump\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_authentication_database()
    {
        $dumpCommand = MongoDumper::create()
            ->setDbName('dbname')
            ->setAuthenticationDatabase('admin')
            ->setDestinationPath('dump')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}mongodump{$this->quote} --db dbname --host localhost --port 27017 --authenticationDatabase admin --out \"dump\"", $dumpCommand);
    }

    /*
     * Restore
     */

    /** @test */
    public function it_will_make_a_restore_command()
    {
        $restoreCommand = MongoDumper::create()
            ->setDbName('dbname')
            ->setRestorePath('dump')
            ->getRestoreCommand();

        $this->assertSame("{$this->quote}mongorestore{$this->quote} --host localhost --port 27017 \"dump\"", $restoreCommand);
    }

    /** @test */
    public function it_will_make_a_restore_command_with_compression_enabled()
    {
        $restoreCommand = MongoDumper::create()
            ->setDbName('dbname')
            ->useCompress()
            ->setRestorePath('dump')
            ->getRestoreCommand();

        $this->assertSame("{$this->quote}mongorestore{$this->quote} --gzip --archive --host localhost --port 27017 < \"dump\"", $restoreCommand);
    }

    /** @test */
    public function it_will_make_a_restore_command_with_absolute_path()
    {
        $restoreCommand = MongoDumper::create()
            ->setDbName('dbname')
            ->setRestorePath('/path/to/directory/dump')
            ->getRestoreCommand();

        $this->assertSame("{$this->quote}mongorestore{$this->quote} --host localhost --port 27017 \"/path/to/directory/dump\"", $restoreCommand);
    }

    /** @test */
    public function it_will_make_a_restore_command_with_username_and_password()
    {
        $restoreCommand = MongoDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setRestorePath('dump')
            ->getRestoreCommand();

        $this->assertSame("{$this->quote}mongorestore{$this->quote} --host localhost --port 27017 --username username \"dump\"", $restoreCommand);
    }

    /** @test */
    public function it_will_make_a_restore_command_with_custom_host_and_port()
    {
        $restoreCommand = MongoDumper::create()
            ->setDbName('dbname')
            ->setHost('test.mongodb.net')
            ->setPort(27000)
            ->setRestorePath('dump')
            ->getRestoreCommand();

        $this->assertSame("{$this->quote}mongorestore{$this->quote} --host test.mongodb.net --port 27000 \"dump\"", $restoreCommand);
    }

    /** @test */
    public function it_will_make_a_restore_command_with_custom_binary_path()
    {
        $restoreCommand = MongoDumper::create()
            ->setDbName('dbname')
            ->setCommandBinaryPath('/path/to/mongodb/bin/')
            ->setRestorePath('dump')
            ->getRestoreCommand();

        $this->assertSame("{$this->quote}/path/to/mongodb/bin/mongorestore{$this->quote} --host localhost --port 27017 \"dump\"", $restoreCommand);
    }
}
