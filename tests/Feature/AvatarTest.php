<?php

namespace Bitfumes\Breezer\Tests\Feature;

use Bitfumes\Breezer\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function api_can_update_user_avatar_field()
    {
        Storage::fake('public');

        $user  = $this->authUser(['avatar' => null]);
        $image = $this->createBase64Image();

        $res  = $this->patchJson(route('user.update'), [
            'email'   => 'abc@def.com',
            'avatar'  => $image,
        ]);

        $this->assertNotNull($user->fresh()->avatar);

        $path     = config('breezer.avatar.path');
        $disk     = config('breezer.avatar.disk');
        Storage::disk('public')->assertExists("{$user->avatar}.jpg");

        $this->assertEquals($user->name, json_decode($res->getContent())->data->name);
    }

    /** @test */
    public function api_can_delete_old_avatar_from_disk_and_create_new_avatar()
    {
        Storage::fake('public');

        $user  = $this->authUser(['avatar' => null]);
        $image = $this->createBase64Image();

        $res   = $this->patchJson(route('user.update'), [
            'email'   => 'abc@def.com',
            'avatar'  => $image,
        ]);

        $path      = config('breezer.avatar.path');
        $oldAvatar = $user->avatar;
        Storage::disk('public')->assertExists("{$oldAvatar}.jpg");

        $res   = $this->patchJson(route('user.update'), [
            'email'   => 'abc@def.com',
            'avatar'  => $image,
        ]);

        Storage::disk('public')->assertMissing("{$oldAvatar}.jpg");
        Storage::disk('public')->assertMissing("{$oldAvatar}_thumb.jpg");
        Storage::disk('public')->assertExists("{$user->avatar}.jpg");
        Storage::disk('public')->assertExists("{$user->avatar}_thumb.jpg");

        $this->assertNotNull($user->fresh()->avatar);

        $this->assertEquals($user->name, json_decode($res->getContent())->data->name);
    }

    protected function createBase64Image()
    {
        $image  = \Illuminate\Http\Testing\File::image('image.jpg');
        return base64_encode(file_get_contents($image));
    }
}
