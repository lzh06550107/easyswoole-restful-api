<?php

declare(strict_types=1);

namespace App\Utility\Traits;

use EasySwoole\EasySwoole\Logger;
use EasySwoole\Log\LoggerInterface;

trait LogTrait
{
    protected function exception_debug(\Throwable $exception)
    {
        Logger::getInstance()->debug($this->exception_message($exception));
    }

    protected function log_debug($msg)
    {
        Logger::getInstance()->debug($this->method_debug_backtrace_info($msg));
    }

    protected function exception_info(\Throwable $exception)
    {
        Logger::getInstance()->info($this->exception_message($exception));
    }

    protected function log_info($msg)
    {
        Logger::getInstance()->info($this->log_message($msg));
    }

    protected function exception_notice(\Throwable $exception)
    {
        Logger::getInstance()->notice($this->exception_message($exception));
    }

    protected function log_notice($msg)
    {
        Logger::getInstance()->notice($this->log_message($msg));
    }

    protected function exception_warning(\Throwable $exception)
    {
        Logger::getInstance()->waring($this->exception_message($exception));
    }

    protected function log_warning($msg)
    {
        Logger::getInstance()->waring($this->log_message($msg));
    }

    protected function exception_error(\Throwable $exception)
    {
        Logger::getInstance()->error($this->exception_message($exception));
    }

    protected function log_error($msg)
    {
        Logger::getInstance()->error($this->log_message($msg));
    }

    protected function exception_console(\Throwable $exception)
    {
        Logger::getInstance()->console($this->exception_message($exception), LoggerInterface::LOG_LEVEL_DEBUG);
    }

    protected function log_console($msg, $loggerInterface_level)
    {
        Logger::getInstance()->console($this->log_message($msg), $loggerInterface_level);
    }

    private function exception_message(\Throwable $e)
    {
        $msg = '';
        $msg .= "- 文件名：**{$e->getFile()}** \n";
        $msg .= "- 第几行：**{$e->getLine()}** \n";
        $msg .= "- 错误信息：**{$e->getMessage()}**\n";
        return $msg;
    }

    private function log_message($message): string
    {
        $debugInfo = debug_backtrace();
        $head = $debugInfo[1]['file']. '('.$debugInfo[1]['line'].') ';
        return $head.$message;
    }

    public function method_debug_backtrace_detail()
    {
        $debugInfos = debug_backtrace();
        return print_r($debugInfos, true);
    }

    public function method_debug_backtrace_info()
    {
        $debugInfos = debug_backtrace();
        foreach ($debugInfos as $key => $debugInfo) {
            $head .= $debugInfo['file']. '('. $debugInfo['function'] .': '.$debugInfo['line'].') ' . PHP_EOL;
        }
        return $head;
    }
}
