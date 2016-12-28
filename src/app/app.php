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
    private $dirImage = "/upload/img/parser/";                                          // путь к фото
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
            /*//tmp for test
            if (++$i > 5){
                break;
            }*/
        }
    }

    private function makeRow($arItem) {
       
        $itemBuilder = new ItemBuilder(new BuilderPriceRu);
        $itemBuilder->constructItem($arItem['longtitle']);
        $shopItem = $itemBuilder->getItem();

        if (count($shopItem->getProperies()) > 0) {
            $res = $this->modx->getObject('msProduct',array('id' => $arItem['id']));
            $res->set('content', $shopItem->getPropTable());
            if ($res->save()) {
                $this->modx->cacheManager->clearCache(); //если сохранение успешно, то чистим кэш
                echo "ok, id ". $arItem['id'] ." was updated<br>";
            }
        }

        $arImgs = array();
        foreach ($shopItem->getImages() as $arImg) {
            $p =  strrpos($arImg['src'], '/');
            $arImgs[] = substr($arImg['src'], $p+1);
        }

        if (count($arImgs)) {
            //import img
            foreach ($arImgs as $val) {
                $image = str_replace('//', '/', MODX_BASE_PATH . $this->dirImage. $val);
                if (!file_exists($image)) {
                    echo "Could not import image $val to gallery. File $image not found on server.<br>";
                }
                else {
                    $res = $this->modx->runProcessor('gallery/upload',
                        array('id' => $arItem['id'], 'name' => $val, 'file' => $image),
                        array('processors_path' => MODX_CORE_PATH.'components/minishop2/processors/mgr/')
                    );
                    if ($res->isError()) {
                        print_r($res->getAllErrors(), 1);
                    }else{
                        echo "Item with id ".$arItem['id']." img update <br>";
                    }
                    unset($res);
                }
            }
        }

        unset($itemBuilder);
    }
}