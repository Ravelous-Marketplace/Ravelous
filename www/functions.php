<?php 
require("inc/GoogleAuthenticator.php");
require("inc/SwiftMailer/lib/swift_required.php");

class Ravelous {
	protected $dbhost = "secret";
	protected $dbuser = "secret";
	protected $dbpassword = "secret";
	protected $dbname = "secret";

	protected $email_username = "secret";
	protected $email_password = "secret";

	protected $captchasecret = "secret";

	protected $salt = "secret";

	public $rave_eth = 2000;


	public function getRavelousDB() {
		$db = new PDO('mysql:host='.$this->dbhost.';dbname='.$this->dbname, $this->dbuser, $this->dbpassword);
		return $db;
	}

	public function loginUser($email, $password, $code) {
		$ravelous_google_authenticator = new RavelousGoogleAuthenticator();

		//verify data
		if ($this->validateInfo($email, $password) !== "ERR_OK") {
			return "ERR_INVALID_INFO";
		}

		$db = $this->getRavelousDB();
		$sql = "SELECT email, password, id FROM users WHERE email = :email";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":email", $email);
		$stmt->execute();

		$data = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
		$email_db = key($data);
		$password_db = @$data[$email][0]["password"];
		$id = @$data[$email][0]["id"];
		$authenticator = @$data[$email][0]["authenticator"];

		if (!empty($authenticator)) {
			if ($ravelous_google_authenticator->verifyGoogleAuthenticator($authenticator, $code) == "ERR_2FA") {
				return "ERR_2FA";
			}
		}

		$password = $this->hashPassword($password);

		if ($password_db == $password and $email_db == $email) {
			if ($this->checkConfirmed($id) == "ERR_UNCONFIRMED") {
				return "ERR_UNCONFIRMED";
			} else {
				//login succesful
				return $id;
			}
		} else {
			return "ERR_LOGIN_INCORRECT";
		}
	}

	public function verifyCaptcha($captcha) {

		$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->captchasecret."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
		$obj = json_decode($response);


		if($obj->success == false) {
			return "ERR_CAPTCHA";
		} else {
			return "ERR_OK";
		}

	}

	public function validateInfo($email, $password) {
		if (empty($email) or empty($password)) {
			return "ERR_EMPTY_VALUES";
		}

		if (strlen($password) < 8 or !preg_match("#[0-9]+#", $password) or !preg_match("#[a-zA-Z]+#", $password)) {
			return "ERR_PASS_WEAK";
		} 

		if (preg_match('/\s/',$email) or strlen($email) < 5 or strlen($email) > 320 or !strpos($email, "@") or !strpos($email, ".")) {
			return "ERR_EMAIL_INVALID";
		}

		return "ERR_OK";

	}

	public function checkIfAlreadyExists($email) {
		$db = $this->getRavelousDB();
		$sql = "SELECT email FROM users WHERE email = :email";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":email", $email);
		$stmt->execute();

		$email = key(array_map('reset', $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC)));

		if (!empty($email)) {
			return "ERR_EXISTS";
		} else {
			return "ERR_OK";
		}
	}

	public function getNewVerificationString() {
		return bin2hex(openssl_random_pseudo_bytes(64));
	}

	public function hashPassword($password) {
		return sha1($this->salt.$password);
	}

	public function getAccountValues($id) {
		$db = $this->getRavelousDB();
		$sql = "SELECT * FROM users WHERE id = :id";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":id", $id);
		$stmt->execute();

		//flatten user array
		return call_user_func_array('array_merge', array_map('reset', $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC)));
	}

	public function confirmAccount($key) {
		$db = $this->getRavelousDB();
		$sql = "SELECT confirmed FROM users WHERE confirmed = :confirmed";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':confirmed', $key);
		$stmt->execute();

		$key = key(array_map('reset', $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC)));
		if (empty($key)) {
			return "ERR_KEY_WRONG";
		} else {
			$sql = "UPDATE users SET confirmed = 1 WHERE confirmed = :confirmed";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':confirmed', $key);
			$stmt->execute();

			return "ERR_CONFIRM_OK";
		}
	}

	public function checkConfirmed($id) {
		$db = $this->getRavelousDB();
		$sql = "SELECT confirmed FROM users WHERE id = :id";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":id", $id);
		$stmt->execute();

		$confirmed = key(array_map('reset', $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC)));
		if ($confirmed == 1) {
			return "ERR_OK";
		} else {
			return "ERR_UNCONFIRMED";
		}

	}

	public function sendEmail($to, $subject, $body) {

		try {
					//sends email to user about signup
					$title = "Ravelous_Email";
				    $transport = Swift_SmtpTransport::newInstance(gethostbyname("mail.gandi.net"), 465, "ssl") 
						->setUsername($this->email_username)
						->setPassword($this->email_password)
						->setSourceIp("0.0.0.0");
					$mailer = Swift_Mailer::newInstance($transport);
					$logger = new \Swift_Plugins_Loggers_ArrayLogger();
					$mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));
					$message = Swift_Message::newInstance("$title");
					$message 
						->setSubject($subject)
						->setFrom(array("bot@lacicloud.net" => "Ravelous"))
						->setTo(array("$to"))
						->setCharset('utf-8') 
						->setBody($body, 'text/html');
					$mailer->send($message, $errors);
					$result = "ERR_OK";
		} catch(\Swift_TransportException $e){
			        $response = $e->getMessage();
			        $result = "ERR_EMAIL_ERROR";
		} catch (Exception $e) {
			    	$response = $e->getMessage();
			    	$result = "ERR_EMAIL_ERROR";
		}

		return $result;

	}

	public function getNewVerificationEmailString() {
		return bin2hex(openssl_random_pseudo_bytes(64));
	}

	public function registerUser($email, $username, $password, $captcha) {
			$ravelous_crypto = new RavelousCrypto();

			if($this->verifyCaptcha($captcha) == "ERR_CAPTCHA") {
				return "ERR_CAPTCHA";
			} else {
				$db = $this->getRavelousDB();

				if ($this->validateInfo($email, $password) !== "ERR_OK") {
					return "ERR_INVALID_INFO";
				}

				//verify whether email already exists in DB
				if ($this->checkIfAlreadyExists($email) !== "ERR_OK") {
					return "ERR_EXISTS";
				}

				$password = $this->hashPassword($password);
				$verification_email = $this->getNewVerificationEmailString();


				$this->sendEmail($email, "Ravelous - Confirm Account",  "<html><body><p>Hi there!</p><p>To confirm your account, please click <a href='https://ravelous.lacicloud.net/confirm/?key=".$verification_email."'>here</a>.</p><p>Thanks!</p></body></html>");

				$authenticator = '';
				$confirmed = '';
				$internal_addresses = $ravelous_crypto->getInternalAddresses();

				$sql = "INSERT INTO users (username, email, password, authenticator, confirmed, internal_addresses) VALUES (:username, :email, :password, :authenticator, :confirmed, :internal_addresses)";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(':username', $username);
				$stmt->bindParam(':email', $email);
				$stmt->bindParam(':password', $password);
				$stmt->bindParam(':authenticator', $authenticator);
				$stmt->bindParam(':confirmed', $verification_email);
				$stmt->bindParam(':internal_addresses', $internal_addresses);
				$stmt->execute();

				return "ERR_REGISTER_OK";

			}

	}

}

class RavelousCrypto extends Ravelous {

	public function generateBitcoin() {
		$result = trim(shell_exec("python2 inc/bitcoin-wallet-generator.py"));
		return $result;
	}

	public function generateEthereum() {
		$result = trim(shell_exec("python3 inc/ethereum-wallet-generator.py"));
		return $result;
	}

	public function getInternalAddresses() {
		$bitcoin = $this->generateBitcoin();
		$ethereum = $this->generateEthereum();
		$internal_addresses = base64_encode(serialize($bitcoin)).":".base64_encode(serialize($ethereum));
		return $internal_addresses;
	}

	public function getCryptoValues($id) {
		
		$ravelous = new Ravelous();
		$account = $ravelous->getAccountValues($id);
		$addresses = $account["internal_addresses"];
	
		$addresses = explode(":", $addresses);

		$bitcoin = $addresses[0];
		$ethereum = $addresses[1];

		$bitcoin = explode(":", unserialize(base64_decode($bitcoin)));
		$ethereum = explode(":", unserialize(base64_decode($ethereum)));

		return array($bitcoin, $ethereum);

	}

	public function displayCryptos($id) {
		$data = $this->getCryptoValues($id);
		$bitcoin = $data[0];
		$ethereum = $data[1];

		echo "Bitcoin deposit address: ".$bitcoin[2]." ";
		echo "<br>";
		echo "Ethereum/Rave deposit address: ".$ethereum[2]." ";

	}

	public function getAddressBalance($type, $address) {

		if ($type == "bitcoin") {
			$curl = curl_init();
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => 'https://blockchain.info/q/addressbalance/'.$address,
			));
			$balance = (int)curl_exec($curl);
			curl_close($curl);

			$balance = $balance / 100000000;
			return $balance;
		} elseif ($type == "ethereum") {
			$curl = curl_init();
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => 'https://api.etherscan.io/api?module=account&action=balance&address='.$address,
			));
			$balance = (int)curl_exec($curl);
			curl_close($curl);

			$balance = $balance / 1000000000000000000;
			return $balance;
		} elseif ($type == "rave") {
			//not yet implemented
			return 0;
		}

	}

	public function getInternalBalances($id) {
		$data = $this->getCryptoValues($id);

		$bitcoin = $data[0];
		$ethereum = $data[1];

		$bitcoin_balance = $bitcoin[3];
		$ethereum_balance = $ethereum[3];

		$bitcoin_balance = $bitcoin_balance + $this->getAddressBalance("bitcoin", $bitcoin[2]);
		$ethereum_balance = $ethereum_balance + $this->getAddressBalance("ethereum", $ethereum[2]);

		return array($bitcoin_balance, $ethereum_balance, 0);

	}

	public function changeBalance($id, $amount, $type) {
		$ravelous = new Ravelous();
		$db = $ravelous->getRavelousDB();

		$data = $this->getCryptoValues($id);

		$bitcoin = $data[0];
		$ethereum = $data[1];

		if ($type == "bitcoin") {
			$bitcoin[3] = $bitcoin[3] + $amount;
		} elseif ($type == "ethereum") {
			$ethereum[3] = $ethereum[3] + $amount;
		}

		//reserialize
		$bitcoin = implode(":", $bitcoin);
		$ethereum = implode(":", $ethereum);
		$internal_addresses = base64_encode(serialize($bitcoin)).":".base64_encode(serialize($ethereum));

		$sql = "UPDATE users SET internal_addresses = :internal_addresses WHERE id = :id";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':internal_addresses', $internal_addresses);
		$stmt->bindParam(':id', $id);
		$stmt->execute();


	}

	public function transferFunds($sender, $receiver, $amount, $type) {
		//only transfers in DB

		if ($type == "bitcoin") {
			$sender_balance = $this->getInternalBalances($sender)[0];
			$receiver_balance = $this->getInternalBalances($receiver)[0];

		} elseif ($type == "ethereum") {
			$sender_balance = $this->getInternalBalances($sender)[1];
			$receiver_balance = $this->getInternalBalances($receiver)[1];
		}
		
		if ($sender_balance < $amount or $sender_balance - $amount < 0.0) {
			return "ERR_NOT_ENOUGH";
		} else {
			//deduct from sender balance
			$this->changeBalance($sender, -1 * $amount, $type);

			//add to receiverbalance
			$this->changeBalance($receiver, 1 * $amount, $type);

			return "ERR_OK";
		}


	}



}

class Errors extends Ravelous  {
	public function matchCodeToMessage($code) {

	}

	public function getRedirectCodeFromErrorCode($code) {

	}
}

class RavelousShop extends Ravelous {

	public function changePassword($id, $current_password, $new_password) {
		$ravelous = new Ravelous;

		//verify data
		if ($ravelous->validateInfo("validemail@gmail.com", $new_password) !== "ERR_OK") {
			return "ERR_INVALID_INFO";
		}


		$db = $ravelous->getRavelousDB();
		$sql = "SELECT password FROM users WHERE id = :id";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":id", $id);
		$stmt->execute();

		$data = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);


		$password_db = @$data[$id][0]["password"];
		$current_password = $ravelous->hashPassword($current_password);

		if ($password_db !== $current_password) {
			return "ERR_UNAUTHORIZED";
		}

		$new_password = $ravelous->hashPassword($new_password);
		$sql = "UPDATE users SET password = :password WHERE id = :id";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':password', $new_password);
		$stmt->bindParam(':id', $id);
		$stmt->execute();

		return "ERR_OK";


	}

	public function currencySettings($id, $accepted, $preferred, $local) {
		$ravelous = new Ravelous;

		$accepted = strip_tags($accepted);
		$preferred = strip_tags($preferred);
		$local = strip_tags($local);

		$currency_settings = base64_encode($accepted).":".base64_encode($preferred).":".base64_encode($local);

		$db = $ravelous->getRavelousDB();
		$sql = "UPDATE users SET currency_settings = :currency_settings WHERE id = :id";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':currency_settings', $currency_settings);
		$stmt->bindParam(':id', $id);
		$stmt->execute();

		return "ERR_OK";
	}

	public function addPhoneNumber($id, $phone_number) {
		$ravelous = new Ravelous;

		$phone_number = strip_tags($phone_number);

		$db = $ravelous->getRavelousDB();
		$sql = "UPDATE users SET phone_number = :phone_number WHERE id = :id";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':phone_number', $phone_number);
		$stmt->bindParam(':id', $id);
		$stmt->execute();

		return "ERR_OK";
	}
	
	public function doesUserHaveShop($id) {
		$ravelous = new Ravelous;

		$db = $ravelous->getRavelousDB();

		$sql = "SELECT name FROM shops WHERE realID = :id";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":id", $id);
		$stmt->execute();

		if (empty(key(array_map('reset', $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC))))) {
			return false;
		} else {
			return true;
		}

	}

	public function getAmountByProduct($product_name, $id) {
		$ravelous = new Ravelous;
		$db = $ravelous->getRavelousDB();

		$products = $this->getShopValues($id)["products"];
		$products = unserialize($products);

		foreach ($products as $key => $value) {
			if (explode(":", $value)[0] == $product_name) {
				$amount = explode(":", $value)[1];
				break;
			}
		}
		
		if (is_null($amount)) {
			return "ERR_NO_SUCH_PRODUCT";
		}

		return $amount;
	}

	public function addToSoldHistory($id)  {

	}

	public function addToBroughtHistory($id) {
		
	}

    public function getShops($amount) {
        $ravelous = new Ravelous();
		$db = $ravelous->getRavelousDB();
		$sql = "SELECT * FROM shops";
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$found_array = array();

		//flatten data array
		$data = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
		$shopsinfo = array();
		$shopnames = array_keys($data);
		for ($j = 0; $j < count($data); $j++) {
		    if ($j < $amount) {
		        $shop = $data[$shopnames[$j]][0];
    		    $shopinfo = array();
    		    $valuenames = array_keys($shop);
    		    for ($i = 0; $i < count($shop); $i++) {
    		        if ($i == 1 || $i == 2 || $i == 3) {
    		            $shopinfo[] = $shop[$valuenames[$i]];
    		        }
    		    }
    		    $shopinfo[] = $shopnames[$j];
    		    $shopsinfo[] = $shopinfo;
		    }
		}
		return $shopsinfo;
    }
    
	public function searchKeyWord($keyword, $filter = 'description') {
		$ravelous = new Ravelous();
		$db = $ravelous->getRavelousDB();
		$sql = "SELECT * FROM shops";
		$stmt = $db->prepare($sql);
		$stmt->execute();

		$found_array = array();

		//flatten data array
		$data = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
		foreach ($data as $key => $value) {
			foreach ($value as $key_1 => $value_1) {
				$products = $value_1["products"];
				$products = unserialize($products);

				$count = 0;
	    		foreach ($products as $key_2 => $value_2) {
	    			//current filter: description
	    			$description = base64_decode(explode(":", $value_2)[3]);
	    			if ($filter == 'description') {
	    				if (stripos($description, $keyword) !== false) {
	    					//found a shop that is compatible
	    					$found_array[] = $key.":".$value_1["realID"].":".$value_1["shop_title"].":".$value_1["shop_description"].":".$count.":".$description;
	    				}
	    				$count++;
	    			//return last 20 products for homepage
	    			} elseif ($filter == "homepageshow") {
	    				$found_array[] = $value_2;
	    			}
		    	}
			}
		}

		//found_array:
		/*
		Example:
		Array
		(
		    [0] => EndPositive:9:kittens:Lovely kittens:0:whatever
		    [1] => laci999:15:New shop title:132132:0:whatever
		    [2] => laci999:15:New shop title:132132:1:whatever
		    [3] => laci999:15:New shop title:132132:3:whatever
		    [4] => laci999:15:New shop title:132132:6.whatever
		)

		Indexed by integers to loop over for. First shop name, ID, title of shop, description of shop, and the number of the product in the products list. 
				*/
		$found_array = array_slice($found_array, -20);
		return $found_array;

	}

	public function changeShopData($id, $change, $data) {
		$ravelous = new Ravelous;
		$db = $ravelous->getRavelousDB();

		//accepts: title, description, cover image
		if ($change == "shop_title") {
			$shop_title = $data;
			if (empty($shop_title)) {
				return "ERR_INVALID_TEXT";
			}
			$shop_title = strip_tags($shop_title);
			$data = $shop_title;
			$sql = "UPDATE shops SET shop_title = :data WHERE realID = :realID";
		} elseif ($change == "shop_description") {
			$shop_description = $data;
			if (empty($shop_description)) {
				return "ERR_INVALID_TEXT";
			}
			$shop_description = strip_tags($shop_description);
			$data = $shop_description;
			$sql = "UPDATE shops SET shop_description = :data WHERE realID = :realID";
		} elseif ($change == "shop_cover_image") {
			//check whether it really is an image
			$shop_cover_image = $data;
			$info = getimagesize($shop_cover_image['tmp_name']);
			if ($info === FALSE) {
			   return "ERR_INVALID_IMAGE";
			}

			$shop_cover_image = file_get_contents($shop_cover_image['tmp_name']);
			$shop_cover_image = base64_encode($shop_cover_image);
			$data = $shop_cover_image;
			$sql = "UPDATE shops SET shop_cover_image = :data WHERE realID = :realID";
		}

		
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':data', $data);
		$stmt->bindParam(':realID', $id);
		$stmt->execute();

		return "ERR_OK";

	}

	public function addProduct($id, $product_name, $product_price, $product_image, $product_description, $product_category, $product_location, $product_currency) {
		$ravelous = new Ravelous;
		$db = $ravelous->getRavelousDB();

		if ($this->doesUserHaveShop($id) == false) {
			return "ERR_USER_HAS_NO_SHOP";
		}
		if (empty($product_name) or empty($product_price) or !is_numeric($product_price) or empty($product_description) or empty($product_category) or empty($product_location)) {
			return "ERR_INVALID_TEXT";
		}

		if (!is_numeric(explode(":", $product_location)[0]) or !is_numeric(explode(":", $product_location)[1]) or !is_string(explode(":", $product_location)[2])) {
			return "ERR_INVALID_TEXT";
		}

		if ($product_currency !== "USD" and $product_currency !== "BTC" and $product_currency !== "RAVE" and $product_currency !== "EUR" and $product_currency !== "ETH") {
			return "ERR_INVALID_TEXT";
		}

		$product_name = strip_tags($product_name);
		$product_price = strip_tags($product_price);
		$product_description = strip_tags($product_description);
		$product_category = strip_tags($product_category);
		$product_location = strip_tags($product_location);

		$info = getimagesize($product_image['tmp_name']);
		if ($info === FALSE) {
		   return "ERR_INVALID_IMAGE";
		}

		$product_image = file_get_contents($product_image['tmp_name']);
		$product_image = base64_encode($product_image);

		$products = unserialize($this->getShopValues($id)["products"]);
		$products[] = base64_encode($product_name).":".$product_price.":".$product_image.":".base64_encode($product_description).":".base64_encode($product_category).":".base64_encode($product_location).":".time().":".$product_currency;
		$products = serialize($products);

		$sql = "UPDATE shops SET products = :products WHERE realID = :id";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':products', $products);
		$stmt->bindParam(':id', $id);
		$stmt->execute();

		return "ERR_OK";

	}

	public function removeProduct($id, $product_name) {
		$ravelous = new Ravelous;
		$db = $ravelous->getRavelousDB();

		$products = $this->getShopValues($id)["products"];
		$products = unserialize($products);

		foreach ($products as $key => $value) {
			if (explode(":", $value)[0] == $product_name) {
				$found = $key;
				break;
			}
		}

		if (!isset($found)) {
			return "ERR_NO_SUCH_PRODUCT";
		}

		unset($products[$found]);
		$products = array_values($products);
		$products = serialize($products);

		$sql = "UPDATE shops SET products = :products WHERE realID = :id";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':products', $products);
		$stmt->bindParam(':id', $id);
		$stmt->execute();

		return "ERR_OK";



	}

	public function showProducts($id) {

		$products = $this->getShopValues($id)["products"];
		$products = unserialize($products);

		if (empty($products)) {
			echo "No products yet!";
			return "ERR_OK";
		}

		$count = 0;
		foreach ($products as $key => $value) {
			
		    echo '<div class="left-corner">
		        <div class="image" style="background: url(data:image/gif;base64,'.explode(":", $value)[2].')">
		            <p>'.($count + 1).'th: '.base64_decode(explode(":", $value)[0]).'</p>
                    <p>'.explode(":", $value)[1].'</p>
                    <a style="color: red;" href="/account?removeproduct='.explode(":", $value)[0].'">Remove Product</a>
                </div>
                <p>'.base64_decode(explode(":", $value)[3]).'</p>
            </div>';
// 			echo $count."th product:<br>";
// 			echo "Name: ".explode(":", $value)[0]."	&nbsp;	&nbsp;	&nbsp;<a href='/account?removeproduct=".base64_encode(explode(":", $value)[0])."'>Remove</a><br>";
// 			echo "Price: ".explode(":", $value)[1]."<br>";
// 			echo "Image: ".'<a target="_blank" onClick=\'openImage("'.explode(":", $value)[2].'")\' href="data:image/gif;base64,'.explode(":", $value)[2].'">View</a><br>';
// 			echo "Description: ".explode(":", $value)[3]."<br>";
// 			echo "Category: ".explode(":", $value)[4]."<br>";
// 			echo "Location: ".explode(":", $value)[5]."<br>";
// 			echo "<br><br>";
			$count++;
		}
	}

	public function getProductCount($id) {
				$products = $this->getShopValues($id)["products"];
				$products = unserialize($products);

				if (empty($products)) {
					return 0;
				}

				$count = 0;
				foreach ($products as $key => $value) {
					$count++;
				}

				return $count;
	}


	public function getShopValues($id) {
		$ravelous = new Ravelous();
		$db = $ravelous->getRavelousDB();
		$sql = "SELECT * FROM shops WHERE realID = :id";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":id", $id);
		$stmt->execute();

		//flatten user array
		return call_user_func_array('array_merge', array_map('reset', $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC)));
	}
	
	public function getShopValuesByName($name) {
		$ravelous = new Ravelous();
		$db = $ravelous->getRavelousDB();
		$sql = "SELECT * FROM shops WHERE name = :name";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":name", $name);
		$stmt->execute();

		//flatten user array
		return call_user_func_array('array_merge', array_map('reset', $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC)));
	}	
 
	public function addShop($username, $id, $shop_title, $shop_description, $shop_cover_image) {
		$ravelous = new Ravelous;

		$db = $ravelous->getRavelousDB();

		if ($this->doesUserHaveShop($id) == true) {
			return "ERR_USER_ALREADY_HAS_SHOP";
		}

		//prevent XSS & validate descriptions
		if (empty($shop_title) or empty($shop_description)) {
			return "ERR_INVALID_TEXT";
		}


		$shop_title = strip_tags($shop_title);
		$shop_description = strip_tags($shop_description);

		//check whether it really is an image
		$info = getimagesize($shop_cover_image['tmp_name']);
		if ($info === FALSE) {
		   return "ERR_INVALID_IMAGE";
		}

		$shop_cover_image = file_get_contents($shop_cover_image['tmp_name']);
		$shop_cover_image = base64_encode($shop_cover_image);

		$products = serialize(array());

		$sql = "INSERT INTO shops (name, products, realID, shop_title, shop_description, shop_cover_image) VALUES (:name, :products, :realID, :shop_title, :shop_description, :shop_cover_image)";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':name', $username);
		$stmt->bindParam(':products', $products);
		$stmt->bindParam(':realID', $id);
		$stmt->bindParam(':shop_title', $shop_title);
		$stmt->bindParam(':shop_description', $shop_description);
		$stmt->bindParam(':shop_cover_image', $shop_cover_image);
		$stmt->execute();


		return "ERR_OK";



	}

}

class RavelousGoogleAuthenticator extends Ravelous {

	public function generateGoogleAuthenticator($username) {

		$ga = new PHPGangsta_GoogleAuthenticator();

		if (!isset($secret) or empty($secret)) {
			$secret = $ga->createSecret();
		}

		$qrCodeUrl = $ga->getQRCodeGoogleUrl($username."@ravelous", $secret);

		echo "<img src='".$qrCodeUrl."'></img>";
		echo "<br>Or enter the secret code: ".$secret."\n\n";

		return $secret;

	}

	public function submitGoogleAuthenticator($secret, $code, $id) {

		$ga = new PHPGangsta_GoogleAuthenticator();
		$checkResult = $ga->verifyCode($secret, $code, 2);    // 2 = 2*30sec clock tolerance
		if ($checkResult) {
		    $sql = "UPDATE users SET authenticator = ? WHERE id = ?";
		    $stmt = $db->prepare($sql);
		    $stmt->bindParam(':authenticator', $secret);
		    $stmt->bindParam(':id', $id);
		    $stmt->execute();

		    $_SESSION["secret"] = $secret;
		    return "ERR_OK";
		} else {
		    return "ERR_CODE_WRONG";
		}

	}

	public function verifyGoogleAuthenticator($secret, $code) {
		$ga = new PHPGangsta_GoogleAuthenticator();
		$checkResult = $ga->verifyCode($secret, $code, 2);    // 2 = 2*30sec clock tolerance
		
		if ($checkResult) {
		    return "ERR_OK";
		} else {
		    return "ERR_2FA";
		}		
	}

}

?>