<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class theme_middleware_rsftockens
{
    public function __construct()
    {
    }

    public function handle($request, Clousure $next)
    {
        kernel::single('base_session')->start();
        $requestTocken = $_POST['rsftockens'];
        $sessionTocken = $_SESSION['rsftocken'];

        unset($_SESSION['rsftocken']);
        if($requestTocken)
        {
            //echo '<pre>';print_r($sessionTocken);print_r($requestTocken);
            if($requestTocken!=$sessionTocken)
            {
                return response::json(array(
                            'error' => true,
                            'message'=>'不要重复提交！',
                            'redirect' => null,
                        ));
            }
        }

        return $next($request);
    }
}
