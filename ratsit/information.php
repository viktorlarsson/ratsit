<?php
/**
 *  This class allows the user to easily consume Ratsit web services
 *
 *
 *  More information here:
 * 	http://www.ratsit.se/Content/API_Webservice.aspx
 *	Made by We made you look (http://wemadeyoulook.at/en/
 */
abstract class Ratsit {

	/**
	 * @var string The url to the APi
	 */
	private static $api_url = "https://www.ratsit.se:7443/ratsvc/apipackageservice.asmx/%s";

	/**
	 * @var string Current Ratsit lib version
	 */
	private static $version        	= '0.1';

	/**
	 * If the result should be returned in JSON
	 *
	 * @var bool
	 */
	private static $asJson       	= false;

	/**
	 * @var null The API token
	 *
	 * Contact Ratsit if you need to generate a new one
	 * http://www.ratsit.se/Content/API_Webservice.aspx
	 */
	private static $api_token 		= null;

	/**
	 * Packages that you have access to. Set through
	 * ->setPackageSmall1();
	 * ->setPackageSmall2();
	 * ->setPackageSmall3();
	 * @var array
	 */
	private static $packages 		= array();

	/**
	 * Sets the API token either from constant (RATSIT_API_TOKEN) or directly from the args (data["token"])
	 *
	 * @param $data
	 * @throws Exception
	 *
	 */
	private static function _set_api_token($data) {
		if(array_key_exists('token',$data)) self::$api_token = $data['token'];
		if((count($data) == 0 || is_null(self::$api_token))&& defined('RATSIT_API_TOKEN')) {
			self::$api_token = RATSIT_API_TOKEN;
		}

		if(!isset(self::$api_token)) throw new Exception('API Token must be set.');
	}

	/**
	 * Sets the specific demanded data required for fetching data from Ratsit.
	 *
	 * @param $data
	 * @return mixed
	 */
	private static function _set_call($data) {

		$data['token'] = self::$api_token;
		$data['packages'] = implode(",", self::$packages);

		if(self::_isPerson($data[0])) {
			$data['pnr'] = "19" . self::_filter($data[0]);
			$data['service'] = 'GetPersonInformationPackage';
		} else {
			$data['orgnr'] = self::_filter($data[0]);
			$data['service'] = 'GetCompanyInformationPackage';
		}
		unset($data[0]);

		return $data;
	}

	/**
	 * Calls the API
	 *
	 * @param $data
	 * @return string
	 */
	private static function _call_api($data) {

		self::_set_api_token($data);
		$data = self::_set_call($data);

		$parsed_url = sprintf(self::$api_url, $data['service']);
		unset($data['service']);

		$ch = curl_init($parsed_url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);

		$result = curl_exec($ch);

		return self::_parse_response($result);

		return utf8_decode($result);
	}

	/**
	 * Parses the response from the API. Use ->asJson() if you want JSON. Will default to stdClass();
	 *
	 * @param $result
	 * @return mixed|string
	 */
	private static function _parse_response($result) {

		$information = new stdClass();

		$data = simplexml_load_string($result);


		// Company
		if($data->CompanyInformationPackage) {
			$information->companyName  = (string)$data->CompanyInformationPackage->JuridicialPerson->CorporateName;
			$information->Street = (string)$data->CompanyInformationPackage->Seat->Street;
			$information->ZipCode = (string)$data->CompanyInformationPackage->Seat->ZipCode;
			$information->City = (string)$data->CompanyInformationPackage->Seat->City;
		}

		if($data->PersonInformationPackage) {
			$information->firstName = (string)$data->PersonInformationPackage->PersonInformation->GivenName;
			$information->lastName = (string)$data->PersonInformationPackage->PersonInformation->SurName;

			$information->Street = (string)$data->PersonInformationPackage->NationalRegistration->Street;
			$information->ZipCode =  (string)$data->PersonInformationPackage->NationalRegistration->ZipCode;
			$information->City = (string)$data->PersonInformationPackage->NationalRegistration->City;
		}

		if(self::$asJson)
			return json_encode($information);
		else
			return self::_utf8_decode_object($information);
	}

	/**
	 * Converts UTF8 to ISO
	 *
	 * @param $object
	 * @return mixed
	 */
	private static function _utf8_decode_object($object) {
		foreach ($object as $key => $value) {
			$value = utf8_decode($value);
			$object->$key = $value;
		}
		return $object;
	}

	/**
	 * Checks if the input number is a person
	 *
	 * @param $pnum
	 * @return bool
	 */
	private static function _isPerson($pnum) {
		if($pnum[2] < 2)
			return true;
		else
			return false;
	}

	/**
	 * Removes unwanted data like - from the number
	 *
	 * @param $number
	 * @return mixed|string
	 *
	 */
	private static function _filter($number) {
		$pnum = preg_replace('/[^0-9]/', '', $number);
		if(strlen($pnum) > 10 && $pnum[0] <= 2) {
			$pnum = substr($pnum, 2);
		}
		return $pnum;
	}

	/**
	 * Rather than defining each method individually we use this method to route the
	 * method call to the appropriate handler. This method should not be used directly.
	 *
	 * @param string $method Method user attempted to use
	 * @param mixed $args Array of arguments the user passed to the method
	 */
	public static function __callStatic($method, $args) {
		switch($method) {
			case 'asJson':
				self::$asJson = true;
				break;
			case 'setPackageSmall1':
				self::$packages[] = 'Small 1';
				break;
			case 'setPackageSmall2':
				self::$packages[] = 'Small 2';
				break;
			case 'setPackageSmall3':
				self::$packages[] = 'Small 3';
				break;
			case 'setPackageCreditNotice':
				self::$packages[] = 'Anmarkning';
				break;
			case 'setPackageMedium':
				self::$packages[] = 'Medium';
				break;
			case 'setPackageLarge':
				self::$packages[] = 'Large';
				break;
			case 'getApiServices':
				return self::api_services();
				break;
			case 'getVersion':
				return self::$version;
				break;
			case 'getInformation':
				if(count(self::$packages) <0) throw new Exception('Must set a minimum of one packages through setPackage. Ratsit::setPackagesSmall1();,  Ratsit::setPackagesSmall2();,  Ratsit::setPackagesSmall3();');
				if(empty($args)) throw new Exception('You must pass a valid SSN or organization number ');
				return self::_call_api($args);
		}
	}
}
