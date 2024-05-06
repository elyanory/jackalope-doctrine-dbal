<?php

namespace Jackalope\Tests;

use Doctrine\DBAL\Connection;
use Jackalope\RepositoryFactoryDoctrineDBAL;
use PHPCR\ConfigurationException;
use PHPUnit\Framework\TestCase;

class RepositoryFactoryDoctrineDBALTest extends TestCase
{
    public function testMissingRequired()
    {
        $factory = new RepositoryFactoryDoctrineDBAL();

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('missing');

        $factory->getRepository([]);
    }

    public function testExtraParameter()
    {
        $factory = new RepositoryFactoryDoctrineDBAL();
        $conn = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('unknown');

        $factory->getRepository([
            'jackalope.doctrine_dbal_connection' => $conn,
            'unknown' => 'garbage',
        ]);
    }
}
