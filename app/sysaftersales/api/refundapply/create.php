<?php

class sysaftersales_api_refundapply_create {

    /**
     * 接口作用说明
     */
    public $apiDescription = '创建退款申请单';

    public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'string','valid'=>'required', 'description'=>'店铺ID'],
            'aftersales_bn' => ['type'=>'string','valid'=>'', 'description'=>'售后编号'],
            'tid' => ['type'=>'string','valid'=>'required', 'description'=>'订单号'],
            'oid' => ['type'=>'string','valid'=>'', 'description'=>'子订单号，如果是取消订单则不需要输入'],
            'refunds_type' => ['type'=>'string','valid'=>'required', 'description'=>'退款申请的类型,aftersalse 售后申请退款, cancel 取消订单退款'],
            'reason' => ['type'=>'json','valid'=>'required', 'description'=>'申请退款理由'],
            'total_price' => ['type'=>'string','valid'=>'', 'description'=>'申请退款的金额，取消订单不需要填写退款金额'],
            'status' => ['type'=>'string','valid'=>'required', 'description'=>'0 未审核, 1 已完成退款,2 已驳回,3 商家审核通过, 4 商家审核不通过, 5 商家强制关单, 6 平台强制关单'],
            'refund_bn' => ['type'=>'string','valid'=>'', 'description'=>'退款申请单编号，如果未填写则自动生成'],
            'return_freight' => ['type'=>'bool','valid'=>'string', 'description'=>'是否返还运费("true":退运费,"false":不退运费)'],
        );
        return $return;
    }

    /**
     * 判断是否可以进行创建退款申请单
     */
    private function __check($params)
    {
        $objMdlRefunds = app::get('sysaftersales')->model('refunds');
        if( $params['refunds_type'] == 'cancel' )
        {
            $data = $objMdlRefunds->getRow('refunds_id',['tid'=>$params['tid']]);
            if( $data )
            {
                throw new \Exception("不能重复申请取消");
            }
        }
        else
        {
            if( empty($params['aftersales_bn']) )
            {
                throw new \Exception("该订单已申请过退款");
            }

            $data = $objMdlRefunds->getRow('refunds_id',['aftersales_bn'=>$params['aftersales_bn']]);
            if( $data )
            {
                throw new \Exception("该售后单已申请过退款");
            }
        }

        return true;
    }

    public function create($params)
    {
        $this->__check($params);
        $objLibRefund = kernel::single('sysaftersales_refunds');

        if( $params['refunds_type'] == 'cancel' )
        {
            $data = $objLibRefund->cancelRefundApply($params['tid'], $params['status'], $params['reason'], $params['shop_id'], $params['refund_bn'],$params['return_freight']);
        }
        else
        {
            $db = app::get('sysuser')->database();
            $db->beginTransaction();
            try
            {
                //售后退款，如果是商家同意则表示提交到平台进行退款
                if( $params['status'] == '3' )
                {
                    $updateStatusParams['aftersales_bn'] = $params['aftersales_bn'];
                    $updateStatusParams['shop_id'] = $params['shop_id'];
                    $updateStatusParams['status'] = '8';
                    app::get('sysaftersales')->rpcCall('aftersales.status.update', $updateStatusParams);
                }
                $data = $objLibRefund->afsRefundApply($params, $params['tid'], $params['oid'], $params['refund_bn']);
            }
            catch( Exception $e )
            {
                $db->rollback();
                throw $e;
            }

            $db->commit();
        }

        event::fire('refund.created', [$params['tid'], $data]);

        return true;
    }
}


