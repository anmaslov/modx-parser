<?php
/**
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

    public function constructItem($itemName) {
        $this->_builderItem->createNewItem();

        $this->_builderItem->loadPage($itemName);
        $this->_builderItem->loadDom();

        $this->_builderItem->getTitle();

        /*$this->_builderItem->getPrice();
        $this->_builderItem->getProperties();
        $this->_builderItem->getImages();*/
    }
}