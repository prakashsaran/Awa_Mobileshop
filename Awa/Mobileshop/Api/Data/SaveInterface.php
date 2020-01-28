<?php
/**
 * @package  Awa\Mobileshop
 * @author axiswebart <dp@axiswebart.com>
 * @copyright (c) 2019 axiswebart 2020
 * @license MIT.
 */

namespace Awa\Mobileshop\Api\Data;

use \Awa\Mobileshop\Api\Data\ReviewInterface;

/**
 * Interface GetInterface
 */
interface SaveInterface
{
    /**
     * @param ReviewInterface $dataModel
     *
     * @return ReviewInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute($dataModel);
}