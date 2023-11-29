<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;

class ShortUrlController extends Controller
{
    /**
     * Список коротких ссылок.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(ShortUrl::all());
    }

    /**
     * Форма для создания короткой ссылки в админке.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        view('auth.short_url.create');
    }

    /**
     * Сохранить новую короткую ссылку.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messageError = [];

        if (!filter_var($request->long_url, FILTER_VALIDATE_URL)) {
            $messageError[] = 'невалидная ссылка';
        }
        // if (!empty($request->short_url) AND !filter_var($request->short_url, FILTER_VALIDATE_URL)) {
        //     $messageError[] = 'невалидная короткая ссылка';
        // }
        if (substr_count($request->short_url, '/') > 2) {
            $messageError[] = 'слэши в короткой ссылке не допустимы';
        }
        if (strlen($request->short_url) > 100) {
            $messageError[] = 'недопустимая длина короткой ссылки';
        }
        
        if ($messageError) {
            return response()->json([
                'status' => 'error',
                'message' => implode(", ", $messageError)
            ]);
        }

        if ($shortUrl = ShortUrl::where('short_url', $request->short_url)->first()) {
            return response()->json([
                'status' => 'exists',
                'message' => 'короткая ссылка уже используеться',
                'id' => $shortUrl->id
            ]);
        }

        $short_url = $request->short_url;

        if (empty($short_url)) {
            $str = '';
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';

            do {                
                $str = substr(str_shuffle($permitted_chars), 0, 10);
                $short_url = $str;
            } while (ShortUrl::where('short_url', $short_url)->first());
        }

        $shortUrl = new ShortUrl;
        $shortUrl->long_url = $request->long_url;
        $shortUrl->short_url = $short_url;
        $shortUrl->number_views = 0;
        $shortUrl->save();

        return response()->json($shortUrl);
    }

    /**
     * Получить короткую ссылку
     *
     * @param  \App\Models\ShortUrl  $shortUrl
     * @return \Illuminate\Http\Response
     */
    public function show(ShortUrl $shortUrl)
    {
        return response()->json($shortUrl);
    }

    /**
     * Форма для обновления короткой ссылки для админки.
     *
     * @param  \App\Models\ShortUrl  $shortUrl
     * @return \Illuminate\Http\Response
     */
    public function edit(ShortUrl $shortUrl)
    {
        view('auth.short_url.update');
    }

    /**
     * Обновить короткую ссылку.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShortUrl  $shortUrl
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShortUrl $shortUrl)
    {
        // конфлик пользователей - из за занятости короткой ссылки -> обновление только основной ссылки
        $messageError = [];

        if (isset($request->long_url) AND !empty($request->long_url) AND !filter_var($request->long_url, FILTER_VALIDATE_URL)) {
            $messageError[] = 'невалидная ссылка';
        }
        // if (isset($request->short_url) AND !empty($request->short_url) AND !filter_var($request->short_url, FILTER_VALIDATE_URL)) {
        //     $messageError[] = 'невалидная короткая ссылка';
        // }
        if (isset($request->short_url) AND substr_count($request->short_url, '/') > 2) {
            $messageError[] = 'слэши в короткой ссылке не допустимы';
        }
        if (isset($request->short_url) AND strlen($request->short_url) > 100) {
            $messageError[] = 'недопустимая длина короткой ссылки';
        }
        
        if ($messageError) {
            return response()->json([
                'status' => 'error',
                'message' => implode(", ", $messageError)
            ]);
        }

        if (ShortUrl::where('short_url', $request->short_url)->first()) {
            return response()->json([
                'status' => 'exists',
                'message' => 'короткая ссылка уже используеться'
            ]);
        }

        if (isset($request->long_url)) {
            $shortUrl->long_url = $request->long_url;
        }
        if (isset($request->short_url)) {
            $shortUrl->short_url = $request->short_url;
        }
        $shortUrl->save();

        return response()->json($shortUrl);
    }

    /**
     * Удалить короткую ссылку.
     *
     * @param  \App\Models\ShortUrl  $shortUrl
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShortUrl $shortUrl)
    {
        ShortUrl::destroy($shortUrl->id);
    }

    /**
     * Переадрисация по короткой ссылке.
     * @param  string $short_url
     */
    public function redirect($short_url)
    {
        if ($shortUrl = ShortUrl::where('short_url', $short_url)->first()) {
            $shortUrl->number_views = ++$shortUrl->number_views;
            $shortUrl->save();
            return redirect()->to($shortUrl->long_url);
        }

        abort(404);
    }
    
    /**
     * Поиск ссылок.
     * @param  \Illuminate\Http\Request  $request
     */
    public function search(Request $request)
    {
        $shortUrlList = ShortUrl::where('short_url', $request->short_url)->orWhere('long_url', $request->long_url)->all();

        return response()->json($shortUrlList);
    }
}
