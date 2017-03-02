<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class sysim_api_getHtml
{

    /**
     * 接口作用说明
     * trade.cart.cartCouponAdd
     */
    public $apiDescription = '生成im的html页面';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        return [
            'params'=>[
                'list' => ['type'=>'array', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'请求列表'],
            ]
        ];

    }

    public function get($params)
    {
        $list = $params['list'];

    }

}
