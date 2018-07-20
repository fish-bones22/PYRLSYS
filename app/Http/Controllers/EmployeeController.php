<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\ImageManagerStatic as Image;

use App\Contracts\IEmployeeService;
use App\Models\Employee;
use App\Models\EmployeePicture;
use App\Entities\EmployeeEntity;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(IEmployeeService $employeeService) {

        $this->employeeService = $employeeService;

    }

    public function index() {

        $employees = $this->employeeService->getAllEmployees();
        return view('employee.index', compact('employees'));

    }


    public function new() {
        return redirect()->action('EmployeeController@show', 0);
    }

    public function show($id = 0) {

        if ($id == 0) {
            return view('employee.show', ['employee' => new EmployeeEntity()]);
        }

        $employee= $this->employeeService->getEmployeeById($id);
        return view('employee.show', compact('employee'));

    }


    public function update(Request $request, $id) {

        $req = $request->all();

        $employee = new EmployeeEntity;
        $employee->id = $id;
        $employee->firstName = $req['firstName'];
        $employee->lastName = $req['lastName'];
        $employee->middleName = $req['middleName'];
        $employee->employeeId = $req['employeeId'];
        $employee->contactNumber = $req['contact_number'];
        $employee->email = $req['email'];

        if (isset($req['other_contacts']) && sizeof($req['other_contacts']) != 0) {
            $employee->details = array();
            $det = $req['other_contacts'];
            foreach ($req['other_contacts'] as $detail) {
                if ($detail['value'] == null)
                    continue;
                $employee->details[] = [
                    'id' => $detail['id'],
                    'key' => $detail['key'],
                    'value' => $detail['value'],
                    'detail' => $detail['detail'],
                    'displayName' => $detail['displayName']
                ];
            }
        }

        if ($id != 0) {
            $this->employeeService->updateEmployee($employee);
        }
        else {

            $id = $this->employeeService->addEmployee($employee);
            // If an image is selected
            if ($request->file('new_image_file')) {

                // Save image file to storage
                $image = $request->file('new_image_file');
                $location = 'profilepictures/';
                $filename = time().$id.'.'.$image->getClientOriginalExtension();
                $this->saveImageToStorage($image, $location, $filename);
                // Save data to DB
                $this->employeeService->addEmployeeImage($id, $location, $filename);
            }
        }


        return redirect()->action('EmployeeController@show', $id)->with('success', '');

    }

    public function destroy($id) {

        $this->employeeService->removeEmployee($id);
        return redirect()->action('EmployeeController@index')->with('success', '');

    }


    public function updateImage(Request $request, $id) {

        if (!$request->file('picture_file')
        && !$request->get('selected_filename')
        && !$request->get('selected_location')) {
            return redirect()->action('EmployeeController@show', $id)->with('error', 'Image file is not valid');
        }

        // Set current picture to not current
        $this->employeeService->unsetCurrentEmployeeImage($id);

        // If new file is uploaded
        if ($request->file('picture_file')) {
            // Get HTTP file
            $image = $request->file('picture_file');
            // Path and file name
            $location = 'profilepictures/';
            $filename = time().$id.'.'.$image->getClientOriginalExtension();

            $this->saveImageToStorage($image, $location, $filename);
            // Save data to DB
            $this->employeeService->addEmployeeImage($id, $location, $filename);

        }
        // If previous image is selected
        else if ($request->get('selected_filename') && $request->get('selected_location')) {

            $location = $request->get('selected_location');
            $filename = $request->get('selected_filename');
            // Update DB
            $this->employeeService->setEmployeeImage($id, $location, $filename);
        }

        return redirect()->action('EmployeeController@show', $id)
        ->with('success', 'Image successfully changed');
    }


    public function deleteImage(Request $request, $id) {

        $location = $request->get('location');
        $filename = $request->get('filename');

        $this->removeImageFromStorage($location.$filename);

        $this->employeeService->removeEmployeeImage($id, $location, $filename);

        return redirect()->action('EmployeeController@show', $id);
    }


    private function saveImageToStorage($file, $location, $filename) {
        // Resize amd crop image to square
        $resizedImg = Image::make($file);
        $resizedImg = $this->resizeImage($resizedImg);
        // Store to file to storage
        Storage::put('public/'.$location.$filename, (string) $resizedImg->encode());
    }

    private function removeImageFromStorage($path) {
        Storage::delete('public/'.$path);
    }

    private function resizeImage($image) {

        $size = 300;
        $newWidth = $image->width();
        $newHeight = $image->height();

        // if image is portrait
        if ($newHeight > $newWidth) {
            $newHeight = ($newHeight/$newWidth) * $size;
            $newWidth = $size;
        } else { // if landscape
            $newWidth = ($newWidth/$newHeight) * $size;
            $newHeight = $size;
        }

        $image->resize($newWidth, $newHeight)->crop($size, $size);
        return $image;

    }
}
