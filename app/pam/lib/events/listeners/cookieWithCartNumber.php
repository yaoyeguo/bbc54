<?php

class pam_events_listeners_cookieWithCartNumber {

    //登录同步购物车数据
    public function login($userId)
    {
        $cartNumber = app::get(pam_auth_user::appId)->rpcCall('trade.cart.getCount', ['user_id' => $userId]);
        userAuth::syncCookieWithCartNumber($cartNumber['number']);
        userAuth::syncCookieWithCartVariety($cartNumber['variety']);

        return true;
    }

    public function logout()
    {
        userAuth::syncCookieWithCartNumber(0);
        userAuth::syncCookieWithCartVariety(0);

        return true;
    }
}
