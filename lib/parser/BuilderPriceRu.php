<?php
/**
 * User: MaslovAN
 * Date: 23.12.2016
 * Time: 11:41
 */

namespace anmaslov\parser;

class BuilderPriceRu extends BuilderItem{

    protected $_uri = 'http://price.ru';
    protected $_itemName;

    public function loadPage($itemName)
    {
        $this->_itemName = $itemName;
        $itemName = urlencode($itemName);

        $this->getPage("$this->_uri/search/?query=$itemName" , $this->_uri);
        $this->loadDom();
    }

    public function getTitle()
    {
        $this->_item->setTitle($this->_itemName);
    }

    public function getPrice()
    {
        // TODO: Implement getPrice() method.
    }

    public function getProperties()
    {
        $properties = $this->_nkg->get('section.b-model-details__list .b-model-details__item')->toArray();

        $propArr = array();
        foreach ($properties as $arDescr) {

            $propKey = $arDescr['div'][0]['div'][0]['span'][0]['a']['0']['#text'][0];
            $propVal = $arDescr['div'][1]['span'][0]['#text'][0];

            $propArr[$propKey] = $propVal;
        }
        $this->_item->setProperies($propArr);
    }

    public function getImages()
    {
        $images = $this->_nkg->get('ul.modelcard__slideshow li.slideshow__item img')->toArray();
        $this->_item->setImages($images);

    }


}