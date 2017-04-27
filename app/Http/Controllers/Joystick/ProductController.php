<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\ImageTrait;
use App\Category;
use App\Company;
use App\Product;
use App\Option;
use Storage;

class ProductController extends Controller
{
    use ImageTrait;

    protected $file;

    public function index()
    {
        $products = Product::orderBy('created_at')->paginate(50);

        return view('joystick-admin.products.index', ['products' => $products]);
    }

    public function create()
    {
        $categories = Category::get()->toTree();
        $companies = Company::get();
        $options = Option::all();

        return view('joystick-admin.products.create', ['categories' => $categories, 'companies' => $companies, 'options' => $options]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:products',
	        'category_id' => 'required|numeric',
	        // 'images' => 'mimes:jpeg,jpg,png,svg,svgs,bmp,gif',
        ]);

        $category = Category::findOrFail($request->category_id);
        $introImage = null;
        $images = [];

        $dirName = $category->id.'/'.time();

        if ( ! file_exists('img/products/'.$dirName)) {
            Storage::makeDirectory('img/products/'.$dirName);
        }

        if ($request->hasFile('images')) {

            $i = 0;

            foreach ($request->file('images') as $key => $image)
            {
                if (isset($image)) {

                    $imageName = 'image-'.$key.'-'.str_random(10).'.'.$image->getClientOriginalExtension();

                    // Creating preview image
                    if ($i == 0) {
                        $i++;
            			$this->resizeImage($image, 360, 360, 'img/products/'.$dirName.'/preview-'.$imageName, 100);
                        $introImage = 'preview-'.$imageName;
                    }

                    // Creating images
            		$this->resizeImage($image, 800, 600, 'img/products/'.$dirName.'/'.$imageName, 100);

                    // Creating mini images
            		$this->resizeImage($image, 130, 130, 'img/products/'.$dirName.'/mini-'.$imageName, 100);

                    $images[$key]['image'] = $imageName;
                    $images[$key]['mini_image'] = 'mini-'.$imageName;
                }
            }
        }

        $product = new Product;
        $product->sort_id = ($request->sort_id > 0) ? $request->sort_id : $product->count() + 1;
        $product->category_id = $request->category_id;
        $product->slug = str_slug($request->title);
        $product->title = $request->title;
        $product->company_id = $request->company_id;
        $product->price = $request->price;
        $product->days = $request->days;
        $product->count = $request->count;
        $product->condition = $request->condition;
        $product->presense = $request->presense;
        $product->description = $request->description;
        $product->characteristic = $request->characteristic;
        $product->image = $introImage;
        $product->images = serialize($images);
        $product->path = $dirName;
        $product->lang = 'ru';
        $product->status = ($request->status == 'on') ? 1 : 0;
        $product->save();

        $product->options()->attach($request->options_id);

        return redirect('admin/products')->with('status', 'Товар добавлен!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::get()->toTree();
        $companies = Company::get();
        $options = Option::all();

        return view('joystick-admin.products.edit', ['product' => $product, 'categories' => $categories, 'companies' => $companies, 'options' => $options]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
	        'category_id' => 'required|numeric',
        ]);

        $product = Product::findOrFail($id);

        if ($request->hasFile('images')) {

            $i = 0;
            $introImage = null;
            $images = unserialize($product->images);

            if ( ! file_exists('img/products/'.$product->path)) {
                Storage::makeDirectory('img/products/'.$product->path);
            }

            foreach ($request->file('images') as $key => $image)
            {
                if (isset($image))
                {
                    $imageName = 'image-'.$key.'-'.str_random(10).'.'.$image->getClientOriginalExtension();

                    // Creating preview image
                    if ($i == 0) {

                        if ($product->image != NULL AND file_exists('img/products/'.$product->path.'/'.$product->image)) {
                            Storage::delete('img/products/'.$product->path.'/'.$product->image);
                        }

                        $i++;
            			$this->resizeImage($image, 360, 360, 'img/products/'.$product->path.'/preview-'.$imageName, 100);
                        $introImage = 'preview-'.$imageName;
                    }

                    // Creating images
            		$this->resizeImage($image, 800, 600, 'img/products/'.$product->path.'/'.$imageName, 100);

                    // Creating mini images
            		$this->resizeImage($image, 130, 130, 'img/products/'.$product->path.'/mini-'.$imageName, 100);

                    if (isset($images[$key])) {

                        Storage::delete([
                            'img/products/'.$product->path.'/'.$images[$key]['image'],
                            'img/products/'.$product->path.'/'.$images[$key]['mini_image']
                        ]);

                        $images[$key]['image'] = $imageName;
                        $images[$key]['mini_image'] = 'mini-'.$imageName;
                    }
                    else {
                        $images[$key]['image'] = $imageName;
                        $images[$key]['mini_image'] = 'mini-'.$imageName;
                    }
                }
            }

            $images = array_sort_recursive($images);
            $images = serialize($images);
        }

        $product->sort_id = ($request->sort_id > 0) ? $request->sort_id : $product->count() + 1;
        $product->category_id = $request->category_id;
        $product->slug = str_slug($request->title);
        $product->title = $request->title;
        $product->company_id = $request->company_id;
        $product->price = $request->price;
        $product->days = $request->days;
        $product->count = $request->count;
        $product->condition = $request->condition;
        $product->presense = $request->presense;
        $product->description = $request->description;
        $product->characteristic = $request->characteristic;

        if (isset($introImage)) $product->image = $introImage;
        if (isset($images)) $product->images = $images;

        $product->status = ($request->status == 'on') ? 1 : 0;
        $product->save();

        $product->options()->sync($request->options_id);

        return redirect('admin/products')->with('status', 'Товар обновлен!');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if (! empty($product->images))
        {
            $images = unserialize($product->images);

            foreach ($images as $image)
            {
                if ($product->image != NULL AND file_exists('img/products/'.$product->path.'/'.$product->image)) {
                    Storage::delete('img/products/'.$product->path.'/'.$product->image);
                }

                Storage::delete([
                    'img/products/'.$product->path.'/'.$image['image'],
                    'img/products/'.$product->path.'/'.$image['mini_image']
                ]);
            }

            Storage::deleteDirectory('img/products/'.$product->path);
        }

        $product->delete();

        return redirect('/admin/products');
    }

}