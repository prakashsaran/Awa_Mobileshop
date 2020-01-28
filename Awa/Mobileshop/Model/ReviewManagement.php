<?php
/**
 * Copyright (c) 2019 axiswebart 2020
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Awa\Mobileshop\Model;
use \Magento\Review\Model\ReviewFactory;
use \Magento\Review\Model\ResourceModel\Review as ReviewResource;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use \Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use \Magento\Review\Model\ResourceModel\Rating\Collection as RatingCollection;
use \Magento\Review\Model\ResourceModel\Rating\CollectionFactory as RatingsCollectionFactory;
use \Awa\Mobileshop\Api\Data\ReviewInterface;
use \Awa\Mobileshop\Api\Data\SaveInterface;

class ReviewManagement implements \Awa\Mobileshop\Api\ReviewManagementInterface
{
    protected $_reviewFactory;
    protected $_reviewResource;
    protected $_productRepository;
    protected $_reviewCollection;
    /**
    * @var _ratingCollection
    */
    private $_ratingCollection;

    /**
    * @var RatingsCollectionFactory
    */
    private $_ratingsCollectionFactory;

    /**
    * @var SaveInterface
    */
    protected $_saveReview;

    public function __construct(
        ReviewFactory $reviewFactory,
        ReviewResource $reviewResource,
        ProductRepositoryInterface $productRepository,
        CollectionFactory $reviewCollection,
        RatingsCollectionFactory $ratingsCollectionFactory,
        SaveInterface $saveReview
    ){
        $this->_reviewFactory = $reviewFactory;
        $this->_reviewResource = $reviewResource;
        $this->_reviewCollection = $reviewCollection;
        $this->_productRepository = $productRepository;
        $this->_ratingsCollectionFactory = $ratingsCollectionFactory;
        $this->_saveReview = $saveReview;
    }

	/**
     * {@inheritdoc}
     */
    public function get($reviewId)
    {
        $reviewModel = $this->_reviewFactory->create();
        $this->_reviewResource->load($reviewModel, $reviewId);

        if (null === $reviewModel->getId()) {
            throw new NoSuchEntityException(
                __('Review with id "%value" does not exist.', ['value' => $reviewId])
            );
        }
        $response[] = array(
            "id"=>$reviewModel->getId(),
            "title"=>$reviewModel->getTitle(),
            "nickname"=>$reviewModel->getNickname(),
            "detail"=>$reviewModel->getDetail()
        );
    	return $response;
    }
	/**
     * {@inheritdoc}
     */
    public function getByProduct($productId)
    {
        //$product = $this->_productRepository->getById($productId);
        $collection = $this->_reviewCollection->create()->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)->addEntityFilter('product',$productId)->setDateOrder()->addRateVotes();
        $collection->getData();
        $reviewData = [];

        foreach ($collection as $review) {
            $storeId = (int)$review->getStoreId();

            $reviewData[]  = [
                "review_id"=>$review->getReviewId(),
                "created_at"=>$review->getCreatedAt(),
                "entity_id"=>$review->getEntityId(),
                "status_id"=>$review->getStatusId(),
                "title"=>$review->getTitle(),
                "detail"=>$review->getDetail(),
                "nickname"=>$review->getNickname(),
                "customer_id"=>$review->getCustomerId(),
                "rating"=>$this->getRevieRate($review->getRatingVotes(),$review->getReviewId(),$storeId)
            ];
        }
    	return $reviewData;
    }

    /**
    * @param int $storeId
    * @return RatingCollection
    */
    private function getRatingCollection($storeId=null)
    {
        if (null === $this->_ratingCollection) {
            $ratingCollection = $this->_ratingsCollectionFactory->create()
            ->addEntityFilter('product')
            ->setStoreFilter($storeId)
            ->addRatingPerStoreName($storeId)
            ->setPositionOrder()->load();
           $this->_ratingCollection = $ratingCollection;
        }
        return $this->_ratingCollection;
    }

    /**
    * Get Review Rating Collection
    * @param Object $ratingsVotes
    * @param int $productId
    * @param int $storeId
    * @return array
    */
    private function getRevieRate($ratingsVotes,$productId,$storeId)
    {

        $ratings = [];
        $ratingCollection = $this->getRatingCollection($storeId);
        // if $ratingsVotes equel to null
            if (null === $ratingsVotes) {
                return $ratings;
            }
            $reviewRatings = $ratingsVotes->getItemsByColumnValue('review_id', $productId);
            if (!count($reviewRatings)) {
               return $ratings;
            }
            // loop of selected product ratings
            foreach ($reviewRatings as $ratingVote) {
                $rating = $ratingCollection->getItemByColumnValue('rating_id', $ratingVote->getRatingId());
                if ($rating) {
                    $ratings[] = [
                        'value' => $ratingVote->getValue(),
                        'percent' => $ratingVote->getPercent(),
                        'vote_id' => $ratingVote->getVoteId(),
                        'rating_id' => $rating->getId(),
                        'rating_name' => $rating->getRatingCode(),
                    ];
                }
            }

        return $ratings;
    }

    /**
     * {@inheritdoc}
     */
    public function getByCustomer($customerId)
    {
        $collection = $this->_reviewCollection->create()
        ->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)
        ->addCustomerFilter($customerId)
        ->setDateOrder()->addRateVotes();
        $collection->getData();

        $reviewData = [];

        foreach ($collection as $review) {
            $storeId = (int)$review->getStoreId();

            $reviewData[]  = [
                "review_id"=>$review->getReviewId(),
                "created_at"=>$review->getCreatedAt(),
                "entity_id"=>$review->getEntityId(),
                "status_id"=>$review->getStatusId(),
                "title"=>$review->getTitle(),
                "detail"=>$review->getDetail(),
                "nickname"=>$review->getNickname(),
                "customer_id"=>$review->getCustomerId(),
                "rating"=>$this->getRevieRate($review->getRatingVotes(),$review->getReviewId(),$storeId)
            ];
        }
        return $reviewData;
    }

    /**
     * {@inheritdoc}
     */
    public function postReview(ReviewInterface $review)
    {
        return $this->_saveReview->execute($review);
    }
}