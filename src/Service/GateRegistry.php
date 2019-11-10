<?php

namespace App\Service;

use App\Gates\GateInterface;
use Psr\Cache\CacheItemPoolInterface;

class GateRegistry
{
    /** @var CacheItemPoolInterface */
    protected $cache;

    /** @var GateInterface[] */
    protected $gates;

    /** @var array */
    protected $disabled;

    protected const PREFIX = 'sms.service.disabled.';

    public function __construct(array $gates, CacheItemPoolInterface $cache, array $disabled)
    {
        $this->cache = $cache;
        $this->gates = $gates;
        $this->disabled = array_combine($disabled, $disabled);
    }

    /**
     * @param string|null $name
     * @return GateInterface|null
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function get(?string $name = null): ?GateInterface
    {
        if ($name) {
            return $this->gates[$name];
        }
        foreach ($this->gates as $name => $gate) {
            if ($this->isDisabled($name)) {
                continue;
            }
            return $gate;
        }
        return null;
    }

    public function disable(GateInterface $gate, int $minutes = 15)
    {
        $name = array_search($gate, $this->gates);
        $item = $this->cache->getItem(self::PREFIX . $name);
        $item->expiresAfter($minutes * 60);
        $item->set($name);
        $this->cache->save($item);
    }

    /**
     * @param $name
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function isDisabled(string $name): bool
    {
        if (isset($this->disabled[$name]) || $this->cache->hasItem(self::PREFIX . $name)) {
            return true;
        } else {
            return false;
        }
    }
}
