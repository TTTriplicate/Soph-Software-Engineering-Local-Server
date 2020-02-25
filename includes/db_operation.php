<?PHP
	class DbOperation{
		
		private $con;
		
		function __construct(){
			require_once dirname(__FILE__) . '/db_connect.php';
			
        $this->con = mysqli_connect("localhost", "root", "S0ph0m0reENG2") or die(mysqli_error());
 
        $this->db = mysqli_select_db($this->con, "SOCALSTYLE") or die(mysqli_error()) or die(mysqli_error());
		}
		
		function getall(){
			$query = mysqli_query($this->con, "SELECT ITEMID FROM ITEM WHERE COLORID=\"BLACK\"");
			
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
			$query = mysqli_query($this->con, "SELECT * FROM ITEM WHERE SEXID=\"" . $which . "\"" );
			
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
			$fields = array("ITEM.SEXID=", "ITEM.SIZEID=", "ITEM.DESIGNID=", "ITEM.COLORID=", "ITEM.TYPEID=");
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
			$query = mysqli_query($this->con, "SELECT FILEPATH, SEXID, COLORID, SIZEID, ITEM.TYPEID, DESIGNID, PRICE FROM ITEM JOIN TSHIRTTYPE on ITEM.TYPEID=TSHIRTTYPE.TYPEID " . implode(" ", $queryString));
			
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
			$query = mysqli_query($this->con, "SELECT FNAME, LNAME, CUSTOMERID FROM CUSTOMER where EMAILADRESS=\"" . $user . "\" AND PASSWORD=\"" . $pass . "\"");
			
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
			$query = mysqli_query($this->con, "SELECT * FROM CUSTOMER where EMAILADRESS=\"" . $newUser[2] . "\"");
			
			$result = array();
			$parts = implode("\", \"", $newUser);
			if($query->num_rows > 0){
				array_push($result, 'registration', 'failed: email address already registered.');
			}
			else{
				$query = mysqli_query($this->con, "INSERT into CUSTOMER(FNAME, LNAME, EMAILADRESS, PASSWORD) values(\"" . $parts . "\")");
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
		
		function userData($id){
			$query = mysqli_query($this->con, "SELECT FNAME, LNAME, EMAILADRESS FROM CUSTOMER WHERE CUSTOMERID=\"" . $id . "\"");

			$result = array();
			
			if ($query->num_rows == 1){
				array_push($result, $query->fetch_assoc());
			}
			else{
				array_push($result, 'status', 'false');
			}
			$inJSON = json_encode($result);
			echo $inJSON;
		}
		function purchase($id){
			$queryString = "insert into TRANSACTIONS(CUSTOMERID, ITEMID, QUANTITY, DATE)
				select CART.CUSTOMERID, CART.ITEMID, CART.QUANTITY, now()
				from CART
				where CART.CUSTOMERID = \"" . $id . "\"";
			$query = mysqli_query($this->con, $queryString);
			$result = array();
			if($query){
				$clear = mysqli_query($this->con, "DELETE FROM CART WHERE CUSTOMERID = \"" . $id . "\"");
				if($clear){
					array_push($result, 'TRANSACTION', 'COMPLETE');
				}
			$inJSON = json_encode($result);
			echo $inJSON;

			}
		}

		function getTransactions($id){
			$query = mysqli_query($this->con, "SELECT ITEMID, QUANTITY, DATE FROM TRANSACTIONS WHERE CUSTOMERID=\"" . $id . "\" ORDER BY DATE DESC");

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
		
		function addToCart($id, $desc){
			$queryString = "INSERT INTO CART (CUSTOMERID, ITEMID, QUANTITY)  VALUES($id, ( SELECT ITEMID FROM ITEM WHERE SEXID = \"" . $desc[0] ."\" AND SIZEID=\"" . $desc[1] . "\" AND COLORID=\"". $desc[2] . "\" AND TYPEID=\"" . $desc[3] . "\" AND DESIGNID=\"" . $desc[4] . "\"), 1) ON DUPLICATE KEY UPDATE QUANTITY = QUANTITY + 1";
			$query = mysqli_query($this->con, $queryString);
#			echo $queryString;
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
		#SELECT ITEMID, QUANTITY, PRICE from (TSHIRTTYPE join ITEM on TSHIRTTYPE.TYPEID=ITEM.TYPEID) join CART on CART.ITEMID = ITEM.ITEMID WHERE CART.CUSTOMERID = $id
		function getFromCart($id){#(SELECT ITEMID, QUANTITY FROM CART WHERE CUSTOMERID = 3 AND QUANTITY > 0) SELECT PRICE from (TSHIRTTYPE join ITEM on TSHIRTTYPE.TYPEID=ITEM.TYPEID) join CART on CART.ITEMID = ITEM.ITEMID WHERE CART.CUSTOMERID = 3
			$queryString = "SELECT CART.ITEMID, CART.QUANTITY, PRICE from (TSHIRTTYPE join ITEM on TSHIRTTYPE.TYPEID=ITEM.TYPEID) join CART on CART.ITEMID = ITEM.ITEMID WHERE CART.CUSTOMERID =$id";
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
		
		function getDesc($ITEMID){
			$queryString = "SELECT SEXID, SIZEID, COLORID, DESIGNID, ITEM.TYPEID, FILEPATH, PRICE FROM ITEM join TSHIRTTYPE on ITEM.TYPEID = TSHIRTTYPE.TYPEID WHERE ITEMID = " . $ITEMID;
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
	
/*		function populate(){
			$colors = array( "red", "yellow", "white", "blue", "black" );
			$sizes = array("xsmall", "small", "medium", "large", "xlarge", "xxlarge");
			$types = array("tanktop", "tshirt", "vneck", "hoodie", "zipup", "sweater");
			$sexes = array("male", "female");
			$designs = array("tiger", "socal", "barrens", "woman1", "black", "white", "pink");
			
			$result = array();
			
			foreach($colors as $color){
				$query = mysqli_query($this->con, "INSERT INTO COLOR(COLORID) VALUES(\"" . $color ."\")");
				foreach($sizes as $size){
					$query = mysqli_query($this->con, "INSERT INTO SIZES(SIZEID) VALUES(\"" . $size ."\")");
					foreach($types as $type){
						$query = mysqli_query($this->con, "INSERT INTO TSHIRTTYPE(TYPEID) VALUES(\"" . $type ."\")");
						foreach($sexes as $sex){
							$query = mysqli_query($this->con, "INSERT INTO SEX(SEXID) VALUES(\"" . $sex ."\")");
							foreach($designs as $design){
								$query = mysqli_query($this->con, "INSERT INTO DESIGN(DESIGNID) VALUES(\"" . $design ."\")");
								$query = mysqli_query($this->con, "INSERT INTO ITEM(COLORID, SIZEID, TYPEID, SEXID, DESIGNID) VALUES(\"" . $color . "\",\"" . $size .  "\",\"" . $type . "\",\"" . $sex . "\",\"" . $design . "\")");
							}
						}
					}
				}
			}
			$query =mysqli_query($this->con, "UPDATE ITEM SET FILEPATH = (SELECT CONCAT(COLORID, TYPEID, SEXID, \".jpg\")");

			$inJSON = json_encode($result);
			echo $inJSON;

		}
 */	 
	}
?>
