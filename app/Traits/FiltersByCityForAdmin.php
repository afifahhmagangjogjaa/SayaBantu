<?php

namespace App\Traits;

trait FiltersByCityForAdmin
{
    /**
     * Apply city filter for admin users
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $cityColumn Column name for city_id (default: 'city_id')
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyCityFilter($query, $cityColumn = 'city_id')
    {
        $user = auth()->user();
        
        if ($user && $user->role === 'admin') {
            $cityIds = $user->getAdminCityIds();
            if (!empty($cityIds)) {
                $query->whereIn($cityColumn, $cityIds);
            }
        }
        
        return $query;
    }

    /**
     * Apply city filter via relationship for admin users
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $relationName Name of the relationship
     * @param string|null $cityColumn Column name for city_id in the related table (default: 'city_id')
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyCityFilterViaRelation($query, $relationName, $cityColumn = 'city_id')
    {
        $user = auth()->user();
        
        if ($user && $user->role === 'admin') {
            $cityIds = $user->getAdminCityIds();
            if (!empty($cityIds)) {
                $query->whereHas($relationName, function ($q) use ($cityColumn, $cityIds) {
                    $q->whereIn($cityColumn, $cityIds);
                });
            }
        }
        
        return $query;
    }

    /**
     * Check if current user is admin with city restriction
     * 
     * @return bool
     */
    protected function isAdminWithCityRestriction()
    {
        $user = auth()->user();
        return $user && $user->role === 'admin' && !empty($user->getAdminCityIds());
    }

    /**
     * Get current admin's city_id
     * 
     * @return int|null
     */
    protected function getAdminCityId()
    {
        $user = auth()->user();
        return ($user && $user->role === 'admin') ? ($user->city_id ?? ($user->getAdminCityIds()[0] ?? null)) : null;
    }
}
