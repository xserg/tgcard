<?php

namespace App\Telegram\Queries;

use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;

class PaidQuery extends AbstractQuery
{
    protected static string $regex = '/paid/';

    /**
     * @param UpdateEvent $event
     * @return bool
     * @throws TelegramSDKException
     */
    public function handle(UpdateEvent $event): bool
    {
        return $event->telegram->answerCallbackQuery([
            'callback_query_id' => $event->update->callbackQuery->id,
            'text' => sprintf('Проводится проверка платежа'),
        ]);
    }
}
