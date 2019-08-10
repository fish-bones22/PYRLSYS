<?php

namespace App\Contracts;

use App\Entities\CategoryEntity;

interface ICategoryService {

    public function hasKey($key);
    public function getAllCategories();
    public function getCategories($key);
    public function getCategoryById($id);
    public function addCategory(CategoryEntity $category);
    public function updateCategory(CategoryEntity $category);
    public function removeCategory($id);
    public function createNewCategory($key, $displayName, $description);
    public function updateCategoryTitle($key, $displayName);
    public function updateCategoryDescription($key, $description);

}
