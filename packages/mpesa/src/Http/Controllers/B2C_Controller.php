<?php

namespace Montanabay39\Mpesa\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Montanabay39\Mpesa\Models\MpesaTransaction;

class B2C_Controller extends Controller
{
    /**
     * Provide for timestamp or live api transactions
     * @var string $timestamp
     */
    private $timestamp;

    /**
     * The Callback common part of the URL eg "https://domain.com/callbacks/"
     * @var string $callbackURL
     */
    private $callbackURL;

    /**
     * Provide environment for sandbox or live api transactions
     * @var string $environment
     */
    private $environment;

    /**
     * Provides common endpoint for transaction, depending on the environment.
     * @var string $baseURL
     */
    private $baseURL;

    /**
     * The consumer key
     * @var string $consumerKey
     */
    private $consumerKey;

    /**
     * The consumer key secret
     * @var string $consumerSecret
     */
    private $consumerSecret;

    /**
     * The MPesa B2C Paybill number
     * @var int $shortCode
     */
    private $shortCode;

    /**
     * The Mpesa portal Username
     * @var string $initiatorUsername
     */
    private $initiatorUsername;

    /**
     * The Mpesa portal Password
     * @var string $initiatorPassword
     */
    private $initiatorPassword;

    /**
     * The signed API credentials
     * @var string $cred
     */
    private $credentials;

    /**
     * Construct method
     *
     * Initializes the class with an array of API values.
     *
     * @param array $config
     * @return void
     * @throws exception if the values array is not valid
     */
    public function __construct()
    {
        $this->timestamp         = Carbon::now()->format('YmdHis');
        $this->callbackURL       = config('app.url');
        $this->environment       = config('mpesa.b2c.environment');
        $this->baseURL           = 'https://' . ($this->environment == 'production' ? 'api' : 'sandbox') . '.safaricom.co';
        $this->consumerKey       = config('mpesa.b2c.consumery');
        $this->consumerSecret    = config('mpesa.b2c.consumer.secret');
        $this->shortCode         = config('mpesa.b2c.shortcode');
        $this->initiatorUsername = config('mpesa.b2c.initiator.username');
        $this->initiatorPassword = config('mpesa.b2c.initiator.password');
        $this->certificate       = File::get(public_path() . '/vendor/mpesa/certificates/' . $this->environment . '.cer');
        openssl_public_encrypt($this->initiatorPassword, $output, $this->certificate, OPENSSL_PKCS1_PADDING);
        $this->credentials       = base64_encode($output);
    }

    /**
     * Business to Client
     *
     * This method is used to send money to the clients Mpesa account.
     *
     * @param int $amount The amount to send to the client
     * @param int $phone The phone number of the client in the format 2547xxxxxxxx
     * @return object Curl Response from submit method, FALSE on failure
     */
    public function transaction(Request $request)
    {
        $data = json_encode([
            'InitiatorName'      => $this->initiatorUsername,
            'SecurityCredential' => $this->credentials,
            'CommandID'          => 'BusinessPayment',
            'Amount'             => $request->amount,
            'PartyA'             => $this->shortCode,
            'PartyB'             => '254' . substr($request->phoneNumber, -9), // supports translations in KENYA only!!
            'Remarks'            => 'B2C Transaction Simulation: ' . $request->reference,
            'QueueTimeOutURL'    => route('mpesa.b2c.callback'),
            'ResultURL'          => route('mpesa.b2c.callback'),
            'Occasion'           => null //Optional
        ]);

        $endpoint = $this->baseURL . '/mpesa/b2c/v1/paymentrequest';
        $response = $this->submit($endpoint, $data);

        try {
            // save transaction details if response is valid
            if (isset($response->ResponseCode) && $response->ResponseCode == 0) {
                $transaction = MpesaTransaction::create([
                    'partyA'               => json_decode($data)->PartyA,
                    'partyB'               => json_decode($data)->PartyB,
                    'transactionType'      => 'B2C',
                    'transactionAmount'    => json_decode($data)->Amount,
                    'transactionCode'      => NULL,
                    'transactionTimeStamp' => $this->timestamp,
                    'transactionDetails'   => json_decode($data)->Remarks,
                    'transactionId'        => $response->ConversationID,
                    'accountReference'     => $request->reference,
                    'responseFeedBack'     => json_encode(['transaction' => $response->all()])
                ]);

                return response()->json($transaction);
            }
        } catch (\Throwable $th) {
            // throw $th;
            Log::info('B2C TRANSACTION');
            Log::info(print_r($th->getMessage()));
        }
    }

    /**
     * B2C Callback
     *
     * This method is used to confirm a B2C Transaction that has passed various methods set by the developer during validation
     *
     * @param array $request from mpesa api
     * @return json response for payment details i.e transaction code and timestamps e.t.c
     */
    public function callback(Request $request)
    {
        try {
            // find transaction via ThirdPartyTransID as the unique transaction Id.
            $transaction = MpesaTransaction::where(['transactionId' => $request['Result']['ConversationID']])->firstOrFail();
            // update transaction status
            $transaction->update([
                'transactionCode' => $request['Result']['TransactionID'],
                '_status'         => MpesaTransaction::ACCEPTED
            ]);
            // response to safaricom:
            return true;
        } catch (\Throwable $th) {
            // throw $th;
            Log::info('B2C CALLBACK');
            Log::info(print_r($th->getMessage()));
        }
    }

    /**
     * Transaction status request
     *
     * This method is used to check a transaction status
     *
     * @param string $tCode eg LH7819VXPE
     * @return object Curl Response from submit method, false on failure
     */
    public function status(Request $request)
    {
        $data = json_encode([
            'CommandID'          => 'TransactionStatusQuery',
            'PartyA'             => $this->shortCode,
            'IdentifierType'     => 4,
            'Remarks'            => $request->transactionCode . ' Transaction Status Query',
            'Initiator'          => $this->initiatorUsername,
            'SecurityCredential' => $this->credentials,
            'QueueTimeOutURL'    => route('mpesa.b2c.status.callback'),
            'ResultURL'          => route('mpesa.b2c.status.callback'),
            'TransactionID'      => $request->transactionCode,
            'Occasion'           => $request->transactionCode . ' Transaction Status Query'
        ]);

        $endpoint = $this->baseURL . '/mpesa/transactionstatus/v1/query';
        $response = $this->submit($endpoint, $data);

        return $response;
    }

    public function statusCallback(Request $request)
    {
        Log::info('B2C STATUS CALLBACK');
        Log::info(print_r($request->all(), true));

        return;
    }

    /**
     * Transaction Reversal
     *
     * This method is used to reverse a transaction
     *
     * @param int $receiver Phone number in the format 2547xxxxxxxx
     * @param string $trx_id Transaction ID of the Transaction you want to reverse eg LH7819VXPE
     * @param int $amount The amount from the transaction to reverse
     * @return object Curl Response from submit method, false on failure
     */
    public function reverse(Request $request)
    {
        $data = json_encode([
            'Initiator'              => $this->initiatorUsername,
            'SecurityCredential'     => $this->credentials,
            'CommandID'              => 'TransactionReversal',
            'TransactionID'          => $request->transactionCode,
            'Amount'                 => $request->amount,
            'ReceiverParty'          => '254' . substr($request->phoneNumber, -9), // supports translations in KENYA only!!
            'RecieverIdentifierType' => 1, // [1 => 'MSISDN', 2 => 'Till_Number', 4 => 'Shortcode']
            'ResultURL'              => route('b2c.reverse.transaction.callback'),
            'QueueTimeOutURL'        => route('b2c.reverse.transaction.callback'),
            'Remarks'                => $request->tCode . ' Transaction Reversal',
            'Occasion'               => $request->tCode . ' Transaction Reversal'
        ]);

        $endpoint = $this->baseURL . '/mpesa/reversal/v1/request';
        $response = $this->submit($endpoint, $data);

        return $response;
    }

    public function reverseCallback(Request $request)
    {
        Log::info('B2C REVERSE CALLBACK');
        Log::info(print_r($request->all(), true));

        return;
    }

        /**
     * Check Balance
     *
     * Check B2C balance
     *
     * @return object Curl Response from submit method, false on failure
     */
    public function balance()
    {
        $data = json_encode([
            'CommandID'          => 'AccountBalance',
            'PartyA'             => $this->shortCode,
            'IdentifierType'     => 4,
            'Remarks'            => 'Checking Account Balance for ' . $this->shortCode,
            'Initiator'          => $this->initiatorUsername,
            'SecurityCredential' => $this->credentials,
            'QueueTimeOutURL'    => route('mpesa.b2c.balance.callback'),
            'ResultURL'          => route('mpesa.b2c.balance.callback')
        ]);

        $endpoint = $this->baseURL . '/mpesa/accountbalance/v1/query';
        $response = $this->submit($endpoint, $data);

        return response()->json($response);
    }

    public function balanceCallback(Request $request)
    {
        Log::info('B2C BALANCE CALLBACK');
        Log::info(print_r($request->all(), true));

        return;
    }

    /**
     * Generate Access Token
     *
     * @return object|boolean Curl response or false on failure
     * @throws exception if the Access Token is not valid
     */
    protected function generateAccessToken()
    {
        try {
            if (!Cache::has('B2C_ACCESS_TOKEN')) {
                return Cache::remember('B2C_ACCESS_TOKEN', now()->addMinutes(59), function () {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $this->baseURL . '/oauth/v1/generate?grant_type=client_credentials');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode($this->consumerKey . ':' . $this->consumerSecret), 'Content-Type: application/json'));
                    $response = curl_exec($ch);
                    curl_close($ch);

                    $response = json_decode($response);

                    if (!$response->access_token) {
                        return false;
                    } else {
                        return $response->access_token;
                    }
                });
            } else {
                return Cache::get('B2C_ACCESS_TOKEN');
            }
        } catch (\Throwable $th) {
            // throw $th;
            Log::info('B2C GENERATE ACCESS TOKEN');
            Log::info(print_r($th->getMessage()));
        }
    }

    /**
     * Submit Request
     *
     * Handles submission of all API endpoints queries
     *
     * @param string $url The API endpoint URL
     * @param json $data The data to POST to the endpoint $url
     * @return object|boolean Curl response or false on failure
     * @throws exception if the Access Token is not valid
     */
    protected function submit($url, $data)
    {
        try {
            if ($this->generateAccessToken() != '' || $this->generateAccessToken() !== false) {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $this->generateAccessToken()));

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                $response = curl_exec($curl);
                curl_close($curl);
                return json_decode($response);
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            // throw $th;
            Log::info('B2C SUBMIT');
            Log::info(print_r($th->getMessage()));
        }
    }
}
