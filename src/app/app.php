<?php
/**
 * User: MaslovAN
 * Date: 23.12.2016
 * Time: 13:42
 */

namespace app;

use anmaslov\parser\ItemBuilder;
use anmaslov\parser\BuilderPriceRu;

define('MODX_API_MODE', true);


class App
{
    public $modx;
    private $username = 'nortel';
    private $password = 'password';
    private $start = 0;
    private $offset = 500;
    private $parent = 18491; //Родительская категория

    public function init()
    {
        require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/core/config/config.inc.php';
        require_once MODX_BASE_PATH . 'index.php';

        $this->modx = $modx;
    }

    public function winToUtf($str){
        return iconv('Windows-1251', 'UTF-8', $str);
    }

    public function run()
    {

        $this->init();
        //Понеслось говнище по трубам
        // Load main services
        $this->modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
        $this->modx->getService('error','error.modError');
        $this->modx->lexicon->load('minishop2:default');
        $this->modx->lexicon->load('minishop2:manager');

        // Логинимся в админку
        $response = $this->modx->runProcessor('security/login', array('username' => $this->username, 'password' => $this->password));
        if ($response->isError()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $response->getMessage());
            return;
        }
        $this->modx->initialize('mgr');

        $query = $this->modx->newQuery('msProduct');
        $query->where(array(
            'content:IN' => array('NULL',''),
            //'parent' => $this->parent,
        ));

        $query->select(array('msProduct.*'));
        $query->limit($this->offset, $this->start);
        $query->sortby('id', 'ASC');
        $total = $this->modx->getCount('msProduct', $query);
        $query->prepare();

        echo date("d.m.Y - H:i:s") . "\r\n Всего пустых записей: ".$total ."\r\n".$query->toSQL() . "\r\n <br />";

        $arItems = $this->modx->getCollection('msProduct', $query);
        foreach ($arItems as $item)
        {
            $arItem = $item->toArray();
            $this->modx->error->reset();

            //make row
            if (($arItem['image'] == '') && ($arItem['pagetitle'] != '')) {
                $this->makeRow($arItem);
            }
        }


        $itemBuilder = new ItemBuilder(new BuilderPriceRu);

        /*$itemBuilder->constructItem('Xiaomi Redmi 3s');
        $shopItem = $itemBuilder->getItem();

        echo '<pre>';
        print_r($shopItem);
        echo '</pre>';*/
    }

    private function makeRow($arItem) {
        print '-----Новый товар-----------------------';
        print '<pre>';
        print_r($arItem);
        print '</pre>';
    }
}