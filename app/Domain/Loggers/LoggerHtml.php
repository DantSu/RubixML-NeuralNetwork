<?php

namespace App\Domain\Loggers;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class LoggerHtml implements LoggerInterface
{
    private array $messages = [];

    public function emergency($message, array $context = [])
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = [])
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = [])
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = [])
    {
        $this->messages[] = ['time' => (new \DateTime())->format('Y-m-d H:i:s.v'), 'color' => $this->getColorLevel($level), 'message' => $message];
    }

    private function getColorLevel(mixed $level): string
    {
        return match ($level) {
            LogLevel::EMERGENCY, LogLevel::ALERT, LogLevel::CRITICAL => '#d72e47',
            LogLevel::ERROR => '#BB4455',
            LogLevel::WARNING => '#e89c5f',
            LogLevel::NOTICE => '#d2cbb8',
            LogLevel::INFO => '#aad2de',
            default => '#ffffff',
        };
    }

    public function getStackTrace(): string
    {
        return \view('logger/messages', ['messages' => $this->messages])->render();
    }
}
