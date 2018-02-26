<?php
/**
 * Class User
 * namespace global
 */
class User
{
    // database connection and table name
    private $conn;
    private $table_name = "users";

    // table fields
    public $id;
    public $username;
    public $password;
    public $token;
    public $secret;

    /**
     * User constructor
     * DB connection
     */
    public function __construct()
    {
        $connectDb = ConnectDb::getInstance();
        $this->conn = $connectDb->getConnection();
    }

    /**
     * Login a user by first call of rest for other auth depend subsequent api calls
     * @param $user
     * @return bool|int
     */
    public function login($user)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE username = '" . $user->username . "'";

            $statement = $this->conn->prepare($query);
            $statement->execute();
            $result = $statement->fetch(\PDO::FETCH_OBJ);
            // password saved into users table as $hash = password_hash('hellofresh', PASSWORD_DEFAULT);
            // $2y$10$BOdMlcfWFGqI0IrMPoWfpu7SetBVWJML08NHZxGain5Tv3.6K9GHy

            // if login successful then check password
            if(!empty($result) && (password_verify($user->password, $result->password))) {
                // save token and secret for 1 hour to process all protected api calls
                $validUpto = time() + 60;
                $token = htmlspecialchars(strip_tags($user->token));
                $secret = htmlspecialchars(strip_tags($user->secret));

                $query = "UPDATE " . $this->table_name . " SET token = :token, secret = :secret, valid_upto = :valid_upto";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':token', $token);
                $stmt->bindParam(':secret', $secret);
                $stmt->bindParam(':valid_upto', $validUpto);
                $stmt->execute();

                // we can use more complex and secure method for this normally we use oauth2 or jwt for whole auth
                return $validUpto;
            }
            return false;
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * verify on logical valid session in subsiquent api call
     * @param $token
     * @param $secret
     * @return array
     */
    public function verifySignature($token, $secret) {
        $result = false;
        $now = time();
        try {
            $sql = "SELECT * FROM " . $this->table_name . " WHERE token = :token AND secret = :secret AND valid_upto > :now";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':secret', $secret);
            $stmt->bindParam(':now', $now);

            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_OBJ);

            if(count($result) > 0) {
                return true;
            }

        } catch( PDOExecption $e ) {
            error_log($e->getMessage());
        }

        return false;
    }
}