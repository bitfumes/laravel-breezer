<?php

namespace Bitfumes\Breezer;

use Illuminate\Database\Eloquent\Model;

class SocialProfile extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        $user  = config('breezer.models.user');
        return $this->belongsTo($user);
    }
}
