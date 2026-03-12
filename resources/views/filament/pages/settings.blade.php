<x-filament-panels::page>
    <form wire:submit="save" class="settings-form-wrapper">
        {{ $this->form }}

        <div class="settings-form-actions">
            <x-filament::button type="submit" color="primary">
                Save Settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
