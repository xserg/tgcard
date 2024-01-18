<?php

namespace App\Telegram\Queries;

use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;

class TermsQuery extends AbstractQuery
{
    protected static string $regex = '/terms/';

    /**
     * @param UpdateEvent $event
     * @return Message
     * @throws TelegramSDKException
     * @throws \JsonException
     */
    public function handle(UpdateEvent $event): Message
    {
        //return $event->telegram->replyWithMessage([
        return $event->telegram->sendMessage([
            'chat_id' => $event->update->getChat()->id,
            'text' => "Условия использования бла бла бла,
условия, соглашение, 1 час на использование карты
отправьте боту сообщение с той суммой  в рублях (только цифры без пробелов)",
        ]);
    }

}
