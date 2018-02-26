<?php
/**
 * Class Recipe
 */
class Recipe
{
    // database connection and table name
    private $conn;
    private $table_name = "recipes";
    private $table_rate = "ratings";
    private $pageSize = 1;

    // table fields
    public $id;
    public $recipeName;
    public $preparationTime;
    public $difficulty;
    public $veg;
    public $createdAt;
    public $updatedAt;

    /**
     * Recipe constructor.
     */
    public function __construct()
    {
        $connectDb = ConnectDb::getInstance();
        $this->conn = $connectDb->getConnection();
    }

    /**
     * Get total active no of recipes
     * @return mixed
     */
    public function totalRecipes()
    {
        try {
            $query = "SELECT count(id) as total FROM " . $this->table_name . " WHERE status = true";

            $statement = $this->conn->prepare($query);
            $statement->execute();
            $result = $statement->fetch(\PDO::FETCH_OBJ);

            return $result->total;

        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * This will fetch all recipes also kep provision for pagination in future
     * @param int $page
     * @return PDOStatement
     */
    public function getAll($page = 0)
    {
        try {
            if ($page != 0) {
                $limit = " LIMIT " . (($page * $this->pageSize) + 1) . ", 10";
            } else {
                $limit = " LIMIT 10";
            }

            $query = "SELECT * FROM " . $this->table_name . " WHERE status = true ORDER BY id " . $limit;

            $statement = $this->conn->prepare($query);
            $statement->execute();

            return $statement;

        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Create new recipe
     * @param $data
     * @return int|string
     */
    public function create($data)
    {
        try {
            $query = "INSERT INTO ".$this->table_name." ( rname, prep_time, difficulty, veg, status, created_at ) values (:rname, :prep_time, :difficulty, :veg, :status, :created_at)";
            $stmt = $this->conn->prepare($query);

            $status = true;
            $currentDate = date('Y-m-d');
            $recipeName = htmlspecialchars(strip_tags($data->recipe_name));
            $preparationTime = htmlspecialchars(strip_tags($data->preparation_time));
            $difficultyLevel = htmlspecialchars(strip_tags($data->difficulty_level));
            $veg = htmlspecialchars(strip_tags($data->veg));

            $stmt->bindParam(':rname', $recipeName);
            $stmt->bindParam(':prep_time', $preparationTime);
            $stmt->bindParam(':difficulty', $difficultyLevel);
            $stmt->bindParam(':veg', $veg);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':created_at', $currentDate);

            if($stmt->execute()){
                return $this->conn->lastInsertId();
            } else {
                return 0;
            }

        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Fetch details of one recipe
     * @param $id
     * @return PDOStatement
     */
    public function getOne($id)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt;

        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }

    }

    /**
     * Update one recipe
     * @param $data
     * @return bool
     */
    public function update($data)
    {
        try {
            $currentDate = date('Y-m-d');

            $query = "UPDATE ".$this->table_name." SET updated_at = '" . $currentDate . "',";

            // Create dynamic where condition for different col
            if (isset($data['recipie_name']) && $data['recipie_name'] != '') {
                $query .= " rname = :rname,";
            }

            if (isset($data['preparation_time']) && $data['preparation_time'] != '') {
                $query .= " prep_time = :prep_time,";
            }

            if (isset($data['difficulty_level']) && $data['difficulty_level'] != '') {
                $query .= " difficulty = :difficulty,";
            }

            if (isset($data['veg']) && $data['veg'] != '') {
                $query .= " veg = :veg,";
            }

            if (isset($data['status']) && $data['status'] != '') {
                $query .= " status = :status,";
            }

            // removing last comma from dyncamically created sql
            $query = rtrim($query, ", ");

            $query .= " WHERE id = " . $data['id'];

            $stmt = $this->conn->prepare($query);

            if (isset($data['recipie_name']) && $data['recipie_name'] != '') {
                $data['recipie_name'] = htmlspecialchars(strip_tags($data['recipie_name']));
                $stmt->bindParam(':rname', $data['recipie_name']);
            }

            if (isset($data['preparation_time']) && $data['preparation_time'] != '') {
                $data['preparation_time'] = htmlspecialchars(strip_tags($data['preparation_time']));
                $stmt->bindParam(':prep_time', $data['preparation_time']);
            }

            if (isset($data['difficulty_level']) && $data['difficulty_level'] != '') {
                $data['difficulty_level'] = htmlspecialchars(strip_tags($data['difficulty_level']));
                $stmt->bindParam(':difficulty', $data['difficulty_level']);
            }

            if (isset($data['veg']) && $data['veg'] != '') {
                $data['veg'] = htmlspecialchars(strip_tags($data['veg']));
                $stmt->bindParam(':veg', $data['veg']);
            }

            if (isset($data['status']) && $data['status'] != '') {
                $data['status'] = htmlspecialchars(strip_tags($data['status']));
                $stmt->bindParam(':status', $data['status']);
            }

            if($stmt->execute()){
                return true;
            }

            return false;

        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete one recipe
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            $currentDate = date('Y-m-d');
            // intentionally kept no hard delete better to update the status
            $query = "UPDATE ".$this->table_name." SET updated_at = '" . $currentDate . "', status = false";
            $query .= " WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);

            if($stmt->execute()){
                return true;
            }

            return false;
            
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Rate a recipe
     * @param $id
     * @return bool
     */
    public function rate($id)
    {
        try {
            $rate = 1; // later we can update the system to insert different rate value also
            $query = "INSERT INTO ".$this->table_rate." ( rate, recipe_id ) values (:rate, :recipe_id)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':rate', $rate);
            $stmt->bindParam(':recipe_id', $id);

            if($stmt->execute()){
                return true;
            }

            return false;

        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Search recipes by keyword simple like onlny for active recipes
     * @param $keyWord
     * @return PDOStatement
     */
    public function search($keyWord)
    {
        try {
            $keyWord = strtolower($keyWord);
            $query = "SELECT * FROM " . $this->table_name . " WHERE status = true AND LOWER(rname) SIMILAR TO :key_word";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':key_word' => '%' . $keyWord . '%']);

            return $stmt;
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }
}