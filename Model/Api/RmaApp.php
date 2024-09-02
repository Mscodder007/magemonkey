<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @category Magemonkey
 * @package  Magemonkey_RMA
 *
 */

namespace Magemonkey\RMA\Model\Api;

use Magento\Store\Model\StoreManagerInterface;
use Magemonkey\RMA\Api\RmaAppInterface;
use Magemonkey\RMA\Helper\Data;

class RmaApp implements RmaAppInterface
{
    
    /**
     * @var Data
     */
    protected $helper;

     /**
      *
      * @param Data $helper
      *
      */

    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * RMA LIST
     *
     * @param mixed $items
     * @return string
     */

    public function RmaList($items = [])
    {
        return $this->helper->rmaListdata($items);
    }

    /**
     * @api
     * @param string $rmaId
     * @return string
     */

    public function RmaItemList($rmaId)
    {   
        return $this->helper->rmaItemList($rmaId);
    }

    /**
     * Delete RMA
     * @api
     * @param string $rmaId
     * @return string
     */
    public function delete($rmaId){

        return $this->helper->deleteRma($rmaId); 
    }

    /**
     * Save and Update Rma
     *
     * @api
     * @param mixed $data
     * @return mixed
     */
    public function save($data = []) {

        return $this->helper->saveRma($data);   
    }

    /**
     * upload Rma image
     *
     * @api
     * @return mixed
     */
    public function upload() {
        return $this->helper->uploadRmaFile();
    }

}
