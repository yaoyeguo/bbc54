<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  www.ec-os.net ShopEx License
 *
 * 获取多条满减促销列表
 * promotion.couponitem.list
 */
final class syspromotion_api_coupon_couponItemList{

    public $apiDescription = '获取指定优惠券促销商品列表';

    public function getParams()
    {
        $return['params'] = array(
            'coupon_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'优惠卷促销id'],
            'page_no' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'每页数据条数,默认10条'],
            'orderBy' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'coupon_id asc'],
            'fields'    => ['type'=>'field_list', 'valid'=>'', 'default'=>'', 'example'=>'', 'description'=>'需要的字段','default'=>'','example'=>''],
        );

        return $return;
    }

    /**
     * 获取满减促销列表
     */
    public function couponItemList($params)
    {
        $objMdlCouponItem = app::get('syspromotion')->model('coupon_item');
        $objMdlCoupon = app::get('syspromotion')->model('coupon');
        if(!$params['fields'])
        {
            $params['fields'] = '*';
        }
        $couponInfo = $objMdlCoupon->getRow('*',array('coupon_id'=>$params['coupon_id']));
        if($couponInfo['coupon_status']=='agree')
        {
            $filter = array('coupon_id'=>$params['coupon_id']);
            $countTotal = $objMdlCouponItem->count($filter);
            if( $countTotal )
            {
                $pageTotal = ceil($countTotal/$params['page_size']);
                $page =  $params['page_no'] ? $params['page_no'] : 1;
                $limit = $params['page_size'] ? $params['page_size'] : 10;
                $currentPage = ($pageTotal < $page) ? $pageTotal : $page; //防止传入错误页面，返回最后一页信息
                $offset = ($currentPage-1) * $limit;
                $orderBy = $params['orderBy'] ? $params['orderBy'] : 'coupon_id DESC';
                $couponItemList = $objMdlCouponItem->getList($params['fields'], $filter, $offset, $limit, $orderBy);
                $couponItem = array(
                    'list'=>$couponItemList,
                    'total_found'=>$countTotal,
                );
            }
            $couponItem['promotionInfo'] = $couponInfo;
            return $couponItem;
        }
        else
        {
            return false;
        }
    }

}

