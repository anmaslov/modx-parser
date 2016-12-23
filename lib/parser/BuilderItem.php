<?php
/**
 * Created by PhpStorm.
 * User: MaslovAN
 * Date: 22.12.2016
 * Time: 14:00
 */
namespace anmaslov\parser;

//абстрактынй строитель

abstract class BuilderItem{

    protected $_item;


    public function getItem() {
        return $this->_item;
    }

    public function createNewItem() {
        $this->_item = new Item();
    }

    /***
     * @return bool
     */
    protected function loadDom() {
        if ($this->_item->pageHtml)
            $this->_item = new nokogiri($this->pageHtml);
        else
            return false;
    }

    /**
     * copy images to local server
     */
    protected function copyImages() {
        foreach ($this->images as $image) {
            //todo copy images to sever
        }
    }

    /***
     * Covert array property to table
     * @return string
     */
    protected function PropertyToTable() {
        if (count($this->properties) > 0){
            $rStr = '<table>';
            foreach ($this->properties as $arKey=>$arItem) {
                $rStr .= '<tr>';
                $rStr .= '<td><b>' . $arKey . '</b></td>';
                $rStr .= '<td>' . $arItem . '</td>';
                $rStr .= '</tr>';
            }
            $rStr .= '</table>';
            return $rStr;
        }else{
            return '';
        }
    }

    abstract public function loadPage();
    abstract public function getTitle();
    abstract public function getPrice();
    abstract public function getProperties();
    abstract public function getImages();

}