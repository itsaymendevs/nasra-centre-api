<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\AppTrait;


class EmployeeController extends Controller {
    
    // :: use trait
    use AppTrait;


    public function index() {

        // ::dependencies
        $employees = Employee::all();

        return response()->json($employees, 200);
        
    } // end function


    // ----------------------------------------------------------



    public function store(Request $request) {


        // :: validator
        $validator = $this->validationTrait($request, 
        ['name' => 'required', 'nameAr' => 'required', 'password' => 'required', 'permission' => 'required']);
        
        // ! if validation not passed
        if ($validator != false) {
            return response()->json($validator->original);
        } // end if

        // ------------------------------------
        // ------------------------------------




        // 1: create item
        $employee = new Employee();

        $employee->serial = $this->createSerial('EM', Employee::count());
        $employee->name = $request->name;
        $employee->nameAr = $request->nameAr;
        $employee->permission = $request->permission;
        $employee->password = Hash::make($request->password);
        $employee->isActive = true;

        $employee->save();

        return response()->json(['status' => true, 'message' => 'employee has been added!'], 200);

        
    } // end function




    // ----------------------------------------------------------




    public function update(Request $request) {


        // :: validator
        $validator = $this->validationTrait($request, 
        ['name' => 'required', 'nameAr' => 'required', 'permission' => 'required']);
        
        // ! if validation not passed
        if ($validator != false) {
            return response()->json($validator->original);
        } // end if

        // ------------------------------------
        // ------------------------------------




        // 1: create item
        $employee = Employee::find($request->id);

        $employee->name = $request->name;
        $employee->nameAr = $request->nameAr;
        $employee->permission = $request->permission;

        $employee->save();

        return response()->json(['status' => true, 'message' => 'employee has been updated!'], 200);

        
    } // end function






    // ----------------------------------------------------------




    public function resetPassword(Request $request) {


        // :: validator
        $validator = $this->validationTrait($request, 
        ['password' => 'required']);
        
        // ! if validation not passed
        if ($validator != false) {
            return response()->json($validator->original);
        } // end if

        // ------------------------------------
        // ------------------------------------


        // 1: check if admin password is correct
        $admin = Employee::all()->first();
        if (!Hash::check($request->adminPassword, $admin->password)) {    

            return response()->json(['status'=> false,'message'=> 'Password Incorrect!'], 200);

        } // end if




        // 2: update employee password
        $employee = Employee::find($request->id);

        $employee->password = Hash::make($request->password);
        $employee->save();

        return response()->json(['status' => true, 'message' => 'Password has been updated!'], 200);

        
    } // end function









    // ----------------------------------------------------------




    public function toggleActive(Request $request, $id) {


        // 1: update employee password
        $employee = Employee::find($id);

        $employee->isActive = !boolval($employee->isActive);
        $employee->save();

        return response()->json(['status' => true, 'message' => 'Status has been updated!'], 200);

        
    } // end function







    // ----------------------------------------------------------




    public function delete(Request $request, $id) {


        // 1: remove employee
        $employee = Employee::find($id);
        $employee->delete();

        return response()->json(['status' => true, 'message' => 'Employee has been removed!'], 200);

        
    } // end function






    // ----------------------------------------------------------
    // ----------------------------------------------------------
    // ----------------------------------------------------------
    // ----------------------------------------------------------
    // ----------------------------------------------------------




    public function login(Request $request) {


        // 1: remove employee
        $employee = Employee::where('name', $request->name)->first();


        // 1.2: correct
        if ($employee && Hash::check($request->password, $employee->password)) {

            return response()->json([
                'employee' => $employee,
                'token' => $employee->createToken('desktop', ['role:employee'])->plainTextToken
            ]);

        } // end if


        // 1.3: incorrect
        return response()->json(['error' => 'Incorrect Credit'], 200);

        
    } // end function





} // end function
