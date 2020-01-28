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

use \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;
use \Magento\Framework\Exception\InputException;
use \Magento\Catalog\Model\ProductFactory;
use \Magento\Framework\Api\SearchCriteriaInterface;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Framework\Api\FilterBuilder;
use \Magento\Framework\Api\Search\FilterGroupBuilder;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Wishlist\Model\WishlistFactory;
use \Magento\Wishlist\Model\ItemFactory;
use \Magento\Wishlist\Model\Wishlist;
class WishlistManagement implements \Awa\Mobileshop\Api\WishlistManagementInterface
{
    protected $_wishlistCollectionFactory;
    protected $_productFactory;
    protected $_productRepositoryInterface;
    protected $_searchCriteriaInterface;
    protected $_filterBuilder;
    protected $_filterGroupBuilder;
    protected $_searchCriteriaBuilder;
    protected $_wishlistRepository;
    protected $_itemFactory;
    protected $_wishlistModel;

    public function __construct(
        CollectionFactory $_wishlistCollectionFactory,
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepositoryInterface,
        SearchCriteriaInterface $searchCriteriaInterface,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        WishlistFactory $wishlistRepository,
        ItemFactory $itemFactory,
        Wishlist $wishlistModel
    ){
        $this->_wishlistCollectionFactory = $_wishlistCollectionFactory;
        $this->_productFactory = $productFactory;
        $this->_productRepositoryInterface = $productRepositoryInterface;
        $this->_searchCriteriaInterface = $searchCriteriaInterface;
        $this->_filterBuilder = $filterBuilder;
        $this->_filterGroupBuilder = $filterGroupBuilder;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_wishlistRepository = $wishlistRepository;
        $this->_itemFactory = $itemFactory;
        $this->_wishlistModel = $wishlistModel;

    }
    /**
     * {@inheritdoc}
     */
    public function getWishlist($customerId)
    {
        if (empty($customerId) || !isset($customerId) || $customerId == "") {
            throw new InputException(__('Id required'));
        } else {

            $collection = $this->_wishlistCollectionFactory->create()->addCustomerIdFilter($customerId);
            $productids = [];
            foreach ($collection as $item) {
               $productids[] = $item->getProductId();
            }
            $filterCondition = $this->_filterBuilder->setField('entity_id')->setConditionType('in')->setValue($productids)->create();
            $filtergroup = $this->_filterGroupBuilder->addFilter($filterCondition)->create();
            $this->_searchCriteriaBuilder->setFilterGroups([$filtergroup]);
            $searchCriteria = $this->_searchCriteriaBuilder->create();

            $productsData = $this->_productRepositoryInterface->getList($searchCriteria);

            return $productsData;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postWishlist($customerId,$productId)
    {
        if ($productId == null) {
           throw new LocalizedException(__('Invalid product, Please select a valid product'));
        }
        try {
            $product = $this->_productRepositoryInterface->getById($productId);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }
        try {
           $wishlist = $this->_wishlistRepository->create()->loadByCustomerId($customerId,true);
           $wishlist->addNewItem($product)->save();
        } catch (NoSuchEntityException $e) {
            
        }
        
        return $product;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteWishlist($customerId,$productId)
    {
        if ($customerId == null || $productId == null) {
            throw new LocalizedException(__('Invalid wishlist item, Please select a valid item'));
        }
        
        $wishlist = $this->_wishlistModel->loadByCustomerId($customerId);
        $items = $wishlist->getItemCollection();
        foreach ($items as $item) {
            if ($item->getProductId()==$productId) {
                $item->delete();
                $wishlist->save();
            }
            return true;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function putWishlist($param)
    {
        return 'hello api PUT return the $param ' . $param;
    }
}
