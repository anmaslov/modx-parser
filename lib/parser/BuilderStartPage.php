<?php
/**
 * User: MaslovAN
 * Date: 23.12.2016
 * Time: 14:35
 */
namespace anmaslov\parser;

//Пример, для теста, тестируется wordPress на localhost
class BuilderStartPage extends BuilderItem{

    protected $_uri = 'http:/localhost';

    public function loadPage($itemName)
    {
        $this->getPage("$this->_uri/?s=$itemName" , $this->_uri);
        $this->loadDom();

        $itemLink = $this->getLinkList();
        $this->getPage($itemLink, $this->_uri);
        $this->loadDom();
    }

    public function getLinkList()
    {
        $links = $this->_nkg->get('.entry-title a')->toArray();
        return $links[0]['href'];
    }

    public function getTitle()
    {
        $caption = $this->_nkg->get('title')->toText();
        $this->_item->setTitle($caption);
    }

    public function getPrice()
    {
        $price = $this->_nkg->get('#wp-statistics ul>li:first-child')->toText();
        //get widget statistic from start page
        $this->_item->setPrice($price);
    }

    public function getProperties()
    {
        $properties = $this->_nkg->get('.entry-content p')->toText();
        $this->_item->setDescription($properties);

        $prop = array(
            'Top' => '30',
            'Color' => 'blue',
            'Size' => 'xxl',
        );
        $this->_item->setProperies($prop);
    }

    public function getImages()
    {
        $images = $this->_nkg->get('.entry-content img')->toArray();
        $this->_item->setImages($images);

        $this->copyImages();
    }

}