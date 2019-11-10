<?php

namespace App\Tests;

use App\Gates\FakeGate;
use App\Service\Registry;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistryTest extends WebTestCase
{
    /**
     * @var Registry
     */
    private $service;

    /**
     * @throws \Exception
     * @throws \Psr\Cache\InvalidArgumentException
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
        $this->service = self::$container->get(Registry::class);
        self::$container->get(CacheItemPoolInterface::class)->clear(); // clear cache for test env
    }
}
