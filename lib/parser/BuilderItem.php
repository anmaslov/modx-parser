<?php
/**
 * User: MaslovAN
 * Date: 22.12.2016
 * Time: 14:00
 */
namespace anmaslov\parser;


//абстрактынй строитель

abstract class BuilderItem{

    protected $_item;
    protected $_html;
    protected $_nkg;

    public function getItem() {
        return $this->_item;
    }

    public function createNewItem() {
        $this->_item = new Item();
    }

    /**
     * copy images to local server
     */
    public function copyImages() {
        foreach ($this->_item->getImages() as $image) {
            copy($image['src'] ,
                __DIR__ . '/../../upload/' .
                substr(md5(rand()), 0, 7) . '.' . Utils::extension($image['src']));
        }
    }

    /***
     * Covert array property to table
     * @return string
     */
    public function PropertyToTable() {
        if (count($this->_item->getProperies()) > 0){
            var_dump($this->_item->getProperies());
            $rStr = '<table>';
            foreach ($this->_item->getProperies() as $arKey=>$arItem) {
                $rStr .= '<tr>';
                $rStr .= '<td><b>' . $arKey . '</b></td>';
                $rStr .= '<td>' . $arItem . '</td>';
                $rStr .= '</tr>';
            }
            $rStr .= '</table>';
            $this->_item->setPropTable($rStr);
            return $rStr;
        }else{
            return '';
        }
    }


    /**
     * load page from URI
     * @param $host
     * @param null $referer
     * @return mixed
     */
    protected function getPage($host, $referer = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Requested-With' => 'XMLHttpRequest'));
        curl_setopt($ch, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $this->_html = curl_exec($ch);
        //echo curl_error($ch);
        curl_close($ch);
    }


    public function loadDom() {
        if ($this->_html)
            $this->_nkg = new \nokogiri($this->_html);
        else
            return false;
    }

    abstract public function loadPage($itemName);
    abstract public function getTitle();
    abstract public function getPrice();
    abstract public function getProperties();
    abstract public function getImages();

}