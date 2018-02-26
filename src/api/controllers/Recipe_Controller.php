<?php
/**
 * Class Recipe_Controller
 * namespace global
 */
class Recipe_Controller
{
    /**
     * This will fetch recipes 
     * also kept future option for pagination by little change routing
     * @param int $page
     * @return array
     */
    public function recipes($page = 0)
    {
        try  {
            $recipeModel = new Recipe();
            $noOfRecipes = $recipeModel->totalRecipes();
            $stmt = $recipeModel->getAll($page);

            if ($noOfRecipes > 0) {
                $recipes = array('noOfRecipes' => $noOfRecipes);
                $recipes["records"] = array();

                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
                    extract($row);

                    $recipesItem = array(
                        'id' => $id,
                        'recipe_name' => $rname,
                        'preparation_time' => $prep_time,
                        'difficulty_level' => $difficulty,
                        'veg' => ($veg != '') ? $veg : false,
                        'status' => $status,
                        'created_at' => $created_at,
                        'updated_at' => ($updated_at != '') ? $updated_at : null
                    );

                    array_push($recipes["records"], $recipesItem);
                }
            } else {
                $recipes = [];
            }

            return $recipes;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * This will insert and create new recipe
     * @param $data
     * @return array|string
     */
    public function create($data)
    {
        try  {
            $recipeModel = new Recipe();
            $recipe = $recipeModel->create(json_decode($data));

            if ($recipe == 0) {
                return ['Not Successful'];
            } else {
                return ['New Recipe Created Successfully with id: ' . $recipe];
            }

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * This will fetch one recipe as per id
     * @param $id
     * @return mixed|null
     */
    public function getRecipe($id)
    {
        try  {
            $recipeModel = new Recipe();
            $stmt = $recipeModel->getOne($id);
            $noOfRecipe = $stmt->rowCount();

            if ($noOfRecipe > 0) {
                $recipe = $stmt->fetch(\PDO::FETCH_ASSOC);

            } else {
                $recipe = null;
            }

            return $recipe;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * This will update a recipe details
     * @param $id
     * @param $data
     * @return array|bool|string
     */
    public function updateRecipe($id, $data)
    {
        try {
            if (empty($id) || empty($data)) {
                return false;
            }

            $recipeModel = new Recipe();
            $data = json_decode($data, true);
            $data['id'] = $id;
            $recipe = $recipeModel->update($data);

            return $recipe;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * This will change the status of a recipe 
     * intentionally kept for soft delete 
     * @param $id
     * @param $data
     * @return bool
     */
    public function deleteRecipe($id)
    {
        try {
            if (empty($id)) {
                return false;
            }

            $recipeModel = new Recipe();
            $recipe = $recipeModel->delete($id);

            return $recipe;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * This will rate a recipe
     * @param $id
     * @return bool
     */
    public function rateRecipe($id)
    {
        try {
            if (empty($id)) {
                return false;
            }

            $recipeModel = new Recipe();
            $recipe = $recipeModel->rate($id);

            return $recipe;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Search recipes by keyword
     * @param $keyWord
     * @return array|bool
     */
    public function searchRecipe($keyWord)
    {
        try {
            if (empty($keyWord)) {
                return false;
            }

            $recipeModel = new Recipe();
            $stmt = $recipeModel->search($keyWord);
            $noOfRecipes = $stmt->rowCount();

            if ($noOfRecipes > 0) {
                $recipes = array('noOfRecipes' => $noOfRecipes);
                $recipes["records"] = array();

                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) { 
                    extract($row);

                    $recipesItem = array(
                        'id' => $id,
                        'recipe_name' => $rname,
                        'preparation_time' => $prep_time,
                        'difficulty_level' => $difficulty,
                        'veg' => ($veg != '') ? $veg : false,
                        'status' => $status,
                        'created_at' => $created_at,
                        'updated_at' => ($updated_at != '') ? $updated_at : null
                    );

                    array_push($recipes["records"], $recipesItem);
                }
            } else {
                $recipes = [];
            }

            return $recipes;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}