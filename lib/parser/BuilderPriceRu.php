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

        $link = $this->getFirstLink();

        if ($link) { //Если попадаем на список ссылок - выбираем первую попавшую
            $this->getPage($this->_uri . '/' . $link, $this->_uri);
            $this->loadDom();
        }
    }

    public function getFirstLink()
    {
        $links = $this->_nkg->get('.b-list-models h3.b-list-viewtile__item-title a')->toArray();
        if (isset($links[0]['href'])) {
            return $links[0]['href'];
        }
        
        return false;
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
        $properties = $this->_nkg->get('section.b-model-details__list .b-model-details-group')->toArray();

        $propArr = array();
        foreach ($properties as $arDescr) {

            $cat = $arDescr['div'][0]['h3'][0]['#text'][0];

            $pArr = array();
            foreach ($arDescr['div'][1]['div'] as $arProp)
            {
                $propKey = $arProp['div'][0]['div'][0]['span'][0]['a']['0']['#text'][0];
                $propVal = $arProp['div'][1]['span'][0]['#text'][0];
                $pArr[$propKey] = $propVal;
            }

            $propArr[] = array(
                'group' => $cat,
                'prop' => $pArr,
                );
        }

        $this->_item->setProperies($propArr);
        $this->PropertyToTable(); //set table
    }
    
    protected function propertyToTable()
    {
        if (count($this->_item->getProperies()) == 0) {
            return;
        }

        $rStr = '';
        foreach ($this->_item->getProperies() as $arProp) {
            $rStr .= '<h3>'.$arProp['group'].'</h3>';

            if (count($arProp['prop']) > 0) {
                $rStr .= '<table><tbody>';
                foreach ($arProp['prop'] as $arKey => $arItem) {
                    $rStr .= '<tr>';
                    $rStr .= '<th><span>' . $arKey . '</span></th>';
                    $rStr .= '<td>' . $arItem . '</td>';
                    $rStr .= '</tr>';
                }
                $rStr .= '</tbody></table>';
            }
        }
        $this->_item->setPropTable($rStr);
        echo $rStr;
    }

    public function getImages()
    {
        $images = $this->_nkg->get('ul.modelcard__slideshow li.slideshow__item img')->toArray();
        $this->_item->setImages($images);

        $this->copyImages(); //copy Images to server
    }


}