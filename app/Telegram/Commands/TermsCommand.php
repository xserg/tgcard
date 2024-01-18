<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Keyboard\Keyboard;

class TermsCommand extends Command
{
    protected string $name = 'terms';
    protected string $description = 'Условия использования';

    public function handle()
    {
        $this->replyWithMessage([
            'text' => "Условия использования бла бла бла,
условия, соглашение, 1 час на использование карты
отправьте боту сообщение с той суммой  в рублях (только цифры без пробелов),
и способ оплаты: СБП, по номеру карты, USDT
sbr, card, usdt
которую вы хотели бы получить в распоряжение. в формате

/summa xxxx sbr
/summa xxxx card
/summa xxxx usdt
            ",
        ]);

    }
}
