@section('title', 'Email Settings')
@section('page-title', 'Email Settings')
@section('page-description', 'Configure SMTP email settings')
@section('breadcrumbs')
<span class="text-secondary">Dashboard</span>
<span class="text-secondary">&middot;</span>
<span class="text-secondary">Settings</span>
<span class="text-secondary">&middot;</span>
<span class="font-semibold text-foreground">Email</span>
@endsection

<div>
    @include('livewire.settings.partials.settings-nav')

<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-border p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="size-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <i data-lucide="mail" class="size-5 text-blue-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-foreground">SMTP Configuration</h3>
                <p class="text-xs text-secondary">Configure your outgoing email server</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">SMTP Host</label>
                    <input wire:model="mail_host" type="text" placeholder="smtp.example.com"
                           class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                    @error('mail_host') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">SMTP Port</label>
                    <input wire:model="mail_port" type="text" placeholder="2525"
                           class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                    @error('mail_port') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Username</label>
                    <input wire:model="mail_username" type="text" placeholder="username"
                           class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                    @error('mail_username') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">Password</label>
                    <input wire:model="mail_password" type="password" placeholder="••••••••"
                           class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                    @error('mail_password') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Encryption</label>
                <select wire:model="mail_encryption" class="w-full h-11 pl-4 pr-10 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                    <option value="">None</option>
                    <option value="tls">TLS</option>
                    <option value="ssl">SSL</option>
                </select>
            </div>

            <hr class="border-border">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">From Address</label>
                    <input wire:model="mail_from_address" type="email" placeholder="noreply@helpdesk.com"
                           class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                    @error('mail_from_address') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1.5">From Name</label>
                    <input wire:model="mail_from_name" type="text" placeholder="Helpdesk"
                           class="w-full h-11 px-4 rounded-xl border border-border bg-white text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all duration-200">
                    @error('mail_from_name') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button wire:click="save" class="h-10 px-5 bg-primary hover:bg-primary-hover text-white rounded-xl text-sm font-semibold shadow-md shadow-primary/20 transition-all duration-200 hover:scale-[1.02] active:scale-95 cursor-pointer flex items-center gap-2" wire:loading.attr="disabled">
                    <svg wire:loading wire:target="save" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Save Settings
                </button>
                <button wire:click="testEmail" class="h-10 px-5 rounded-xl border border-border text-sm font-semibold text-secondary hover:text-foreground hover:border-primary transition-all duration-200 cursor-pointer flex items-center gap-2" wire:loading.attr="disabled">
                    <svg wire:loading wire:target="testEmail" class="animate-spin size-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <i data-lucide="send" class="size-4"></i>
                    Send Test Email
                </button>
            </div>
        </div>
    </div>
</div>
</div>
