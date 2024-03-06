<?php
/**
 * AbstractXmlValidator
 *
 * @author k.tsiapalis86@gmail.com
 * @copyright Netsteps S.A
 * @package Netsteps_Marketplace
 */

namespace Netsteps\Marketplace\Model\Feed\Validation\Xml;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Dir\Reader;
use Netsteps\Logger\Model\Logger;
use Netsteps\Marketplace\Api\Data\FeedMetadataInterface;
use Netsteps\Marketplace\Exception\InvalidValueException;
use Netsteps\Marketplace\Model\Feed\Validation\ValidatorInterface;
use Magento\Framework\Xml\Parser as XmlParser;
use Netsteps\Marketplace\Model\Adminhtml\Context;
use Netsteps\Marketplace\Traits\Feed\ErrorHandleTrait;

/**
 * Class AbstractXmlValidator
 * @package Netsteps\Marketplace\Model\Feed\Validation\Xml
 */
class AbstractXmlValidator implements ValidatorInterface
{
    use ErrorHandleTrait;

    /**
     * @var string|null
     */
    private ?string $schema = null;

    /**
     * @var XmlParser
     */
    protected XmlParser $_xmlParser;

    /**
     * @var bool
     */
    protected bool $_throwException;

    /**
     * @var Logger
     */
    protected Logger $_logger;

    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $_eventManager;

    /**
     * @var ErrorHandlerInterface|null
     */
    private ?ErrorHandlerInterface $_errorHandler;

    /**
     * @param Reader $moduleReader
     * @param XmlParser $parser
     * @param Context $context
     * @param string $schemaFile
     * @param ErrorHandlerInterface|null $errorHandler
     * @param bool $throwException
     */
    public function __construct(
        Reader $moduleReader,
        XmlParser $parser,
        Context $context,
        string $schemaFile,
        ?ErrorHandlerInterface $errorHandler = null,
        bool $throwException = false
    )
    {
        $this->_throwException = $throwException;
        $this->_xmlParser = $parser;
        $this->schema = $moduleReader->getModuleDir('etc', 'Netsteps_Marketplace') . '/' . $schemaFile;
        $this->_errorHandler = $errorHandler;
        $this->_logger = $context->getLogger();
        $this->_eventManager = $context->getEventManager();
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function validateSchema(
        \Netsteps\Marketplace\Api\Data\FeedInterface $feed,
        \Netsteps\Marketplace\Model\File\ProcessorInterface $processor
    ): array
    {
        $errors = [];

        if ($this->schema){
            $domElement = $this->getDomElement($feed);

            if (is_null($domElement)){
                throw new LocalizedException(__('Cannot generate DOM element for feed %1', $feed->getFeedId()));
            }

            libxml_use_internal_errors(true);

            $domElement->schemaValidate($this->schema);
            $xmlErrors = libxml_get_errors();

            libxml_clear_errors();
            libxml_use_internal_errors(false);

            foreach ($xmlErrors as $error){
                $errors[] = $this->parseErrorAsString($error);
            }

            if (count($errors) > 0){
                $this->handleSchemaErrors($feed, $errors);
                $feed->setStatus(FeedMetadataInterface::STATUS_INVALID);
                $this->_eventManager->dispatch('feed_schema_validation_failed', ['errors' => $errors, 'feed' => $feed]);

                $raiseException = $this->_errorHandler?->handle($domElement, $xmlErrors, $feed) ?? $this->_throwException;

                if ($raiseException) {
                    throw new InvalidValueException(
                        __('Feed %1 has invalid data format.', $feed->getFeedId())
                    );
                }
            }
        } else {
            throw new LocalizedException(__('No schema provided for validation for feed %1', $feed->getFeedId()));
        }

        return $errors;
    }

    /**
     * Get error
     * @param \LibXMLError $error
     * @return string
     */
    protected function parseErrorAsString(\LibXMLError $error): string {
        return __(
            'XML parse error on line %line. %error',
            ['line' => $error->line, 'error' => $error->message]
        );
    }

    /**
     * Get DOM element from feed data
     * @param \Netsteps\Marketplace\Api\Data\FeedInterface $feed
     * @return \DOMDocument|null
     * @throws LocalizedException
     */
    protected function getDomElement(\Netsteps\Marketplace\Api\Data\FeedInterface $feed): ?\DOMDocument
    {
        return $this->_xmlParser->loadXML($feed->getFeedData())->getDom();
    }
}
