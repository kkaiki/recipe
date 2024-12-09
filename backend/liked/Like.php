<?php

require_once '../BaseModel.php';

class Like extends BaseModel {
    public function getLikesUserIds($recipe_id){
        try {
            $query = "SELECT created_by FROM liked WHERE recipe_id = :recipe_id";
            $params = [':recipe_id' => $recipe_id];
            $stmt = $this->executeQuery($query, $params);
            if (is_string($stmt)) {
                return $stmt;
            }
            $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (!$userIds) {
                return [];
            }
            return $userIds;
        } catch (Exception $e) {
            error_log("Error in Like.php: " . $e->getMessage());
            return $e->getMessage();
        }
    }
}
