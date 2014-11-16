<?php

namespace Tee\Backup;
use Alchemy\Zippy\Zippy;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot() 
    {
        $this->commands('Tee\Backup\Commands\BackupCreate');
    }

    public function register()
    {
        $this->app->bind('backup.zippy', function() {
            return Zippy::load();
        });
    }
}
