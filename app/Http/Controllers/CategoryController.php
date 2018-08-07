<?php

namespace App\Http\Controllers;

use App\Contracts\ICategoryService;
use App\Entities\CategoryEntity;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $categoryService;
    private $key;

    public function __construct(ICategoryService $categoryService) {
        $this->categoryService = $categoryService;
        //$this->key = 'department';
        //$this->setKey($this->key);
    }

    private function setKey($key) {
        $this->key = $key;

        if (!$this->categoryService->hasKey($key))
            $this->categoryService->createNewCategory($key, $key, '');

        $this->categoryService->setKey($key);

    }

    public function categories() {
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
        $categories = $this->categoryService->getCategories($key);
        return view('category.index', compact('categories'));
    }


    public function create()
    {
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
        $this->categoryService->addCategory($category);

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
        $category = $this->categoryService->getCategoryById($id);
        return view('category.show', compact('category'));
    }


    public function edit($id)
    {
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
