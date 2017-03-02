<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysim_api_row
{

    /**
     * 接口作用说明
     * trade.cart.cartCouponAdd
     */
    public $apiDescription = '生成im的html页面(单个), 如果商家没有设置im的email，则返回空';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        return [
            'params'=>[
                'shop_id' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'1','description'=>'店铺id,或者“platform”'],
                'type' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'index','description'=>'客服入口点的地方([index, help, guest, assessment, consultation, shop, itemInfo, tradeList, tradeInfo],)'],
                'content' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'1','description'=>'显示内容，会原封不动的返回并在这个外面套上点击链接'],
                'user_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'1','description'=>'c的id，可以不填。'],
                'params' => ['type'=>'array', 'valid'=>'', 'default'=>'', 'example'=>'["note"=>"aaaa", "loc"=>"http://www.baidu.com"]','description'=>'扩展字段，各种乱七八糟的东西都会来'],
            ]
        ];

    }

    public function get($params)
    {
        $shop_id = $params['shop_id'];

        if( in_array($params['type'], config::get('im.positionArea', [])) )
            $type = $params['type'];
        else
            throw new LogicException(app::get('sysim')->_('该客服入口类型未定义'));

        $content = $params['content'];
        $params = $params['params'];

        return kernel::single('sysim_im')->getRow($shop_id, $type, $content, $user_id, $params);
    }

}
