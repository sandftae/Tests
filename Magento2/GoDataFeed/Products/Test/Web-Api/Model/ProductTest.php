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

namespace GoDataFeed\Products\Test\Unit\Model;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class ProductTest
 * @package GoDataFeed\Products\Test\Unit\Model
 */
class ProductTest extends WebapiAbstract
{
    const TEST_ITEM_ID = 13;
    const API_SERVICE_VERSION = '/V1';
    const TEST_PRODUCT_NAME = 'Simple Product 3';

    /**
     * @var $idNewProduct
     */
    private $idNewProduct;

    /**
     * @var $skuNewProduct
     */
    private $skuNewProduct;

    /**
     * @var $nameNewProduct
     */
    private $nameNewProduct;

    /**
     * Create product for testing
     */
    public function setUp()
    {
        $product = Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Product::class);
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
            ->setId($this->idNewProduct = rand(43, 350))
            ->setAttributeSetId(4)
            ->setWebsiteIds([1])
            ->setName($this->nameNewProduct = 'Simple Product (version random ' . rand(1, 99) . ')')
            ->setSku($this->skuNewProduct = 'simple-' . (rand(0, 15) . '-' . rand(15, 35) . '-product'))
            ->setPrice(rand(1, 50))
            ->setDescription( 'Description with <b>html tag</b>' )
            ->setMetaTitle('meta title')
            ->setMetaKeyword('meta keyword')
            ->setMetaDescription('meta description')
            ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->setCategoryIds([2])
            ->setStockData(['use_config_manage_stock' => 0])
            ->setCanSaveCustomOptions(true)
            ->setHasOptions(true)
            ->save();
    }

    /**
     * The method verifies that a specific item has been returned.
     * Cases:
     *  - check whether the array returned
     *  - check if the array is not empty
     *  - check whether there is a new product name in the returned array
     *
     * @test
     */
    public function getProduct()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::API_SERVICE_VERSION . '/godatafeed/getproduct/id/' . $this->idNewProduct,
                'httpMethod' => Request::HTTP_METHOD_GET
            ]
        ];
        $product = $this->_webApiCall($serviceInfo);

        // Check whether the array is
        $this->assertTrue(is_array($product), 'returned not an array.');

        // Check if the array is not empty
        $this->assertEquals(!empty($product), true, 'empty massive.');

        // Check whether there is a new product name in the returned array
        $this->assertTrue(in_array( $this->nameNewProduct, $product), 'There is no such value in the array.');
    }

    /**
     * The method checks that the returned collection of products.r
     * Cases:
     *  - check whether the array returned
     *  - check that the SKU of the new product is in the returned array
     *  - check that the ID of the new product is in the returned array
     *
     * @test
     */
    public function getProducts()
    {
        $checkedId  = false;
        $checkedSKu = false;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::API_SERVICE_VERSION . '/godatafeed/products',
                'httpMethod' => Request::HTTP_METHOD_GET
            ]
        ];

        $productCollection = $this->_webApiCall($serviceInfo);

        array_map(function($product) use (&$checkedId, &$checkedSKu){
           if($this->idNewProduct == $product['id']) {
               $checkedId = true;
           }
           if($this->skuNewProduct == $product['sku']) {
               $checkedSKu = true;
           }
        }, $productCollection);

        // Check whether the array is
        $this->assertEquals(!empty($productCollection), true, 'empty massive.');

        // Check that the SKU of the new product is in the returned array
        $this->assertTrue($checkedSKu, 'there is no such sku.');

        // Check that the ID of the new product is in the returned array
        $this->assertTrue($checkedId, 'there is no such id.');
    }

    /**
     * The method checks that the number of rows is returned.
     * Cases:
     * - return value is not a number
     * - check that there are fields in the collection
     *
     * @test
     */
    public function getProductsCount()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::API_SERVICE_VERSION . '/godatafeed/products/count',
                'httpMethod' => Request::HTTP_METHOD_GET
            ]
        ];

        $count = $this->_webApiCall($serviceInfo);

        // Check value type
        $this->assertTrue(is_int($count), 'Return value is not a number.');

        // Check qty in the array returned
        $this->assertEquals(($count > 0), true, 'qty of fields < 0.');
    }
}