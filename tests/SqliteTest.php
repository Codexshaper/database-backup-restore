<?php

namespace CodexShaper\Dumper\Test;

use CodexShaper\Dumper\Drivers\SqliteDumper;
use CodexShaper\Dumper\Dumper;
use PHPUnit\Framework\TestCase;

class SqliteTest extends TestCase
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
        $this->assertInstanceOf(SqliteDumper::class, SqliteDumper::create());
    }

    /** @test */
    public function it_will_make_a_dump_command()
    {
        $dumpCommand = SqliteDumper::create()
            ->setDbName('database.sqlite')
            ->setDestinationPath('dump.sql')
            ->getDumpCommand();

        $this->assertEquals("{$this->quote}sqlite3{$this->quote} database.sqlite .dump > \"dump.sql\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_compression_enabled()
    {
        $dumpCommand = SqliteDumper::create()
            ->setDbName('database.sqlite')
            ->useCompress()
            ->setDestinationPath('dump.sql')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}sqlite3{$this->quote} database.sqlite .dump | {$this->quote}gzip{$this->quote} > \"dump.sql.gz\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_absolute_path()
    {
        $dumpCommand = SqliteDumper::create()
            ->setDbName('database.sqlite')
            ->setDestinationPath('/path/to/sqlite/dump.sql')
            ->getDumpCommand();
        $this->assertSame("{$this->quote}sqlite3{$this->quote} database.sqlite .dump > \"/path/to/sqlite/dump.sql\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_custom_binary_path()
    {
        $dumpCommand = SqliteDumper::create()
            ->setDbName('dbname')
            ->setCommandBinaryPath('/path/to/mongodb/bin/')
            ->setDestinationPath('dump')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}/path/to/mongodb/bin/sqlite3{$this->quote} dbname .dump > \"dump\"", $dumpCommand);
    }

    /*
     * Restore
     */

    /** @test */
    public function it_will_make_a_restore_command()
    {
        $restoreCommand = SqliteDumper::create()
            ->setDbName('database.sqlite')
            ->setRestorePath('dump.sql')
            ->getRestoreCommand();

        $this->assertEquals("{$this->quote}sqlite3{$this->quote} database.sqlite < \"dump.sql\"", $restoreCommand);
    }

    /** @test */
    public function it_will_make_a_restore_command_with_compression_enabled()
    {
        $restoreCommand = SqliteDumper::create()
            ->setDbName('database.sqlite')
            ->useCompress()
            ->setRestorePath('dump.sql.gz')
            ->getRestoreCommand();

        $this->assertSame("{$this->quote}gzip{$this->quote} < \"dump.sql.gz\" | {$this->quote}sqlite3{$this->quote} database.sqlite", $restoreCommand);
    }

    /** @test */
    public function it_will_make_a_restore_command_with_absolute_path()
    {
        $restoreCommand = SqliteDumper::create()
            ->setDbName('database.sqlite')
            ->setRestorePath('/path/to/sqlite/dump.sql')
            ->getRestoreCommand();
        $this->assertSame("{$this->quote}sqlite3{$this->quote} database.sqlite < \"/path/to/sqlite/dump.sql\"", $restoreCommand);
    }

    /** @test */
    public function it_will_make_a_restore_command_with_custom_binary_path()
    {
        $restoreCommand = SqliteDumper::create()
            ->setDbName('dbname')
            ->setCommandBinaryPath('/path/to/mongodb/bin/')
            ->setRestorePath('dump')
            ->getRestoreCommand();

        $this->assertSame("{$this->quote}/path/to/mongodb/bin/sqlite3{$this->quote} dbname < \"dump\"", $restoreCommand);
    }
}
