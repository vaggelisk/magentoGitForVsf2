<?php
/**
 * MethodMapper
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_MarketplaceSales
 */

namespace Netsteps\MarketplaceSales\Model\Order\Payment;

/**
 * Class MethodMapper
 * @package Netsteps\MarketplaceSales\Model\Order\Payment
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
     * Payment methods codes to generate invoice automatically
     * during split process
     *
     * @var string[]
     */
    private array $invoicedMethods;

    /**
     * @param array $methodMap
     * @param string[] $invoicedMethods
     */
    public function __construct(
        array $methodMap = [],
        array $invoicedMethods = []
    )
    {
        $this->map = $methodMap;
        $this->invoicedMethods = $invoicedMethods;
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
    public function getInvoicedMethods(): array
    {
        return $this->invoicedMethods;
    }

    /**
     * @inheritDoc
     */
    public function needInvoice(string $method): bool
    {
        return in_array($method, $this->invoicedMethods);
    }

    /**
     * @inheritDoc
     */
    public function getOriginalMethod(string $method): string {
        $originalMethod = array_search($method, $this->map);
        return $originalMethod ? $originalMethod : $method;
    }
}
