<?php

namespace App\Http\Controllers;


use App\Contracts\ICategoryService;
use App\Contracts\IUserService;
use App\Entities\CategoryEntity;
use Illuminate\Http\Request;
use Auth;

class CategoryController extends Controller
{
    private $categoryService;
    private $userService;
    private $key;
    private $pageKey = 'accountsmanagement';

    public function __construct(ICategoryService $categoryService, IUserService $userService) {
        $this->categoryService = $categoryService;
        $this->userService = $userService;
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
        $category->subvalue1 = isset($req['subvalue1']) ? $req['subvalue1'] : null;
        $category->subvalue2 = isset($req['subvalue2']) ? $req['subvalue2'] : null;
        $result = $this->categoryService->addCategory($category);

        if (Auth::user() != null) {
            $this->userService->addDepartmentToUser($result, Auth::user()->id);
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
        $category->subvalue1 = isset($req['subvalue1']) ? $req['subvalue1'] : null;
        $category->subvalue2 = isset($req['subvalue2']) ? $req['subvalue2'] : null;
        $this->categoryService->updateCategory($category);

        return redirect()->action('CategoryController@index', $req['key'])->with('success', 'Successfully updated');
    }


    public function destroy(Request $request, $id)
    {
        $this->categoryService->removeCategory($id);

        return redirect()->action('CategoryController@index', $request->get('key'))->with('success', 'Successfully deleted');
    }


    public function getDetails($id) {

        $category = $this->categoryService->getCategoryById($id);

        return response()->json([
            'name' => $category->value,
            'description' => $category->detail,
            'subvalue1' => $category->subvalue1,
            'subvalue2' => $category->subvalue2
        ]);
    }


    public function getSubvalues($id) {

        $category = $this->categoryService->getCategoryById($id);

        return response()->json([
            'subvalue1' => $category->subvalue1,
            'subvalue2' => $category->subvalue2
        ]);
    }
}
