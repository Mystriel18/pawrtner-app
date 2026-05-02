<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

class FilamentBrand
{
    public static function logo(): HtmlString
    {
        $logoUrl = asset('images/PAWrtner_Logo.png');

        return new HtmlString(<<<HTML
<span class="pawrtner-brand-mark">
    <img alt="PAWrtner" src="{$logoUrl}" class="pawrtner-brand-mark__logo">
    <span class="pawrtner-brand-mark__text">PAWrtner</span>
</span>
HTML);
    }

    public static function stylesheets(): HtmlString
    {
        return new HtmlString(
            '<link rel="stylesheet" href="' . asset('css/pawrtner-glass.css') . '">' .
            '<link rel="stylesheet" href="' . asset('css/pawrtner-brand.css') . '">',
        );
    }
}