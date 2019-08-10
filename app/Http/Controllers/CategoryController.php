<?php

namespace App\Http\Controllers;


use App\Contracts\ICategoryService;
use App\Contracts\IEmployeeService;
use App\Contracts\IUserService;
use App\Entities\CategoryEntity;
use Illuminate\Http\Request;
use Auth;

class CategoryController extends Controller
{
    private $categoryService;
    private $userService;
    private $employeeService;
    private $key;
    private $pageKey = 'accountsmanagement';

    public function __construct(ICategoryService $categoryService, IUserService $userService, IEmployeeService $employeeService) {
        $this->categoryService = $categoryService;
        $this->userService = $userService;
        $this->employeeService = $employeeService;
    }

    private function setKey($key) {
        $this->key = $key;

        if (!$this->categoryService->hasKey($key))
            $this->categoryService->createNewCategory($key, $key, '');

        $this->categoryService->setKey($key);

    }

    public function categories() {
        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();
        $categories = $this->categoryService->getAllCategories();
        return view('category.category', compact('categories'));
    }


    public function setCategory(Request $request) {
        $this->setKey($request->get('category'));
        return redirect()->action('CategoryController@index');
    }


    public function manage($key) {
        $this->setKey($key);
        return redirect()->action('CategoryController@index', $key);
    }


    public function index($key) {

        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();

        $categories = $this->categoryService->getCategoriesNofilter($key);

        if ($categories === null)
            return view('layout.404');

        $details = array();
        $details['key'] = $key;
        $details['displayName'] = $this->categoryService->getDisplayName($key);
        return view('category.index',['categories' => $categories, 'details' => $details]);
    }


    public function create()
    {
        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();
        return view('category.add');
    }


    public function store(Request $request)
    {
        //
        $req = $request->all();

        $category = new CategoryEntity();
        $category->key = $req['key'];
        $category->value = $req['name'];
        $category->detail = $req['description'] != null && $req['description'] != '' ? $req['description'] : '';

        if ($req['key'] === 'department') {
            $category->timeTable = array();
            $category->timeTable['timein'] = isset($req['subvalue1']) ? $req['subvalue1'] : null;
            $category->timeTable['timeout'] = isset($req['subvalue2']) ? $req['subvalue2'] : null;
            $category->timeTable['break'] = isset($req['subvalue3']) ? $req['subvalue3'] : null;
            $category->timeTable['startdate'] = isset($req['subvalue4']) ? $req['subvalue4'] : null;
            $category->timeTable['enddate'] = isset($req['subvalue5']) ? $req['subvalue5'] : null;
        }
        else {
            $category->subvalue1 = isset($req['subvalue1']) ? $req['subvalue1'] : null;
            $category->subvalue2 = isset($req['subvalue2']) ? $req['subvalue2'] : null;
            $category->subvalue3 = isset($req['subvalue3']) ? $req['subvalue3'] : null;
        }

        $result = $this->categoryService->addCategory($category);

        if (!$result['result']) {
            return redirect()->action('CategoryController@index', $req['key'])->with('error', $result['message']);
        }

        $newId = $result['message'];

        if (Auth::user() != null) {
            $this->userService->addDepartmentToUser($newId, Auth::user()->id);
        }

        return redirect()->action('CategoryController@index', $req['key'])->with('success', 'Successfully added');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();
        $category = $this->categoryService->getCategoryById($id);
        return view('category.show', compact('category'));
    }


    public function edit($id)
    {
        if (AuthUtility::checkAuth($this->pageKey)) return AuthUtility::redirect();
        $category = $this->departmentService->getCategoryById($id);
        return view('category.edit', compact('category'));
    }


    public function update(Request $request, $id)
    {

        $req = $request->all();
        $id = $request['id'];

        if ($id == 0)
            return redirect()->action('CategoryController@index')->with('error', 'Update failed');

        $category = $this->categoryService->getCategoryById($id);
        $category->key = $req['key'];
        $category->value = $req['name'];
        $category->detail = $req['description'] != null && $req['description'] != '' ? $req['description'] : '';

        if ($req['key'] === 'department') {
            $timeTable = array();
            $timeTable['timein'] = isset($req['subvalue1']) ? $req['subvalue1'] : null;
            $timeTable['timeout'] = isset($req['subvalue2']) ? $req['subvalue2'] : null;
            $timeTable['break'] = isset($req['subvalue3']) ? $req['subvalue3'] : null;
            $timeTable['startdate'] = isset($req['subvalue4']) ? $req['subvalue4'] : null;
            $timeTable['enddate'] = isset($req['subvalue5']) ? $req['subvalue5'] : null;
            $category->timeTable = $timeTable;
        }
        else {
            $category->subvalue1 = isset($req['subvalue1']) ? $req['subvalue1'] : null;
            $category->subvalue2 = isset($req['subvalue2']) ? $req['subvalue2'] : null;
            $category->subvalue3 = isset($req['subvalue3']) ? $req['subvalue3'] : null;
        }

        $res = $this->categoryService->updateCategory($category);

        // If saving is successful and key is 'department' and checkbox is checked
        if ($res['result']
        && $req['key'] === 'department'
        && isset($req['checkbox1'])) {
            $employees = $this->employeeService->getEmployeesByDepartment($id);
            foreach ($employees as $employee) {
                \var_dump($employee->id);
                $res = $this->employeeService->addEmployeeTimeTable($employee->id, $timeTable);
                if (!$res['result']) break;
            }
        }


        if ($res['result']) {
            return redirect()->action('CategoryController@index', $req['key'])->with('success', 'Successfully updated');
        }
        else {
            return redirect()->action('CategoryController@index', $req['key'])->with('error', $res['message']);
        }
    }


    public function destroy(Request $request, $id)
    {
        $this->categoryService->removeCategory($id);

        return redirect()->action('CategoryController@index', $request->get('key'))->with('success', 'Successfully deleted');
    }


    public function getDetails($key, $id) {

        $category = $this->categoryService->getCategoryById($id);
        $timeTable = $category->timeTable;

        $sub1 = $category->subvalue1;
        $sub2 = $category->subvalue2;
        $sub3 = $category->subvalue3;
        $sub4 = null;
        $sub5 = null;

        if ($key === 'department') {
            $sub1 = isset($timeTable['timein']) ? date_format(date_create($timeTable['timein']), 'H:i') : null;
            $sub2 = isset($timeTable['timeout']) ? date_format(date_create($timeTable['timeout']), 'H:i') : null;
            $sub3 = isset($timeTable['break']) ? $timeTable['break'] : null;
            $sub4 = isset($timeTable['startdate']) ? $timeTable['startdate'] : null;
            $sub5 = isset($timeTable['enddate']) ? $timeTable['enddate'] : null;
        }

        return response()->json([
            'name' => $category->value,
            'description' => $category->detail,
            'subvalue1' => $sub1,
            'subvalue2' => $sub2,
            'subvalue3' => $sub3,
            'subvalue4' => $sub4,
            'subvalue5' => $sub5
        ]);
    }


    public function getSubvalues($id) {

        $category = $this->categoryService->getCategoryById($id);

        return response()->json([
            'subvalue1' => $category->subvalue1,
            'subvalue2' => $category->subvalue2,
            'subvalue3' => $category->subvalue3
        ]);
    }
}
