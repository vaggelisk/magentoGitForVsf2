<?php
/**
 * Media
 *
 * @copyright Copyright © 2018 Kostas Tsiapalis. All rights reserved.
 * @author    k.tsiapalis86@gmail.com
 */

namespace Netsteps\Base\Plugin\Widget;

class Media
{

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendData;

    /**
     * Widget constructor.
     * @param \Magento\Backend\Helper\Data $backendData
     */
    public function __construct(
        \Magento\Backend\Helper\Data $backendData
    ) {
        $this->backendData = $backendData;
    }

    /**
     * @param \Magento\Widget\Model\Widget $subject
     * @param $type
     * @param array $params
     * @param bool $asIs
     * @return array
     */
    public function beforeGetWidgetDeclaration(\Magento\Widget\Model\Widget $subject, $type, $params = [], $asIs = true)
    {
        foreach ($params as $name => $value) {
            if(gettype($value) == 'string') {
                if (preg_match('/(___directive\/)([a-zA-Z0-9,_-]+)/', $value, $matches)) {
                    $directive = base64_decode(strtr($matches[2], '-_,', '+/='));
                    $params[$name] = str_replace(['{{media url="', '"}}'], ['', ''], $directive);
                }
            }
        }
        return [$type, $params, $asIs];
    }
}