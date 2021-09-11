<?php
namespace PhpPot\Service;

require_once 'vendor/autoload.php';

use \Stripe\Stripe;
use \Stripe\Customer;
use \Stripe\ApiOperations\Create;
use \Stripe\Charge;
use \Stripe\Subscription;

class StripePayment
{

    private $apiKey;

    private $stripeService;

    public function __construct()
    {
        require_once "config.php";
        $this->apiKey = STRIPE_SECRET_KEY;
        $this->stripeService = new \Stripe\Stripe();
        $this->stripeService->setVerifySslCerts(false);
        $this->stripeService->setApiKey($this->apiKey);
    }

    public function addCustomer($customerDetailsAry)
    {
        
        

        try {
            $customer = new Customer();
        
            $customerDetails = $customer->create($customerDetailsAry);
          } catch(\Stripe\Exception\CardException $e) {
            // Since it's a decline, \Stripe\Exception\CardException will be caught

            return ['code'=>$e->getHttpStatus(),'message'=> $e->getError()->message];
            // echo 'Status is:' . $e->getHttpStatus() . '\n';
            // echo 'Message is:' . . '\n';
          } catch (\Stripe\Exception\RateLimitException $e) {
            // Too many requests made to the API too quickly
          } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Invalid parameters were supplied to Stripe's API
          } catch (\Stripe\Exception\AuthenticationException $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
          } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Network communication with Stripe failed
          } catch (\Stripe\Exception\ApiErrorException $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
          } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe
          }

        return $customerDetails;
    }

    public function chargeAmountFromCard($cardDetails)
    {
        $customerDetailsAry = array(
            'email' => $cardDetails['email'],
            'source' => $cardDetails['token']
        );
        $customerResult = $this->addCustomer($customerDetailsAry);
        $charge = new Charge();
        $cardDetailsAry = array(
            'customer' => $customerResult->id,
            'amount' => $cardDetails['amount']*100 ,
            'currency' => $cardDetails['currency_code'],
            'description' => $cardDetails['item_name'],
            'metadata' => array(
                'order_id' => $cardDetails['item_number']
            )
        );
        $result = $charge->create($cardDetailsAry);

        return $result->jsonSerialize();
    }
    public function SubscribePlanFromCard($cardDetails)
    {
        $customerDetailsAry = array(
            'email' => $cardDetails['email'],
            'source' => $cardDetails['token']
        );
        $customerResult = $this->addCustomer($customerDetailsAry);

        if( $customerResult['code'] == 402){
            return  $customerResult;
        }
        
        $Subscription = new Subscription();
        $cardDetailsAry = array(
            'customer' => $customerResult->id,
            "items" => array( 
                        array( 
                            "plan" => $cardDetails['plan'], 
                        ), 
                    ),
            'metadata' => array(
                'order_id' => $cardDetails['item_number']
            )
        );
        $result = $Subscription->create($cardDetailsAry);

        return $result->jsonSerialize();
    }
}
