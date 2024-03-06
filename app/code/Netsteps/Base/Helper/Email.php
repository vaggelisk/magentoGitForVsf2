<?php
/**
 * Email
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Base
 */

namespace Netsteps\Base\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var string|null
     */
    protected $template_id;

    /**
     * Email constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder)
    {
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        parent::__construct($context);
    }

    /**
     * Get current store
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore(){
        return $this->_storeManager->getStore();
    }

    /**
     * Generate an email based on given informations
     * @param $emailVariables
     * @param $sender
     * @param $receiverInfo
     * @param array $copyTo
     * @return $this
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function generateTemplate($emailVariables, $sender, $receiverInfo, array $copyTo = []){
        $template = $this->_transportBuilder->setTemplateIdentifier($this->template_id)
                         ->setTemplateOptions([
                           'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                           'store' => $this->getStore()->getId(),
                         ])
                         ->setTemplateVars($emailVariables)
                         ->setFromByScope($sender)
                         ->addTo($receiverInfo['email'], $receiverInfo['name']);

        if (!empty($copyTo)) {
            $this->_transportBuilder->addCc($copyTo);
        }

        return $this;
    }

    /**
     * Send email
     * @param string $templateId
     * @param array $emailTemplateVariables
     * @param string $sender
     * @param array $receiverInfo
     * @param array $copyTo
     * @return bool
     */
    public function sendEmail(
        string $templateId,
        array $emailTemplateVariables,
        string $sender,
        array $receiverInfo,
        array $copyTo = []
    ): bool {
        $this->template_id = $templateId;

        if($this->template_id){
            try {
                $this->inlineTranslation->suspend();
                $this->generateTemplate($emailTemplateVariables, $sender, $receiverInfo, $copyTo);
                $transport = $this->_transportBuilder->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
                return true;
            } catch (\Exception $e) {
                $data = [
                    'template' => $templateId,
                    'variables' => $emailTemplateVariables,
                    'sender' => $sender,
                    'receiver' => $receiverInfo
                ];
                $this->_logger->error('Netsteps Email Helper sendEmail: ' . $e->getMessage());
                $this->_logger->error('Details:' . PHP_EOL . json_encode($data));
                return false;
            }

        }

        return false;
    }
}
