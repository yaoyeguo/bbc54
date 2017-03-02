<?php
/**
 * detail.php 
 * Created Time 2016年3月18日 上午9:38:03
 *
 * @author     Xiaodc
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class importexport_data_settlement_detail{
    
    // 商家后台指定的导出字段
    public $shop_export_fields = 'tid,settlement_time,price,pay_time,bn,title,spec_nature_info,num,part_mjz_discount,payment,post_fee,refund_fee,cat_service_rate,commission_fee,settlement_fee,settlement_type';
    
    public function __construct()
    {
        $this->model = app::get('sysclearing')->model('settlement_detail');
    }
    
    public function get_title()
    {
        $fields = 'oid,tid,shop_id,settlement_time,pay_time,bn,title,spec_nature_info,num,divide_order_fee,part_mjz_discount,payment,post_fee,refund_fee,cat_service_rate,commission_fee,settlement_fee,settlement_type,discount_fee,adjust_fee';
        $fields = explode(',', $fields);
        $tmpTitle = $this->_title();
        $title = array();
        foreach ($fields as $val)
        {
            if(array_key_exists($val, $tmpTitle))
            {
                $title[$val] = $tmpTitle[$val];
            }
        }
        
        if(!$title)
        {
            $title = $tmpTitle;
        }
        
        return $title;
    }
    
    private function _title()
    {
        $cols = $this->model->_columns();
        $title = array();
        foreach( $cols as $col => $val )
        {
            if( !$val['deny_export'] ){//不进行导出导入字段
                if(!$val['label'] && !$val['comment'])
                {
                    $title[$col] = $col;
                }
                else
                {
                    $title[$col] = $val['label'] ? $val['label'] : $val['comment'];
                }
            }
        }
        return $title;
    }
}