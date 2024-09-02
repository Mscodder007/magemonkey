<?php
/**
 * Magemonkey RMA
 *
 * RMA For The B2B Customer
 *
 * @category    RMA
 * @package     Magemonkey_RMA
 * @author      Anurag Chitnis<anurag@webtechsystem.com>
 * @version     1.0.0
 */
declare(strict_types=1);

namespace Magemonkey\RMA\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;

class EmailNotification extends AbstractHelper
{
    protected $inlineTranslation;

    protected $escaper;

    protected $transportBuilder;

    protected $logger;

    protected $objectManager;

    protected $quoteFactory;

    protected $storeManager;

    protected $scopeConfig;

    protected $currentCustomer;

    const XML_RMA_SENDEMAILTO = 'rma/general/admin_email';

    const XML_RMA_ADMINNOTIFTEMPL = 'rma/general/rma_email_notifications_template';
    const XML_RMA_UPDATE_STATUS = 'rma/general/rma_update_status_email_notifications';


    const XML_FROM_EMAIL = 'trans_email/ident_support/email';

    const XML_FROM_NAME = 'trans_email/ident_support/name';


    /**
     * Constructor
     *
     * @param Context $context
     * @param StateInterface $inlineTranslation
     * @param Escaper $escaper
     * @param TransportBuilder $transportBuilder
     * @param ObjectManagerInterface $objectManager
     * @param QuoteFactory $quoteFactory
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param CurrentCustomer $currentCustomer
     */

    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        ObjectManagerInterface $objectManager,
        QuoteFactory $quoteFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        CurrentCustomer $currentCustomer
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->objectManager = $objectManager;
        $this->quoteFactory = $quoteFactory;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * Email Notification
     *
     * @param int $rmaId
     * @return void
     */
    public function sendEmail($rmaId)
    {
        try {
            $customerName = $this->currentCustomer->getCustomer()->getFirstname().' '.$this->currentCustomer->getCustomer()->getLastname();
            $customerEmail = $this->currentCustomer->getCustomer()->getEmail();
            $sendto = $this->scopeConfig->getValue(self::XML_RMA_SENDEMAILTO, ScopeInterface::SCOPE_STORE);

            $newData = [
                'rmaNo' =>$rmaId,
                'customerName' => $customerName,
                'customerEmail' => $customerEmail,

            ];

            $fromEmail = $this->scopeConfig->getValue(self::XML_FROM_EMAIL, ScopeInterface::SCOPE_STORE);
            $fromName = $this->scopeConfig->getValue(self::XML_FROM_NAME, ScopeInterface::SCOPE_STORE);
            $from = ['email' => $customerEmail, 'name' => $customerName];
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($newData);

            $this->inlineTranslation->suspend();

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_RMA_ADMINNOTIFTEMPL, ScopeInterface::SCOPE_STORE))
                ->setTemplateOptions([
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ])
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($from)
                ->addTo($sendto)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            return true;        
            // $postObject = new \Magento\Framework\DataObject();
            // $postObject->setData($newData);

            // $this->inlineTranslation->suspend();

            // $transport = $this->transportBuilder
            //     ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_QUOTE_CUSTOMERNOTIFTEMPL, ScopeInterface::SCOPE_STORE))
            //     ->setTemplateOptions([
            //         'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            //         'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            //     ])
            //     ->setTemplateVars(['data' => $postObject])
            //     ->setFrom($this->scopeConfig->getValue(self::XML_QUOTE_CUSTOMERNOTIFSENDER, ScopeInterface::SCOPE_STORE))
            //     ->addTo($customerEmail)
            //     ->getTransport();

            // $transport->sendMessage();
            // $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    public function emailRMAItemNotification($email, $name){

        try {

            $sender = ['name' => $this->scopeConfig->getValue('trans_email/ident_general/name',  \Magento\Store\Model\ScopeInterface::SCOPE_STORE),'email' => $this->scopeConfig->getValue('trans_email/ident_general/email',  \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            ];

            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId()
            ];
            
            $templateVars = [
                'customer_name' => $name
            ];
            
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateNotify = $this->scopeConfig->getValue(self::XML_RMA_UPDATE_STATUS, ScopeInterface::SCOPE_STORE);
            $transport = $this->transportBuilder->setTemplateIdentifier($templateNotify, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($sender)
                ->addTo($email)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
