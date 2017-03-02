<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 *
 * 商家基本设置
 */

return array(
    '交易设置' => array(
        'trade.cancel.spacing.time' => array( 'type'=>SET_T_INT,'default'=>72,'desc'=>'交易关闭间隔时间','vtype'=>'required&&unsignedint','helpinfo'=>'<span class=\'notice-inline\'>单位：小时(h)</span>'),
        'trade.finish.spacing.time' => array( 'type'=>SET_T_INT,'default'=>7, 'desc'=>'交易完成间隔时间','vtype'=>'required&&unsignedint','helpinfo'=>'<span class=\'notice-inline\'>单位：天(d)</span>'),
    ),
    '积分设置' => array(
        'point.ratio' => array('type'=>SET_T_STR,'default'=>1,'desc'=>'积分换算比率:','vtype'=>'required&&unsignedint','helpinfo'=>'<span class=\'notice-inline\'>默认1元 = 1积分</span>'),
        'point.expired.month' => array('type'=>SET_T_STR,'default'=>12,'desc'=>'积分过期月份:','vtype'=>'required&&unsignedint','helpinfo'=>'<span class=\'notice-inline\'>默认12【12代表每年的12月最后一天】 </span>'),
        'open.point.deduction' => array('type'=>SET_T_BOOL,'default'=>0,'desc'=>'开启积分抵扣:','vtype'=>'required','helpinfo'=>'<span class=\'notice-inline\'>【积分抵扣开启后，会员下单结算时将可使用积分抵扣订单金额】</span>','class'=>'point_deduction','javascript'=>'$$(".point_deduction").addEvent("click",function(e){if(this.value==0){$$(".point-deduction-setting").getParent("tr").hide();}else{$$(".point-deduction-setting").getParent("tr").show();}});if($$(".point_deduction")[0].getValue() == 1){$$(".point-deduction-setting").getParent("tr").show();}else{$$(".point-deduction-setting").getParent("tr").hide();}'),
        'point.deduction.rate' => array('type'=>SET_T_STR,'default'=>100,'desc'=>'积分抵扣金额比率:','vtype'=>'unsignedint','helpinfo'=>'<span class=\'notice-inline\'> 默认100积分 = 1元 </span>','class'=>'point-deduction-setting'),
        //'point.deduction.max' => array('type'=>SET_T_ENUM,'options'=>array('10'=>'10%','20'=>'20%','30'=>'30%','40'=>'40%','50'=>'50%','60'=>'60%','70'=>'70%','80'=>'80%','90'=>'90%'),'default'=>90,'desc'=>'每单积分抵扣金额上限:','vtype'=>'','helpinfo'=>'<span class=\'notice-inline\'>默认为订单总金额*0.9 </span>','class'=>'point-deduction-setting'),
        'point.deduction.max' => array('type'=>SET_T_INT,'default'=>99,'desc'=>'每单积分抵扣金额上限:','vtype'=>'positive&&unsignedint','helpinfo'=>'<span class=\'notice-inline\'> 1 <= x <=99;默认为订单总金额*0.99 </span>','class'=>'point-deduction-setting'),
	'open.sendPoint' => array('type'=>SET_T_BOOL,'default'=>0,'desc'=>'开启注册送积分:','vtype'=>'required','helpinfo'=>'<span class=\'notice-inline\'>【开启后，会员注册成功将赠送积分】</span>','class'=>'open_sendPoint','javascript'=>'$$(".open_sendPoint").addEvent("click",function(e){if(this.value==0){$$(".sendPoint_num").getParent("tr").hide();}else{$$(".sendPoint_num").getParent("tr").show();}});if($$(".open_sendPoint")[0].getValue() == 1){$$(".sendPoint_num").getParent("tr").show();}else{$$(".sendPoint_num").getParent("tr").hide();}'),
	'sendPoint.num' => array('type'=>SET_T_INT,'default'=>5,'desc'=>'注册送积分:','vtype'=>'unsignedint','class'=>'sendPoint_num'),
    ),
    '基本设置' => array(
        'user.deposit.password.limit'=> array( 'type'=>SET_T_INT,'default'=>5,'desc'=>'预存款支付密码输错','vtype'=>'required&&unsignedint','helpinfo'=>'<span class=\'notice-inline\'>次停用</span>'),
        'user.deposit.password.expire'=> array( 'type'=>SET_T_INT,'default'=>3,'desc'=>'预存款支付密码输错次数达到上限后，停用','vtype'=>'required&&unsignedint','helpinfo'=>'<span class=\'notice-inline\'>小时</span>'),
        'shop.cleanlog.time'=> array( 'type'=>SET_T_INT,'default'=>30,'desc'=>'商家操作日志保存天数','vtype'=>'required&&unsignedint','helpinfo'=>'<span class=\'notice-inline\'>单位：天。默认30天</span>'),
        'admin.cleanlog.time'=> array( 'type'=>SET_T_INT,'default'=>30,'desc'=>'平台操作日志保存天数','vtype'=>'required&&unsignedint','helpinfo'=>'<span class=\'notice-inline\'>单位：天。默认30天</span>'),
    ),

    #'购物设置'=>array(
    #    'site.buy.target',
    #    'system.money.decimals',
    #    'system.money.operation.carryset',
    #    'site.trigger_tax', //是否开启发票
    #    'site.personal_tax_ratio',
    #    'site.company_tax_ratio',
    #    'site.tax_content',
    #    'site.checkout.zipcode.required.open',
    #    'site.checkout.receivermore.open',
    #    'site.combination.pay',//组合支付
    #    'cart.show_order_sales.type',
    #),
    #'购物显示设置'=>array(
    #    'site.login_type',
    #    'site.register_valide',
    #    'site.login_valide',
    #    'gallery.default_view',
    #    'site.show_mark_price',
    #    'site.market_price',
    #    'site.market_rate',
    #    'selllog.display.switch',
    #    'selllog.display.limit',
    #    'selllog.display.listnum',
    #    'site.save_price',
    #    'goods.show_order_sales.type',
    #    'site.member_price_display',
    #    'site.show_storage',
    #    'goodsbn.display.switch',
    #    'goods.recommend',
    #    'goodsprop.display.position',
    #    'site.isfastbuy_display',
    #    'gallery.display.listnum',
    #    'gallery.display.pagenum',
    #    'gallery.deliver.time',
    #    'gallery.comment.time',
    #    'site.cat.select',
    #    'gallery.display.buynum',
    #    'gallery.display.price',
    #    'gallery.display.tag.goods',
    #    'gallery.display.tag.promotion',
    #    'gallery.display.promotion',
    #    'gallery.display.store_status',
    #    'gallery.store_status.num',
    #    'gallery.display.stock_goods',
    #    'site.imgzoom.show',
    #    'site.imgzoom.width',
    #    'site.imgzoom.height',
    #),
    #'其他设置'=>array(
    #    'system.product.alert.num',
    #    'system.goods.freez.time',
    #),
);

