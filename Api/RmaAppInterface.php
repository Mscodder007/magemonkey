<?php
/**
 * Magemonkey RMA
 *
 * RMA For The B2B Customer
 *
 * @category    RMA
 * @package     Magemonkey_RMA
 * @author      Fateh Momin<fatteh@webtechsystem.com>
 * @version     1.0.0
 */
declare(strict_types=1);

namespace Magemonkey\RMA\Api;


interface RmaAppInterface
{

    /**
     * List Of RMA
     *
     * @api
     * @param mixed $data
     * @return mixed
     */
    public function RmaList($data = []);

    /**
     * RMA ITEMS LIST
     * @api
     * @param string $rmaId
     * @return string
     */
    public function RmaItemList($rmaId);

    /**
     * Delete RMA
     * @api
     * @param string $rmaId
     * @return string
     */
    public function delete($rmaId);


    /**
     * Save and Update Rma
     *
     * @api
     * @param mixed $data
     * @return mixed
     */
    public function save($data = []);

    /**
     * upload Rma image
     *
     * @api
     * @return mixed
     */
    public function upload();
}

