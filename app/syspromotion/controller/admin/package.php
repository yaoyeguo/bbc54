<?php
class syspromotion_ctl_admin_package extends desktop_controller{

    public function index()
    {
        return $this->finder('syspromotion_mdl_package',array(
            'title' => app::get('syspromotion')->_('组合促销列表'),
            'use_buildin_delete' => false,
            'actions' => array(

            ),
        ));
    }

}