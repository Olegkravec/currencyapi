<?php


namespace App\Filters;

use Illuminate\Support\Facades\Auth;
use JeroenNoten\LaravelAdminLte\Menu\Builder;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class MainMenuPermissionFilter
{
    /**
     * Filter that just checks if the current user has permission see menu item
     * @param $item
     * @param Builder $builder
     * @return bool
     */
    public function transform($item, Builder $builder)
    {
        if (isset($item['permissions']) && !Auth::user()->can($item['permissions'])) {
            return false;
        }

        return $item;
    }
}