<?php

namespace Jackalope\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Jackalope\Factory;
use Jackalope\Repository;
use Jackalope\Session;
use Jackalope\Transport\DoctrineDBAL\Client;
use Jackalope\Transport\DoctrineDBAL\RepositorySchema;
use Jackalope\Transport\TransportInterface;
use Jackalope\Transport\WorkspaceManagementInterface;
use PHPCR\RepositoryException;
use PHPCR\SimpleCredentials;

/**
 * Base class for testing jackalope clients.
 */
class FunctionalTestCase extends TestCase
{
    protected TransportInterface $transport;
    protected Repository $repository;
    protected Session $session;

    public function setUp(): void
    {
        parent::setUp();

        $conn = $this->getConnection();
        $this->loadFixtures($conn);
        $this->transport = $this->getClient($conn);
        $this->assertInstanceOf(WorkspaceManagementInterface::class, $this->transport);

        $this->transport->createWorkspace('default');
        $this->repository = new Repository(null, $this->transport);

        try {
            $this->transport->createWorkspace($GLOBALS['phpcr.workspace']);
        } catch (RepositoryException $e) {
            if ($e->getMessage() !== "Workspace '".$GLOBALS['phpcr.workspace']."' already exists") {
                // if the message is not that the workspace already exists, something went really wrong
                throw $e;
            }
        }
        $this->session = $this->repository->login(new SimpleCredentials('user', 'passwd'), $GLOBALS['phpcr.workspace']);
    }

    protected function loadFixtures(Connection $conn): void
    {
        $options = ['disable_fk' => $conn->getDatabasePlatform() instanceof SqlitePlatform];
        $schema = new RepositorySchema($options, $conn);
        $tables = $schema->getTables();

        foreach ($tables as $table) {
            $conn->executeStatement('DELETE FROM '.$table->getName());
        }
    }

    protected function getClient(Connection $conn): TransportInterface
    {
        return new Client(new Factory(), $conn);
    }
}
