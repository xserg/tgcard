<?php

namespace App\Http\Controllers;


use App\Telegram\Queries\AbstractQuery;
use Illuminate\Http\JsonResponse;
use Telegram\Bot\Events\UpdateEvent;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Api;
use App\Models\Client;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{

    public function handle(Request $request)
    {

      Telegram::on('callback_query.text', function (UpdateEvent $event) {
          $action = AbstractQuery::match($event->update->callbackQuery->data);

          if ($action) {
              $action = new $action();
              $action->handle($event);
              return null;
          }

          return $event->telegram->answerCallbackQuery([
              'callback_query_id' => $event->update->callbackQuery->id,
              'text' => 'Unfortunately, there is no matched action to respond to this callback',
          ]);
      });

        $update = Telegram::commandsHandler(true);
        $message = $update->getMessage();
        $text = $message->getText();
        $from = $message->getFrom();
        $id = $from->getId();


        if (!$update->callback_query && $id) {
            Client::updateOrCreate([
                'id' => $id,
                'username' => $from->getUsername(),
                'is_bot' => $from->getIs_bot(),
                'first_name' => $from->getFirst_name(),
                'last_name' => $from->getLast_name(),
                'language_code' => $from->getLanguage_code(),
              ],
              ['id' => $id]
            );

            //$order = Order::where('client_id', $id)->first();
            if (is_numeric($text)) {
              Telegram::sendMessage([
                  'chat_id' => $id,
                  'text' => 'Выберите способ оплаты',
                  'reply_markup' => $this->buildKeyboard($text),
              ]);
            } else if ($text[0] != '/') {
              Telegram::sendMessage([
                  'chat_id' => $id,
                  'text' => 'Введите число!',
              ]);
            }
        }
    }

    /**
     * @throws JsonException
     */
    private function buildKeyboard($sum): false|string
    {
        return json_encode([
            'inline_keyboard' => [
                [
                    ['text' => 'СБП', 'callback_data' => 'type sbp ' . $sum],
                    ['text' => 'Карта', 'callback_data' => 'type card ' . $sum],
                    ['text' => 'USDT', 'callback_data' => 'type trc20 ' . $sum],
                ],
            ]
        ], JSON_THROW_ON_ERROR);
    }

    /*
    public function setwebhook($url)
    {
      echo $url;
      if (!$url) {
          echo 'no url';
          return;
      }
      $response = Telegram::setWebhook([
          'url' => 'https://' . $url . '/' . env('TELEGRAM_BOT_TOKEN') . '/webhook',
      ]);
      return $response;
    }
    */
}
