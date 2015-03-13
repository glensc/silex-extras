<?php

namespace src\Aptoma\Cache;

use Aptoma\Cache\PredisCache;
use Predis\Client;

class PredisCacheTest extends \PHPUnit_Framework_TestCase
{
    /** @var Client */
    private $redisClient;
    /** @var PredisCache */
    private $cache;

    protected function setUp()
    {
        $this->redisClient = $this->createClient();
        $this->cache = new PredisCache($this->redisClient);
        $this->cache->setNamespace('test');
        $this->cache->flushAll();
    }

    public function testFetchShouldHandleUnserializedData()
    {
        $this->redisClient->set('test[foo][1]', 'bar');
        $value = $this->cache->fetch('foo');

        $this->assertEquals('bar', $value);
    }

    public function testFetchShouldHandleSerializedData()
    {
        $redisClient = $this->createClient();
        $cache = new PredisCache($redisClient);
        $cache->save('foo', 'bar');
        $value = $cache->fetch('foo');

        $this->assertEquals('bar', $value);
    }

    private function createClient()
    {
        return new Client(
            array(
                'host' => 'localhost',
                'port' => 6379,
                'database' => 15,
            ),
            array(
                'prefix' => 'test::'
            )
        );
    }
}
