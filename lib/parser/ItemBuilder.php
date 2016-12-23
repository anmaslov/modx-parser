<?php
/**
 * Created by PhpStorm.
 * User: MaslovAN
 * Date: 23.12.2016
 * Time: 11:42
 * Управляющий класс
 */

namespace anmaslov\parser;

class ItemBuilder{

    private $_builderItem;

    public function setBuilderItem(BuilderItem $bi)
    {
        $this->_builderItem = $bi;
    }

    public function getItem()
    {
        return $this->_builderItem->getItem();
    }

    public function constructItem() {
        $this->_builderItem->createNewItem();
        $this->_builderItem->loadPage();
        /*$this->_builderItem->loadDom();
        $this->_builderItem->getTitle();
        $this->_builderItem->getPrice();
        $this->_builderItem->getProperties();
        $this->_builderItem->getImages();*/

        /*$this->_builderPizza->createNewPizza ();
        $this->_builderPizza->buildPastry ();
        $this->_builderPizza->buildSauce ();
        $this->_builderPizza->buildGarniture ();*/
    }
}