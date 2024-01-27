## Card_bot

Telegram discount bot

## Install

composer Install

Create db [card_bot] db_user DB_PASSWORD
Setup telegram bot

Update .env



run migrations

php artisan migrate

setWebhook

## Web admin

url: /admin/

register user
env BACKPACK_REGISTRATION_OPEN=true

## ORDERS API

Authorization
Bearer token

1. regicter user post /api/register
2. create token post /api//token/add
spec in app/Http/Controllers/AuthController

ORDERS

get /api/orders/{id}
post /api/order-card
spec in app/Http/Controllers/OrderController
