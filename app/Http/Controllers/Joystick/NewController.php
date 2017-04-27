<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\News;

class NewController extends Controller
{
    public function index()
    {
        $news = News::orderBy('sort_id')->paginate(50);

        return view('joystick-admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('joystick-admin.news.create');
    }

    public function store(Request $request)
    {    	
        $this->validate($request, [
            'title' => 'required|min:2|max:80|unique:news',
        ]);

        $new = new News;

        $new->sort_id = ($request->sort_id > 0) ? $request->sort_id : $new->count() + 1;
        $new->slug = (empty($request->slug)) ? str_slug($request->title) : $request->slug;
        $new->title = $request->title;
        $new->headline = $request->headline;
        $new->title_description = $request->title_description;
        $new->meta_description = $request->meta_description;
        $new->content = $request->content;
        $new->lang = $request->lang;
        $new->status = ($request->status == 'on') ? 1 : 0;
        $new->save();

        return redirect('/admin/news')->with('status', 'Запись добавлена.');
    }

    public function edit($id)
    {
        $new = News::findOrFail($id);

        return view('joystick-admin.news.edit', compact('new'));
    }

    public function update(Request $request, $id)
    {    	
        $this->validate($request, [
            'title' => 'required|min:2|max:80',
        ]);

        $new = News::findOrFail($id);
        $new->sort_id = ($request->sort_id > 0) ? $request->sort_id : $new->count() + 1;
        $new->slug = (empty($request->slug)) ? str_slug($request->title) : $request->slug;
        $new->title = $request->title;
        $new->headline = $request->headline;
        $new->title_description = $request->title_description;
        $new->meta_description = $request->meta_description;
        $new->content = $request->content;
        $new->lang = $request->lang;
        $new->status = ($request->status == 'on') ? 1 : 0;
        $new->save();

        return redirect('/admin/news')->with('status', 'Запись обновлена.');
    }

    public function destroy($id)
    {
        $new = News::find($id);
        $new->delete();

        return redirect('/admin/news')->with('status', 'Запись удалена.');
    }
}