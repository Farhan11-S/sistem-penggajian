<?php

namespace App\Traits;

trait UseCreatedBy
{
    protected static function bootUseCreatedBy()
    {
        $user = auth()->user();

        static::creating(function ($object) use ($user) {
            $userIdentifier = $user != null ? $user->getAuthIdentifier() : null;
            $object->created_by = $userIdentifier;
        });
        // static::updating(function ($object) {
        //     $object->created_by = auth()->user()->getAuthIdentifier();
        // });`
    }
}
