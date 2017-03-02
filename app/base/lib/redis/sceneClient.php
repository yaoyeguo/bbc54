<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://club.shopex.cn/ ShopEx License
 */

class base_redis_sceneClient
{
    static private $scriptMaps = null;
    
    static private $useCount = 0;
    
    protected $client;

    public function __construct($scene, $client)
    {
        $this->sceneName = $scene;
        
        $this->client = $client;
    }

    public function __call($commandID, $arguments)
    {

        static::$useCount++;
        $arguments[0] = $this->sceneName.':'.$arguments[0];
        logger::debug(sprintf('REDIS:%d %s, arguments: %s', static::$useCount, $commandID, var_export($arguments, 1)));
        
        return $this->client->__call($commandID, $arguments);
    }

    public function loadScripts($names)
    {
        $scriptMaps = static::$scriptMaps ? : (static::$scriptMaps = config::get('redis.scripts'));
        foreach((array)$names as $name)
        {
            if ($class = $scriptMaps[$name])
            {
                $this->client->getProfile()->defineCommand($name, $class);
            }
        }
    }    
}
