<?php
namespace Tee\Backup\Database;
use App, Config;

use Rah\Danpu\Dump;
use Rah\Danpu\Export;

/**
 * Create a backup of an Database
 * @author Anderson Danilo
 */
class Backup {

    public function backup($tempDir, $connection) {
        $databaseBackup = new \Tee\Backup\Package\Database();
        $databaseBackup->connection = $connection;
        $info = Config::get("database.connections.$connection");
        if(!$info)
            throw new \Exception("Connection $connection not found");

        if($info['driver'] == 'mysql') {
            $filename = "$tempDir/".md5($connection).'.sql';
            $this->dumpMysqlDatabase($info, $filename);
        } else if($info['driver'] == 'sqlite') {
            $filename = "$tempDir/".md5($connection).'.sqlite';
            $this->dumpSqliteDatabase($info, $filename);
        } else {
            throw new \Exception("Backup not avaliable to driver: {$info['driver']}");
        }
        $databaseBackup->filename = $filename;
        $databaseBackup->md5 = md5_file($filename);
        return $databaseBackup;
    }

    public function dumpMysqlDatabase($config, $filename) {
        $username = $config['username'];
        $password = $config['password'];
        $database = $config['database'];
        $host = $config['host'];

        $dump = new Dump;
        $dump
            ->file($filename)
            ->dsn("mysql:dbname=$database;host=$host")
            ->user($username)
            ->pass($password)
            ->tmp(sys_get_temp_dir());

        new Export($dump);
    }

    public function dumpSqliteDatabase($config, $filename) {
        $database = $config['database'];
        copy($database, $filename);
        return true;
    }
}