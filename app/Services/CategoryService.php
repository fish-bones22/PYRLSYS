<?php

namespace App\Services;

use App\Contracts\ICategoryService;
use App\Entities\CategoryEntity;
use App\Models\Category;
use App\Models\CategoryDetail;

class CategoryService extends EntityService implements ICategoryService {

    public function hasKey($key) {
        if (CategoryDetail::where('key', $key)->first() != null)
            return true;

        return false;
    }


    public function getAllCategories() {
        $categories = CategoryDetail::all();
        $categoryEntities = array();

        foreach ($categories as $category) {
            $categoryEntities[] = [
                'displayName' => $category->displayName,
                'key' => $category->key,
                'description' => $category->description
            ];
        }

        return $categoryEntities;
    }


    public function getCategories($key) {

        $categories = Category::all()->where('key', $key);
        //$categories = Category::where('key', $key);
        $categoryEntities = array();

        foreach ($categories as $category) {
            $categoryEntities[] = $this->mapToEntity($category, new CategoryEntity());
        }

        return $categoryEntities;
    }


    public function getCategoryById($id) {
        $category = Category::find($id);
        $categoryEntity = $this->mapToEntity($category, new CategoryEntity());

        return $categoryEntity;
    }


    protected function mapToEntity($model, $entity) {
        $entity = parent::mapToEntity($model, $entity);
        $entity->key = $model->key;
        $entity->value = $model->value;
        $entity->detail = $model->detail;
        $entity->subvalue1 = $model->subvalue1;
        $entity->subvalue2 = $model->subvalue2;

        $details = $model->details;
        $entity->displayName = $details->displayName;
        $entity->description = $details->description;

        return $entity;
    }


    public function addCategory(CategoryEntity $category) {
        $categoryModel = new Category();
        $categoryModel->key = $category->key;
        $categoryModel->value = $category->value;
        $categoryModel->detail = $category->detail;
        $categoryModel->subvalue1 = $category->subvalue1;
        $categoryModel->subvalue2 = $category->subvalue2;
        $categoryModel->save();
    }


    public function updateCategory(CategoryEntity $category) {
        $categoryModel = Category::find($category->id);
        $categoryModel->key = $category->key;
        $categoryModel->value = $category->value;
        $categoryModel->detail = $category->detail;
        $categoryModel->subvalue1 = $category->subvalue1;
        $categoryModel->subvalue2 = $category->subvalue2;
        $categoryModel->save();
    }


    public function removeCategory($id) {
        $categoryModel = Category::find($id);
        $categoryModel->delete();
    }

    public function createNewCategory($key, $displayName, $description) {
        $category = new CategoryDetail();
        $category->key = $key;
        $category->displayName = $displayName;
        $category->description = $description;
        $category->save();
    }

    public function updateCategoryTitle($key, $displayName) {
        $category = CategoryDetail::where('key', $key)->first();
        $category->displayName = $displayName;
        $category->save();
    }

    public function updateCategoryDescription($key, $description) {
        $category = CategoryDetail::where('key', $key)->first();
        $category->description = $description;
        $category->save();
    }

}
