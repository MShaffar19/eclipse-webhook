<?php
/**
* MySQL store - provides functions for serializing validation and user details using db storage
*/
class MySQLStore
{
  private $db;
  
  function __construct() {
    $this->db = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DBNAME);

    if ($this->db->connect_errno) {
      echo "Failed to connect to MySQL: (" 
        . $this->db->connect_errno . ") " . $this->db->connect_error;
    }

    $create_table =
    'CREATE TABLE IF NOT EXISTS users  
    (
        email VARCHAR(200) NOT NULL,
        json BLOB NOT NULL,
        PRIMARY KEY(email)
    )';

    // Create table
    $create_tbl = $this->db->query($create_table);
    if ($create_table) {
      echo "[Info][MySQLStore] Github users table ok\n";
    }
    else {
      echo "[Error] MySQL store failed to create users table";  
    }
  }
  
  function __destruct() {
    $this->db->close();
  }
  
  public function load($key) {
    $sql = "SELECT json FROM users WHERE email = '$key'";
    $result = $this->db->query($sql);
    $row = $result->fetch_assoc();
    $result->free_result();
    if (!$row) {
      return NULL;
    }
    //error_log("[INFO][MySQLStore] loading mysql data: $sql\n");
    return json_decode($row['json'])|| NULL;
  }
  
  public function save($key, $data) {
    $sql = "INSERT INTO users VALUES('$key', '$data')";
    //error_log("[INFO][MySQLStore] storing data: $sql\n");
    $result = $this->db->query($sql);
    return $result;
  }
  
  public function test($key) {
    return $this->db->query("SELECT COUNT(email) from users where email='$key'");
  }
}
?>