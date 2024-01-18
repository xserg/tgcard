<?php

namespace App\Telegram\Queries;

use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Models\Order;

class CancelQuery extends AbstractQuery
{
    protected static string $regex = '/cancel/';

    /**
     * @param UpdateEvent $event
     * @return Message
     * @throws TelegramSDKException
     * @throws \JsonException
     */
    public function handle(UpdateEvent $event): Message
    {
        $client_id = $event->update->getChat()->id;
        $order = Order::where('client_id', $client_id)->orderByDesc('id')->first();
        $order->update(['status' => 5]);
        return $event->telegram->sendMessage([
            'chat_id' => $event->update->getChat()->id,
            'text' => "Ваша заявка отменена, для продолжения /start",
        ]);
    }

}
