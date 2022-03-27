<?php
namespace tests\units\Cache;

use atoum\atoum;
use Cache\Cache as _Cache;

require_once __DIR__ . '/../bootstrap.php';

class Cache extends atoum\test
{
    public function beforeTestMethod($method)
    {
        // Executed *before each* test method.
        switch ($method) {
            case 'testFlush':
                // Prepare testing environment for testFlush().
                $files = new _Cache();
                // create a few files to delete
                $files->setKey('file_1', 'foobar');
                $files->setKey('file_2', 'foobar');
                $files->setKey('file_3', 'foobar');
                break;
            case 'testGetKey':
                // Prepare testing environment for testGetKey().
                $files = new _Cache();
                $files->setKey('valid', 'foobar');
                $files->setKey('expired', 'foobar');
                // Change the timestamp to 100 seconds in the past so we can test expiration
                touch(CACHE_PATH . sha1('expired') . '.cache', time() - 100);
                break;
        }
    }

    public function testSetKey()
    {
        $obj = new _Cache();
        $this
            ->boolean($obj->setKey('test', 'foobar'))
                ->isEqualTo(true);
    }

    public function getKeyDP()
    {
        return [
            ['valid', 0, 'foobar'],             // valid key
            ['expired', 2, false],              // expired key
            ['id_that_doesnt_exist', 0, false], // non-existing key
        ];
    }

    /**
     * @dataProvider getKeyDP
     */
    public function testGetKey($a, $b, $c)
    {
        $obj = new _Cache();
        $this
            ->variable($obj->getKey($a, $b))
                ->isEqualTo($c);
    }

    public function testFlush()
    {
        if (! getenv('TRAVIS')) {
            $obj = new _Cache();
            $this
                ->boolean($obj->flush())
                    ->isEqualTo(true);
        }
    }
}
