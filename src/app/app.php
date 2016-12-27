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
    private $offset = 50;
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

        $pdo = $this->modx->getService('pdoFetch');
        $arItems = $pdo->getCollection(
            'msProduct',
            array(
                'deleted' => false,
                'published' => true,
                'isfolder' => false,
                //'content:IN' => array('NULL',''),
                'Data.image:IS' => null,
            ),
            array(
                'parents' => $this->parent, // Категория с товарами
                'innerJoin' => array(
                    'Data' => array('class' => 'msProductData')
                ),
                'select' => array('msProduct' => '*', 'Data' => '*'),
                'sortby' => 'Data.price',
                //'sortdir' => 'asc',
                )
        );
        echo "<pre>".date("d.m.Y - H:i:s") . "\r\nВсего пустых записей: ".count($arItems) . "\r\n <br />";
        print_r($this->modx->getPlaceholder('pdoTools.log'));
        //print_r($arItems);
        //cnt = 95
        echo '</pre>';

        $i = 0;
        foreach ($arItems as $arItem)
        {
            if (trim($arItem['pagetitle']) != '') {
                $this->makeRow($arItem);
            }
            if (++$i > 5){
                break;
            }
        }

        /*$itemBuilder->constructItem('Xiaomi Redmi 3s');
        $shopItem = $itemBuilder->getItem();

        echo '<pre>';
        print_r($shopItem);
        echo '</pre>';*/
    }

    private function makeRow($arItem) {
        print '-----Новый товар-----------------------';
        /*print '<pre>';
        print_r($arItem);
        print '</pre>';*/

        $itemBuilder = new ItemBuilder(new BuilderPriceRu);
        $itemBuilder->constructItem($arItem['longtitle']);
        $shopItem = $itemBuilder->getItem();

        echo '<pre>';
        print_r($shopItem);
        echo '</pre>';

        unset($itemBuilder);
    }
}