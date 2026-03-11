<x-filament-panels::page>
    <div style="min-width: 1200px; margin: 0 auto; background: white; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <form wire:submit="save">
            {{ $this->form }}

            <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 12px;">
                <x-filament::button type="submit" color="primary">
                    Save Settings
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
