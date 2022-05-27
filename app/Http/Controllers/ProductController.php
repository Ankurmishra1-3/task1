<?php
         
namespace App\Http\Controllers;
          
use App\Models\Product;
use Illuminate\Http\Request;
use DataTables;
        
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
   
        $products = Product::latest()->get();
        
        if ($request->ajax()) {
            $data = Product::latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
   
                           $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-route="'.route('products.edit',[base64_encode($row->id)]).'" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">Edit</a>';
   
                           $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-route="'.route('products.destroy',[base64_encode($row->id)]).'" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">Delete</a>';
    
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
      
        return view('product',$products);
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
            'title'             => 'required',
            'description'       => 'required',
            'price'             => 'required',
            'name'             => 'required',
        ]);

        $data = $request->only(['title','description','price','name']);

        $product = Product::create($data);   
        if($product){
            return response()->json(['success'=>'Product saved successfully.']);
        }     
   
        return response()->json(['error'=>'Product not saved.']);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find(base64_decode($id));
        if($product){
            return response()->json($product);
        }     
   
        return response()->json(['error'=>'Product not found.']);
    }

    public function update(Request $request,$id)
    {
        $this->validate($request, [
            'title'          => 'required',
            'description'    => 'required',
            'price'          => 'required',
            'name'             => 'required',
        ]);

        $data = $request->only(['title','description','price','name']);

        $product = Product::find(base64_decode($id));
        if($product){
            $product = $product->update($data);
            return response()->json(['success'=>'Product updated successfully.']);
        }     
   
        return response()->json(['error'=>'Product not found.']);
    }
  
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find(base64_decode($id));
        if($product){
            $product->delete();
            return response()->json(['success'=>'Product deleted successfully.']);
        }     
   
        return response()->json(['error'=>'Product not found.']);
    }
}