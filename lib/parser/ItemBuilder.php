<?php
/**
 * Date: 23.12.2016
 * Time: 11:42
 * Управляющий класс
 */

namespace anmaslov\parser;

class ItemBuilder{

    private $_builderItem;

    /**
     * ItemBuilder constructor.
     * @param $builderItem
     */
    public function __construct(BuilderItem $builderItem)
    {
        $this->_builderItem = $builderItem;
    }

    /**
     * Get item
     * @return mixed
     */
    public function getItem()
    {
        return $this->_builderItem->getItem();
    }

    /**
     * Main constructor
     * @param $itemName
     */
    public function constructItem($itemName) {
        $this->_builderItem->createNewItem();

        $this->_builderItem->loadPage($itemName);

        $this->_builderItem->getTitle();
        $this->_builderItem->getPrice();
        $this->_builderItem->getProperties();
        $this->_builderItem->getImages();
    }
}