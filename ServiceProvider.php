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

        \Event::listen('admin::menu.load', function($menu) {
            $format = '<img src="%s" class="fa" />&nbsp;&nbsp;<span>%s</span>';
            $menu->add(
                sprintf($format, moduleAsset('backup', 'images/icon_backup.png'), 'Backup'),
                route('admin.backup.index')
            );
        });
    }
}
