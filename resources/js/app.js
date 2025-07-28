import './bootstrap';
import Alpine from 'alpinejs';
import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Initialize Livewire
Livewire.start();
