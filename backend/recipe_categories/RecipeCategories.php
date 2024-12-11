<?php

require_once '../BaseModel.php';

class RecipeCategories extends BaseModel {
    public function getRecipesByIds($connection, $recipe_ids) {
        $recipe_ids_array = explode(',', $recipe_ids);
        $placeholders = implode(',', array_fill(0, count($recipe_ids_array), '?'));

        $query = "SELECT * FROM recipes WHERE recipe_id IN ($placeholders)";
        $stmt = $connection->prepare($query);
        $stmt->execute($recipe_ids_array);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}