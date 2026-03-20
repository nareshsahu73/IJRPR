<x-filament-panels::page>
    <form wire:submit="save" class="settings-form-wrapper">
        {{ $this->form }}

        <div class="settings-form-actions" style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
            <x-filament::button type="submit" color="primary">
                Save Settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
