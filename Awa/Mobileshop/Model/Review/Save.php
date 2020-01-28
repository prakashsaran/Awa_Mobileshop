<?php
/**
 * @package  Divante\ReviewApi
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2018 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Awa\Mobileshop\Model\Review;

use \Awa\Mobileshop\Api\Data\SaveInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Awa\Mobileshop\Api\Data\ReviewInterface;
use \Magento\Review\Model\ReviewFactory;
use \Magento\Review\Model\RatingFactory;
use \Magento\Framework\Reflection\DataObjectProcessor;
use \Magento\Review\Model\Review;
use \Awa\Mobileshop\Api\Data\ReviewInterface as ReviewData;
use \Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory;
use \Magento\Review\Model\ResourceModel\Rating\Option\Vote\Collection;
use \Magento\Review\Model\ResourceModel\Rating\CollectionFactory as RatingsFactory;
use \Magento\Framework\Data\Collection as dataCollection;
use \Magento\Framework\Exception\CouldNotSaveException;
/**
 * Class Save
 */
class Save implements SaveInterface
{

	/**
     * @var CollectionFactory
     */
    private $_voteCollectionFactory;

	/**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    private $_dataObjectProcessor;

	/**
	* @var StoreManagerInterface
	*/
	protected $_storeManager;

	/**
	* @var ReviewFactory
	*/
	protected $_reviewFactory;

	/**
	* @var RatingFactory
	*/
	protected $_ratingFactory;

	/**
	* @var RatingsFactory
	*/
	protected $_ratingsCollectionFactory;


	protected $_errorMessage = null;

	/**
	* Svae construct
	* @param StoreManagerInterface $storeManager
	* @param ReviewFactory $reviewFactory
	* @param RatingFactory $ratingFactory
	* @param DataObjectProcessor $dataObjectProcessor
	* @param CollectionFactory $collectionFactory
	* @param RatingsFactory $ratingsCollectionFactory
	*/
    public function __construct(
        StoreManagerInterface $storeManager,
        ReviewFactory $reviewFactory,
        RatingFactory $ratingFactory,
        DataObjectProcessor $dataObjectProcessor,
        CollectionFactory $collectionFactory,
        RatingsFactory $ratingsCollectionFactory

    ){
        $this->_storeManager = $storeManager;
        $this->_reviewFactory = $reviewFactory;
        $this->_ratingFactory = $ratingFactory;
        $this->_dataObjectProcessor = $dataObjectProcessor;
        $this->_voteCollectionFactory = $collectionFactory;
        $this->_ratingsCollectionFactory = $ratingsCollectionFactory;
    }

    /**
     * @inheritdoc
     *
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute($dataModel)
    {
    	$stores = $dataModel->getStores();
    	if (null === $stores || empty($stores)) {
            $dataModel->setStores([$this->_storeManager->getStore()->getId()]);
        }
        if ((null === $dataModel->getId()) && (null === $dataModel->getStoreId())) {
            $dataModel->setStoreId($this->_storeManager->getStore()->getId());
        }

        $valid = $this->isValid($dataModel);
        if (($valid===true) && (null === $this->_errorMessage)) {
        	$result = $this->saveModel($dataModel);
        	return $result;
        }else{
        	throw new CouldNotSaveException(__($this->_errorMessage));
        }
        return true;
    }

    /**
    * @param Object $review
    * @return boolean
    */
    private function isValid($review)
    {
    	$valid = true;

    	//** title is valid
    	$title = (string)$review->getTitle();
    	if ('' === trim($title)) {
	        $this->_errorMessage = __('"%field" can not be empty.', ['field' => ReviewInterface::TITLE]);
	    	return false;
        }

        //** product Entity PK Value
        $entityPk = (int)$review->getEntityPkValue();
        if (!$entityPk) {
        	$this->_errorMessage = __('"%field" can not be empty. Add Product ID.', ['field' => ReviewInterface::ENTITY_PK_VALUE]);
	    	return false;
        }

        //** detail is valid
        $detail = (string)$review->getDetail();
        if ('' === trim($detail)) {
            $this->_errorMessage = __('"%field" can not be empty.', ['field' => ReviewInterface::DETAIL]);
            return false;
        }

        //** nickname is valid
        $nickname = (string)$review->getNickname();
        if ('' === trim($nickname)) {
            $this->_errorMessage = __('"%field" can not be empty.', ['field' => ReviewInterface::NICKNAME]);
            return false;
        }

        //** reviewEntity is valid
        $reviewEnt = (string)$review->getReviewEntity();
        if ('' === trim($reviewEnt)) {
            $this->_errorMessage = __('"%field" can not be empty.', ['field' => ReviewInterface::REVIEW_ENTITY]);
            return false;
        }

        //** store is valid
        $store = (array)$review->getStores();
        if (empty($store)) {
            $this->_errorMessage = __('"%field" can not be empty.', ['field' => ReviewInterface::STORES]);
            return false;
        }
        $this->_errorMessage = null;
        return $valid;
    }

    /**
    * @param Object $reviewData
    * @return int|null
    */
    private function saveModel($reviewData)
    {
        $reviewModel = $this->_reviewFactory->create();
    	$data = $this->_dataObjectProcessor->buildOutputDataArray(
            $reviewData,
            ReviewInterface::class
        );
        $modelData = $reviewModel->getData();
        $mergedData = array_merge($modelData, $data);
        $reviewModel->setData($mergedData);
        $this->mapFields($reviewModel, $reviewData);
    	$reviewModel->save();
    	$this->saveRatings($reviewModel,$reviewData);
    	$reviewModel->aggregate();
    	return $this->returnFormat($reviewModel,$reviewData);
    }

    /**
     * @param Review $reviewModel
     * @param ReviewData $reviewData
     */
    private function mapFields(Review $reviewModel, ReviewData $reviewData)
    {
        $reviewModel->setEntityId($reviewModel->getEntityIdByCode($reviewData->getReviewEntity()));
        $reviewModel->setStatusId($reviewData->getReviewStatus());
        $reviewModel->setStores($reviewData->getStores());

        if (!$reviewModel->getStatusId()) {
            $reviewModel->setStatusId(Review::STATUS_PENDING);
        }
    }

    /**
     * @param Object $entity
     * @param Object $reviewData
     */
    private function returnFormat($entity,$reviewData)
    {
    	$reviewData->setId($entity->getId());
    	return $reviewData;
    }

    /**
     * @param Object $entity
     * @param Object $reviewData
     */
    private function saveRatings($entity,$reviewData)
    {
    	$reviewRatings = $reviewData->getRatings() ?? [];
    	$storeId = $reviewData->getStoreId();
    	$reviewId = $entity->getId();
    	//$votes = $this->getVotes($reviewId);
    	$ratingCollection = $this->_ratingFactory->create();
    	foreach ($reviewRatings as $ratingVote) {
    		$ratingCode = $ratingVote->getRatingName();
    		$ratingId = $this->getRatingIdByName($ratingCode,$storeId);
    		 //$vote = $votes->getItemByColumnValue('rating_id', $ratingId);
    		if (!$ratingId) {
                continue;
            }else{
            	$ratingCollection->setRatingId($ratingId)
            	->setReviewId($entity->getId())
            	->addOptionVote($ratingVote->getValue(), $reviewData->getEntityPkValue());
            }
            
    	}
    }

    /**
     * @param int $reviewId
     *
     * @return Collection
     */
    private function getVotes(int $reviewId): Collection
    {
        /** @var Collection $collection */
        $collection = $this->_voteCollectionFactory->create();
        $collection->setReviewFilter($reviewId)
            ->addOptionInfo()
            ->load()
            ->addRatingOptions();
        return $collection;
    }

    /**
     * @param string $ratingName
     * @param int $storeId
     *
     * @return int|null
     */
    public function getRatingIdByName(string $ratingName, int $storeId): ?int
    {
        if (!isset($this->ratings[$storeId])) {
            $collection = $this->_ratingsCollectionFactory->create();
            $collection->setStoreFilter($storeId);
            $collection->addRatingPerStoreName($storeId);

            $this->ratings[$storeId] = $this->toOptionHash($collection, 'rating_code');
        }

        $ratingId = array_search($ratingName, $this->ratings[$storeId]);

        return $ratingId !== false ? (int)$ratingId : null;
    }

    /**
     * @param Collection $collection
     * @param string $labelField
     *
     * @return array
     */
    private function toOptionHash(dataCollection $collection, string $labelField): array
    {
        $valueField = $collection->getResource()->getIdFieldName();
        $res = [];

        foreach ($collection as $item) {
            $res[$item->getData($valueField)] = $item->getData($labelField);
        }

        return $res;
    }
}