<?php

namespace CodexShaper\Dumper\Test;

use CodexShaper\Dumper\Drivers\MysqlDumper;
use CodexShaper\Dumper\Dumper;
use PHPUnit\Framework\TestCase;

class MysqlTest extends TestCase
{
    protected $credentialFile = 'credentials.temp';
    protected $quote          = "'";

    public function __construct()
    {
        parent::__construct();
        $isWindows   = Dumper::isWindows();
        $this->quote = $isWindows ? '"' : "'";
    }
    /** @test */
    public function it_provides_a_factory_method()
    {
        $this->assertInstanceOf(MysqlDumper::class, MysqlDumper::create());
    }

    /** @test */
    public function it_will_make_a_dump_command()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setDestinationPath('dump.sql')
            ->getDumpCommand($this->credentialFile);
        $this->assertSame("{$this->quote}mysqldump{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" --skip-comments dbname > \"dump.sql\"", $command);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_compression_enabled()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useCompress()
            ->setDestinationPath('dump.sql')
            ->getDumpCommand($this->credentialFile);
        $this->assertSame("{$this->quote}mysqldump{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" --skip-comments dbname | {$this->quote}gzip{$this->quote} > \"dump.sql.gz\"", $command);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_absolute_path()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setDestinationPath('/path/to/mysql/dump.sql')
            ->getDumpCommand($this->credentialFile);
        $this->assertSame("{$this->quote}mysqldump{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" --skip-comments dbname > \"/path/to/mysql/dump.sql\"", $command);
    }

    /** @test */
    public function it_will_make_a_dump_command_without_using_comments()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->doNotUseSkipComments()
            ->setDestinationPath('dump.sql')
            ->getDumpCommand($this->credentialFile);
        $this->assertSame("{$this->quote}mysqldump{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" dbname > \"dump.sql\"", $command);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_custom_binary_path()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setCommandBinaryPath('/custom/mysql/mysql/bin/')
            ->setDestinationPath('dump.sql')
            ->getDumpCommand($this->credentialFile);
        $this->assertSame("{$this->quote}/custom/mysql/mysql/bin/mysqldump{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" --skip-comments dbname > \"dump.sql\"", $command);
    }

    /** @test */
    public function it_will_make_a_dump_command_using_single_transaction()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useSingleTransaction()
            ->setDestinationPath('dump.sql')
            ->getDumpCommand($this->credentialFile);
        $this->assertSame("{$this->quote}mysqldump{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" --skip-comments --single-transaction dbname > \"dump.sql\"", $command);
    }

    /** @test */
    public function it_will_make_a_dump_command_using_skip_lock_tables()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useSkipLockTables()
            ->setDestinationPath('dump.sql')
            ->getDumpCommand($this->credentialFile);
        $this->assertSame("{$this->quote}mysqldump{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" --skip-comments --skip-lock-tables dbname > \"dump.sql\"", $command);
    }

    /** @test */
    public function it_will_make_a_dump_command_using_quick()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useQuick()
            ->setDestinationPath('dump.sql')
            ->getDumpCommand($this->credentialFile);

        $this->assertSame("{$this->quote}mysqldump{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" --skip-comments --quick dbname > \"dump.sql\"", $command);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_a_custom_socket()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setSocket(1111)
            ->setDestinationPath('dump.sql')
            ->getDumpCommand($this->credentialFile);
        $this->assertSame("{$this->quote}mysqldump{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" --socket=1111 --skip-comments dbname > \"dump.sql\"", $command);
    }

    /** @test */
    public function it_will_make_a_dump_command_for_multiple_table()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setTables(['test1', 'test2'])
            ->setDestinationPath('dump.sql')
            ->getDumpCommand($this->credentialFile);
        $this->assertSame("{$this->quote}mysqldump{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" --skip-comments dbname --tables test1 test2 > \"dump.sql\"", $command);
    }

    /** @test */
    public function it_will_make_a_dump_command_for_single_table()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setTables('test')
            ->setDestinationPath('dump.sql')
            ->getDumpCommand($this->credentialFile);
        $this->assertSame("{$this->quote}mysqldump{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" --skip-comments dbname --tables test > \"dump.sql\"", $command);
    }

    /** @test */
    public function it_will_make_a_dump_command_for_ignore_multiple_table()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setIgnoreTables(['test1', 'test2'])
            ->setDestinationPath('dump.sql')
            ->getDumpCommand($this->credentialFile);
        $this->assertSame("{$this->quote}mysqldump{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" --skip-comments dbname --ignore-table=dbname.test1 --ignore-table=dbname.test2 > \"dump.sql\"", $command);
    }

    /** @test */
    public function it_will_make_a_dump_command_for_ignore_single_table()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setIgnoreTables('test')
            ->setDestinationPath('dump.sql')
            ->getDumpCommand($this->credentialFile);
        $this->assertSame("{$this->quote}mysqldump{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" --skip-comments dbname --ignore-table=dbname.test > \"dump.sql\"", $command);
    }

    /** @test */
    public function it_will_throw_an_exception_when_setting_tables_and_ignore_tables_together()
    {
        $this->expectException(\Exception::class);
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setTables(['test1', 'test2'])
            ->setIgnoreTables(['test3', 'test4'])
            ->setDestinationPath('dump.sql')
            ->getDumpCommand($this->credentialFile);
    }

    /** @test */
    public function it_will_make_a_dump_command_with_no_create_info()
    {
        $dumpCommand = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->doNotCreateTables()
            ->setDestinationPath('dump.sql')
            ->getDumpCommand($this->credentialFile);

        $this->assertSame("{$this->quote}mysqldump{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" --skip-comments --no-create-info dbname > \"dump.sql\"", $dumpCommand);
    }

    /*
     * Restore Testing
     */

    /** @test */
    public function it_will_make_a_restore_command()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setRestorePath('dump.sql')
            ->getRestoreCommand($this->credentialFile);
        $this->assertSame("{$this->quote}mysql{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" dbname < \"dump.sql\"", $command);
    }

    /** @test */
    public function it_will_make_a_restore_command_with_compression_enabled()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useCompress("gunzip")
            ->setRestorePath('dump.sql.gz')
            ->getRestoreCommand($this->credentialFile);
        $this->assertSame("{$this->quote}gunzip{$this->quote} < \"dump.sql.gz\" | {$this->quote}mysql{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" dbname", $command);
    }

    /** @test */
    public function it_will_make_a_restore_command_with_absolute_path()
    {
        $command = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setRestorePath('/path/to/mysql/dump.sql')
            ->getRestoreCommand($this->credentialFile);
        $this->assertSame("{$this->quote}mysql{$this->quote} --defaults-extra-file=\"{$this->credentialFile}\" dbname < \"/path/to/mysql/dump.sql\"", $command);
    }
}
