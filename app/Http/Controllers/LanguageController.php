<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch application language locale.
     */
    public function switchLanguage(string $locale): RedirectResponse
    {
        if (in_array($locale, ['id', 'en'])) {
            Session::put('locale', $locale);
        }

        return redirect()->back();
    }
}
