@section('title', 'General Settings')
@section('page-title', 'General Settings')
@section('page-description', 'Manage application branding and appearance')
@section('breadcrumbs')
<span class="text-secondary">Dashboard</span>
<span class="text-secondary">&middot;</span>
<span class="text-secondary">Settings</span>
<span class="text-secondary">&middot;</span>
<span class="font-semibold text-foreground">General</span>
@endsection

<div>
    @include('livewire.settings.partials.settings-nav')

<div class="max-w-2xl space-y-6">
    <!-- Branding -->
    <div class="bg-white rounded-2xl border border-border p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="size-10 rounded-xl bg-purple-50 flex items-center justify-center">
                <i data-lucide="palette" class="size-5 text-purple-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-foreground">Application Branding</h3>
                <p class="text-xs text-secondary">Set the application name, title, and description</p>
            </div>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Application Name</label>
                <input wire:model="app_name" type="text" placeholder="Helpdesk"
                       class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                @error('app_name') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-secondary mt-1">Displayed in the sidebar and browser tab</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Application Title</label>
                <input wire:model="app_title" type="text" placeholder="IT Helpdesk System"
                       class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                @error('app_title') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-secondary mt-1">Displayed on the login page</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Description</label>
                <textarea wire:model="app_description" rows="2" placeholder="Short description..."
                          class="w-full px-4 py-3 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200 resize-none"></textarea>
                @error('app_description') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <button wire:click="saveBranding" class="h-10 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold shadow-md shadow-primary/20 transition-all duration-200 hover:scale-[1.02] active:scale-95 cursor-pointer flex items-center gap-2" wire:loading.attr="disabled">
                <svg wire:loading wire:target="saveBranding" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Save Branding
            </button>
        </div>
    </div>

    <!-- Logo Upload -->
    <div class="bg-white rounded-2xl border border-border p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="size-10 rounded-xl bg-green-50 flex items-center justify-center">
                <i data-lucide="image" class="size-5 text-green-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-foreground">Logo</h3>
                <p class="text-xs text-secondary">Used in the sidebar and login page</p>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="shrink-0">
                @if($currentLogo)
                <img src="{{ Storage::url($currentLogo) }}" alt="Logo" class="h-16 max-w-[200px] object-contain rounded-lg border border-border p-2">
                @else
                <div class="h-16 w-16 rounded-xl bg-primary flex items-center justify-center">
                    <i data-lucide="headset" class="size-8 text-white"></i>
                </div>
                @endif
            </div>
            <div class="flex-1 space-y-3">
                <div class="flex items-center gap-2">
                    <input wire:model="logo" type="file" accept="image/*" id="logo-upload" class="hidden">
                    <label for="logo-upload" class="inline-flex items-center gap-2 h-10 px-4 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold transition-all duration-200 cursor-pointer">
                        <i data-lucide="upload" class="size-4"></i> Choose Logo
                    </label>
                    @if($currentLogo)
                    <button wire:click="removeLogo" class="h-10 px-4 rounded-xl border border-error text-error text-sm font-semibold hover:bg-error-light transition-all duration-200 cursor-pointer">Remove</button>
                    @endif
                </div>
                @if($logo)
                <div class="flex items-center gap-3">
                    <img src="{{ $logo->temporaryUrl() }}" class="h-10 object-contain rounded border border-border">
                    <button wire:click="uploadLogo" class="h-9 px-4 bg-success text-white rounded-xl text-sm font-semibold hover:opacity-90 transition-all duration-200 cursor-pointer">Upload</button>
                </div>
                @endif
                @error('logo') <p class="text-xs text-error">{{ $message }}</p> @enderror
                <p class="text-xs text-secondary">PNG, SVG, or JPG. Max 2MB. Recommended: 200x50px</p>
            </div>
        </div>
    </div>

    <!-- Favicon Upload -->
    <div class="bg-white rounded-2xl border border-border p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="size-10 rounded-xl bg-orange-50 flex items-center justify-center">
                <i data-lucide="globe" class="size-5 text-orange-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-foreground">Favicon</h3>
                <p class="text-xs text-secondary">The small icon displayed in browser tabs</p>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="shrink-0">
                @if($currentFavicon)
                <img src="{{ Storage::url($currentFavicon) }}" alt="Favicon" class="size-12 object-contain rounded-lg border border-border p-1">
                @else
                <div class="size-12 rounded-xl bg-muted flex items-center justify-center">
                    <i data-lucide="globe" class="size-6 text-secondary"></i>
                </div>
                @endif
            </div>
            <div class="flex-1 space-y-3">
                <div class="flex items-center gap-2">
                    <input wire:model="favicon" type="file" accept="image/*" id="favicon-upload" class="hidden">
                    <label for="favicon-upload" class="inline-flex items-center gap-2 h-10 px-4 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold transition-all duration-200 cursor-pointer">
                        <i data-lucide="upload" class="size-4"></i> Choose Favicon
                    </label>
                    @if($currentFavicon)
                    <button wire:click="removeFavicon" class="h-10 px-4 rounded-xl border border-error text-error text-sm font-semibold hover:bg-error-light transition-all duration-200 cursor-pointer">Remove</button>
                    @endif
                </div>
                @if($favicon)
                <div class="flex items-center gap-3">
                    <img src="{{ $favicon->temporaryUrl() }}" class="size-8 object-contain rounded border border-border">
                    <button wire:click="uploadFavicon" class="h-9 px-4 bg-success text-white rounded-xl text-sm font-semibold hover:opacity-90 transition-all duration-200 cursor-pointer">Upload</button>
                </div>
                @endif
                @error('favicon') <p class="text-xs text-error">{{ $message }}</p> @enderror
                <p class="text-xs text-secondary">PNG or ICO. Max 1MB. Recommended: 32x32px</p>
            </div>
        </div>
    </div>
</div>
</div>
