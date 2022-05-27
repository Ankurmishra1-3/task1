<?php
         
namespace App\Http\Controllers;
          
use App\Models\Employee;
use Illuminate\Http\Request;
use DataTables;
        
class EmployeeController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
   
        $employees = Employee::latest()->get();
        
        if ($request->ajax()) {
            $data = Employee::latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
   
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-route="'.route('employees.edit',[base64_encode($row->id)]).'" data-original-title="Edit" class="edit btn btn-primary btn-sm editEmployee">Edit</a>';
   
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-route="'.route('employees.destroy',[base64_encode($row->id)]).'" data-original-title="Delete" class="btn btn-danger btn-sm deleteEmployee">Delete</a>';
 
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
      
        return view('company.employee',$employees);
    }
     
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'    => 'required',
        ]);

        $data = $request->only(['name']);

        $employee = Employee::create($data);   
        if($employee){
            return response()->json(['success'=>'Employee saved successfully.']);
        }     
   
        return response()->json(['error'=>'Employee not saved.']);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = Employee::find(base64_decode($id));
        if($employee){
            return response()->json($employee);
        }     
   
        return response()->json(['error'=>'Employee not found.']);
    }

    public function update(Request $request,$id)
    {
        $this->validate($request, [
            'name'             => 'required',
        ]);

        $data = $request->only(['name']);

        $employee = Employee::find(base64_decode($id));
        if($employee){
            $employee = $employee->update($data);
            return response()->json(['success'=>'Employee updated successfully.']);
        }     
   
        return response()->json(['error'=>'Employee not found.']);
    }
  
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employee = Employee::find(base64_decode($id));
        if($employee){
            $employee->delete();
            return response()->json(['success'=>'Employee deleted successfully.']);
        }     
   
        return response()->json(['error'=>'Employee not found.']);
    }
}