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

namespace Magemonkey\RMA\Ui\Component\Listing\Column;

class Customer extends \Magento\Ui\Component\Listing\Columns\Column
{


    protected $urlBuilder;

    protected $customerFactory;


    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {

                if ($item[$fieldName] != '') {
                    $adminName = $this->getCustomerName($item[$fieldName]);
                    $item[$fieldName] = $adminName;
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param $userId
     * @return string
     */
    private function getCustomerName($userId)
    {
        $user = $this->customerFactory->create()->load($userId);
        $name = $user->getName();
        return $name;
    }
}

