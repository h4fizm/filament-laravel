<x-filament::page>
    {{ $this->form }}

    <div class="flex justify-end mt-4">
        <x-filament::button wire:click="save" color="primary" icon="heroicon-o-check">
            Save Changes
        </x-filament::button>
    </div>
</x-filament::page>
