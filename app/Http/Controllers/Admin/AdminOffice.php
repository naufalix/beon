<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Meta;
use App\Models\Office;
use Illuminate\Http\Request;

class AdminOffice extends Controller
{

    private function meta(){
        $meta = Meta::$data_meta;
        $meta['title'] = 'Admin | Kantor';
        return $meta;
    }

    public function index(){
        return view('admin.office',[
            "meta" => $this->meta(),
            "office" => Office::first(),
        ]);
    }

    public function postHandler(Request $request){
        if($request->submit=="update"){
            $res = $this->update($request);
            return back()->with($res['status'],$res['message']);
        }
        else{
            return redirect('/admin/office')->with("info","Submit not found");
        }
    }

    public function update(Request $request){
        $validatedData = $request->validate([
            'name'=>'required',
            'address' => 'required',
            'hour_in' => 'required',
            'hour_out'=>'required',
        ]);
        
        $office = Office::first();
 
        //Check if the data is found
        if($office){
            
            // Update data
            $office->update($validatedData);   
            return ['status'=>'success','message'=>'Office updated successfully']; 
        }else{
            return ['status'=>'error','message'=>'Data not found'];
        }
    }

}
