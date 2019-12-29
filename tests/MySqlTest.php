<?php

namespace CodexShaper\Dumper\Test;

use CodexShaper\Dumper\Drivers\MysqlDumper;
use PHPUnit\Framework\TestCase;

class MySqlTest extends TestCase
{
    /** @test */
    public function it_provides_a_factory_method()
    {
        $this->assertInstanceOf(MysqlDumper::class, MysqlDumper::create());
    }

    /** @test */
    public function it_can_generate_a_dump_command()
    {
        $dumper = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->dump('dump.sql');
        $tempFile = $dumper->getTempFile();
        $command  = $dumper->getCommand();
        $this->assertSame('mysqldump --defaults-extra-file=' . $tempFile . ' "dbname" --skip-comments > dump.sql', $command);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_compression_enabled()
    {
        $dumper = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useCompress()
            ->dump('dump.sql');
        $command  = $dumper->getCommand();
        $tempFile = $dumper->getTempFile();
        $this->assertSame('mysqldump --defaults-extra-file=' . $tempFile . ' "dbname" --skip-comments | gzip > dump.sql.gz', $command);
    }

    /** @test */
    public function it_can_generate_a_restore_command()
    {
        $dumper = MysqlDumper::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->restore('dump.sql');
        $tempFile = $dumper->getTempFile();
        $command  = $dumper->getCommand();

        $this->assertSame('mysql --defaults-extra-file=' . $tempFile . ' "dbname" < dump.sql', $command);
    }
}
