<?php
class syspromotion_finder_activity_xydiscount{

    public $column_rule = "促销规则";
    public $column_rule_order = 1;
    public $column_rule_width = 300;

    public function column_rule(&$colList,$list)
    {
        $registerData = app::get('syspromotion')->model('activity');
        foreach($list as $k=>$row)
        {
            if($row['condition_value'])
            {
                $data = explode(',',$row['condition_value']);
                foreach($data as $val)
                {
                    $value = explode('|',$val);
                    $colList[$k] .= '满'.$value[0].'件,给予'.$value['1'].'%的折扣'."；";
                }
            }
        }
    }
}
