<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class ApiController extends Controller
{

    private $gateway;


    public function __construct()
    {
        $this->enviroment = env('PAGOFACIL_ENVIROMENT', 'DESARROLLO');
    }


    public function pay(Request $request)
    {
        $this->validate($request, [
            'account_id' => 'required',
            'token_secret' => 'required',
            'amount' => 'required',
            'reference' => 'required',
            'customer_email' => 'required|email',
            'completed_url' => 'required',
            'canceled_url' => 'required'
        ]);

        $token_secret = $request->input('token_secret');
        $completed_url = urlencode($request->input('completed_url'));
        $canceled_url = urlencode($request->input('canceled_url'));

        $pagoFacil_request = new \PagoFacil\lib\Request();

        $pagoFacil_request->account_id        = $request->input('account_id');
        $pagoFacil_request->amount            = $request->input('amount');
        $pagoFacil_request->currency          = $request->input('currency', 'CLP');
        $pagoFacil_request->reference         = $request->input('reference');
        $pagoFacil_request->customer_email    = $request->input('customer_email');
        $pagoFacil_request->url_complete      = url() . "/payment_complete/{$token_secret}?completed_url={$completed_url}&canceled_url={$canceled_url}";
        $pagoFacil_request->url_cancel        = $canceled_url;
        $pagoFacil_request->url_callback      = url() . "/payment_callback/{$token_secret}?completed_url={$completed_url}&canceled_url={$canceled_url}";
        $pagoFacil_request->shop_country      = $request->input('shop_country', 'CL');
        $pagoFacil_request->session_id        = date('Ymdhis').rand(0,9).rand(0,9).rand(0,9);


        $transaction = new \PagoFacil\lib\Transaction($pagoFacil_request);
        $transaction->environment = $this->enviroment;
        $transaction->setToken( $token_secret );
        $transaction->initTransaction($pagoFacil_request);
        die();
    }


    public function payment_complete( Request $request, $token_secret ){
        $transaction = new \PagoFacil\lib\Transaction();
        $transaction->setToken( $token_secret );

        if($transaction->validate($_POST)){
            echo '
    		<body style="width:100%;height:100%">
    			<form id="roadTocommerce" method="post" action="'.urldecode($request->input('completed_url')).'">
    				<input type="hidden" name="account_id" value="'.$request->input('x_account_id').'" />
    				<input type="hidden" name="amount" value="'.$request->input('x_amount').'" />
    				<input type="hidden" name="currency" value="'.$request->input('x_currency').'" />
    				<input type="hidden" name="gateway_reference" value="'.$request->input('x_gateway_reference').'" />
    				<input type="hidden" name="reference" value="'.$request->input('x_reference').'" />
    				<input type="hidden" name="result" value="'.$request->input('x_result').'" />
    				<input type="hidden" name="timestamp" value="'.$request->input('x_timestamp').'" />
    			</form>
    			<script>
    				window.onload = function(){	document.getElementById("roadTocommerce").submit(); }
    			</script>
    		</body>';
        }else{
            return redirect(urldecode($request->input('canceled_url')));
        }
    }


    public function payment_callback( Request $request, $token_secret ){

        $transaction = new \PagoFacil\lib\Transaction();
        $transaction->setToken( $token_secret );

        if($transaction->validate($_POST)){
            echo '
    		<body style="width:100%;height:100%">
    			<form id="roadTocommerce" method="post" action="'.urldecode($request->input('completed_url')).'">
    				<input type="hidden" name="account_id" value="'.$request->input('x_account_id').'" />
    				<input type="hidden" name="amount" value="'.$request->input('x_amount').'" />
    				<input type="hidden" name="currency" value="'.$request->input('x_currency').'" />
    				<input type="hidden" name="gateway_reference" value="'.$request->input('x_gateway_reference').'" />
    				<input type="hidden" name="reference" value="'.$request->input('x_reference').'" />
    				<input type="hidden" name="result" value="'.$request->input('x_result').'" />
    				<input type="hidden" name="timestamp" value="'.$request->input('x_timestamp').'" />
    			</form>
    			<script>
    				window.onload = function(){	document.getElementById("roadTocommerce").submit(); }
    			</script>
    		</body>';
        }else{
            return redirect(urldecode($request->input('canceled_url')));
        }

    }

}
