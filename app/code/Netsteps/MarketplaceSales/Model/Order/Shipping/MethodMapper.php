<?php
/**
 * MethodMapper
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Shipping;

/**
 * Class MethodMapper
 * @package Netsteps\MarketplaceSales\Model\Order\Shipping
 */
class MethodMapper implements MethodMapperInterface
{
    /**
     * Payment method map
     * array(
     *    'original_method_code' => 'mapped_method_code'
     * )
     * @var array
     */
    private array $map;

    /**
     * @param array $methodMap
     */
    public function __construct(array $methodMap = [])
    {
        $this->map = $methodMap;
    }

    /**
     * @inheritDoc
     */
    public function getMappedMethod(string $method): string
    {
        return $this->map[$method] ?? $method;
    }

    /**
     * @inheritDoc
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * @inheritDoc
     */
    public function match(string $method): array {
        return array_filter($this->map, function ($item) use ($method) {
            return $item === $method;
        });
    }
}
