<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use Telegram\Bot\Laravel\Facades\Telegram;

class AuthController extends BaseController
{

    /**
    * @OA\Post(
    *     path="/api/token/add",
    *     summary="Add new access token to user",
    *     tags={"Auth"},
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *             required={"enail"},
    *             required={"password"},
    *             @OA\Property(property="email", type="string"),
    *             @OA\Property(property="password", type="string"),
    *             ),
    *             )
    *         )
    *    ),
    *     @OA\Response(
    *         response=200,
    *         description="OK",
    *         response=200,
    *         description="control response",
    *         @OA\JsonContent(
    *             type="array",
    *             @OA\Items(ref="#/components/schemas/User")
    *         ),
    *     ),
    * )
    */
    public function signin(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'email' => 'required|email',
          'password' => 'required',
          //'access' => 'required',
      ]);

      if($validator->fails()){
          return $this->sendError('Error validation', $validator->errors());
      }
      if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
          $authUser = Auth::user();
          $success['token'] =  $authUser->createToken('MyAuthApp', ['*'])->plainTextToken;
          $success['name'] =  $authUser->name;

          return $this->sendResponse($success, 'Token created');
      }
      else{
          return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
      }
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyAuthApp', ["*"])->plainTextToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User created successfully.');
    }

    /**
    * @OA\Delete(
    *     path="/api/token/delete",
    *     summary="Delete access token by id",
    *     tags={"Auth"},
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *             required={"enail"},
    *             required={"password"},
    *             required={"token_id"},
    *             @OA\Property(property="email", type="string"),
    *             @OA\Property(property="password", type="string"),
    *             @OA\Property(property="token_id", type="integer")
    *         ))
    *    ),
    *     @OA\Response(
    *         response=200,
    *         description="OK",
    *         response=200,
    *         description="control response",
    *         @OA\JsonContent(
    *             type="array",
    *             @OA\Items(ref="#/components/schemas/User")
    *         ),
    *     ),
    * )
    */
    public function delete_token(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'email' => 'required|email',
          'password' => 'required',
          'token_id' => 'required',
      ]);

      if($validator->fails()){
          return $this->sendError('Error validation', $validator->errors());
      }
      if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
          $authUser = Auth::user();
          $token = $authUser->tokens()->find($request->token_id);
          if (is_null($token)) {
              return $this->sendError('Token does not exist.');
          }
          $token->delete();
          return $this->sendResponse([], 'Token deleted');
      }
      else{
          return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
      }
    }

    public function setwebhook(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'url' => 'required',
      ]);
      if($validator->fails()){
          return $this->sendError('Error validation', $validator->errors());
      }
      $response = Telegram::setWebhook([
          'url' => 'https://' . $request->url . '/' . env('TELEGRAM_BOT_TOKEN') . '/webhook',
      ]);
      return $response;
    }


}
