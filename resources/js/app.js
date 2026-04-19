import './bootstrap';
import * as Turbo from '@hotwired/turbo';
import Alpine from 'alpinejs';

window.Turbo = Turbo;
window.Alpine = Alpine;

Turbo.start();
Alpine.start();
