<?php
/**
 * User: MaslovAN
 * Date: 22.12.2016
 * Time: 14:44
 */
namespace anmaslov\parser;

class BuilderYandexMarket extends BuilderItem {

    protected $_uri = 'https://market.yandex.ru/';
    
    public function loadPage($itemName)
    {
        $itemName = urlencode($itemName);

        $this->getPage($this->_uri. "catalog/54726/list?text=$itemName&deliveryincluded=1&onstock=1" , $this->_uri);
        $this->loadDom();

        $itemLink = $this->getLinkList();

        $this->getPage($this->_uri . '/' . $itemLink, $this->_uri);
        $this->loadDom();
    }

    private function getLinkList()
    {
        $links = $this->_nkg->get('.snippet-card__header-link')->toArray();
        return $links[0]['href'];
    }

    public function getTitle()
    {
        $caption = $this->_nkg->get('.n-product-title h1')->toText();
        $this->_item->setTitle($caption);
    }

    public function getPrice()
    {
        $price = $this->_nkg->get('.n-product-default-offer__price-value .price')->toText();
        $this->_item->setPrice($price);
    }

    public function getProperties()
    {
        $prop = $this->_nkg->get('.n-product-content-block__content ul li')->toArray();
        $this->_item->setProperies($prop);
    }

    public function getImages()
    {
        $images = $this->_nkg->get('img.n-gallery__image')->toArray();
        $this->_item->setImages($images);
    }


}