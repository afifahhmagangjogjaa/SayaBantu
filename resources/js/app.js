import './bootstrap';

// Do NOT import Alpine here to avoid loading two Alpine instances.
// Livewire's bundled script already includes Alpine; loading Alpine twice
// causes the "Detected multiple instances of Alpine running" warning
// and can interfere with Livewire event handling.

// Alpine and its navigation plugin (wire:navigate) are bundled and initialized natively by Livewire 3.
// We avoid manual overrides to Alpine.navigate to ensure seamless SPA page transitions.
