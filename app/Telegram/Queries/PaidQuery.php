<?php

namespace App\Telegram\Queries;

use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class PaidQuery extends AbstractQuery
{
    protected static string $regex = '/paid (\d+)/';

    /**
     * @param UpdateEvent $event
     * @return bool
     * @throws TelegramSDKException
     */
    public function handle(UpdateEvent $event): bool
    {
      $paid_arr = explode(' ', $event->update->callbackQuery->data);
      $order_id = $paid_arr[1];
      $client_id = $event->update->getChat()->id;

      $order = Order::where('client_id', $client_id)->where('id', $order_id)->first();
      if ($order && $order->status == 1) {
          $order->update(['status' => 2]);
          $this->markPaid($order_id, $client_id);
      }

        return $event->telegram->answerCallbackQuery([
            'callback_query_id' => $event->update->callbackQuery->id,
            'text' => sprintf('Проводится проверка платежа ')
              //. $client_id . " " . $order_id),
        ]);
    }

    private function markPaid($order_id, $client_id)
    {
        $response = Http::retry(3, 100)->withHeaders([
                'Accept' => 'application/json',
        ])->withToken(env('PAYMENT_INFO_AUTH_TOKEN'))->post(env('PAYMENT_PAID_URL'), [
            'client_id' => $client_id,
            'order_id' => $order_id,
        ]);
        if($response->successful()) {
            return true;
        } else {
          return false;
        }
    }
}
