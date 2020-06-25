<?php


namespace App\Filters;

use Illuminate\Support\Facades\Auth;
use JeroenNoten\LaravelAdminLte\Menu\Builder;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class MainMenuPermissionFilter
{
    public function transform($item, Builder $builder)
    {
        if (isset($item['permissions']) && !Auth::user()->can($item['permissions'])) {
            return false;
        }

        return $item;
    }
}