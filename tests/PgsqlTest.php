<?php

namespace CodexShaper\Dumper\Test;

use CodexShaper\Dumper\Drivers\PgsqlDumper;
use CodexShaper\Dumper\Dumper;
use PHPUnit\Framework\TestCase;

class PgsqlTest extends TestCase
{
    protected $quote = "'";

    public function __construct()
    {
        parent::__construct();
        $isWindows   = Dumper::isWindows();
        $this->quote = $isWindows ? '"' : "'";
    }
    /** @test */
    public function it_will_make_a_dump_command()
    {
        $dumpCommand = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setDestinationPath('dump.sql')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}pg_dump{$this->quote} -U username -h localhost -p 5432 dbname > \"dump.sql\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_compression_enabled()
    {
        $dumpCommand = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useCompress()
            ->setDestinationPath('dump.sql')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}pg_dump{$this->quote} -U username -h localhost -p 5432 dbname | {$this->quote}gzip{$this->quote} > \"dump.sql.gz\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_absolute_path()
    {
        $dumpCommand = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setDestinationPath('/path/to/dump.sql')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}pg_dump{$this->quote} -U username -h localhost -p 5432 dbname > \"/path/to/dump.sql\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_using_inserts()
    {
        $dumpCommand = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useInserts()
            ->setDestinationPath('dump.sql')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}pg_dump{$this->quote} -U username -h localhost -p 5432 --inserts dbname > \"dump.sql\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_a_custom_port()
    {
        $dumpCommand = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setPort(1111)
            ->setDestinationPath('dump.sql')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}pg_dump{$this->quote} -U username -h localhost -p 1111 dbname > \"dump.sql\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_custom_binary_path()
    {
        $dumpCommand = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setCommandBinaryPath('/path/to/pgsql/')
            ->setDestinationPath('dump.sql')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}/path/to/pgsql/pg_dump{$this->quote} -U username -h localhost -p 5432 dbname > \"dump.sql\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_a_custom_socket()
    {
        $dumpCommand = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setSocket('/path/to/socket.1111')
            ->setDestinationPath('dump.sql')
            ->getDumpCommand();

        $this->assertEquals("{$this->quote}pg_dump{$this->quote} -U username -h /path/to/socket.1111 -p 5432 dbname > \"dump.sql\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_for_multiple_table()
    {
        $dumpCommand = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setTables(['test1', 'test2'])
            ->setDestinationPath('dump.sql')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}pg_dump{$this->quote} -U username -h localhost -p 5432 -t test1 -t test2 dbname > \"dump.sql\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_for_single_table()
    {
        $dumpCommand = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setTables('test')
            ->setDestinationPath('dump.sql')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}pg_dump{$this->quote} -U username -h localhost -p 5432 -t test dbname > \"dump.sql\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_for_ignore_multiple_table()
    {
        $dumpCommand = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setIgnoreTables(['test1', 'test2'])
            ->setDestinationPath('dump.sql')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}pg_dump{$this->quote} -U username -h localhost -p 5432 -T test1 -T test2 dbname > \"dump.sql\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_dump_command_for_ignore_single_table()
    {
        $dumpCommand = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setIgnoreTables('test')
            ->setDestinationPath('dump.sql')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}pg_dump{$this->quote} -U username -h localhost -p 5432 -T test dbname > \"dump.sql\"", $dumpCommand);
    }

    /** @test */
    public function it_will_throw_an_exception_when_setting_tables_and_ignore_tables_together()
    {
        $this->expectException(\Exception::class);
        $command = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setTables(['test1', 'test2'])
            ->setIgnoreTables(['test3', 'test4'])
            ->setDestinationPath('dump.sql')
            ->getDumpCommand();
    }

    /** @test */
    public function it_will_make_a_dump_command_with_no_create_info()
    {
        $dumpCommand = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->doNotCreateTables()
            ->setDestinationPath('dump.sql')
            ->getDumpCommand();

        $this->assertSame("{$this->quote}pg_dump{$this->quote} -U username -h localhost -p 5432 --data-only dbname > \"dump.sql\"", $dumpCommand);
    }

    /*
     * Restore
     */

    /** @test */
    public function it_will_make_a_restore_command()
    {
        $dumpCommand = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setRestorePath('dump.sql')
            ->getRestoreCommand();

        $this->assertSame("{$this->quote}psql{$this->quote} -U username -h localhost -p 5432 dbname < \"dump.sql\"", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_restore_command_with_compression_enabled()
    {
        $dumpCommand = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useCompress()
            ->setRestorePath('dump.sql.gz')
            ->getRestoreCommand();

        $this->assertSame("{$this->quote}gzip{$this->quote} < \"dump.sql.gz\" | {$this->quote}psql{$this->quote} -U username -h localhost -p 5432 dbname", $dumpCommand);
    }

    /** @test */
    public function it_will_make_a_restore_command_with_absolute_path()
    {
        $dumpCommand = PgsqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setRestorePath('/path/to/dump.sql')
            ->getRestoreCommand();

        $this->assertSame("{$this->quote}psql{$this->quote} -U username -h localhost -p 5432 dbname < \"/path/to/dump.sql\"", $dumpCommand);
    }
}
