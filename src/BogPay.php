<?php

namespace Devadze\BogPay;

use Carbon\Carbon;
use Devadze\BogPay\Models\BogPayLog;
use Devadze\BogPay\Models\BogPayTransaction;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class BogPay
{
    protected $token = null;
    protected $units = [];
    protected $currency = 'GEL';
    protected $payment_method = [];
    protected $loan_config = [];
    protected $model_id = null;
    protected $model_type = '';

    public function __construct()
    {
        $token = $this->token();
        $this->debug = config('bog-pay.debug');
    }

    public function token(){
        $clientId = config('bog-pay.client_id');
        $secretKey = config('bog-pay.client_secret');
        $url = 'https://oauth2.bog.ge/auth/realms/bog/protocol/openid-connect/token';
        $authorization = 'Basic ' . base64_encode($clientId . ':' . $secretKey);
        $request = $this->postRequest($url, [
            'grant_type' => 'client_credentials',
        ], $authorization);
        $this->token = $request['access_token'];
        return $this->token;
    }

    public function setBasket(int $quantity, float $unitPrice, string $productId)
    {
        return [
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'product_id' => $productId
        ];
    }

    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function paymentMethod(array $data = [])
    {
        $this->payment_method = $data;
        return $this;
    }

    public function loanConfig(array $data = [])
    {
        $this->loan_config = [
            'loan' => $data
        ];
        return $this;
    }

    public function purchaseUnits(float $amount = 1,array $basket = [])
    {
        $this->units = [
            'currency' => $this->currency,
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
        $url = config('bog-pay.url').'/ecommerce/orders';
        $request = $this->postRequest($url, [
            'callback_url' => config('bog-pay.callback_url'),
            'purchase_units' => $this->units,
            'redirect_urls' => [
                'fail' => url('user/dashboard'),
                'success' => url('user/dashboard'),
            ],
            'payment_method' => $this->payment_method,
            'config' => $this->loan_config
        ], null, 'json');
        return $request;
        $link = collect($request['_links'])['redirect']['href'];
        $this->logTransactionCreate([
            'amount' => $this->units['total_amount'],
            'order_id' => $request['id'],
            'status' => 'created'
        ]);
        if ($this->debug){
            BogPayLog::create([
                'order_id' => $request['id'],
                'message' => 'Starting Transaction id: '.$request['id'],
                'payload' => json_encode($request)
            ]);
        }
        return redirect()->to($link);
    }

    public function transactionMark(Model $model,$data = [])
    {
        $body = $data;
        $isPaid = 0;
        if ($data['order_status']['key'] == 'completed'){
            $isPaid = 1;
        }
        $model->update([
            'status' => $body['order_status']['key'],
            'is_paid' => $isPaid,
            'completed_at' => ($isPaid == 1) ? Carbon::now() : null
        ]);
        if ($this->debug){
            BogPayLog::create([
                'order_id' => $data['order_id'],
                'message' => 'Transaction Marked id: '.$body['order_id'],
                'payload' => json_encode($data)
            ]);
        }
        return true;
    }

    public function orderDetail(string $orderId)
    {
        $url = config('bog-pay.url').'/receipt/'.$orderId;
        return $this->getRequest($url);
    }

    protected function postRequest($url, array $data, string $authorization = null, string $type = 'form_params')
    {
//        return  $data;
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

        return json_decode($response->getBody(),true);
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

        return json_decode($response->getBody(),true);
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
            'status' => $data['status'],
            'order_id' => $data['order_id']
        ]);
    }
}
