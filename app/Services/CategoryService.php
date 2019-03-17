<?php

namespace App\Services;

use App\Contracts\ICategoryService;
use App\Entities\CategoryEntity;
use App\Models\Category;
use App\Models\CategoryDetail;
use App\Models\DepartmentTimeTable;
use AuthUtility;

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

        $hasKey = CategoryDetail::where('key', $key)->first();
        $categories = Category::all()->where('key', $key);

        if (sizeof($categories) === 0 && $hasKey === null)
            return null;

        $categoryEntities = array();

        foreach ($categories as $category) {
            if (!AuthUtility::hasDepartmentAccess($category->id) && $key === 'department')
                continue;
            $categoryEntities[] = $this->mapToEntity($category, new CategoryEntity());
        }

        return $categoryEntities;
    }

    public function getCategoriesNofilter($key) {

        $hasKey = CategoryDetail::where('key', $key)->first();
        $categories = Category::all()->where('key', $key);

        if (sizeof($categories) === 0 && $hasKey === null)
            return null;

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


    public function getCategoryByValueAndKey($value, $key) {

        if ($value === null) return null;
        if ($key === null) return null;

        $category = Category::where('value', $value)->where('key', $key)->first();

        if ($category === null) return null;

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
        $entity->subvalue3 = $model->subvalue3;

        $details = $model->details;
        $entity->displayName = $details->displayName;
        $entity->description = $details->description;

        // Time table
        if ($entity->key == 'department') {
            $entity->timeTable = $this->getCurrentTimeTable($model->timeTable, $model->subvalue1, $model->subvalue2, $model->subvalue3);
            $entity->timeTableHistory = $this->getTimeTableHistory($model->timeTable);
        }

        return $entity;
    }


    public function addCategory(CategoryEntity $category) {

        $categoryModel = new Category();
        $categoryModel->key = $category->key;
        $categoryModel->value = $category->value;
        $categoryModel->detail = $category->detail;
        $categoryModel->subvalue1 = $category->subvalue1;
        $categoryModel->subvalue2 = $category->subvalue2;
        $categoryModel->subvalue3 = $category->subvalue3;

        try {
            $categoryModel->save();
        } catch (\Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }

        $id = Category::orderBy('created_at', 'desc')->first()->id;

        if ($category->timeTable != null) {
            $res = $this->setTimeTable($id, $category->timeTable);
            if (!$res['result']) {
                return $res;
            }
        }

        return [
            'result' => true,
            'message' => $id
        ];

    }


    public function updateCategory(CategoryEntity $category) {

        $categoryModel = Category::find($category->id);
        $categoryModel->key = $category->key;
        $categoryModel->value = $category->value;
        $categoryModel->detail = $category->detail;
        $categoryModel->subvalue1 = $category->subvalue1;
        $categoryModel->subvalue2 = $category->subvalue2;
        $categoryModel->subvalue3 = $category->subvalue3;

        if ($category->timeTable != null) {
            $res = $this->setTimeTable($category->id, $category->timeTable);
            if (!$res['result']) {
                return $res;
            }
        }

        try {
            $categoryModel->save();
        } catch (\Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }

        return [
            'result' => true
        ];
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

    public function getDisplayName($key) {
        $category = CategoryDetail::where('key', $key)->first();
        return $category->displayName;
    }


    public function getDepartmentTimeTable($departmentId, $date) {

        $department = $this->getCategoryById($departmentId);

        if ($department == null)
            return null;

        $timeTable = null;
        $possibleFallback = null;

        foreach ($department->timeTableHistory as $model) {
            if (date_create($model['startdate']) > $date) {
                continue;
            }

            // Store first level fallback;
            if ($model['enddate'] == null && $possibleFallback == null) {
                $possibleFallback = $model;
                continue;
            }

            if ($model['enddate']!= null && date_create($model['enddate']) < $date) {
                continue;
            }

            $timeTable = $model;
            break;
        }

        // If no record found for the date
        // get from latest record with null endDate
        if ($timeTable == null) {

            if ($possibleFallback != null) {
                $timeTable = $possibleFallback;
            }
            else {
                $timeTable = array();
                $timeTable['timein'] = $department->subvalue1;
                $timeTable['timeout'] = $department->subvalue2;
                $timeTable['break'] = $employee->subvalue3;
            }

        }

        return $timeTable;
    }

    private function setTimeTable($id, $timeTable) {

        if (!isset($timeTable['timein']) || $timeTable['timein'] == null) {
            return [
                'result' => false,
                'message' => 'No Time In provided'
            ];
        }

        if (!isset($timeTable['startdate']) || $timeTable['startdate'] == null) {
            return [
                'result' => false,
                'message' => 'No Start Date for schedule provided'
            ];
        }

        // Get most compatible record
        $date = date_create($timeTable["startdate"]);
        $timeTableRecord =  DepartmentTimeTable::where('startDate', $date)->orderBy('id', 'DESC')->first();

        if ($timeTableRecord == null) {
            $timeTableRecord = new DepartmentTimeTable();
            $timeTableRecord->department_id = $id;
        }

        $timeTableRecord->timeIn = $timeTable["timein"];
        $timeTableRecord->timeOut = $timeTable["timeout"];
        $timeTableRecord->break = $timeTable["break"]*1;
        $timeTableRecord->startDate = $timeTable["startdate"];
        $timeTableRecord->endDate = $timeTable["enddate"];

        try {
            $timeTableRecord->save();
        } catch (\Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage()
            ];
        }

        return [
            'result' => true
        ];
    }

    private function getTimeTableHistory($timeTableHistory) {

        $history = array();

        foreach ($timeTableHistory as $model) {
            $timeTable = array();
            $timeTable['id'] = $model->id;
            $timeTable['timein'] = $model->timeIn;
            $timeTable['timeout'] = $model->timeOut;
            $timeTable['startdate'] = $model->startDate;
            $timeTable['enddate'] = $model->endDate;
            $timeTable['break'] = $model->break;

            $history[] = $timeTable;
        }

        return $history;
    }


    private function getTimeTableOnDate($timeTableHistory, $date, $fallBackTimeIn, $fallBackTimeout, $fallBackBreak) {

        $timeTable = null;
        $possibleFallback = null;

        foreach ($timeTableHistory as $model) {

            if (date_create($model->startDate) > date_create($date)) {
                continue;
            }

            // Store first level fallback;
            if ($model->endDate == null && $possibleFallback == null) {
                $possibleFallback = $model;
                continue;
            }

            if ($model->endDate != null && date_create($model->endDate) < date_create($date)) {
                continue;
            }

            $timeTable = array();
            $timeTable['id'] = $model->id;
            $timeTable['timein'] = $model->timeIn;
            $timeTable['timeout'] = $model->timeOut;
            $timeTable['startdate'] = $model->startDate;
            $timeTable['enddate'] = $model->endDate;
            $timeTable['break'] = $model->break;
            break;
        }

        // If no record found for the date
        // get from latest record with null endDate
        if ($timeTable == null) {

            $timeTable = array();
            if ($possibleFallback != null) {
                $timeTable['id'] = $possibleFallback->id;
                $timeTable['timein'] = $possibleFallback->timeIn;
                $timeTable['timeout'] = $possibleFallback->timeOut;
                $timeTable['startdate'] = $possibleFallback->startDate;
                $timeTable['enddate'] = $possibleFallback->endDate;
                $timeTable['break'] = $possibleFallback->break;
            }
            else {
                $timeTable['timein'] = $fallBackTimeIn;
                $timeTable['timeout'] = $fallBackTimeout;
                $timeTable['break'] = $fallBackBreak;
            }

        }

        return $timeTable;
    }


    private function getCurrentTimeTable($timeTableHistory, $fallBackTimeIn, $fallBackTimeout, $fallBackBreak) {
        return $this->getTimeTableOnDate($timeTableHistory, NOW(), $fallBackTimeIn, $fallBackTimeout, $fallBackBreak);
    }
}
