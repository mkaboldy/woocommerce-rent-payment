<?php
/**
 * Implements Rent Payment API operations
 */
class RentPayment_API {
    private $api_url;
    private $api_username;
    private $api_password;
    private $property_code;

    /**
     * Constructor, init private vars
     * @param mixed $api_url
     * @param mixed $api_username
     * @param mixed $api_password
     * @param mixed $property_code
     */
    public function __construct($api_url='',$api_username='',$api_password='',$property_code='') {
        $this->api_url = $api_url;
        $this->api_username = $api_username;
        $this->api_password = $api_password;
        $this->property_code = $property_code;
    }

	/**
	 * Charge a credit card specified in the param sructure
	 * @param rentpayment_CC_params $params
	 * @throws Exception
	 * @return SimpleXMLElement
	 */
	public function CreditCardPayment(rentpayment_CC_params $params){

		$username       = $this->api_username;
		$password       = $this->api_password;
		$propertycode   = $this->property_code;
		$number			= $params->number;
		$expiration		= $params->expiration;
		$cardholder		= $params->cardholder;
		$type			= $params->type;
		$street			= $params->street;
		$city			= $params->city;
		$state			= $params->state;
		$zip			= $params->zip;
		$country		= $params->country;
		$phone			= $params->phone;
		$email			= $params->email;
		$amount			= $params->amount;
		$id             = $params->id;
		$firstname	    = $params->firstname;
		$lastname		= $params->lastname;

		$requestdata =
"<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<CreditCardPayment>
	<username>$username</username>
	<password>$password</password>
	<propertyCode>$propertycode</propertyCode>
	<number>$number</number>
	<expiration>$expiration</expiration>
	<cardholder>$cardholder</cardholder>
	<type>$type</type>
	<street>$street</street>
	<city>$city</city>
	<state>$state</state>
	<zip>$zip</zip>
	<country>$country</country>
	<phone>$phone</phone>
	<email>$email</email>
	<amount>$amount</amount>
	<id>$id</id>
	<personFirstname>$firstname</personFirstname>
	<personLastname>$lastname</personLastname>
	<ReturnToken>true</ReturnToken>
</CreditCardPayment>";

        self::log(__FUNCTION__,$requestdata);

        // mask CC before logging

        $logrequest = json_decode(json_encode(simplexml_load_string($requestdata)),TRUE);
        $logrequest['number'] = 'XXXX-XXXX-XXXX-'.substr($logrequest['number'],-4);

        // self::log(__FUNCTION__,$logrequest);

		$curl_request = curl_init($this->api_url);

		curl_setopt($curl_request, CURLOPT_POST, 1);
		curl_setopt($curl_request, CURLOPT_POSTFIELDS, array('xml'=>$requestdata));
		curl_setopt($curl_request, CURLOPT_HEADER, 0);
		curl_setopt($curl_request, CURLOPT_TIMEOUT, 45);
		curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($curl_request);

		if ($response === false) {
            $curl_error = curl_error($curl_request);
            self::log(__FUNCTION__,$curl_error);
			throw new Exception($curl_error);
		}

        self::log(__FUNCTION__,$response);

        $xml_response = simplexml_load_string($response);

        if ($xml_response->getName() !== 'CreditCardPaymentResponse'){
            throw new Exception("Payment processing error: " . $xml_response->description);
        }

		return $xml_response; // return an xml object

	}

    /**
     * Local logging function
     * @param mixed $type
     * @param mixed $what
     */
    private function log($type,$what) {
		file_put_contents(dirname(WC_RENTPAYMENT_MAIN_FILE).'/logs/rent-payment/'.$type.'-'.date('Y-m-d h.i.s').'.log',
			print_r($what, true) . "\n",
			FILE_APPEND | LOCK_EX);
    }
}

/**
 * Object structure for CC payment params
 */
class rentpayment_CC_params {
	/**
	 * CC Number or token
	 *
	 * @var string
	 */
    public $number;
	/**
	 * Expiration date expected format mm-yyyy
	 *
	 * @var string
	 */
	public $expiration;
	/**
	 * Name on card
	 *
	 * @var string
	 */
	public $cardholder;
	/**
     * Card type (Visa/MasterCard/Discover/Amex)
     *
	 * @var string
	 */
	public $type;
	/**
	 * Billing street address
	 *
	 * @var string
	 */
	public $street;
	/**
	 * Billing city
	 *
	 * @var string
	 */
	public $city;
	/**
	 * Billing state, 2 letters state code
	 *
	 * @var string
	 */
	public $state;
	/**
	 * Billing ZIP
	 *
	 * @var string
	 */
	public $zip;
	/**
	 * Billing country, 2 letters country code
	 *
	 * @var string
	 */
	public $country;
	/**
	 * Billing phone, max 10 char no spaces
	 *
	 * @var string
	 */
	public $phone;
	/**
	 * Billing email
	 *
	 * @var string
	 */
	public $email;
	/**
	 * Charged amount in cents = round($amount,2)*100;
	 *
	 * @var int
	 */
	public $amount;
	/**
	 * Unique payment id
	 *
	 * @var string
	 */
	public $id;
	/**
	 * Customer first name
	 *
	 * @var string
	 */
	public $firstname;
	/**
     * Customer last name
     *
     * @var string
     */
	public $lastname;
    private $valid_cardtypes = array(
        'visa'          => 'Visa',
        'mastercard'    => 'MasterCard',
        'discover'      => 'Discover',
        'amex'          => 'AmEx'
        );
    /**
     * Translate the card type to an API acceptable card type name
     * @param mixed $cardtype_key
     * @return mixed
     */
    public function get_cardtype_name($cardtype_key) {
        if (isset($this->valid_cardtypes[$cardtype_key])) {
            return $this->valid_cardtypes[$cardtype_key];
        } else {
            return false;
        }
    }
}
?>