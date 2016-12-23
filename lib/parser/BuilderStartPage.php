<?php
/**
 * User: MaslovAN
 * Date: 23.12.2016
 * Time: 14:35
 */
namespace anmaslov\parser;


class BuilderStartPage extends BuilderItem{

    protected $_uri = 'http://localhost';

    public function loadPage($itemName)
    {
        //$this->getPage("$this->_uri/search/?query=$itemName" , $this->_uri);
        $this->getPage("$this->_uri" , $this->_uri);
    }

    public function getTitle()
    {
        $caption = $this->_nkg->get('#wp-statistics ul>li')->toArray();
        //get widget statistic from start page
        $this->_item->setTitle($caption);
    }

    public function getPrice()
    {
        // TODO: Implement getPrice() method.
    }

    public function getProperties()
    {
        // TODO: Implement getProperties() method.
    }

    public function getImages()
    {
        // TODO: Implement getImages() method.
    }

}