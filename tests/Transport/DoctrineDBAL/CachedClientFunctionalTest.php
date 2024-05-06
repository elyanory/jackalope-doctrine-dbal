<?php

namespace Jackalope\Tests\Transport\DoctrineDBAL;

use Doctrine\DBAL\Connection;
use Jackalope\Factory;
use Jackalope\Transport\DoctrineDBAL\CachedClient;
use Jackalope\Transport\TransportInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;

class CachedClientFunctionalTest extends ClientTest
{
    protected function getClient(Connection $conn): TransportInterface
    {
        $nodeCacheAdapter = new ArrayAdapter();
        $nodeCache = new Psr16Cache($nodeCacheAdapter);

        $metaCacheAdapter = new ArrayAdapter();
        $metaCache = new Psr16Cache($metaCacheAdapter);

        return new CachedClient(new Factory(), $conn, [
            'nodes' => $nodeCache,
            'meta' => $metaCache,
        ]);
    }
}
