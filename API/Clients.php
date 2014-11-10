<?php

require_once("Rest.inc.php");
class Client
{
	/** @var int */
	public $ID;

	/** @var string */
	public $Name;

	/** @var string */
	public $Surname;

	/** @var int */
	public $Age;

}
class API extends NetREST {

	var $data = "";

	var $parameters= array();

	var $ID = 0;
	
	var $dbhost = "HOST";
	var $dbuser = "USER";
	var $dbpassword = "PASSWORD";
	var $dbname = "DBNAME";


	/*
	 * Public method for access api.
	 * This method dynmically call the method based on the query string
	 *
	 */
	public function processApi(){

		$params = array();
		$parts = $this->getRequestPartsFrom("API");
		$this->parseIncomingParams();
		
		if($parts[1]=='Clients')
		{
			if(($parts[2]=='Client')&&(is_numeric($parts[3])))
			{
				$this->ID = $parts[3];

				switch($this->getRequestMethod()) {
					case "PUT":
						$this->updateClient();
						break;
					case "GET":
						$this->getClient();
						break;
					case "DELETE":
						$this->deleteClient();
						break;
					default:
						$this->response('',404);
				}
			}
			else if ($parts[2]=='Client')
			{
				switch($this->getRequestMethod()) {
					case "POST":
						$this->newClient();
						break;
					default:
						$this->response('',404);
				}
			}
			else
			{
				$this->getClients();
			}
		}
		else
		{
			$this->response('',404);
		}
			
	}


	private function getClients()
	{
		$results = array();
		$mysqli = new mysqli($this->dbhost, $this->dbuser, $this->dbpassword, $this->dbname);
		
		if (!mysqli_connect_errno()) {
			$query = "SELECT _id,Name,Surname,Age FROM Clients";
			if ($resultado = $mysqli->query($query)) {
				$i=0;
				while($row = mysqli_fetch_array( $resultado )) {
					$results[$i] = new Client();
					$results[$i]->ID = $row['_id'];
					$results[$i]->Name = $row['Name'];
					$results[$i]->Age = $row['Age'];
					$results[$i]->Surname = $row['Surname'];
					$i++;
				}
				$resultado->free();
			}
			$mysqli->close();
		}
		$this->response($this->encodeJSONforHTML($results),200);
	}
	function getClient()
	{
		$ID = $this->ID;
		$client = new Client();
		$mysqli = new mysqli($this->dbhost, $this->dbuser, $this->dbpassword, $this->dbname);
		if (!mysqli_connect_errno()) {
			$query = "SELECT _id,Name,Surname,Age FROM Clients WHERE _id=".$ID;
			if ($resultado = $mysqli->query($query)) {
				if($resultado->num_rows>0)
				{
					$row = mysqli_fetch_array($resultado);
					$client->ID = $row['_id'];
					$client->Name = $row['Name'];
					$client->Age = $row['Age'];
					$client->Surname = $row['Surname'];
				}
				$resultado->free();
			}
			$mysqli->close();
		}
		$this->response($this->encodeJSONforHTML($client),200);
	}

	function newClient()
	{
		$Name = $this->parameters['Name'];
		$Surname = $this->parameters['Surname'];
		$Age = $this->parameters['Age'];
		$i=0;
		$mysqli = new mysqli($this->dbhost, $this->dbuser, $this->dbpassword, $this->dbname);
		if (!mysqli_connect_errno()) {
			$query = "INSERT INTO Clients (Name, Surname, Age) VALUES ('".mysql_real_escape_string($Name)."', '".mysql_real_escape_string($Surname)."', ".mysql_real_escape_string($Age).")";
			$mysqli->query($query);
			$i = $mysqli->affected_rows;
		}
		$this->response($this->encodeJSONforHTML($i),200);
	}

	function updateClient()
	{
		$ID = $this->ID;
		$Name = $this->parameters['Name'];
		$Surname = $this->parameters['Surname'];
		$Age = $this->parameters['Age'];
		$i=0;
		$mysqli = new mysqli($this->dbhost, $this->dbuser, $this->dbpassword, $this->dbname);
		if (!mysqli_connect_errno()) {
			$query = "UPDATE Clients  SET Name = '".mysql_real_escape_string($Name)."', Surname = '".mysql_real_escape_string($Surname)."', Age =".mysql_real_escape_string($Age)." WHERE _id = ".intval($ID)."";
			$mysqli->query($query);
			$i = $mysqli->affected_rows;
		}
		$this->response($this->encodeJSONforHTML($i),200);
	}
	
	function deleteClient()
	{
		$ID = $this->ID;
		$i=0;
		$mysqli = new mysqli($this->dbhost, $this->dbuser, $this->dbpassword, $this->dbname);
		if (!mysqli_connect_errno()) {
			$query = "DELETE FROM Clients WHERE _id = ".intval($ID);
			$mysqli->query($query);
			$i = $mysqli->affected_rows;
		}
		$this->response($this->encodeJSONforHTML($i),200);
	}
}

// Initiiate Library

$api = new API;
$api->processApi();
?>
