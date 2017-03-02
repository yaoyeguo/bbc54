<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class system_queue_message_redis extends system_queue_message_abstract implements system_interface_queue_message{
    private $__id = null;
    private $__params = null;
    private $__worker = null;

    public function __construct($queueData)
    {
        $this->__queueData = $queueData;

        $queueData = json_decode($queueData, true);
        $this->__params = $queueData['params'];
        $this->__worker = $queueData['worker'];
        $this->__queueName = $queueData['queue_name'];
    }

    public function get_params()
    {
        return $this->__params;
    }

    public function get_worker()
    {
        return $this->__worker;
    }

    public function getQueueData()
    {
        return $this->__queueData;
    }

    public function getQueueName()
    {
        return $this->__queueName;
    }
}
