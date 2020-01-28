<?php
/**
 * @package  Awa\Mobileshop
 * @author axiswebart <dp@axiswebart.com>
 * @copyright (c) 2019 axiswebart 2020
 * @license MIT.
 */

namespace Awa\Mobileshop\Model\Data;

use Awa\Mobileshop\Api\Data\RatingVoteInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class Rating
 */
class RatingVote extends AbstractSimpleObject implements RatingVoteInterface
{

    /**
     * @inheritdoc
     */
    public function getVoteId()
    {
        return (int)$this->_get(self::KEY_VOTE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setVoteId($id)
    {
        return $this->setData(self::KEY_VOTE_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getRatingId()
    {
        return (int)$this->_get(self::KEY_RATING_ID);
    }

    /**
     * @inheritdoc
     */
    public function setRatingId($ratingId)
    {
        return $this->setData(self::KEY_RATING_ID, $ratingId);
    }

    /**
     * @inheritdoc
     */
    public function getRatingName()
    {
        return $this->_get(self::KEY_RATING_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setRatingName($attributeCode)
    {
        return $this->setData(self::KEY_RATING_NAME, $attributeCode);
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->_get(self::KEY_VALUE);
    }

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        return $this->setData(self::KEY_VALUE, $value);
    }

    /**
     * @inheritdoc
     */
    public function getPercent()
    {
        return (int)$this->_get(self::KEY_PERCENT);
    }

    /**
     * @inheritdoc
     */
    public function setPercent($percent)
    {
        return $this->setData(self::KEY_PERCENT, $percent);
    }
}