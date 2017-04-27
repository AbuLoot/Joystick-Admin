<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Page;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('sort_id')->get();

        return view('joystick-admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('joystick-admin.pages.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:pages',
        ]);

        $page = new Page;

        $page->sort_id = ($request->sort_id > 0) ? $request->sort_id : $page->count() + 1;
        $page->slug = (empty($request->slug)) ? str_slug($request->title) : $request->slug;
        $page->title = $request->title;
        $page->icon = $request->icon;
        $page->title_description = $request->title_description;
        $page->meta_description = $request->meta_description;
        $page->content = $request->content;
        $page->lang = $request->lang;
        $page->status = ($request->status == 'on') ? 1 : 0;
        $page->save();

        return redirect('/admin/pages')->with('status', 'Запись добавлена!');
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);

        return view('joystick-admin.pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {    	
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $page = Page::findOrFail($id);
        $page->sort_id = ($request->sort_id > 0) ? $request->sort_id : $page->count() + 1;
        $page->slug = (empty($request->slug)) ? str_slug($request->title) : $request->slug;
        $page->title = $request->title;
        $page->icon = $request->icon;
        $page->title_description = $request->title_description;
        $page->meta_description = $request->meta_description;
        $page->content = $request->content;
        $page->lang = $request->lang;
        $page->status = ($request->status == 'on') ? 1 : 0;
        $page->save();

        return redirect('/admin/pages')->with('status', 'Запись обновлена!');
    }

    public function destroy($id)
    {
        $page = Page::find($id);
        $page->delete();

        return redirect('/admin/pages')->with('status', 'Запись удалена!');
    }
}
