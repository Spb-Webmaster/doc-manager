<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\View\View;

/**
 * Публичная главная страница сайта.
 */
class HomeController extends Controller
{
    /**
     * GET / — главная страница.
     *
     * Берёт контент из настроек (группа 'home') и передаёт текущего
     * пользователя (или false для гостя). Возвращает view 'home'.
     */
    public function index():View
    {
        $home = Setting::getGroup('home')->data;

        if(auth()->check()) {
            $user = auth()->user();
        } else {
            $user = false;
        }



        return view('home', [
                'user' => $user,
                'home' => $home,
            ]
        );
    }
}
