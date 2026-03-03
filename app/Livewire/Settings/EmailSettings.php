<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Setting;

#[Layout('layouts.master')]
class EmailSettings extends Component
{
    public string $mail_host = '';
    public string $mail_port = '';
    public string $mail_username = '';
    public string $mail_password = '';
    public string $mail_from_address = '';
    public string $mail_from_name = '';
    public string $mail_encryption = 'tls';

    public function mount(): void
    {
        $this->mail_host = Setting::getValue('mail_host', config('mail.mailers.smtp.host', ''));
        $this->mail_port = Setting::getValue('mail_port', config('mail.mailers.smtp.port', '2525'));
        $this->mail_username = Setting::getValue('mail_username', config('mail.mailers.smtp.username', ''));
        $this->mail_password = Setting::getValue('mail_password', config('mail.mailers.smtp.password', ''));
        $this->mail_from_address = Setting::getValue('mail_from_address', config('mail.from.address', ''));
        $this->mail_from_name = Setting::getValue('mail_from_name', config('mail.from.name', ''));
        $this->mail_encryption = Setting::getValue('mail_encryption', config('mail.mailers.smtp.encryption', 'tls'));
    }

    public function save(): void
    {
        $this->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|string',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
            'mail_encryption' => 'nullable|string',
        ]);

        Setting::setValue('mail_host', $this->mail_host);
        Setting::setValue('mail_port', $this->mail_port);
        Setting::setValue('mail_username', $this->mail_username);
        Setting::setValue('mail_password', $this->mail_password);
        Setting::setValue('mail_from_address', $this->mail_from_address);
        Setting::setValue('mail_from_name', $this->mail_from_name);
        Setting::setValue('mail_encryption', $this->mail_encryption);

        $this->dispatch('toast', type: 'success', message: 'Email settings saved successfully');
    }

    public function testEmail(): void
    {
        try {
            config([
                'mail.mailers.smtp.host' => $this->mail_host,
                'mail.mailers.smtp.port' => (int) $this->mail_port,
                'mail.mailers.smtp.username' => $this->mail_username,
                'mail.mailers.smtp.password' => $this->mail_password,
                'mail.mailers.smtp.encryption' => $this->mail_encryption ?: null,
                'mail.from.address' => $this->mail_from_address,
                'mail.from.name' => $this->mail_from_name,
            ]);

            \Illuminate\Support\Facades\Mail::raw('This is a test email from Helpdesk.', function ($msg) {
                $msg->to(auth()->user()->email)->subject('Helpdesk - Test Email');
            });

            $this->dispatch('toast', type: 'success', message: 'Test email sent to ' . auth()->user()->email);
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'Failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.settings.email-settings');
    }
}
