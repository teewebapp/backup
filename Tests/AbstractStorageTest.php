<?php

namespace Tee\Backup\Tests;

use Tee\System\Tests\TestCase;
use Tee\Backup\Storage\File as IFile;

abstract class AbstractStorageTest extends TestCase
{
    public function getStorage()
    {
        throw new Exception('Not Implemented');
    }

    public function testCreateStorage()
    {
        $tempName = tempnam(sys_get_temp_dir(), 'localbackup');
        $storage = $this->getStorage();
        $this->assertTrue($storage->login());
        return $storage;
    }

    /**
     * @depends testCreateStorage
     */
    public function testListFiles($storage) {
        $files = $storage->listFiles();
        foreach($files as $file) {
            $this->assertTrue($file instanceof IFile);
            $this->assertNotEmpty($file->getName());
        }
    }

    /**
     * @depends testCreateStorage
     */
    public function testUploadFile($storage)
    {
        $storage->chunkSizeBytes = 0.5 * 1024 * 1024;
        $fileName = tempnam(sys_get_temp_dir(), 'test');
        $content = str_repeat("i", round($storage->chunkSizeBytes * 1.5));
        file_put_contents($fileName, $content);
        $file = $storage->uploadFile($fileName);
        $this->assertTrue($file instanceof IFile);
        return array($storage, $fileName);
    }

    /**
     * @depends testUploadFile
     */
    public function testGetByName($params)
    {
        $storage = $params[0];
        $fileName = $params[1];
        $file = $storage->getByName(basename($fileName));
        $this->assertTrue($file instanceof IFile);
        return $file;
    }

    /**
     * @depends testGetByName
     */
    public function testDownload($file)
    {
        $tempName = tempnam(sys_get_temp_dir(), 'other');
        unlink($tempName);
        $file->download($tempName);
        $this->assertTrue(file_exists($tempName));
        $content = str_repeat("i", round((0.5 * 1024 * 1024) * 1.5));
        $this->assertEquals($content, file_get_contents($tempName));
        $file->delete();
    }
}