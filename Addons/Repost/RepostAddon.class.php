<?php

namespace Addons\Repost;

use Common\Controller\Addon;
use Weibo\Api\WeiboApi;

/**
 * 转发插件
 * @author 想天软件工作室
 */
class RepostAddon extends Addon
{

    public $info = array(
        'name' => 'Repost',
        'title' => '转发',
        'description' => '转发',
        'status' => 1,
        'author' => '想天软件工作室',
        'version' => '0.1'
    );

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    //实现的repost钩子方法
    public function repost($param)
    {
        $weibo = $this->getweiboDetail($param['weiboId']);


        $sourseId = $weibo['data']['sourseId'];

        if (!$sourseId) {
            $sourseId = $param['weiboId'];
        }
        $param['sourseId'] = $sourseId;
        $this->assign('repost_count', $weibo['repost_count']);
        $this->assign($param);
        $this->display('repost');
    }

    public function fetchRepost($weibo)
    {

        $weibo_data = unserialize($weibo['data']);
        $weibo_data['attach_ids'] = explode(',', $weibo_data['attach_ids']);

        $sourse_weibo = $this->getweiboDetail($weibo_data['sourse']['id']);

        foreach ($weibo_data['attach_ids'] as $k_i => $v_i) {
            $weibo_data['image'][$k_i]['small'] = getRootUrl() . '/' . getThumbImageById($v_i, 100, 100);
            $bi = M('Picture')->where(array('status' => 1))->getById($v_i);
            $weibo_data['image'][$k_i]['big'] = getRootUrl() . '/' . $bi['path'];

        }

        $param['weibo'] = $weibo;
        $param['weibo']['weibo_data'] = $weibo_data;
        $param['weibo']['sourse_weibo'] = $sourse_weibo;
        $this->assign($param);
        return $this->fetch('display');
    }


    private function getweiboDetail($weiboId)
    {
        $weibo_check = D('Weibo/Weibo')->where(array('id' => $weiboId, 'status' => 1))->find();

        if ($weibo_check) {
            $this->weiboApi = new WeiboApi();
            $weibo = $this->weiboApi->getWeiboDetail($weiboId);
        } else {
            $weibo['weibo'] = false;
        }

        return $weibo['weibo'];
    }

}