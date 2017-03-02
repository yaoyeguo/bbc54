<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysim_im
{
    var $_instance;

    //初始化 实例化im标签生成工具
    public function __construct()
    {
        $class = config::get('im.plugin');
        if($this->_instance == null)
            $this->_instance = kernel::single($class);

        return null;
    }

    //生成聊天窗口
    //list = [
    //    [
    //        'shop_id',   店铺id，如果是平台则传platmform,  不能为空
    //        'type',      类型，标示是在哪里显示, 默认值是“default”
    //        'content',      内容，需要显示的名称，可以为图片<img src="http://example.com/example.jgp">或者是“联系客服”等
    //        'user_id',   会员的唯一识别码，可以为空
    //        'params'=>['info'],    带的参数，是一个数组，目前只定义了info
    //     ]
    //]
    public function getList($list)
    {
        return $this->_instance->getList($list);
    }

    //生成聊天入口
    public function getRow($shop_id, $type, $content, $user_id, $params)
    {
        return $this->_instance->getRow($shop_id, $type, $content, $user_id, $params);
    }

}

