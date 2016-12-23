<?php
/**
 * User: MaslovAN
 * Date: 23.12.2016
 * Time: 13:42
 */

namespace app;

use anmaslov\parser\ItemBuilder;

use anmaslov\parser\BuilderStartPage;


class App
{
    public function run()
    {
        $itemBuilder = new ItemBuilder();

        $builderStartPage = new BuilderStartPage();

        $itemBuilder->setBuilderItem( $builderStartPage );
        $itemBuilder->constructItem('15 лет');

        $shopItem = $itemBuilder->getItem();

        echo '<pre>';
        print_r($shopItem);
        echo '</pre>';
    }
}