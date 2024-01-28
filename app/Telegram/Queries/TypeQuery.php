<?php

namespace App\Telegram\Queries;

use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class TypeQuery extends AbstractQuery
{
    protected static string $regex = '/type (\w+)/';

    /**
     * @param UpdateEvent $event
     * @return bool
     * @throws TelegramSDKException
     */
    public function handle(UpdateEvent $event): mixed
    {
        $type_arr = explode(' ', $event->update->callbackQuery->data);
        $type = $type_arr[1];
        $sum = $type_arr[2];
        $client_id = $event->update->getChat()->id;

        $order = Order::where('client_id', $client_id)->where('status', '<', 3)->orderByDesc('id')->first();
        if ($order) {
            $order_data = $order->toArray();
            if ($order_data['status'] < 3) {
              /*
              return $event->telegram->answerCallbackQuery([
                  'callback_query_id' => $event->update->callbackQuery->id,
                  'text' => 'У вас уже есть неоплаженная заявка '. $order_data['id']
                  . ' ' .$order->status_arr[$order_data['status']],
              ]);
              */
              return $event->telegram->sendMessage([
                  'chat_id' => $event->update->getChat()->id,
                  'text' => "У вас уже есть неоплаченная заявка!" //. $order_data['id']
                  . "\nПереведите СТРОГО указанную к оплате сумму (" . $order_data['sum_discount'] . " р)."
                  . "\nРеквизиты для оплаты: " . $order_data['payment_info'],
                  'reply_markup' => $this->buildKeyboard($order->id),
              ]);
            }
        } else {

        $sum_discount = self::getSum($sum);

        $order = Order::create([
          'client_id' => $client_id,
          'sum' => $sum,
          'sum_discount' => $sum_discount,
          'payment_type' => $type,
          //'payment_info' => $payment_info,
          'status' => 0,
        ]);

        $payment_info = $this->getRekvizit($order->id, $client_id, $sum, $sum_discount, $type);

        if ($payment_info) {
          $order->update(['payment_info' => $payment_info, 'status' => 1]);
          return $event->telegram->sendMessage([
            'chat_id' => $event->update->getChat()->id,
            'text' => "Реквизит для оплаты: " . $payment_info
            . "\nПереведите СТРОГО указанную к оплате сумму (" . self::getSum($sum)." р).
В противном случае зачисление не произоидет автоматически - придется писать в поддержку. ",
            'reply_markup' => $this->buildKeyboard($order->id),
        ]);
      } else {
        return $event->telegram->sendMessage([
          'chat_id' => $event->update->getChat()->id,
          'text' => "Ошибка получения реквизитов",
          'reply_markup' => $this->buildKeyboard(),
        ]);
      }
       }
    }

    private function getSum($sum)
    {
        $discont = 5;
        return round($sum * (100 - $discont) / 100);
    }

    private function getRekvizit($order_id, $client_id, $sum, $sum_discount, $type)
    {
        $response = Http::retry(3, 100)->withHeaders([
                'Accept' => 'application/json',
        ])->withToken(env('PAYMENT_INFO_AUTH_TOKEN'))->post(env('PAYMENT_INFO_URL'), [
            'order_id' => $order_id,
            'client_id' => strval($client_id),
            'payment_sum' => $sum,
            'payment_sum_discount' => $sum_discount,
            'payment_type' => $type,
        ]);

        if($response->successful()) {
            return $response->json('payment_props');
        } else {
          return false;
        }

    }

    /**
     * @throws JsonException
     */
    private function buildKeyboard($id): false|string
    {
        return json_encode([
            'inline_keyboard' => [
                [
                    ['text' => 'Я оплатил!', 'callback_data' => 'paid '.$id],
                    ['text' => 'Отмена', 'callback_data' => 'cancel'],
                ],
            ]
        ], JSON_THROW_ON_ERROR);
    }
}
