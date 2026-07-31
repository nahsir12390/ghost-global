<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $phone;
    public $address;
    public $store_name;
    public $store_description;
    public $store_whatsapp;
    public $store_instagram;
    public $store_facebook;
    public $store_website;
    public $current_password;
    public $password;
    public $password_confirmation;
    public $photo;
    public $store_banner;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->address = $user->address;
        $this->store_name = $user->store_name;
        $this->store_description = $user->store_description;
        $this->store_whatsapp = $user->store_whatsapp;
        $this->store_instagram = $user->store_instagram;
        $this->store_facebook = $user->store_facebook;
        $this->store_website = $user->store_website;
    }

    public function updateProfile()
    {
        $user = Auth::user();
        
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'store_name' => 'nullable|string|max:255',
            'store_description' => 'nullable|string|max:1000',
            'store_whatsapp' => 'nullable|string|max:30',
            'store_instagram' => 'nullable|string|max:255',
            'store_facebook' => 'nullable|string|max:255',
            'store_website' => 'nullable|url|max:255',
            'photo' => 'nullable|image|max:10240',
            'store_banner' => 'nullable|image|max:10240',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->address = $validated['address'] ?? null;

        if ($user->isVendor()) {
            $user->store_name = $validated['store_name'] ?? null;
            $user->store_description = $validated['store_description'] ?? null;
            $user->store_whatsapp = $validated['store_whatsapp'] ?? null;
            $user->store_instagram = $validated['store_instagram'] ?? null;
            $user->store_facebook = $validated['store_facebook'] ?? null;
            $user->store_website = $validated['store_website'] ?? null;
        }
        
        $imageService = new ImageUploadService();

        if ($this->photo) {
            $path = $imageService->uploadAndCompress($this->photo, 'profile-photos', 75, 800);
            
            if ($path) {
                // Delete old photo if it exists
                if ($user->profile_photo_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo_path);
                }
                $user->profile_photo_path = $path;
            } else {
                session()->flash('error', 'Failed to upload and compress photo.');
                return;
            }
        }

        if ($user->isVendor() && $this->store_banner) {
            $bannerPath = $imageService->uploadAndCompress($this->store_banner, 'store-banners', 82, 1920);

            if ($bannerPath) {
                if ($user->store_banner_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->store_banner_path);
                }

                $user->store_banner_path = $bannerPath;
            } else {
                session()->flash('error', 'Failed to upload and compress the store banner.');
                return;
            }
        }
        
        $user->save();
        
        session()->flash('success', 'Profile updated successfully.');
        $this->reset(['photo', 'store_banner']);
        $this->resetValidation();
    }

    public function updatePassword()
    {
        $validated = $this->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        session()->flash('success', 'Password updated successfully.');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.profile')
            ->layout('layouts.admin');
    }
}
