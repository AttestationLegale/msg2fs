<?php

use Alg\Msg2fs\Msg2fs;

class Msg2fsTest extends PHPUnit_Framework_TestCase {

    protected $validTmpDir = '/tmp/msg2fs/';
    protected $invalidTmpDir = '/tmp/msg2fs/fail/';

    protected function setUp()
    {
        if (!file_exists($this->validTmpDir)) {
            mkdir($this->validTmpDir);
        }
    }

    /**
     * @expectedException RuntimeException
     */
    public function testSpoolDirIsNotWritable() {
        putenv("SPOOLDIR=$this->invalidTmpDir");

        (new Msg2fs())->save([], '', '');
    }


    public function testSpoolDirIsWritable() {
        putenv("SPOOLDIR=$this->validTmpDir");

        (new Msg2fs())->save([], '', '');

        $files = array_diff(scandir($this->validTmpDir), array('.','..'));
        foreach ($files as $file) {
            $this->assertEquals(1, preg_match('#.v1$#', $file));
        }
    }

    protected function tearDown()
    {
        $files = array_diff(scandir($this->validTmpDir), array('.','..'));
        foreach ($files as $file) {
            unlink($this->validTmpDir . $file);
        }

        if (file_exists($this->validTmpDir)) {
            rmdir($this->validTmpDir);
        }
    }
}
