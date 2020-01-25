<?php

namespace stepankarasov\mailing\common\models\telegramSendLog;

/**
 *
 */
trait TelegramSendLogFormatter
{
    /**
     * @return mixed
     */
    public function createdAtFormatAdmin()
    {
        return $this->createdAt->toDateTime()->format('d.m.Y H:i');
    }

    /**
     * @return string
     */
    public function sendAtFormatAdmin()
    {
        return $this->sendAt ? $this->sendAt->toDateTime()->format('d.m.Y H:i') : '-';
    }

    /**
     * @return string
     */
    public function openAtFormatAdmin()
    {
        return $this->openAt ? $this->openAt->toDateTime()->format('d.m.Y H:i') : '-';
    }
}
