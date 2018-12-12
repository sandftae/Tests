<?php
/**
 * Copyright 2018 Amazon.com, Inc. or its affiliates. All Rights Reserved.

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

namespace GoDataFeed\Products\Api;

/**
 * Interface ProductInterface
 * @package GoDataFeed\Products\Api
 */
interface ProductInterface
{
	/**
     * @param string $id of the param.
     * @return mixed|string of the param Value.
     */
    public function getProduct($id);
    /**
     * @return array of the param Value.
     */
    public function getProducts();
    /**
     * @return mixed|string of the param Value.
     */
    public function getProductsCount();
}
