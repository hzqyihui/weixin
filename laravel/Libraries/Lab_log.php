<?php
/**
 *       Filename: Lab_log.php
 *
 *    Description: 日志记录类库
 *
 *        Created: 2017-04-06 18:38
 *
 *         Author: huazhiqiang
 */
namespace App\Libraries;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class Lab_log{

    /**
     * 重定义各类级别日志路径
     * @return Logger
     */
    public function createLog()
    {
        $log = new Logger('debug');
        $date = date('Y-m');
        $logDir = storage_path('logs/'.$date);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $log->pushHandler(new StreamHandler($logDir . '/' . date('Y-m-d') . '.log', Logger::DEBUG));
        $log->pushHandler(new StreamHandler($logDir . '/' . date('Y-m-d') . '.log', Logger::INFO));
        $log->pushHandler(new StreamHandler($logDir . '/' . date('Y-m-d') . '.log', Logger::ALERT));
        $log->pushHandler(new StreamHandler($logDir . '/' . date('Y-m-d') . '.log', Logger::CRITICAL));
        $log->pushHandler(new StreamHandler($logDir . '/' . date('Y-m-d') . '.log', Logger::EMERGENCY));
        $log->pushHandler(new StreamHandler($logDir . '/' . date('Y-m-d') . '.log', Logger::ERROR));
        $log->pushHandler(new StreamHandler($logDir . '/' . date('Y-m-d') . '.log', Logger::WARNING));
        $log->pushHandler(new StreamHandler($logDir . '/' . date('Y-m-d') . '.log', Logger::NOTICE));
        return $log;
    }
}