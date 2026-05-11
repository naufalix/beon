<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Meta;
use App\Models\House;
use Illuminate\Http\Request;

class AdminHouse extends Controller
{

    private function meta(){
        $meta = Meta::$data_meta;
        $meta['title'] = 'Admin | Rumah';
        return $meta;
    }

    public function index(){
        return view('admin.house',[
            "meta" => $this->meta(),
            "houses" => House::with(['activeResidents', 'headOfFamily'])->orderBy("house_number","ASC")->get(),
        ]);
    }

    public function postHandler(Request $request){
        if($request->submit=="store"){
            $res = $this->store($request);
            return back()->with($res['status'],$res['message']);
        }
        if($request->submit=="update"){
            $res = $this->update($request);
            return back()->with($res['status'],$res['message']);
        }
        if($request->submit=="destroy"){
            $res = $this->destroy($request);
            return back()->with($res['status'],$res['message']);
        }
    }

    public function store(Request $request){
        
        $validatedData = $request->validate([
            'house_number'=>'required',
            'address'=>'nullable',
            'status'=>'required|in:occupied,vacant',
        ]);

        // Check house_number uniqueness
        if(House::where('house_number', $request->house_number)->first()){
            return ['status'=>'error','message'=>'Nomor rumah sudah terpakai'];
        }
        
        House::create($validatedData);
        return ['status'=>'success','message'=>'Rumah berhasil ditambahkan'];

    }

    public function update(Request $request){
        $validatedData = $request->validate([
            'id'=>'required|numeric',
            'house_number'=>'required',
            'address'=>'nullable',
            'status'=>'required|in:occupied,vacant',
        ]);
        
        $house = House::find($request->id);
 
        // Check if the data is found
        if(!$house){
            return ['status'=>'error','message'=>'Data tidak ditemukan'];
        }

        // Check house_number uniqueness
        if ($request->house_number !== $house->house_number && House::where('house_number', $request->house_number)->exists()) {
            return ['status' => 'error', 'message' => 'Nomor rumah sudah terpakai'];
        }

        // Update data
        $house->update($validatedData);    
        return ['status'=>'success','message'=>'Rumah berhasil diedit'];
    }

    public function destroy(Request $request){
        
        $validatedData = $request->validate([
            'id' => 'required|numeric',
        ]);
        
        $house = House::find($request->id);
        
        // Check if the data is found
        if (!$house) {
            return ['status' => 'error', 'message' => 'Rumah tidak ditemukan'];
        }
        
        $house->delete();
        return ['status' => 'success', 'message' => 'Rumah berhasil dihapus'];
    }

}
