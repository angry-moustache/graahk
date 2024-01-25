<?php

namespace App\Livewire\Profiles;

use AngryMoustache\Media\Models\Attachment;
use App\Livewire\Traits\CanToast;
use App\Models\User;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;
    use CanToast;

    public User $user;

    public null | string $username = null;
    public null | string $email = null;

    public null | TemporaryUploadedFile $avatar = null;

    public function mount()
    {
        $this->user = auth()->user();

        $this->username = $this->user->username;
        $this->email = $this->user->email;
    }

    public function update()
    {
        $this->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $this->user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
        ]);

        $this->user->update([
            'username' => $this->username,
            'email' => $this->email,
        ]);

        $this->user = User::find($this->user->id);

        $this->toast('Profile has been saved!');
    }

    public function updatedAvatar()
    {
        $this->validate([
            'avatar' => ['image', 'max:1024'],
        ]);

        $avatar = Attachment::livewireUpload($this->avatar);

        $this->user->avatar()->associate($avatar);
        $this->user->save();

        $this->avatar = null;
        $this->user = User::find($this->user->id);

        $this->toast('Avatar has been updated!');
    }
}
