<?php

namespace Bitfumes\Breezer\Traits;

use Illuminate\Support\Str;
use Bitfumes\Breezer\SocialProfile;
use Bitfumes\Breezer\Helpers\Upload;
use Illuminate\Support\Facades\Storage;

trait Breezer
{
    public static function bootBreezer(): void
    {
        static::retrieved(function ($model) {
            $model->fillable = array_merge($model->fillable, ['avatar']);
        });
    }

    // /**
    //  * Send the email verification notification.
    //  *
    //  * @return void
    //  */
    public function sendEmailVerificationNotification()
    {
        $verify = app()['config']['breezer.notifications.verify'];
        $this->notify(new $verify());
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $reset_notification = app()['config']['breezer.notifications.reset'];
        $this->notify(new $reset_notification($token));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function social()
    {
        return $this->hasMany(SocialProfile::class);
    }

    public function uploadProfilePic($image)
    {
        $path     = config('breezer.avatar.path');
        $disk     = config('breezer.avatar.disk');
        $height   = config('breezer.avatar.thumb_height');
        $width    = config('breezer.avatar.thumb_width');
        $filename = $path . '/' . Str::random();
        if ($this->avatar) {
            Storage::disk($disk)->delete("{$this->avatar}.jpg");
            Storage::disk($disk)->delete("{$this->avatar}_thumb.jpg");
        }
        $big    = Upload::resize($image, 400);
        $thumb  = Upload::resize($image, $width, $height);
        Storage::disk($disk)->put("{$filename}.jpg", $big);
        Storage::disk($disk)->put("{$filename}_thumb.jpg", $thumb);
        $this->update(['avatar' => "{$filename}"]);
    }
}
