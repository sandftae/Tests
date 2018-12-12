<?php
/**
 * Copyright 2018 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace GoDataFeed\Products\Model;

use GoDataFeed\Products\Api\ProductInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;
use Magento\Framework\App\Request\Http;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Product
 * @package GoDataFeed\Products\Model
 */
class Product extends AbstractModel implements ProductInterface
{
    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepositoryInterface;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var AttributeSetRepositoryInterface
     */
    protected $attributeSet;

    /**
     * @var Collection
     */
    protected $attributeCollection;

    /**
     * @var $_storeManager
     */
    protected $_storeManager;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var SearchCriteriaInterface
     */
    protected $searchCriteria;

    /**
     * @var FilterGroup
     */
    protected $filterGroup;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var Status
     */
    protected $productStatus;

    /**
     * @var Visibility
     */
    protected $productVisibility;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Grouped
     */
    protected $groupedProduct;

    /**
     * @var Configurable
     */
    protected $configureProduct;

    /**
     * @var $productData
     */
    protected $productData;

    const PAGE_SIZE_DEFAULT = 50;
    const PAGE_SIZE_MAX = 250;
    const RESOURCE_COLLECTION_PAGING_ERROR = 'Resource collection paging error.';
    const RESOURCE_COLLECTION_PAGING_LIMIT_ERROR = 'The paging limit exceeds the allowed number.';
    const RESOURCE_COLLECTION_ORDERING_ERROR = 'Resource collection ordering error.';

    /**
     * Product constructor.
     * @param CollectionFactory $productCollectionFactory
     * @param AttributeRepositoryInterface $attributeRepositoryInterface
     * @param AttributeSetRepositoryInterface $attributeSet
     * @param Collection $attributeCollection
     * @param Http $request
     * @param StockRegistryInterface $stockRegistry
     * @param StoreManagerInterface $storeManager
     * @param ProductRepository $productRepository
     * @param SearchCriteriaInterface $searchCriteria
     * @param FilterGroup $filterGroup
     * @param FilterBuilder $filterBuilder
     * @param Status $productStatus
     * @param Visibility $productVisibility
     * @param CategoryRepository $categoryRepository
     * @param Grouped $grouped
     * @param Configurable $configurable
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        AttributeRepositoryInterface $attributeRepositoryInterface,
        AttributeSetRepositoryInterface $attributeSet,
        Collection $attributeCollection,
        Http $request,
        StockRegistryInterface $stockRegistry,
        StoreManagerInterface $storeManager,
        ProductRepository $productRepository,
        SearchCriteriaInterface $searchCriteria,
        FilterGroup $filterGroup,
        FilterBuilder $filterBuilder,
        Status $productStatus,
        Visibility $productVisibility,
        CategoryRepository $categoryRepository,
        Grouped $grouped,
        Configurable $configurable
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->attributeRepositoryInterface = $attributeRepositoryInterface;
        $this->request = $request;
        $this->stockRegistry = $stockRegistry;
        $this->attributeSet = $attributeSet;
        $this->attributeCollection = $attributeCollection;
        $this->productRepository = $productRepository;
        $this->searchCriteria = $searchCriteria;
        $this->filterGroup = $filterGroup;
        $this->filterBuilder = $filterBuilder;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->groupedProduct = $grouped;
        $this->configureProduct = $configurable;
    }

    /**
     * @param string $id
     * @return mixed|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct($id)
    {
        $product = $this->productRepository->getById($id);
        $attributes = $this->_getAllAttributes();
        return $this->_prepareProductForResponse($product, $attributes);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProducts()
    {
        $products = [];

        $productCollection = $this->_productCollectionFactory->create();
        $productCollection = $this->_applyCollectionCustomModifiers($productCollection);
        $productCollection = $this->_applyCategoryFilter($productCollection);

        $attributes = $this->_getAllAttributes();
        foreach ($productCollection as $product) {
            $_product = $this->productRepository->getById($product->getId());
            $products[] = $this->_prepareProductForResponse($_product, $attributes);
        }

        return $products;
    }

    /**
     * @return int|mixed|string|void
     */
    public function getProductsCount()
    {
        $this->filterGroup->setFilters([
            $this->filterBuilder
                ->setField('status')
                ->setConditionType('in')
                ->setValue($this->productStatus->getVisibleStatusIds())
                ->create(),
            $this->filterBuilder
                ->setField('visibility')
                ->setConditionType('in')
                ->setValue($this->productVisibility->getVisibleInSiteIds())
                ->create(),
        ]);

        $this->searchCriteria->setFilterGroups([$this->filterGroup]);
        $products = $this->productRepository->getList($this->searchCriteria);
        $productItems = $products->getItems();

        return count($productItems);
    }

    /**
     * @param $collection
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function _applyCategoryFilter($collection)
    {
        $params = $this->request->getParams();
        if (array_key_exists("category_id", $params)) {
            $categoryId = $params['category_id'];
            if ($categoryId) {
                $category = $this->categoryRepository->get($categoryId);
                if (!$category->getId()) {
                    $this->error('category_id');
                }
                $collection->addCategoryFilter($category);
            }
        }

        return $collection;
    }

    /**
     * @param $collection
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function _applyCollectionCustomModifiers($collection)
    {
        $params = $this->request->getParams();
        if (array_key_exists("page", $params)) {
            $page = $params['page'];
            if ($page != abs($page)) {
                return (self::RESOURCE_COLLECTION_PAGING_ERROR);
            }
        } else {
            $page = 1;
        }

        if (array_key_exists("limit", $params)) {
            $limit = $params['limit'];
            if (null == $limit) {
                $limit = self::PAGE_SIZE_DEFAULT;
            } else {
                if ($limit != abs($limit) || $limit > self::PAGE_SIZE_MAX) {
                    return (self::RESOURCE_COLLECTION_PAGING_LIMIT_ERROR);
                }
            }
        } else {
            $limit = self::PAGE_SIZE_DEFAULT;
        }

        if (array_key_exists("order_field", $params)) {
            $orderField = $params['order_field'];
            if (array_key_exists("order_direction", $params)) {
                $orderDirection = $params['order_direction'];
                if (null !== $orderField) {
                    $attribute = $this->attributeRepositoryInterface->get('catalog_product', $orderField);
                    if (!is_string($orderField) || !$attribute) {
                        return (self::RESOURCE_COLLECTION_ORDERING_ERROR);
                    }
                    $collection->setOrder($orderField, $orderDirection);
                }
            }
        }

        $collection->setCurPage($page)->setPageSize($limit);
        return $collection;
    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    private function _getAllAttributes()
    {
        $collection = $this->attributeCollection->addFieldToFilter(Set::KEY_ENTITY_TYPE_ID, 4);
        $attributes = $collection->load()->getItems();
        return $attributes;
    }

    /**
     * @param $product
     * @param $attributes
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function _prepareProductForResponse($product, $attributes)
    {
        $images = $product->getMediaGalleryImages();
        $baseImageUrl = $this->getBaseImageUrl($product);

        $this->setCategoriesToResponse($product);
        $this->setGalleryDataToResponse($images, $baseImageUrl);
        $this->createMageMagicalMethodAndFillArray($product);
        $this->setCustomAttributeToResponse($attributes, $product);

        $this->productData['is_in_stock'] = $this->stockRegistry->getStockItem($product->getId())->getIsInStock();
        $this->productData['attribute_set_name'] = $this->attributeSet->get($product->getAttributeSetId())->getAttributeSetName();

        $productType = $product->getTypeId();

        switch ($productType) {
            case 'configurable':
                $this->setChildSkusByConfigureProductToResponse($product);
                break;

            case 'simple':
                $this->setParentSkuBySimpleProductToResponse($product);
                break;
        }

        return $this->productData;
    }

    /**
     * @param $data
     */
    private function error($data)
    {
        $error = [];
        $message = [];
        switch ($data) {
            case "category_id":
                $message['error'] = [['code' => 400, 'message' => 'Category not found.']];
                break;
        }
        $error['messages'] = $message;
        print_r(json_encode($error));
    }

    /**
     * @param $productEntity
     */
    private function createMageMagicalMethodAndFillArray($productEntity)
    {
        $fieldArrayAndNameMethod = [
            'attribute_set_id',
            'special_from_date',
            'special_price',
            'special_to_date',
            'store_ids',
            'qty',
            'is_saleable',
            'url',
            'website_ids',
            'weight',
            'keyword',
            'msrp',
            'shipping_price',
            'news_from_date',
            'news_to_date',
            'custom_design_from',
            'custom_design_to',
            'id'
        ];

        foreach ($fieldArrayAndNameMethod as $name) {
            $data = explode('_', $name);

            if (empty($data)) {
                $nameMethod = 'get' . ucfirst($name);
                $this->productData[$name] = $productEntity->$nameMethod();
            } else {
                $nameMethod = '';
                foreach ($data as $part) {
                    $nameMethod .= ucfirst($part);
                }
                $nameMethod = 'get' . ucfirst($nameMethod);
                $this->productData[$name] = $productEntity->$nameMethod();
            }


        }
    }

    /**
     * @param $attributes
     * @param $product
     */
    private function setCustomAttributeToResponse($attributes, $product)
    {
        foreach ($attributes as $attribute) {
            $aType = $attribute->getFrontendInput();
            if ($aType === 'price') { // Get the price and the final price (after discounts)
                $attributeName = $attribute->getAttributeCode();
                $attributeValue_final = $product->getFinalPrice();
                $attributeValue = $product->getPrice();
                $this->productData[$attributeName] = $attributeValue;
                $this->productData[$attributeName . '_final'] = $attributeValue_final;
            }
            if ($aType === 'text' || $aType === 'textarea') {
                $attributeName = $attribute->getAttributeCode();
                $attributeValue = $attribute->getFrontend()->getValue($product);
                $this->productData[$attributeName] = $attributeValue;
            }
            if ($aType === 'select' || $aType === 'multiselect' || $aType === 'boolean' || $aType === 'swatch_visual' || $aType === 'swatch_text') {
                $attributeName = $attribute->getAttributeCode();
                if ($attributeName != 'quantity_and_stock_status') {
                    $attributeValue = $product->getAttributeText($attributeName);
                    if (is_object($attributeValue)) {
                        $this->productData[$attributeName] = (string)$attributeValue;
                    } else {
                        $this->productData[$attributeName] = $attributeValue;
                    }
                }
            }
        }
    }

    /**
     * @param $product
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function setCategoriesToResponse($product)
    {
        $cats = $product->getCategoryIds();
        $categoryName = $categoryParentId = $categoryParentName = $categoryParentNameArr = [];

        foreach ($cats as $categoryId) {
            $_cat = $this->categoryRepository->get($categoryId);
            $categoryName[] = $_cat->getName();
            $path = $_cat->getPath();

            $ids = explode('/', $path);
            array_shift($ids);
            $categoryParentId = implode('/', $ids);

            foreach ($ids as $key => $id) {
                $categoryParentName[$key] = $this->categoryRepository->get($id)->getName();
            }
            $categoryParentNameArr = implode('/', $categoryParentName);
        }

        $this->productData['category_breadcrumb'] = implode('/', $categoryName);
        $this->productData['category_id'] = implode('/', $cats);
        $this->productData['category_parent_id'] = $categoryParentId;

        $this->productData['category_parent_name'] = $categoryParentNameArr;
    }

    /**
     * @param $images
     * @param $baseImageUrl
     */
    private function setGalleryDataToResponse($images, $baseImageUrl)
    {
        $galleryImages = [];
        $img = $sm = $tn = '';

        foreach ($images as $image) {
            $galleryImages[] = $image->getUrl();
            $imageType = $image->getMediaType();

            switch ($imageType) {
                case 'image':
                    $img = $image->getFile();
                    break;

                case 'small_image':
                    $sm = $image->getFile();
                    break;

                case 'thumbnail':
                    $tn = $image->getFile();
                    break;
            }
        }

        if (($key = array_search($baseImageUrl, $galleryImages)) !== false) {
            unset($galleryImages[$key]);
        }

        $this->productData['gallery_images'] = array_values($galleryImages);
        $this->productData['image_path'] = $img;
        $this->productData['image_url'] = $baseImageUrl;
        $this->productData['image_url_small'] = $sm;
        $this->productData['image_url_thumbnail'] = $tn;
    }

    /**
     * @param $product
     */
    private function setChildSkusByConfigureProductToResponse($product)
    {
        $associatedIds = [];

        $children = $product->getTypeInstance()->getUsedProducts($product);

        array_map(function($child){
            $associatedIds[] = $child->getId();

        },$children);

        $this->productData['child_skus'] = implode(',', $associatedIds);
    }

    /**
     * @param $product
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function setParentSkuBySimpleProductToResponse($product)
    {
        $parentIds = $this->groupedProduct->getParentIdsByChild($product->getId());
        if (!$parentIds) {
            $parentIds = $this->configureProduct->getParentIdsByChild($product->getId());
        }

        if (isset($parentIds[0])) {
            $parentSku = $this->productRepository->get($parentIds[0])->getSku();
            $this->productData['parent_sku'] = $parentSku;
        }
    }

    /**
     * @param $product
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getBaseImageUrl($product)
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
    }
}

