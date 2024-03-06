<?php
/**
 * @author Vasilis Neris
 * @copyright Copyright (c) 2022 Netsteps S.A
 * @package Netsteps_Seller
 */

namespace Netsteps\Seller\Model\Seller\Validator;

use Netsteps\Seller\Api\Data\SellerInterface;

class Email extends \Magento\Framework\Validator\AbstractValidator
{
    /**
     * @param \Netsteps\Seller\Api\Data\SellerInterface $value
     * @return bool|void
     * @throws \Zend_Validate_Exception
     */
    public function isValid($value)
    {
        /** @var SellerInterface $value */
        $messages = [];
        if ($value->hasEmail() && !\Zend_Validate::is(trim($value->getEmail()), 'EmailAddress')) {
            $this->addErrorMessage(
                $messages,
                '"%fieldName" is not a valid email. Enter and try again.',
                ['fieldName' => $value->getEmail()]
            );
        }
        $this->_addMessages($messages);
        return empty($messages);
    }
    /**
     * Format error message
     *
     * @param string[] $messages
     * @param string $message
     * @param array $params
     * @return void
     */
    protected function addErrorMessage(&$messages, $message, $params)
    {
        $messages[$params['fieldName']] = __($message, $params);
    }
}
