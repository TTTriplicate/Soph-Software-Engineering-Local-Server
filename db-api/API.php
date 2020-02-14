<?PHP

	require_once '../includes/db_operation.php';
	
	function isTheseParametersAvailable($params){
		//assuming all parameters are available 
		$available = true; 
		$missingparams = ""; 
		
		foreach($params as $param){
			if(!isset($_POST[$param]) || strlen($_POST[$param])<=0){
				$available = false; 
				$missingparams = $missingparams . ", " . $param; 
			}
		}
	
	
			if(!$available){
			$response = array(); 
			$response['error'] = true; 
			$response['message'] = 'Parameters ' . substr($missingparams, 1, strlen($missingparams)) . ' missing';
			
			//displaying error
			echo json_encode($response);
			
			//stopping further execution
			die();
		}
	}
	
	//an array to display response
	$response = array();
	
	//if it is an api call 
	//that means a get parameter named api call is set in the URL 
	//and with this parameter we are concluding that it is an api call
	if(isset($_GET['apicall'])){
		
		switch($_GET['apicall']){

			case 'getall':
				$db = new DbOperation();
				$response['error'] = false; 
				$response['message'] = 'Request successfully completed';
				$response['table'] = $db->getall();
			break;
			
			case 'popPage':
				$db = new DbOperation();
				$response['error'] = false; 
				$response['message'] = 'Request successfully completed';
				$response['table'] = $db->popPage($_GET['which']);
			break;
			
			case 'login':
				$db = new DbOperation();
				$response['error'] = false; 
				$response['message'] = 'Request successfully completed';
				$response['table'] = $db->login($_GET['user'], $_GET['pass']);
				break;
			case 'register':
				$db = new DbOperation();
				$response['error'] = false; 
				$response['message'] = 'Request successfully completed';
				$response['table'] = $db->register($_GET['newUser']);
				break;
			case 'filter':
				$db = new DbOperation();
				$response['error'] = false; 
				$response['message'] = 'Request successfully completed';
				$response['table'] = $db->filter($_GET['limits']);
				break;
			case 'populate':
				$db = new DbOperation();
				$response['error'] = false; 
				$response['message'] = 'Request successfully completed';
				$response['table'] = $db->populate();
				break;
			case 'addToCart':
				$db = new DbOperation();
				$response['error'] = false; 
				$response['message'] = 'Request successfully completed';
				$response['table'] = $db->addToCart($_GET['id'], $_GET['desc']);
				break;
			case 'cartPull':
				$db = new DbOperation();
				$response['error'] = false; 
				$response['message'] = 'Request successfully completed';
				$response['table'] = $db->getFromCart($_GET['id']);
				break;
			case 'getDesc':
				$db = new DbOperation();
				$response['error'] = false; 
				$response['message'] = 'Request successfully completed';
				$response['table'] = $db->getDesc($_GET['itemid']);
				break;
			case 'get':
				$db = new DbOperation();
				$response['error'] = false;
				$response['message'] = 'Request successfully completed';
				$response['table'] = $db->get($_GET['what'], $_GET['where']);
				break;
		}
		
	}else{
		//if it is not api call 
		//pushing appropriate values to response array 
		$response['error'] = true; 
		$response['message'] = 'Invalid API Call';
	}
	
	//displaying the response in json structure 
	echo json_encode($response);
	
?>
