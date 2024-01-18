<?php

namespace App\Telegram\Commands;

use JsonException;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    protected string $name = 'start';

    /**
     * @inheritDoc
     */
    public function handle(): void
    {
      $keyboard = [
          [
            ['text' => 'Купить со скидкой', 'callback_data' => 'terms']
          ]
      ];

      $reply_markup = Keyboard::make([
          'inline_keyboard' => $keyboard,
          'resize_keyboard' => true,
          'one_time_keyboard' => true
      ]);

        $this->replyWithMessage([
            'text' => 'Привет!',
            'reply_markup' => $reply_markup
            //'reply_markup' => $this->buildKeyboard(),
        ]);
    }
}
