<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Profession;
use App\Models\Speaker;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Maatwebsite\Excel\Facades\Excel;

class AdminCourse extends Controller
{

    public function index(){
        return view('admin.course',[
            "title" => "Admin | Course Data",
            "courses" => Course::orderBy("id","DESC")->get(),
            "categories" => CourseCategory::orderBy("name","ASC")->get(),
            "professions" => Profession::orderBy("id","ASC")->get(),
            "speakers" => Speaker::orderBy("name","ASC")->get(),
        ]);
    }
    
    public function courseDetail(Course $course){
        return view('admin.course-detail', [
            "title" => "Course | " . $course->name,
            "course" => $course,
            "categories" => CourseCategory::orderBy("name", "ASC")->get(),
            "professions" => Profession::orderBy("id", "ASC")->get(),
            "speakers" => Speaker::orderBy("name", "ASC")->get(),
            "userCourses" => UserCourse::whereCourseId($course->id)->orderBy("id","ASC")->get(),
        ]);
    }

    public function customer(){
        return view('admin.course-customer',[
            "courses" => Course::orderBy("name","ASC")->get(),
            "title" => "Admin | Course In Customer",
            "userCourses" => UserCourse::orderBy("id","DESC")->get(),
        ]);
    }
    public function filter(Request $request){
        $validated = $request->validate([
            'course_id' => 'nullable|exists:courses,id',
        ]);
        $course_id = $request->course_id;
        return view('admin.course-customer',[
            "courses" => Course::orderBy("name","ASC")->get(),
            "title" => "Admin | Course In Customer",
            "userCourses" => UserCourse::where("course_id",$course_id)->orderBy("id","DESC")->get(),
        ]);
    }

    public function postHandler(Request $request){
        //dd($request);
        if($request->submit=="store"){
            $res = $this->store($request);
            return back()->with($res['status'],$res['message']);
        }
        if($request->submit=="update"){
            $res = $this->update($request);
            return back()->with($res['status'],$res['message']);
        }
        if($request->submit=="destroy"){
            $res = $this->softDelete($request);
            return back()->with($res['status'],$res['message']);
            // return back()->with("info","Fitur hapus sementara dinonaktifkan");
        }
    }

    public function store(Request $request){
        
        $validatedData = $request->validate([
            'profession_id' => 'required|integer',
            'category_id' => 'required|integer',
            'name' => 'required',
            'code' => 'required|unique:courses,code',
            'description' => 'required',
            'instructor' => 'required',
            'speaker' => 'nullable',
            'duration' => 'required',
            'enrolled' => 'required|integer',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric|min:0|max:100',
            'currency' => 'required|string|size:3',
            'post_date' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'url_youtube' => 'nullable',
            'url_zoom' => 'nullable',
            'url_material' => 'nullable',
            'image' => 'required|image|file|max:1024',
            'location' => 'required',
            'position' => 'required',
            'type' => 'required',
            'article' => 'required',
            'pinned' => 'nullable',
            'status' => 'nullable',
        ]);        
        
        //Read image
        $manager = new ImageManager(new Driver());
        $image = $manager->read($request->file('image'));
        
        // Resize image
        $maxwidth = 800;
        $maxheight = 450;
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

        //Convert to .webp
        $imageWebp = $image->toWebp(100);
        
        // Upload new image
        $validatedData['image'] = time().".webp";
        $imageWebp->save('storage/img/course/'.$validatedData['image']);
        
        Course::create($validatedData);
        return ['status'=>'success','message'=>'Course berhasil ditambahkan'];

    }

    public function update(Request $request){
        $validatedData = $request->validate([
            'id'=>'required|numeric',
            'profession_id' => 'required|integer',
            'category_id' => 'required|integer',
            'name' => 'required',
            'code' => 'required',
            'description' => 'required',
            'instructor' => 'required',
            'speaker' => 'nullable',
            'duration' => 'required',
            'enrolled' => 'required|integer',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric|min:0|max:100',
            'currency' => 'required|string|size:3',
            'post_date' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'url_youtube' => 'nullable',
            'url_zoom' => 'nullable',
            'url_material' => 'nullable',
            'image' => 'image|file|max:1024',
            'location' => 'required',
            'position' => 'required',
            'type' => 'required',
            'article' => 'required',
            'pinned' => 'nullable',
            'status' => 'nullable',
        ]);
        
        $course = Course::find($request->id);

        //Check if the data is found
        if(!$course){
            return ['status'=>'error','message'=>'Course tidak ditemukan'];
        }
        
        // Check Code
        if ($request->code !== $course->code && Course::where('code', $request->code)->exists()) {
            return ['status' => 'error', 'message' => 'Kode telah terpakai'];
        }

        //Check if has image
        if($request->file('image')){

            // Delete old image
            $image_path = public_path().'/storage/img/course/'.$course->image;
            if (file_exists($image_path)) {
                unlink($image_path); // Delete the image file
            }
            
            //Read image
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('image'));
            
            // Resize image
            $maxwidth = 800;
            $maxheight = 450;
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

            //Convert to .webp
            $imageWebp = $image->toWebp(100);
            
            // Upload new image
            $validatedData['image'] = $validatedData['id'].'-'.time().".webp";
            $imageWebp->save('storage/img/course/'.$validatedData['image']);
            
            $course->update($validatedData);
            return ['status'=>'success','message'=>'Course berhasil diupdate'];
            
        }else{
            // Update data
            $course->update($validatedData);    
            return ['status'=>'success','message'=>'Course berhasil diedit'];
        }
        
    }

    public function destroy(Request $request){
        
        $validatedData = $request->validate([
            'id' => 'required|numeric',
        ]);

        $course = Course::find($request->id);

        // Check if the data is found
        if (!$course) {
            return ['status' => 'error', 'message' => 'Course tidak ditemukan'];
        }

        $image_path = public_path().'/storage/img/course/'.$course->image;

        // Check if the image file exists before attempting to delete it
        if (file_exists($image_path)) {
            unlink($image_path); // Delete the image file
        }

        Course::destroy($request->id);
        return ['status' => 'success', 'message' => 'Course berhasil dihapus'];
    }

    public function softDelete(Request $request){
        
        $validatedData = $request->validate([
            'id' => 'required|numeric',
        ]);
        
        $course = Course::find($request->id);
        
        // Check if the data is found
        if (!$course) {
            return ['status' => 'error', 'message' => 'Course tidak ditemukan'];
        }
        
        $course->delete();
        return ['status' => 'success', 'message' => 'Course berhasil dihapus'];
    }

}
