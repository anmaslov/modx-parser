<?php
/**
 * User: MaslovAN
 * Date: 23.12.2016
 * Time: 13:42
 */

namespace app;

use anmaslov\parser\ItemBuilder;

use anmaslov\parser\BuilderYandexMarket;


class App
{
    public function run()
    {
        $itemBuilder = new ItemBuilder();

        //$builderStartPage = new BuilderStartPage();
        $builderYandexMarket = new BuilderYandexMarket();

        $itemBuilder->setBuilderItem( $builderYandexMarket );
        $itemBuilder->constructItem('THL W');

        $shopItem = $itemBuilder->getItem();

        echo '<pre>';
        print_r($shopItem);
        echo '</pre>';
    }
}