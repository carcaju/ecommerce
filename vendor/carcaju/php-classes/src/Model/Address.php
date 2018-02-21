<?php

namespace carcaju\Model;

use \carcaju\DB\Sql;
use \carcaju\Model;

class Address extends Model {

	const SESSION_ERROR = "Address Error";

	public static function getCEP($nrcep) {

		$nrcep = str_replace("-","",$nrcep);

		$link = "http://viacep.com.br/ws/$nrcep/json/";

		$ch = curl_init($link);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		$response = curl_exec($ch);

		curl_close($ch);

		$data = json_decode($response, true);

		return $data;

	}


	public function loadFromCEP($nrcep) {

		$data = Address::getCEP($nrcep);

		if(isset($data['logradouro']) && $data['logradouro']) {
			$this->setdesaddress($data['logradouro']);
			$this->setdescomplement($data['complemento']);
			$this->setdesdistrict($data['bairro']);
			$this->setdescity($data['localidade']);
			$this->setdesstate($data['uf']);
			$this->setdescountry('Brasil');
			$this->setdeszipcode($nrcep);

		}


	}

	public function save() {

		$sql = new Sql();

		$results = $sql->select("CALL sp_addresses_save(:idaddress, :idperson, :desaddress, :descomplement, :descity, :desstate, :descountry, :deszipcode, :desdistrict)", [
				':idaddress'=>$this->getidaddress(),
				':idperson'=>$this->getidperson(), 
				':desaddress'=>$this->getdesaddress(),
				':descomplement'=>$this->getdescomplement(),
				':descity'=>$this->getdescity(),
				':desstate'=>$this->getdesstate(),
				':descountry'=>$this->getdescountry(),
				':deszipcode'=>$this->getdeszipcode(),
				':desdistrict'=>$this->getdesdistrict()
		]);

		if(count($results)>0) {

			$this->setData($results[0]);

		}

	}


 	public static function setMsgError($msg) {

 		$_SESSION[Address::SESSION_ERROR] = $msg;

 	}

 	public static function getMsgError() {

 		$msg = (isset($_SESSION[Address::SESSION_ERROR])) ? $_SESSION[Address::SESSION_ERROR] : "";

 		Address::clearMsgError();

 		return $msg;

 	}

 	public static function clearMsgError() {

 		$_SESSION[Address::SESSION_ERROR] = NULL;

 	}



}

?>