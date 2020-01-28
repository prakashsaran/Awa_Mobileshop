<?php
/**
 * @package  Awa\Mobileshop
 * @author axiswebart <dp@axiswebart.com>
 * @copyright (c) 2019 axiswebart 2020
 * @license MIT.
 */

namespace Awa\Mobileshop\Api\Data;

/**
 * Interface ReviewInterface
 */
interface ReviewInterface
{
    const ID = 'id';
    const STATUS_ID = 'status_id';
    const TITLE = 'title';
    const DETAIL = 'detail';

    /**
     * Product id
     */
    const ENTITY_PK_VALUE = 'entity_pk_value';
    const STORES = 'stores';
    const STORE_ID = 'store_id';

    const CUSTOMER_ID = 'customer_id';
    const NICKNAME = 'nickname';

    const REVIEW_ENTITY = 'review_entity';
    const REVIEW_TYPE = 'review_type';
    const REVIEW_STATUS = 'review_status';
    const CREATED_AT = 'created_at';
    const RATINGS = 'ratings';

    const REVIEW_TYPE_CUSTOMER = 1;
    const REVIEW_TYPE_GUEST = 2;
    const REVIEW_TYPE_ADMIN = 3;

    /**
     * Get review id
     *
     * @return int
     */
    public function getId();

    /**
     * Get review title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get Review detail.
     *
     * @return string
     */
    public function getDetail();

    /**
     * Get author nickname.
     *
     * @return string
     */
    public function getNickname();

    /**
     * Get customer id.
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Get review ratings.
     *
     * @return \Awa\Mobileshop\Api\Data\RatingVoteInterface[]
     */
    public function getRatings();

    /**
     * Get review entity type.
     *
     * @return string
     */
    public function getReviewEntity();

    /**
     * Get reviewer type.
     * Possible values: 1 - Customer, 2 - Guest, 3 - Administrator.
     *
     * @return int
     */
    public function getReviewType();

    /**
     * Get review status.
     * Possible values: 1 - Approved, 2 - Pending, 3 - Not Approved.
     *
     * @return int
     */
    public function getReviewStatus();

    /**
     * Set review id.
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @param $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * Set Review detail.
     *
     * @param string $detail
     *
     * @return $this
     */
    public function setDetail($detail);

    /**
     * Set author nickname.
     *
     * @param string $nickName
     *
     * @return $this
     */
    public function setNickname($nickName);

    /**
     * Set customer id.
     *
     * @param int|null $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Set review ratings.
     *
     * @param \Awa\Mobileshop\Api\Data\RatingVoteInterface[] $ratings
     *
     * @return void
     */
    public function setRatings($ratings);

    /**
     * Set review entity type.
     *
     * @param string $entity
     *
     * @return $this
     */
    public function setReviewEntity($entity);

    /**
     * Set review status.
     * Possible values: 1 - Approved, 2 - Pending, 3 - Not Approved.
     *
     * @param int $status
     *
     * @return $this
     */
    public function setReviewStatus($status);

    /**
     * Set reviewer type.
     * Possible values: 1 - Customer, 2 - Guest, 3 - Administrator.
     *
     * @param int $type
     *
     * @return string
     */
    public function setReviewType($type);

    /**
     * Posted date
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setEntityPkValue($id);

    /**
     * @return int
     */
    public function getEntityPkValue();

    /**
     * Store id in which review was added
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Stores in which review is visible
     * @return int[]
     */
    public function getStores();

    /**
     * @param array $stores
     *
     * @return $this
     */
    public function setStores($stores);
}
