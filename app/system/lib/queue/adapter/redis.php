<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

use base_support_arr as Arr;

class system_queue_adapter_redis implements system_interface_queue_adapter {

    /**
     * 创建执行队列的有效时间
     *
     * @var int|null
     */
    protected $expire = 3600;


    /**
     * 创建一个队列任务
     *
     * @param  string  $queueName 队列标示
     * @param  array   $data 执行队列参数
     * @return mixed
     */
    public function publish($queueName, $queueData )
    {
        $time = time();
        $data = [
            'queue_name' => $queueData['queue_name'],
            'worker' => $queueData['worker'],
            'params' => $queueData['params'],
            'create_time' => $time,
        ];
        return $this->pushRaw($this->createPayload($data), $queueName);
    }

    /**
     * 将队列保存到redis
     *
     * @param  string  $payload
     * @param  string  $queue
     * @return mixed
     */
    public function pushRaw($payload, $queueName )
    {
        redis::scene('queue')->rpush($queueName, $payload);

        return Arr::get(json_decode($payload, true), 'id');
    }

    /**
     * 获取一个队列任务ID
     *
     * @param string $queueName
     * @return mixed 队列任务数据
     */
    public function get($queueName)
    {
        if (! is_null($this->expire) )
        {
            $this->migrateAllExpiredJobs($queueName);
        }

        $objectReids = redis::scene('queue');
        $objectReids->loadScripts('queueGet');

        $queueData = $objectReids->queueGet($queueName, $queueName.':reserved', time() + $this->expire);

        if( ! is_null($queueData) )
        {
            return new system_queue_message_redis($queueData);
        }

        return false;
    }

    /**
     * 确认消息已经被消费.
     *
     * @param  string  $queueData
     * @return void
     */
    public function ack($queueMessage)
    {
        $queueName = $queueMessage->getQueueName();
        $queueData = $queueMessage->getQueueData();

        return redis::scene('queue')->zrem($queueName.':reserved', $queueData);
    }

    /**
     * 清空一个队列
     *
     * @param string $queue
     */
    public function purge($queueName)
    {
        redis::scene('queue')->ltrim($queueName,-1,0);
    }

    public function is_end($queueName)
    {
        $len = redis::scene('queue')->llen($queueName);
        return $len > 0 ? false : true;
    }

    public function consume($queueName)
    {
    }

    /**
     * 将所有处理失败的或者处理超时的队列重新加入到队列中
     *
     * @param  string  $queue
     * @return void
     */
    protected function migrateAllExpiredJobs($queueName)
    {
        return $this->migrateExpiredJobs($queueName.':reserved', $queueName);
    }

    /**
     * 将处理失败的或者处理超时的队列重新加入到队列中
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    public function migrateExpiredJobs($from, $to)
    {
        $options = ['cas' => true, 'watch' => $from, 'retry' => 10];

        redis::scene('queue')->transaction($options, function ($transaction) use ($from, $to) {

            $queueData = $this->getExpiredJobs(
                $transaction, $from, $time = time()
            );

            if (count($queueData) > 0)
            {
                $this->removeExpiredJobs($transaction, $from, $time);

                $this->pushExpiredJobsOntoNewQueue($transaction, $to, $queueData);
            }
        });
    }

    /**
     * 获取失效的队列
     *
     * @param  \Predis\Transaction\MultiExec  $transaction
     * @param  string  $from
     * @param  int  $time
     * @return array
     */
    protected function getExpiredJobs($transaction, $from, $time)
    {
        return $transaction->zrangebyscore($from, '-inf', $time);
    }

    /**
     * 删除失效的队列
     *
     * @param  \Predis\Transaction\MultiExec  $transaction
     * @param  string  $from
     * @param  int  $time
     * @return void
     */
    protected function removeExpiredJobs($transaction, $from, $time)
    {
        $transaction->multi();

        $transaction->zremrangebyscore($from, '-inf', $time);
    }

    /**
     * 将失效的队列加入到正常队列中
     *
     * @param  \Predis\Transaction\MultiExec  $transaction
     * @param  string  $to
     * @param  array  $jobs
     * @return void
     */
    protected function pushExpiredJobsOntoNewQueue($transaction, $to, $jobs)
    {
        call_user_func_array([$transaction, 'rpush'], array_merge([$to], $jobs));
    }

    /**
     * 创建队列执行参数，数组转为字符串
     *
     * @param  mixed   $data
     * @return string
     */
    protected function createPayload($data = '')
    {
        $payload = $this->setMeta($data, 'id', $this->getRandomId());

        return $this->setMeta($payload, 'attempts', 1);
    }

    /**
     * Get a random ID string.
     *
     * @return string
     */
    protected function getRandomId()
    {
        return str_random(32);
    }

    /**
     * 在队列执行参数中追加其他参数
     *
     * @param string $payload 队列参数
     * @param string $key
     * @param string $value
     */
    protected function setMeta($payload, $key, $value)
    {
        if( ! is_array($payload) )
        {
            $payload = json_decode($payload, true);
        }

        return json_encode(Arr::set($payload, $key, $value));
    }

    /**
     * Get the expiration time in seconds.
     *
     * @return int|null
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * Set the expiration time in seconds.
     *
     * @param  int|null  $seconds
     * @return void
     */
    public function setExpire($seconds)
    {
        $this->expire = $seconds;
    }

}

