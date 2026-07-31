<?php

namespace App\Livewire\Profile;

use App\Services\ImageUploadService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class UpdateProfilePhoto extends Component
{
    use WithFileUploads;

    public $photo;
    public $previewUrl;

    public function mount()
    {
        if (auth()->user()->profile_photo_path) {
            $this->previewUrl = asset('storage/' . auth()->user()->profile_photo_path);
        }
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:10240', // 10MB max
        ]);
    }

    public function updatePhoto()
    {
        $this->validate([
            'photo' => 'required|image|max:10240|dimensions:min_width=100,min_height=100',
        ], [
            'photo.required' => 'Please select a photo to upload.',
            'photo.image' => 'The file must be a valid image.',
            'photo.max' => 'The image must not be larger than 10MB.',
            'photo.dimensions' => 'The image must be at least 100x100 pixels.',
        ]);

        try {
            // Delete old photo if it exists
            if (auth()->user()->profile_photo_path) {
                Storage::disk('public')->delete(auth()->user()->profile_photo_path);
            }

            // Compress and store new photo
            $imageService = new ImageUploadService();
            $path = $imageService->uploadAndCompress($this->photo, 'profile-photos', 75, 800);

            if (!$path) {
                session()->flash('error', 'Failed to compress and upload profile picture.');
                return;
            }

            // Update user
            auth()->user()->update([
                'profile_photo_path' => $path
            ]);

            // Update preview URL
            $this->previewUrl = asset('storage/' . $path);
            $this->photo = null;

            session()->flash('success', 'Profile picture updated successfully!');
            $this->dispatch('refresh-user-session');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to upload profile picture: ' . $e->getMessage());
        }
    }

    public function deletePhoto()
    {
        if (auth()->user()->profile_photo_path) {
            Storage::disk('public')->delete(auth()->user()->profile_photo_path);
            auth()->user()->update(['profile_photo_path' => null]);
            $this->previewUrl = null;
            session()->flash('success', 'Profile picture deleted successfully!');
            $this->dispatch('refresh-user-session');
        }
    }

    public function render()
    {
        return view('livewire.profile.update-profile-photo');
    }
}
