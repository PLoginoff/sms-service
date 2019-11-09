<?php

namespace App\Tests;

use App\Gates\FakeGate;
use App\Service\GateRegistry;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GateRegistryTest extends WebTestCase
{
    /**
     * @var GateRegistry
     */
    private $service;

    /**
     * @throws \Exception
     */
    public function testFakeGate(): void
    {
        $gate = $this->service->get();
        $this->assertNotNull($gate);
        $this->assertInstanceOf(FakeGate::class, $gate);
        $status = $gate->send('+79260613031', 'Hello, Paul!');
        $this->assertTrue($status);
    }

    public function testDisableGate(): void
    {
        $gate = $this->service->get();
        $this->service->disable($gate);
        $gate = $this->service->get();
        $this->assertNull($gate);
    }


    public function setUp()
    {
        parent::setUp();
        self::bootKernel();
        $this->service = self::$container->get(GateRegistry::class);
        self::$container->get(CacheItemPoolInterface::class)->clear(); // clear cache for test env
    }
}
