<?php

namespace App\Http\Controllers;

use App\Contracts\IDepartmentService;
use App\Entities\DepartmentEntity;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    private $departmentService;

    public function __construct(IDepartmentService $departmentService) {
        $this->departmentService = $departmentService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $departments = $this->departmentService->getAllDepartments();

        return view('department.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('department.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $req = $request->all();

        $department = new DepartmentEntity();
        $department->name = $req['name'];
        $department->description = $req['description'] != null && $req['description'] != '' ? $req['description'] : '';
        $this->departmentService->addDepartment($department);

        return redirect()->action('DepartmentController@index')->with('success', 'Successfully added');;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $department = $this->departmentService->getDepartmentById($id);
        return view('department.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $department = $this->departmentService->getDepartmentById($id);
        return view('department.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $req = $request->all();
        $id = $request['id'];

        if ($id == 0)
            return redirect()->action('DepartmentController@index')->with('error', 'Update failed');

        $department = $this->departmentService->getDepartmentById($id);
        $department->name = $req['name'];
        $department->description = $req['description'] != null && $req['description'] != '' ? $req['description'] : '';
        $this->departmentService->updateDepartment($department);

        return redirect()->action('DepartmentController@index')->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->departmentService->removeDepartment($id);

        return redirect()->action('DepartmentController@index')->with('success', 'Successfully deleted');
    }


    public function getDetails($id) {

        $department = $this->departmentService->getDepartmentById($id);
        return response()->json([
            'name' => $department->name,
            'description' => $department->description
        ]);
    }
}
