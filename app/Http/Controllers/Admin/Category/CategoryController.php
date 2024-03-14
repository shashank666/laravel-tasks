<?php

namespace App\Http\Controllers\Admin\Category;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use App\Model\Category;
use App\Model\Post;
use App\Model\CategoryPost;
use DB;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        View::share('menu','category');
    }

    public function showCategories(Request $request){
        /* $categories = DB::table('categories')
        ->join('category_posts', 'categories.id', '=', 'category_posts.category_id')
        ->select('categories.*',DB::raw('COUNT(category_posts.post_id) as posts_count'))
        ->groupBy('categories.id')
        ->get(); */
        $categories = Category::withCount(['followers','posts','threads'])->get();

        if($request->has('json') && $request->query('json')==1){
            return response()->json(array('categories'=>$categories));
        }
        return view('admin.dashboard.category.index',compact('categories'));
    }

    public function showPostsByCategory(Request $request,$categoryid){
        $post_category=Category::find($categoryid);
        if($post_category){

            $posts=CategoryPost::where('category_id',$post_category->id)->with('post')->paginate(50);
            $active_count=CategoryPost::where(['category_id'=>$post_category->id,'is_active'=>1])->count();
            $disabled_count=CategoryPost::where(['category_id'=>$post_category->id,'is_active'=>0])->count();

            if($request->has('json') && $request->query('json')==1){
                return response()->json(array('posts'=>$posts));
            }
          return view('admin.dashboard.category.posts',compact('post_category','posts','active_count','disabled_count'));
        }
    }

    public function showAddCategoryForm(){
        return view('admin.dashboard.category.create');
    }

    public function showEditCategoryForm(Request $request,$categoryid){
        $category=Category::find($categoryid);
        if($category){
            if($request->has('json') && $request->query('json')==1){
                return response()->json(array('category'=>$category));
            }
            return view('admin.dashboard.category.edit',compact('category'));
        }else{
            return view('admin.error.404');
        }
    }

    public function createCategory(Request $request){
        $this->validate($request,[
            'name'=>'required',
            'category_group'=>'required',
            'imagefile'=>'nullable|max:50000'
        ]);

        $name=$request->input('name');
        $description=$request->input('description');
        if($request->hasFile('imagefile')){
            $imageurl=$this->uploadCategoryImage($request->file('imagefile'));
        }else{
           $imageurl=$request->has('imageurl')?$request->input('imageurl'):null;
        }
        $slug = str::slug($request->input('name'),'-');
        $group=$request->has('category_group')?$request->input('category_group'):'DEFAULT';

        $category=new Category();
        $this->saveCategory($category,$name,$slug,$description,$group,$imageurl,0);
        return redirect()->route('admin.categories');
    }

    public function updateCategory(Request $request){
        $this->validate($request,[
            'name'=>'required',
            'category_group'=>'required',
            'imagefile'=>'nullable|max:50000'
        ]);
        $name=$request->input('name');
        $description=$request->input('description');
        if($request->hasFile('imagefile')){
            $imageurl=$this->uploadCategoryImage($request->file('imagefile'));
        }else{
            $imageurl=$request->has('imageurl')?$request->input('imageurl'):null;
        }
        $is_active=$request->input('is_active')=='on'?1:0;
        $slug = str::slug($request->input('name'),'-');
        $group=$request->has('category_group')?$request->input('category_group'):'DEFAULT';
        $category = Category::find($request->input('categoryid'));
        $this->saveCategory($category,$name,$slug,$description,$group,$imageurl,$is_active);
        return redirect()->route('admin.categories');
    }

    protected function uploadCategoryImage($file){
        $filenameWithExt=$file->getClientOriginalName();
        $filename=pathinfo($filenameWithExt,PATHINFO_FILENAME);
        $extension=$file->getClientOriginalExtension();
        $fileNameToStore=$filename.'_'.time().'.'.$extension;
        $imageurl= url('/storage/category/'.$fileNameToStore);
        $file->storeAs('public/category',$fileNameToStore);
        return $imageurl;
    }

    protected function saveCategory(Category $category,$name,$slug,$description,$group,$imageurl,$is_active){
        $category->name=$name;
        $category->slug= $slug;
        $category->description=$description;
        $category->category_group=$group;
        $category->image=$imageurl;
        $category->is_active=$is_active;
        $category->save();
    }

    public function deleteCategory(Request $request){
        return;
    }


}
