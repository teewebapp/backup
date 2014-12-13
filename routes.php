<?php

namespace Tee\Backup;
use Route, Config;

Route::group(['prefix' => 'admin'], function() {

    Route::get('backup', [
        'as' => 'admin.backup.index',
        'uses' => __NAMESPACE__.'\Controllers\AdminController@index'
    ]);

    Route::get('backup/create', [
        'as' => 'admin.backup.create',
        'uses' => __NAMESPACE__.'\Controllers\AdminController@create'
    ]);

    Route::get('backup/{id}/download', [
        'as' => 'admin.backup.download',
        'uses' => __NAMESPACE__.'\Controllers\AdminController@download'
    ]);
    
    Route::delete('backup/{id}', [
        'as' => 'admin.backup.destroy',
        'uses' => __NAMESPACE__.'\Controllers\AdminController@destroy'
    ]);
});
