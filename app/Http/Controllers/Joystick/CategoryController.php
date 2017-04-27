<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::get()->toTree();

        return view('joystick-admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::get()->toTree();

        return view('joystick-admin.categories.create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:categories',
        ]);

        $category = new Category;

        $category->sort_id = ($request->sort_id > 0) ? $request->sort_id : $category->count() + 1;
        $category->slug = (empty($request->slug)) ? str_slug($request->title) : $request->slug;
        $category->title = $request->title;
        $category->title_description = $request->title_description;
        $category->meta_description = $request->meta_description;

        $parent_node = Category::find($request->category_id);

        if (is_null($parent_node)) {
            $category->saveAsRoot();
        }
        else {
            $category->appendToNode($parent_node)->save();
        }

        $category->lang = $request->lang;
        $category->save();

        return redirect('/admin/categories')->with('status', 'Запись добавлена.');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::get()->toTree();

        return view('joystick-admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $category = Category::findOrFail($id);
        $category->sort_id = ($request->sort_id > 0) ? $request->sort_id : $category->count() + 1;
        $category->slug = (empty($request->slug)) ? str_slug($request->title) : $request->slug;
        $category->title = $request->title;
        $category->title_description = $request->title_description;
        $category->meta_description = $request->meta_description;

        $parent_node = Category::find($request->category_id);

        if (is_null($parent_node)) {
            $category->saveAsRoot();
        }
        elseif ($category->id != $request->category_id) {
            $category->appendToNode($parent_node)->save();
        }

        $category->lang = $request->lang;
        $category->save();

        return redirect('/admin/categories')->with('status', 'Запись обновлена.');
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        $category->delete();

        return redirect('/admin/categories')->with('status', 'Запись удалена.');
    }
}
