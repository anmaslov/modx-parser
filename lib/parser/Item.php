<?php
/**
 * User: MaslovAN
 * Date: 22.12.2016
 * Time: 15:22
 */

namespace anmaslov\parser;

/**
 * Базовый класс
 * Class Item
 * @package anmaslov\parser
 */

class Item
{
    private $_title = '';
    private $_description = '';
    private $_price = '';
    private $_images = '';
    private $_properies = '';
    private $_propTable = '';

    /**
     * @return string
     */
    public function getProperies()
    {
        return $this->_properies;
    }

    /**
     * @return string
     */
    public function getPropTable()
    {
        return $this->_propTable;
    }

    /**
     * @param string $propTable
     */
    public function setPropTable($propTable)
    {
        $this->_propTable = $propTable;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }
    
    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * @param string $price
     */
    public function setPrice($price)
    {
        $this->_price = $price;
    }

    /**
     * @param string $images
     */
    public function setImages($images)
    {
        $this->_images = $images;
    }

    /**
     * @param string $properies
     */
    public function setProperies($properies)
    {
        $this->_properies = $properies;
    }

    /**
     * @return string
     */
    public function getImages()
    {
        return $this->_images;
    }


}