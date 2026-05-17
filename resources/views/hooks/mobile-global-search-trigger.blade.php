@php
    use Filament\Support\Icons\Heroicon;
@endphp

<div class="mlb-mobile-global-search-trigger-ctn">
    <x-filament::icon-button
        color="gray"
        :icon="Heroicon::OutlinedMagnifyingGlass"
        :icon-alias="\Filament\View\PanelsIconAlias::GLOBAL_SEARCH_FIELD"
        icon-size="lg"
        :label="__('filament-panels::global-search.field.label')"
        x-data="{}"
        x-on:click="window.dispatchEvent(new CustomEvent('open-global-search-modal', { detail: { id: 'global-search-modal::plugin' }, bubbles: true }))"
        class="mlb-mobile-global-search-trigger"
    />
</div>
