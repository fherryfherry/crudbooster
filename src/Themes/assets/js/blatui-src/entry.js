// CRUDBooster ships this pre-bundled (see package.json → build:js).
// Registers the BlatUI engine (theme store, directives, Alpine plugins)
// onto the Alpine instance bundled with Livewire 3, which every CRUDBooster
// host app loads. The `alpine:init` hook is the one moment when plugins and
// directives can still be added — Livewire's Alpine dispatches it right
// before starting.
//
// Do NOT bundle/start a second Alpine here: `blatui.min.js` loads as a plain
// <script> before Livewire, so a self-start would run before <body> exists
// and a second "multiple instances of Alpine" fight with Livewire's.
import { registerBlatUI } from './blatui-core.js';

document.addEventListener('alpine:init', () => registerBlatUI(window.Alpine));