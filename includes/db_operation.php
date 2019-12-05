<?PHP
	
	class DbOperation{
		
		private $con;
		
		function __construct(){
			require_once dirname(__FILE__) . '/db_connect.php';
			
        $this->con = mysqli_connect("localhost", "root", "") or die(mysqli_error());
 
        $this->db = mysqli_select_db($this->con, "socalstyle") or die(mysqli_error()) or die(mysqli_error());
		}
		
		function getall(){
			$query = mysqli_query($this->con, "SELECT itemid FROM item WHERE colorid=\"BLACK\"");
			
			$table = array();
				
			if ($query->num_rows > 0) {
				// output data of each row
				while($row = $query->fetch_assoc()) {
#					echo $row["id"] . "\t" . $row["country"] . "\t" . $row["city"] ."\n";
					array_push($table, $row);
				}
				
			} else {
				echo "0 results";
			}		
			$inJSON = json_encode($table);
			echo $inJSON;
		}
		
		function get($what, $where){
			$query = mysqli_query($this->con, "SELECT " . $what . " FROM " . $where );
			
			$table = array();
			
			if ($query->num_rows > 0){
				while($row = $query->fetch_assoc()){
					array_push($table, $row);
				}
			}
			else {
				echo "0 results";
			}		
			$inJSON = json_encode($table);
			echo $inJSON;		
		}
		
		function popPage($which){
			$query = mysqli_query($this->con, "SELECT * FROM item WHERE sexid=\"" . $which . "\"" );
			
			$table = array();
			
			if ($query->num_rows > 0){
				while($row = $query->fetch_assoc()){
					array_push($table, $row);
				}
			}
			else {
				echo "0 results";
			}		
			$inJSON = json_encode($table);
			echo $inJSON;		
		}
		
		function filter($limits){//sex, size, design, color, style
			$fields = array("item.sexid=", "item.sizeid=", "item.designid=", "item.colorid=", "item.typeid=");
			$queryString = array("WHERE");


			for($i = 0; $i < count($limits); $i++){
				if($limits[$i] != ""){
					if(count($queryString) > 1){
						array_push($queryString, "AND " . $fields[$i] . "\"" . $limits[$i] . "\"");
					}
					else{
						array_push($queryString, $fields[$i] . "\"" . $limits[$i] . "\"");
					}
				}
			}
			
			#echo implode(" ", $queryString);
			$query = mysqli_query($this->con, "SELECT FILEPATH, SEXID, COLORID, SIZEID, item.TYPEID, DESIGNID, PRICE FROM item JOIN tshirttype on item.TYPEID=tshirttype.TYPEID " . implode(" ", $queryString));
			
			$table = array();
			
			if ($query->num_rows > 0){
				while($row = $query->fetch_assoc()){
					array_push($table, $row);
				}
			}
			else {
				echo "0 results";
			}		
			$inJSON = json_encode($table);
			echo $inJSON;		
		
		}
		
		function login($user, $pass){#add collate to query
			$query = mysqli_query($this->con, "SELECT FNAME, LNAME, CUSTOMERID FROM customer where emailadress=\"" . $user . "\" AND PASSWORD=\"" . $pass . "\"");
			
			$result = array();
			
			if ($query->num_rows == 1){
				array_push($result, $query->fetch_assoc());
			}
#			else{
#				array_push($result, 'status', 'false');
#			}
			$inJSON = json_encode($result);
			echo $inJSON;
		}
		
		function register($newUser){#maintain the same order in URL construction....add collate to push
			$query = mysqli_query($this->con, "SELECT * FROM customer where emailadress=\"" . $newUser[2] . "\"");
			
			$result = array();
			$parts = implode("\", \"", $newUser);
			if($query->num_rows > 0){
				array_push($result, 'registration', 'failed: email address already registered.');
			}
			else{
				$query = mysqli_query($this->con, "INSERT into customer(FNAME, LNAME, EMAILADRESS, PASSWORD) values(\"" . $parts . "\")");
				if($query){
					array_push($result, 'registration', 'success');
				}
				else{
					array_push($result, 'registration', 'failed: please retry in a few minutes');
				}
			}
			
			$inJSON = json_encode($result);
			echo $inJSON;
		}
		
		function arrayTest($array){
#			echo $array . "\n";
			for ($i = 0; $i < sizeOf($array); $i += 1){
				if ($array[$i] != ""){
					echo $array[$i] . "\n";
				}
				else{
					echo "Empty String\n";
				}
			}
		}
		
		function addToCart($id, $desc){
			$queryString = "INSERT INTO cart (customerid, itemid, quantity)  VALUES($id, ( SELECT itemid FROM item WHERE SEXID = \"" . $desc[0] ."\" AND SIZEID=\"" . $desc[1] . "\" AND colorID=\"". $desc[2] . "\" AND typeid=\"" . $desc[3] . "\" AND designid=\"" . $desc[4] . "\"), 1) ON DUPLICATE KEY UPDATE QUANTITY = QUANTITY + 1";
			$query = mysqli_query($this->con, $queryString);
			echo $queryString;
			$result = array();
			if ($query){
				array_push($result, "added", "success");
			}
			else{
				array_push($result, "added", "failed");
			}
			$inJSON = json_encode($result);
			echo $inJSON;
		}
		#SELECT itemid, quantity, price from (tshirttype join item on tshirttype.typeid=item.typeid) join cart on cart.itemid = item.itemid WHERE cart.CUSTOMERID = $id
		function getFromCart($id){#(SELECT itemid, QUANTITY FROM cart WHERE customerid = 3 AND quantity > 0) SELECT price from (tshirttype join item on tshirttype.typeid=item.typeid) join cart on cart.itemid = item.itemid WHERE cart.CUSTOMERID = 3
			$queryString = "SELECT cart.itemid, cart.quantity, price from (tshirttype join item on tshirttype.typeid=item.typeid) join cart on cart.itemid = item.itemid WHERE cart.CUSTOMERID =$id";
			$query = mysqli_query($this->con, $queryString);
			
			$result = array();
			
			if ($query->num_rows > 0){
				while($row = $query->fetch_assoc()){
					array_push($result, $row);
				}
			}
			$inJSON = json_encode($result);
			echo $inJSON;
		}
		
		function getDesc($itemID){
			$queryString = "SELECT SEXID, SIZEID, COLORID, DESIGNID, item.TYPEID, FILEPATH, PRICE FROM item join tshirttype on item.typeid = tshirttype.typeid WHERE itemid = " . $itemID;
			$query = mysqli_query($this->con, $queryString);
			
			$result = array();
			
			if ($query->num_rows > 0){
				while($row = $query->fetch_assoc()){
					array_push($result, $row);
				}
			}
			$inJSON = json_encode($result);
			echo $inJSON;
		}
		
		function populate(){
			$colors = array( "red", "yellow", "white", "blue", "black" );
			$sizes = array("xsmall", "small", "medium", "large", "xlarge", "xxlarge");
			$types = array("tanktop", "tshirt", "vneck", "hoodie", "zipup", "sweater");
			$sexes = array("male", "female");
			$designs = array("tiger", "socal", "barrens", "woman1", "black", "white", "pink");
			
			$result = array();
			
			foreach($colors as $color){
				foreach($sizes as $size){
					foreach($types as $type){
						foreach($sexes as $sex){
							foreach($designs as $design){
								$query = mysqli_query($this->con, "INSERT INTO ITEM(COLORID, SIZEID, TYPEID, SEXID, DESIGNID) VALUES(\"" . $color . "\",\"" . $size .  "\",\"" . $type . "\",\"" . $sex . "\",\"" . $design . "\")");
							}
						}
					}
				}
			}
			$inJSON = json_encode($result);
			echo $inJSON;

		}
		
#		function reduceCount($itemid){
#			$query = mysqli_query($this->con, "SELECT quantity FROM quantity where itemid = \"" . $itemid . "\"");
#			
#			if($query->num_rows > 0){
#				$quantity = query->fetch_assoc()['quantity'] - 1;
#				$query = mysqli_query($this->con, "UPDATE 
#		}

	}
?>