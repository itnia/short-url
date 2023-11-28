<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function index()
    {
        // только для админки
        $urls = Url::all();
        
        return response()->json($urls);
    }

    public function create()
    {
        // только для админки
    }

    public function store(Request $request)
    {
        // Валидация url
        if (!filter_var($request->long_url, FILTER_VALIDATE_URL)) {
            return response()->json([
                'status' => 'error',
            ]);
        }
        if (!empty($request->short_url) AND !filter_var($request->short_url, FILTER_VALIDATE_URL)) {
            return response()->json([
                'status' => 'error',
            ]);
        }

        // ++ валидация short_url - на лишние слэши
        // ++ валидация short_url - на длину

        // Проверка на существования long_url
        if ($res = Url::where('long_url', $request->long_url)->first()) {
            return response()->json([
                'status' => 'exists_long_url',
                'long_url' => $res->long_url,
                'short_url' => $res->short_url,
                'number_views' => $res->number_views,
            ]);
        }

        // Проверка на существования short_url
        if ($res = Url::where('short_url', $request->short_url)->first()) {
            return response()->json([
                'status' => 'exists_short_url',
                'long_url' => $res->long_url,
                'short_url' => $res->short_url,
                'number_views' => $res->number_views,
            ]);
        }

        // Создание записи
        $short_url = $request->short_url;

        if (empty($short_url)) {
            // сгенерировать url
            $str = '';
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';

            do {                
                $str = substr(str_shuffle($permitted_chars), 0, 10);
                $short_url = config('app.url') . '/' . $str;
            } while (Url::where('short_url', $short_url)->first()); // проверка
        }

        $url = new Url;
        $url->long_url = $request->long_url;
        $url->short_url = $short_url;
        $url->number_views = 0;
        $url->save();

        return response()->json([
            'status' => 'created',
            'long_url' =>$url->long_url,
            'short_url' => $url->short_url,
            'number_views' => $url->number_views,
        ]);
    }

    public function show(Url $url)
    {
        $url = Url::find(1); // ?
        return response()->json([
            'status' => 'show',
            'long_url' =>$url->long_url,
            'short_url' => $url->short_url,
            'number_views' => $url->number_views,
        ]);
    }

    public function edit(Url $url)
    {
        // только для админки
    }

    public function update(Request $request, Url $url)
    {
        $url = Url::find(1); // ?
    }

    public function destroy(Url $url)
    {
        $url = Url::find(1); // ?
        $url->delete();
    }

    public function redirect($short_url)
    {
        if (Url::where('short_url', $short_url)->first()) {
            redirect()->to($short_url);
        }

        abort(404);
    }
}
