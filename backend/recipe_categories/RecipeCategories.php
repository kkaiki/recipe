<?php

require_once '../BaseModel.php';

class RecipeCategories extends BaseModel {
    public function getRecipesByIds($connection, $recipe_ids) {
        $placeholders = implode(',', array_fill(0, count($recipe_ids), '?'));

        $query = "SELECT * FROM recipe WHERE id IN ($placeholders)";
        $stmt = $connection->prepare($query);

        $stmt->execute($recipe_ids);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addRecipeCategory($connection, $recipe_id, $category_id) {
        $query = "INSERT INTO recipe_categories (recipe_id, category_id) VALUES (?, ?)";
        $stmt = $connection->prepare($query);
        return $stmt->execute([$recipe_id, $category_id]);
    }

    public function deleteRecipeCategory($connection, $recipe_id, $category_id) {
        $query = "DELETE FROM recipe_categories WHERE recipe_id = ? AND category_id = ?";
        $stmt = $connection->prepare($query);
        return $stmt->execute([$recipe_id, $category_id]);
    }
}