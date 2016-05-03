<?php
/**
 * @name db_sql
 */
class db_sql {
	/* connection parameters */
	private $host;
	private $database;
	private $user;
	private $password;
	private $cus_pwd;

	/* public: configuration parameters */
	public  $Auto_Free     = 0;     // Set to 1 for automatic mysql_free_result()
	public  $Debug         = 0;     // Set to 1 for debugging messages.
	public  $Halt_On_Error; 		// "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)
	public  $Seq_Table     = "db_sequence";

	/* public: result array and current row number */
	public $Record = array();
	public $Row;

	/* public: current error number and error text */
	public $Errno = 0;
	public $Error = "";

	/* public: this is an api revision, not a CVS revision. */
	public $type     = "mysql";
	public $revision = "1.2";

	/* private: link and query handles */
	private $Link_ID    = 0;
	private $Query_ID   = 0;
	private $Jobtype    = "";
	private $Callid	    = "";
	private $Errortype  = "";
	private $Script	    = "";


	/**
	 * @name __construct
	 * @param database - the database being used
	 * @param host - where the database is
	 * @param user - username to log into db with
	 * @param password - user's password
	 * @param Halt_On_Error - what to do with errors
	 */
	public function __construct($database="") {
		
		$this->Halt_On_Error = "no";

		switch ($database)
		{
			default:
			case 'center':
				$this->host = "localhost";
				$this->database = 'center_org';
				$this->user = 'center';
				$this->password = 'G&6j37dZQB4h$93x';
				break;
		}
	}
	
	public function get_database() {
		return $this->database;	
	}

	public function link_id() {
		return $this->Link_ID;
	}

	public function query_id() {
		return $this->Query_ID;
	}

	function connect($database = "", $host = "", $user = "", $password = "") {
		/* Handle defaults */
		if ($database == "")
			$database = $this->database;
		if ($host == "") 
			$host = $this->host;
		if ($user == "")
			$user = $this->user;
		if ($password == "")
			$password = $this->password;
		
			/* establish connection, select database */
		if ($this->Link_ID == 0) {
			$this->Link_ID = mysql_connect($host, $user, $password, true, 2);  //must set new_link param to true!
			if (!$this->Link_ID) {
				$this->halt("connect($host, $user, $password) failed.");
				return 0;
			}
			if (!@mysql_select_db($database,$this->Link_ID)) {
				$this->halt("cannot use database ".$database);
				return 0;
			}
		}
		return $this->Link_ID;
	}

	/* discard the query result */
	public function free() {
		@mysql_free_result($this->Query_ID);
		$this->Query_ID = 0;
	}

	/* perform a query */
	public function query($Query_String) {
		/* No empty queries, please, since PHP4 chokes on them. */
		if ($Query_String == "") {
			return 0;
		}

		if (!$this->connect())  {
			return 0; /* we already complained in connect() about that. */
		}
			
		/* New query, discard previous result. */
		if ($this->Query_ID) 
			$this->free();

		if ($this->Debug) 
			printf("Debug: query = %s<br>\n", $Query_String);
			
		$this->Query_ID = @mysql_query($Query_String,$this->Link_ID);
		$this->Row = 0;
		$this->Errno = mysql_errno();
		$this->Error = mysql_error();
		if (!$this->Query_ID) {
			$this->halt("Invalid SQL: ".$Query_String);
		}
		/* Will return nada if it fails. That's fine. */
		return $this->Query_ID;
	}

	/* walk result set */
	public function next_record() {
		if (!$this->Query_ID) {
			$this->halt("next_record called with no query pending.");
			return 0;
		}

		$this->Record = @mysql_fetch_array($this->Query_ID);
		$this->Row   += 1;
		$this->Errno  = mysql_errno();
		$this->Error  = mysql_error();

		$stat = is_array($this->Record);
		if (!$stat && $this->Auto_Free) {
			$this->free();
		}
		return $stat;
	}

	/* position in result set */
	public function seek($pos = 0) {
		$status = @mysql_data_seek($this->Query_ID, $pos);
		if ($status)
		$this->Row = $pos;
		else {
			$this->halt("seek($pos) failed: result has ".$this->num_rows()." rows");

			/* half assed attempt to save the day,
			* but do not consider this documented or even
			* desireable behaviour.
			*/
			@mysql_data_seek($this->Query_ID, $this->num_rows());
			$this->Row = $this->num_rows;
			return 0;
		}

		return 1;
	}

	/* table locking */
	function lock($table, $mode="write") {
		$this->connect();

		$query="lock tables ";
		if (is_array($table)) {
			while (list($key,$value)=each($table)) {
				if ($key=="read" && $key!=0) {
					$query.="$value read, ";
				} else {
					$query.="$value $mode, ";
				}
			}
			$query=substr($query,0,-2);
		} else {
			$query.="$table $mode";
		}
		$res = @mysql_query($query, $this->Link_ID);
		if (!$res) {
			$this->halt("lock($table, $mode) failed.");
			return 0;
		}
		return $res;
	}

	function unlock() {
		$this->connect();

		$res = @mysql_query("unlock tables");
		if (!$res) {
			$this->halt("unlock() failed.");
			return 0;
		}
		return $res;
	}


	/* evaluate the result (size, width) */
	public function affected_rows() {
		return @mysql_affected_rows($this->Link_ID);
	}

	function num_rows() {
		return @mysql_num_rows($this->Query_ID);
	}

	function num_fields() {
		return @mysql_num_fields($this->Query_ID);
	}

	/* shorthand notation */
	public function nf() {
		return $this->num_rows();
	}

	function np() {
		print $this->num_rows();
	}

	function f($Name) {
		return stripslashes($this->Record[$Name]);
	}

	function p($Name) {
		print $this->Record[$Name];
	}

	/* sequence numbers */
	public function nextid($seq_name) {
		$this->connect();

		if ($this->lock($this->Seq_Table)) {
			/* get sequence number (locked) and increment */
			$q  = sprintf("select nextid from %s where seq_name = '%s'",
			$this->Seq_Table,
			$seq_name);
			$id  = @mysql_query($q, $this->Link_ID);
			$res = @mysql_fetch_array($id);

			/* No current value, make one */
			if (!is_array($res)) {
				$currentid = 0;
				$q = sprintf("insert into %s values('%s', %s)",
				$this->Seq_Table,
				$seq_name,
				$currentid);
				$id = @mysql_query($q, $this->Link_ID);
			} else {
				$currentid = $res["nextid"];
			}
			$nextid = $currentid + 1;
			$q = sprintf("update %s set nextid = '%s' where seq_name = '%s'",
			$this->Seq_Table,
			$nextid,
			$seq_name);
			$id = @mysql_query($q, $this->Link_ID);
			$this->unlock();
		} else {
			$this->halt("cannot lock ".$this->Seq_Table." - has it been created?");
			return 0;
		}
		return $nextid;
	}

	/* return table metadata */
	public function metadata($table='',$full=false) {
		$count = 0;
		$id    = 0;
		$res   = array();

		/*
		* Due to compatibility problems with Table we changed the behavior
		* of metadata();
		* depending on $full, metadata returns the following values:
		*
		* - full is false (default):
		* $result[]:
		*   [0]["table"]  table name
		*   [0]["name"]   field name
		*   [0]["type"]   field type
		*   [0]["len"]    field length
		*   [0]["flags"]  field flags
		*
		* - full is true
		* $result[]:
		*   ["num_fields"] number of metadata records
		*   [0]["table"]  table name
		*   [0]["name"]   field name
		*   [0]["type"]   field type
		*   [0]["len"]    field length
		*   [0]["flags"]  field flags
		*   ["meta"][field name]  index of field named "field name"
		*   The last one is used, if you have a field name, but no index.
		*   Test:  if (isset($result['meta']['myfield'])) { ...
		*/

		// if no $table specified, assume that we are working with a query
		// result
		if ($table) {
			$this->connect();
			$id = @mysql_list_fields($this->database, $table);
			if (!$id)
			$this->halt("Metadata query failed.");
		} else {
			$id = $this->Query_ID;
			if (!$id)
			$this->halt("No query specified.");
		}

		$count = @mysql_num_fields($id);

		// made this IF due to performance (one if is faster than $count if's)
		if (!$full) {
			for ($i=0; $i<$count; $i++) {
				$res[$i]["table"] = @mysql_field_table ($id, $i);
				$res[$i]["name"]  = @mysql_field_name  ($id, $i);
				$res[$i]["type"]  = @mysql_field_type  ($id, $i);
				$res[$i]["len"]   = @mysql_field_len   ($id, $i);
				$res[$i]["flags"] = @mysql_field_flags ($id, $i);
			}
		} else { // full
		$res["num_fields"]= $count;

		for ($i=0; $i<$count; $i++) {
			$res[$i]["table"] = @mysql_field_table ($id, $i);
			$res[$i]["name"]  = @mysql_field_name  ($id, $i);
			$res[$i]["type"]  = @mysql_field_type  ($id, $i);
			$res[$i]["len"]   = @mysql_field_len   ($id, $i);
			$res[$i]["flags"] = @mysql_field_flags ($id, $i);
			$res["meta"][$res[$i]["name"]] = $i;
		}
		}

		// free the result only if we were called on a table
		if ($table) @mysql_free_result($id);
		return $res;
	}

	/* error handling */
	private function halt($msg) {
		$this->Error = @mysql_error($this->Link_ID);
		$this->Errno = @mysql_errno($this->Link_ID);
		if ($this->Halt_On_Error == "no")
		return;

		$this->haltmsg($msg);

		if ($this->Halt_On_Error != "report")
		die("Session halted.");
	}

	function haltmsg($msg) {
		printf("</td></tr></table><b>Database error:</b> %s<br>\n", $msg);
		printf("<b>MySQL Error</b>: %s (%s)<br>\n",
		$this->Errno,
		$this->Error);
	}

	function table_names() {
		$this->query("SHOW TABLES");
		$i=0;
		while ($info=mysql_fetch_row($this->Query_ID))
		{
			$return[$i]["table_name"]= $info[0];
			$return[$i]["tablespace_name"]=$this->database;
			$return[$i]["database"]=$this->database;
			$i++;
		}
		return $return;
	}
	
	function last_id() {
		return @mysql_insert_id($this->Link_ID);
	}
}
?>
