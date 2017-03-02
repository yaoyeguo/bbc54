<?php
/**
 * 分类api数据
 */
class syscategory_data_cat {

    public function __construct()
    {
        $this->objMdlCat = app::get('syscategory')->model('cat');
    }

    /**
     * 删除分类
     * @todo 需要判断是否关联了商品
     * @param  int $catId 分类id
     * @param  string $msg 返回错误信息
     * @return bool
     */
    public function toRemove($catId)
    {
        $aCats = $this->objMdlCat->getRow('cat_id', array('parent_id'=>intval($catId)));
        if($aCats)
        {
            $msg = '删除失败：本分类下面还有子分类';
            throw new \LogicException($msg);
            return false;
        }

        $searchParams['page_no'] = 1;
        $searchParams['page_size'] = 1;
        $searchParams['cat_id'] = intval($catId);
        $searchParams['fields'] = 'item_id';
        $itemsList = app::get('syscategory')->rpcCall('item.search',$searchParams);
        if($itemsList['total_found'] > 0 )
        {
            $msg = '删除失败：本分类下面还有商品';
            throw new \LogicException($msg);
            return false;
        }

        //判断该类目下是否有店铺
        $shopParams = ['cat_id' => intval($catId),'page_no'=>1,'page_size'=>1];
        $shop = app::get('syscategory')->rpcCall('shop.get.by.cat',$shopParams);
        if(isset($shop['list']) && $shop['list'])
        {
            $msg = '删除失败：本类目下面有签约店铺';
            throw new \LogicException($msg);
            return false;
        }

        //判断该类目下是否有为开店的入驻申请
        //$applyParams = ['cat_id' => implode(',',$catId),'page_no'=>1,'page_size'=>1];
        //$apply = app::get('syscategory')->rpcCall('apply.get.by.cat',$applyParams);
        //if($apply)
        //{
        //    $msg = '删除失败：本分类下面有入住申请关联';
        //    throw new \LogicException($msg);
        //    return false;
        //}

        $parentRow = $this->objMdlCat->getRow('parent_id', array('cat_id'=>intval($catId)));

        $db = app::get('syscategory')->database();
        $db->beginTransaction();
        try
        {
            $result = $this->objMdlCat->database()->delete('syscategory_cat', ['cat_id' => $catId], [\PDO::PARAM_INT]);
            if(!$result) throw new \LogicException("删除类目失败");

            if($parentRow['parent_id'])
            {
                $result = $this->objMdlCat->database()->executeUpdate('UPDATE syscategory_cat SET child_count = child_count-1 WHERE cat_id=?', [$parentRow['parent_id']], [\PDO::PARAM_INT]);
                if(!$result) throw new \LogicException("更新父级下的子级数量失败");
            }

            $objMdlProps = app::get('syscategory')->model('cat_rel_prop');
            $relProp = $objMdlProps->getRow('prop_id',array('cat_id'=>$catId));
            if($relProp)
            {
                $result = $objMdlProps->delete(array('cat_id'=>$catId));
                if(!$result) throw new \LogicException("删除关联关系失败");
            }
            $db->commit();
        }
        catch(\LogicException $e)
        {
            $db->rollback();
            throw new \LogicException($e->getMessage());
            return false;
        }

        $this->objMdlCat->cleanCatsCache();
        return true;
    }

    /**
     * 更新分类排序
     * @param  array $sortData 分类排序数组 array('order_sort'=>array($cat_id=>$sort_number,......))
     * @param  string $msg 返回错误信息
     * @return bool
     */
    public function updateSort($sortData, &$msg)
    {
        foreach( $sortData as $k => $v )
        {
            $this->objMdlCat->update( array('order_sort'=>($v==='' ? null : $v)), array('cat_id'=>$k) );
        }
        return $this->objMdlCat->cleanCatsCache();
    }
}


