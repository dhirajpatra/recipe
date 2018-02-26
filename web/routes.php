<?php
/**
 * this script will process all routing eg. http://localhost/api/recipes/2 [get/patch/delete]
 * along with headers for authentication see the details in readme
 */
// Grabs the URI and breaks it apart
$requestUri = str_replace($_SERVER['HTTP_HOST'], '', $_SERVER['REQUEST_URI']);

// Route it up by catching uri as well as headers and input values
switch ($requestUri) {
    // Home pagea
    case '/':
        echo json_encode(['status' => '200', 'message' => 'Recipe API home page']);
        break;
    // First time need to send login method to create logical session with a valid time span as well as save token+secret or signature into DB for that session [we can use more complex auth methtod eg. jwt]
    case '/api/user/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'));

            $user = new User_Controller();
            $valid = $user->login($data);

            if ($valid != false && $valid != -1) {
                echo json_encode(['status' => '200', 'message' => 'Login successful', 'valid_upto' => $valid]);
            } elseif ($valid == -1) {
                echo json_encode(['status' => '400', 'message' => 'Bad request. Request parameter invalid or not complete']);
            } else {
                echo json_encode(['status' => '401', 'message' => 'Login un-successful']);
            }
        } else {
            echo json_encode(['status' => '400', 'message' => 'Bad request']);
        }
        break;

    // api/recipes
    case '/api/recipes':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = file_get_contents('php://input');

            // check signature auth with valid session
            $user = new User_Controller();
            $valid = $user->verify();

            if($valid) {
                $recipe = new Recipe_Controller();
                $recipes = $recipe->create($data);

                echo json_encode(['status' => '200', 'message' => 'Recipe created successfully']);
            } else {
                echo json_encode(['status' => '401', 'message' => 'Invalid auth signature']);
            }

        } else { // also kept a provision for pagination by little change of routing to send page no as well
            $recipe = new Recipe_Controller();
            $recipes = $recipe->recipes();

            echo json_encode(['status' => '200', 'message' => 'Recipes fetched successfully', 'data' => $recipes]);
        }

        break;
    // give a rate to a recipe
    case (preg_match('/\/api\/recipes\/\d+\/rating/', $requestUri, $matches) ? true : false):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            preg_match('/\d+/', $matches[0], $idValue);
            $id =  $idValue[0];
            $recipe = new Recipe_Controller();
            $recipeResult = $recipe->rateRecipe($id);

            echo json_encode(['status' => '200', 'message' => 'Recipe rated successfully']);
        } else {
            echo json_encode(['status' => '400', 'message' => 'Bad request']);
        }
        break;
    // search by key word for recipes
    case (preg_match('/\/api\/recipes\/search\/\w+$/', $requestUri, $matches) ? true : false):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $matchValues = explode('/', $matches[0]);
            $searchString =  $matchValues[count($matchValues) - 1];

            $recipe = new Recipe_Controller();
            $recipeResult = $recipe->searchRecipe($searchString);

            if (count($recipeResult) > 0) {
                echo json_encode(['status' => '200', 'message' => 'Recipe found successfully', 'data' => $recipeResult]);
            } else {
                echo json_encode(['status' => '200', 'message' => 'No Recipe found with your keyword']);
            }

        } else {
            echo json_encode(['status' => '400', 'message' => 'Bad request']);
        }
        break;

    //  /api/recipes/{id}  fetch recipe
    case (preg_match('/\/api\/recipes\/\d+$/', $requestUri, $matches) ? true : false):
        preg_match('/\d+/', $matches[0], $idValue);
        $id =  $idValue[0];

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $recipe = new Recipe_Controller();
            $recipeResult = $recipe->getRecipe($id);

            echo json_encode(['status' => '200', 'message' => 'Recipe fetched successfully', 'data' => $recipeResult]);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH') { // update a recipe
            $data = file_get_contents('php://input');

            // check signature auth with valid session
            $user = new User_Controller();
            $valid = $user->verify();

            if($valid) {
                $recipe = new Recipe_Controller();
                $recipeResult = $recipe->updateRecipe($id, $data);

                if ($recipeResult === false) {
                    echo json_encode(['status' => '400', 'message' => 'Bad request parameters']);
                } else {
                    echo json_encode(['status' => '200', 'message' => 'Recipe updated successfully']);
                }
            } else {
                echo json_encode(['status' => '401', 'message' => 'Invalid auth signature']);
            }

        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') { // soft delete a recipe
            // check signature auth with valid session
            $user = new User_Controller();
            $valid = $user->verify();

            if($valid) {
                $recipe = new Recipe_Controller();
                $recipeResult = $recipe->deleteRecipe($id);

                if ($recipeResult === false) {
                    echo json_encode(['status' => '400', 'message' => 'Bad request parameters']);
                } else {
                    echo json_encode(['status' => '200', 'message' => 'Recipe deleted successfully']);
                }
            } else {
                echo json_encode(['status' => '401', 'message' => 'Invalid auth signature']);
            }
        } else {
            echo json_encode(['status' => '400', 'message' => 'Bad request']);
        }

        break;

    // Everything else
    default:
        echo json_encode(['status' => '404', 'message' => 'Not found']);
        break;
}
