<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Meta;
use App\Models\House;
use App\Models\Resident;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AdminResident extends Controller
{

    private function meta($house){
        $meta = Meta::$data_meta;
        $meta['title'] = 'Admin | Penghuni Rumah '.$house->house_number;
        return $meta;
    }

    public function index(House $house){
        return view('admin.resident',[
            "meta" => $this->meta($house),
            "house" => $house,
            "residents" => Resident::where('house_id', $house->id)->orderBy("full_name","ASC")->get(),
        ]);
    }

    public function postHandler(Request $request, House $house){
        if($request->submit=="store"){
            $res = $this->store($request, $house);
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

    public function store(Request $request, House $house){
        
        $validatedData = $request->validate([
            'full_name'=>'required',
            'status'=>'required|in:permanent,contract',
            'phone_number'=>'nullable',
            'is_married'=>'nullable',
            'move_in_date'=>'nullable|date',
            'move_out_date'=>'nullable|date',
            'ktp_photo'=>'nullable|image|file|max:2048',
        ]);

        $validatedData['house_id'] = $house->id;
        $validatedData['is_married'] = $request->has('is_married') ? true : false;
        $validatedData['is_head_of_family'] = false; // Default to false on create
        
        // Auto-set is_active_resident based on move_out_date
        $validatedData['is_active_resident'] = empty($request->move_out_date) ? true : false;

        // Handle KTP photo upload
        if($request->file('ktp_photo')){
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('ktp_photo'));
            
            // Resize image
            $maxwidth = 800;
            $maxheight = 600;
            if ($image->width() > $image->height()) {
                if ($image->width() > $maxwidth) {
                    $newheight = $image->height() / ($image->width() / $maxwidth);
                    $image->resize($maxwidth, $newheight);
                }
            } else {
                if ($image->height() > $maxheight) {
                    $newwidth = $image->width() / ($image->height() / $maxheight);
                    $image->resize($newwidth, $maxheight);
                }
            }

            // Convert to .webp
            $imageWebp = $image->toWebp(80);
            
            // Upload new image
            $validatedData['ktp_photo'] = time().".webp";
            $imageWebp->save('storage/img/ktp/'.$validatedData['ktp_photo']);
        }
        
        Resident::create($validatedData);
        return ['status'=>'success','message'=>'Penghuni berhasil ditambahkan'];

    }

    public function update(Request $request){
        $validatedData = $request->validate([
            'id'=>'required|numeric',
            'full_name'=>'required',
            'status'=>'required|in:permanent,contract',
            'phone_number'=>'nullable',
            'is_married'=>'nullable',
            'is_head_of_family'=>'nullable',
            'move_in_date'=>'nullable|date',
            'move_out_date'=>'nullable|date',
            'ktp_photo'=>'nullable|image|file|max:2048',
        ]);
        
        $resident = Resident::find($request->id);
 
        // Check if the data is found
        if(!$resident){
            return ['status'=>'error','message'=>'Data tidak ditemukan'];
        }

        $validatedData['is_married'] = $request->has('is_married') ? true : false;
        $validatedData['is_head_of_family'] = $request->has('is_head_of_family') ? true : false;
        
        // Validate only one head of family per house
        if($validatedData['is_head_of_family']){
            $existingHead = Resident::where('house_id', $resident->house_id)
                ->where('is_head_of_family', true)
                ->where('id', '!=', $request->id)
                ->first();
            
            if($existingHead){
                return ['status'=>'error','message'=>'Sudah ada kepala keluarga di rumah ini: '.$existingHead->full_name];
            }
        }
        
        // Auto-set is_active_resident based on move_out_date
        $validatedData['is_active_resident'] = empty($request->move_out_date) ? true : false;

        // Handle KTP photo upload
        if($request->file('ktp_photo')){
            // Delete old image
            if($resident->ktp_photo){
                $image_path = public_path().'/storage/img/ktp/'.$resident->ktp_photo;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('ktp_photo'));
            
            // Resize image
            $maxwidth = 800;
            $maxheight = 600;
            if ($image->width() > $image->height()) {
                if ($image->width() > $maxwidth) {
                    $newheight = $image->height() / ($image->width() / $maxwidth);
                    $image->resize($maxwidth, $newheight);
                }
            } else {
                if ($image->height() > $maxheight) {
                    $newwidth = $image->width() / ($image->height() / $maxheight);
                    $image->resize($newwidth, $maxheight);
                }
            }

            // Convert to .webp
            $imageWebp = $image->toWebp(80);
            
            // Upload new image
            $validatedData['ktp_photo'] = $validatedData['id'].'-'.time().".webp";
            $imageWebp->save('storage/img/ktp/'.$validatedData['ktp_photo']);
        }

        // Update data
        $resident->update($validatedData);    
        return ['status'=>'success','message'=>'Penghuni berhasil diedit'];
    }

    public function destroy(Request $request){
        
        $validatedData = $request->validate([
            'id' => 'required|numeric',
        ]);
        
        $resident = Resident::find($request->id);
        
        // Check if the data is found
        if (!$resident) {
            return ['status' => 'error', 'message' => 'Penghuni tidak ditemukan'];
        }

        // Delete KTP photo if exists
        if($resident->ktp_photo){
            $image_path = public_path().'/storage/img/ktp/'.$resident->ktp_photo;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $resident->delete();
        return ['status' => 'success', 'message' => 'Penghuni berhasil dihapus'];
    }

}
