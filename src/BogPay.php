<?php

namespace Devadze\BogPay;

use Devadze\BogPay\Models\BogPayTransaction;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class BogPay
{
    protected $token = null;
    protected $units = [];
    protected $model_id = null;
    protected $model_type = '';

    public function __construct()
    {
        $token = $this->token();
    }

    public function token(){
        $clientId = config('bogpay.client_id');
        $secretKey = config('bogpay.client_secret');
        $url = 'https://oauth2.bog.ge/auth/realms/bog/protocol/openid-connect/token';
        $authorization = 'Basic ' . base64_encode($clientId . ':' . $secretKey);
        $request = $this->postRequest($url, [
            'grant_type' => 'client_credentials',
        ], $authorization);
        $this->token = $request->access_token;
        return $this->token;
    }

    public function purchaseUnit(float $amount, string $currency = 'GEL',array $basket = [])
    {
        $this->units = [
            'currency_code' => $currency,
            'total_amount' => $amount,
            'basket' => $basket
        ];
        return $this;
    }

    public function for(Model $model = null)
    {
        $this->model_id = $model ? data_get($model, 'id') : null;
        $this->model_type = $model ? get_class($model) : null;
        return $this;
    }

    public function order()
    {
        $url = 'https://api.bog.ge/payments/v1/ecommerce/orders';
        $request = $this->postRequest($url, [
            'callback_url' => config('bogpay.callback_url'),
            'purchase_units' => $this->units,
            'payment_method' => [
                'card',
//                'bog_p2p'
            ]
        ], null, 'json');
        $link = collect($request->_links)['redirect']->href;
        $this->logTransactionCreate([
            'amount' => $this->units['total_amount'],
            'order_id' => $request->id
        ]);
        return redirect()->to($link);
    }

    public function callbackPayment(Model $model,$data = [])
    {
        $model->update([
            'status' => $data->order_status->key
        ]);
        return $model->model->id;
    }

    public function orderDetail(string $orderId)
    {
        $url = 'https://api.bog.ge/payments/v1/receipt/'.$orderId;
        return $this->getRequest($url);
//        return $this->callbackPayment($data);
    }

    protected function postRequest($url, array $data, string $authorization = null, string $type = 'form_params'): ?\stdClass
    {
        if (!$authorization) {
            $token = $this->requestToken($this->token);
        }

        $client = new Client();
        try {
            $params = $type === 'json' ? ['json' => $data] : ['form_params' => $data];
            $response = $client->post($url, array_merge($params, [
                'headers' => [
                    'Authorization' => $authorization ?: 'Bearer ' . $this->token,
                ],
            ]));
        } catch (ClientException $exception) {
            $error = json_decode($exception->getResponse()->getBody());

            if (!isset($error->error_code)) {
                return $error;
            }

            abort($error->error_code, isset($error->error_message) ? $error->error_message : '');
        }

        return json_decode($response->getBody());
    }

    protected function getRequest(string $url)
    {
        $token = $this->requestToken($this->token);
        $client = new Client();
        try {
            $response = $client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);
        } catch (ClientException $exception) {
            $error = json_decode($exception->getResponse()->getBody());

            if (!isset($error->error_code)) {
                return $error;
            }

            abort($error->error_code, isset($error->error_message) ? $error->error_message : '');
        }

        return json_decode($response->getBody());
    }


    protected function requestToken(string $token = null): string
    {
        if (!$token) {
            $request = self::token();

            if (isset($request->access_token)) {
                return $request->access_token;
            }
        }

        return $token;
    }

    public function logTransactionCreate(array $data)
    {
        BogPayTransaction::create([
            'locale' => 'ka',
            'model_id' => $this->model_id,
            'model_type' => $this->model_type,
            'amount' => $data['amount'],
            'order_id' => $data['order_id']
        ]);
    }
}
