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
        //$service = App::make('Tee\Backup\Services\BackupService');
        $databaseBackup = new \Tee\Backup\Package\Database();
        $databaseBackup->connection = $connection;
        $info = Config::get("database.connections.$connection");
        if(!$info)
            throw new \Exception("Connection $connection not found");

        if($info['driver'] == 'mysql') {
            $databaseBackup->filename = md5($connection).'.sql';
            $filename = "$tempDir/".$databaseBackup->filename;
            $this->dumpMysqlDatabase($info, $filename);
        } else if($info['driver'] == 'sqlite') {
            $databaseBackup->filename = md5($connection).'.sqlite';
            $filename = "$tempDir/".$databaseBackup->filename;
            $this->dumpSqliteDatabase($info, $filename);
        } else {
            throw new \Exception("Backup not avaliable to driver: {$info['driver']}");
        }
        $databaseBackup->md5 = md5_file($filename);
        return $databaseBackup;
    }

    public function dumpMysqlDatabase($config, $filename) {
        $service = App::make('Tee\Backup\Services\BackupService');

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
            ->tmp($service->getTemporaryDirectory());

        new Export($dump);
    }

    public function dumpSqliteDatabase($config, $filename) {
        $database = $config['database'];
        copy($database, $filename);
        return true;
    }
}