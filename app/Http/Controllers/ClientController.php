<?php
         
namespace App\Http\Controllers;
          
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Http\Request;
use DataTables;
        
class ClientController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
   
        $data['clients'] = Client::with('employee')->latest()->get();
        $data['employees'] = Employee::latest()->get();
        
        if ($request->ajax()) {
            $data = Client::with('employee')->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
   
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-route="'.route('clients.edit',[base64_encode($row->id)]).'" data-original-title="Edit" class="edit btn btn-primary btn-sm editClient">Edit</a>';
   
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-route="'.route('clients.destroy',[base64_encode($row->id)]).'" data-original-title="Delete" class="btn btn-danger btn-sm deleteClient">Delete</a>';
 
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
      
        return view('company.client',$data);
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
            'name'           => 'required',
        ]);
        $data = $request->only(['name','id']);
        if(isset($request->employees) && count($request->employees)){
            if( $request->id){
                Client::where('id',$request->id)->where(function($query) use($request) {
                    $query->whereNotIn('employee_id',$request->employees);
                    $query->orWhereNull('employee_id');
                })->delete();
             }
            foreach ($request->employees as $key => $employee) {
                Client::updateOrCreate([
                    'name' => $request->name,
                    'employee_id' => $employee
                ]);  
            }
            return response()->json(['success'=>'Client saved successfully.']);
        }else{
            if( $request->id){
                Client::where('id',$request->id)->where(function($query) use($request) {
                    $query->whereNotIn('employee_id',$request->employees);
                    $query->orWhereNull('employee_id');
                })->delete();
            }
           $client = Client::updateOrCreate([
                        'name' => $request->name
                                ]);  
            if($client){
                return response()->json(['success'=>'Client saved successfully.']);
            } 
        }
   
        return response()->json(['error'=>'Client not saved.']);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['client'] = Client::with('employee')->find(base64_decode($id));
        $data['employees'] = Employee::latest()->get();
        if($data){
            return response()->json($data);
        }     
   
        return response()->json(['error'=>'Client not found.']);
    }

  
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = Client::find(base64_decode($id));
        if($client){
            $client->delete();
            return response()->json(['success'=>'Client deleted successfully.']);
        }     
   
        return response()->json(['error'=>'Client not found.']);
    }
}