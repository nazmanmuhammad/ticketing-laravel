<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.master')]
class GeneralSettings extends Component
{
    use WithFileUploads;

    public string $app_name = '';
    public string $app_title = '';
    public string $app_description = '';
    public $logo;
    public $favicon;

    public function mount(): void
    {
        $this->app_name = Setting::getValue('app_name', config('app.name', 'Helpdesk'));
        $this->app_title = Setting::getValue('app_title', 'IT Helpdesk System');
        $this->app_description = Setting::getValue('app_description', 'Streamline your workflows, ensure compliance, and accelerate results.');
    }

    public function saveBranding(): void
    {
        $this->validate([
            'app_name' => 'required|string|max:255',
            'app_title' => 'required|string|max:255',
            'app_description' => 'nullable|string|max:500',
        ]);

        Setting::setValue('app_name', $this->app_name);
        Setting::setValue('app_title', $this->app_title);
        Setting::setValue('app_description', $this->app_description);

        $this->dispatch('toast', type: 'success', message: 'Branding settings saved');
    }

    public function uploadLogo(): void
    {
        $this->validate(['logo' => 'required|image|max:2048']);

        $oldLogo = Setting::getValue('app_logo');
        if ($oldLogo) {
            Storage::disk('public')->delete($oldLogo);
        }

        $path = $this->logo->store('branding', 'public');
        Setting::setValue('app_logo', $path);
        $this->logo = null;

        $this->dispatch('toast', type: 'success', message: 'Logo uploaded successfully');
    }

    public function removeLogo(): void
    {
        $oldLogo = Setting::getValue('app_logo');
        if ($oldLogo) {
            Storage::disk('public')->delete($oldLogo);
            Setting::setValue('app_logo', null);
        }
        $this->dispatch('toast', type: 'success', message: 'Logo removed');
    }

    public function uploadFavicon(): void
    {
        $this->validate(['favicon' => 'required|image|max:1024']);

        $oldFav = Setting::getValue('app_favicon');
        if ($oldFav) {
            Storage::disk('public')->delete($oldFav);
        }

        $path = $this->favicon->store('branding', 'public');
        Setting::setValue('app_favicon', $path);
        $this->favicon = null;

        $this->dispatch('toast', type: 'success', message: 'Favicon uploaded successfully');
    }

    public function removeFavicon(): void
    {
        $oldFav = Setting::getValue('app_favicon');
        if ($oldFav) {
            Storage::disk('public')->delete($oldFav);
            Setting::setValue('app_favicon', null);
        }
        $this->dispatch('toast', type: 'success', message: 'Favicon removed');
    }

    public function render()
    {
        return view('livewire.settings.general-settings', [
            'currentLogo' => Setting::getValue('app_logo'),
            'currentFavicon' => Setting::getValue('app_favicon'),
        ]);
    }
}
