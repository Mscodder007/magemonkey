<?php
/**
 * @author Magemonkeys Team
 * @package Magemonkey_RMA
 */
namespace Magemonkey\RMA\Helper;

class Constant
{
    const RMA_LOG_FILE = 'RMAAPI';
    const RMA_STATUS_PENDING = 0;
    const RMA_STATUS_FULLY_APPROVED = 1;
    const RMA_STATUS_REJECTED = 2;
    const RMA_STATUS_PARTIALLY_APPROVED = 3;
    const RMA_CREATED_BY = 2;
    const RMA_STATUS = 1;
    const RMA_ITEM_STATUS = 'pending';
    const STATUS = 0;
    const RESPONSE_SUCCSESS = 200;
    const RESPONSE_ERROR = 500;
    const RESPONSE_WARNING = 401;
    const RESPONSE_AUTH = 401;
}
