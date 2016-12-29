<?php
/**
 * User: MaslovAN
 * Date: 23.12.2016
 * Time: 13:42
 */

namespace app;

use anmaslov\parser\ItemBuilder;
use anmaslov\parser\BuilderPriceRu;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

define('MODX_API_MODE', true);


class App
{
    public $modx;
    private $log;
    private $username = 'nortel';
    private $password = 'password';
    private $dirImage = "/upload/img/";
    private $parsed = 0; //count of parsed items
    private $parent = 18491; //Родительская категория

    public function init()
    {
        require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/core/config/config.inc.php';
        require_once MODX_BASE_PATH . 'index.php';

        $this->modx = $modx;

        $this->log = new Logger('name');
        $this->log->pushHandler(new StreamHandler(MODX_BASE_PATH . 'parser/log/' . date("Y_m_d_H") . '.log', Logger::DEBUG));
    }

    public function winToUtf($str){
        return iconv('Windows-1251', 'UTF-8', $str);
    }

    public function run()
    {
        $this->init();
        $this->log->debug('init system');
        //Понеслось говнище по трубам
        // Load main services
        $this->modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
        $this->modx->getService('error','error.modError');
        $this->modx->lexicon->load('minishop2:default');
        $this->modx->lexicon->load('minishop2:manager');
        $this->log->debug('load modx modules');

        // Логинимся в админку
        $response = $this->modx->runProcessor('security/login', array('username' => $this->username, 'password' => $this->password));
        if ($response->isError()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $response->getMessage());
            $this->log->warning('error auth', $response->getMessage());
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
        $this->log->info('load empty items '. count($arItems));
        //$this->log->debug($this->modx->getPlaceholder('pdoTools.log'));

        $i = 0;
        foreach ($arItems as $arItem)
        {
            $this->log->debug("Parse " . ++$i . "of " . count($arItems));

            if (trim($arItem['pagetitle']) != '') {
                $this->makeRow($arItem);
            }
            /*//tmp for test
            if (++$i > 5){
                break;
            }*/
        }

        $this->log->info("Parse complete, parsed {$this->parsed}, items to parse (" . count($arItems) .")");
    }

    private function makeRow($arItem)
    {
        $itemBuilder = new ItemBuilder(new BuilderPriceRu);
        $itemBuilder->constructItem($arItem['longtitle']);
        $this->log->debug('start parse from remote host');
        $shopItem = $itemBuilder->getItem();
        //$this->log->debug('parse complite', $shopItem);

        if (count($shopItem->getProperies()) > 0) {
            $res = $this->modx->getObject('msProduct',array('id' => $arItem['id']));
            $res->set('content', $shopItem->getPropTable());
            if ($res->save()) {
                $this->modx->cacheManager->clearCache(); //если сохранение успешно, то чистим кэш
                $this->log->debug("ok, id ". $arItem['id'] ." was updated");
                $this->parsed++;
            }else{
                $this->log->error("error when trying save, id ". $arItem['id'], $shopItem->getProperies());
            }
        }else{
            $this->log->info("Item no parsed", $arItem);
        }

        $arImgs = array();
        foreach ($shopItem->getImages() as $arImg) {
            $p =  strrpos($arImg['src'], '/');
            $arImgs[] = substr($arImg['src'], $p+1);
        }
        $this->log->debug("Cnt of items image gallery " . count($arImgs), $arImgs);

        if (count($arImgs)) {
            //import img
            foreach ($arImgs as $val) {
                $image = str_replace('//', '/', MODX_BASE_PATH . $this->dirImage. $val);
                if (!file_exists($image)) {
                    $this->log->error("Could not import image $val to gallery. File $image not found on server");
                }
                else {
                    $res = $this->modx->runProcessor('gallery/upload',
                        array('id' => $arItem['id'], 'name' => $val, 'file' => $image),
                        array('processors_path' => MODX_CORE_PATH.'components/minishop2/processors/mgr/')
                    );
                    if ($res->isError()) {
                        $this->log->error("Error when trying save image to gallery", $res->getAllErrors());
                    }else{
                        $this->log->info("Item with id ".$arItem['id']." img update");
                    }
                    unset($res);
                }
            }
        }
        unset($itemBuilder);
    }
}