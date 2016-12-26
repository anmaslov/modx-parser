<?php
/**
 * User: MaslovAN
 * Date: 23.12.2016
 * Time: 13:42
 */

namespace app;

use anmaslov\parser\ItemBuilder;

use anmaslov\parser\BuilderPriceRu;


class App
{
    public function run()
    {
        $itemBuilder = new ItemBuilder(new BuilderPriceRu);

        $itemBuilder->constructItem('Xiaomi Redmi 3s');
        $shopItem = $itemBuilder->getItem();

        echo '<pre>';
        print_r($shopItem);
        echo '</pre>';
    }
}