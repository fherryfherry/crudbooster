import anchor from '@alpinejs/anchor';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';
import { computePosition, autoUpdate, flip, shift, offset as flOffset, size } from '@floating-ui/dom';

// ---------------------------------------------------------------------------
// Theme store — dark mode + color preset + radius, persisted to localStorage.
// Mirrors the data-attributes that resources/css/app.css keys off of.
// ---------------------------------------------------------------------------
const themeStore = {
    // Dark-mode policy — set via registerBlatUI(Alpine, { darkMode }).
    //   'class'  (default) light until an explicit toggle; NEVER auto-applies the OS
    //            prefers-color-scheme (that silently broke light-only apps).
    //   'system' follow the OS preference by default.
    //   false    hard light-only (dark disabled, toggle is a no-op).
    darkMode: 'class',
    // Every dimension shadcn exposes, each persisted independently.
    mode: localStorage.getItem('theme:mode') || 'light',
    base: localStorage.getItem('theme:base') || 'neutral',
    preset: localStorage.getItem('theme:preset') || 'default',
    radius: localStorage.getItem('theme:radius') || '0.625',
    font: localStorage.getItem('theme:font') || 'sans',
    shadow: localStorage.getItem('theme:shadow') || 'default',
    spacing: localStorage.getItem('theme:spacing') || 'default',
    tracking: localStorage.getItem('theme:tracking') || 'normal',
    inputStyle: localStorage.getItem('theme:inputStyle') || 'outline',
    fontHeading: localStorage.getItem('theme:fontHeading') || 'sans',

    init() {
        // No stored choice → fall back per the darkMode policy: 'system' follows the OS,
        // anything else stays light (dark only after an explicit toggle).
        if (!localStorage.getItem('theme:mode')) {
            this.mode = this.darkMode === 'system' ? 'system' : 'light';
        }
        this.apply();
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (this.mode === 'system') this.apply();
        });
        // Keep every same-origin document in sync (e.g. block-preview iframes):
        // localStorage writes in one document fire a `storage` event in the others.
        const defaults = { mode: this.darkMode === 'system' ? 'system' : 'light', base: 'neutral', preset: 'default', radius: '0.625', font: 'sans', shadow: 'default', spacing: 'default', tracking: 'normal', inputStyle: 'outline', fontHeading: 'sans' };
        window.addEventListener('storage', (e) => {
            if (!e.key || !e.key.startsWith('theme:')) return;
            const key = e.key.slice('theme:'.length);
            if (key in defaults) {
                this[key] = e.newValue ?? defaults[key];
                this.apply();
            }
        });
    },

    get isDark() {
        if (this.darkMode === false) return false;
        return this.mode === 'dark' || (this.mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
    },

    set(key, value) {
        this[key] = value;
        localStorage.setItem('theme:' + key, value);
        this.apply();
    },

    setMode(mode) { this.set('mode', mode); },
    setBase(base) { this.set('base', base); },
    setPreset(preset) { this.set('preset', preset); },
    setRadius(radius) { this.set('radius', radius); },
    setFont(font) { this.set('font', font); },
    setShadow(shadow) { this.set('shadow', shadow); },
    setSpacing(spacing) { this.set('spacing', spacing); },
    setTracking(tracking) { this.set('tracking', tracking); },
    setInputStyle(inputStyle) { this.set('inputStyle', inputStyle); },
    setFontHeading(fontHeading) { this.set('fontHeading', fontHeading); },

    toggle() {
        if (this.darkMode === false) return;
        this.setMode(this.isDark ? 'light' : 'dark');
    },

    // Roll a random, tasteful combination across every visual dimension — a quick
    // way to stumble on a starting point. Mode (light/dark) is left untouched so the
    // page doesn't flip out from under you; everything else is fair game.
    randomize() {
        const pick = (arr) => arr[Math.floor(Math.random() * arr.length)];
        const fonts = ['sans', 'inter', 'geist', 'manrope', 'jakarta', 'space-grotesk', 'dm-sans', 'outfit', 'sora', 'lora', 'source-serif', 'system', 'serif', 'mono'];
        const body = pick(fonts);
        const next = {
            base: pick(['neutral', 'stone', 'zinc', 'slate', 'gray', 'mauve', 'olive', 'mist', 'taupe']),
            preset: pick(['default', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose', 'red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky']),
            radius: pick(['0', '0.3', '0.5', '0.625', '0.75', '1']),
            inputStyle: pick(['outline', 'fill', 'inset']),
            font: body,
            // Heading mostly follows the body font; sometimes gets its own pairing.
            fontHeading: Math.random() < 0.6 ? 'sans' : pick(fonts),
            shadow: pick(['none', 'sm', 'default', 'lg', 'xl']),
            spacing: pick(['compact', 'default', 'comfortable']),
            tracking: pick(['tight', 'normal', 'wide']),
        };
        Object.entries(next).forEach(([k, v]) => {
            this[k] = v;
            localStorage.setItem('theme:' + k, v);
        });
        this.apply();
    },

    reset() {
        ['mode', 'base', 'preset', 'radius', 'font', 'shadow', 'spacing', 'tracking', 'inputStyle', 'fontHeading'].forEach((k) =>
            localStorage.removeItem('theme:' + k),
        );
        this.mode = this.darkMode === 'system' ? 'system' : 'light';
        this.base = 'neutral';
        this.preset = 'default';
        this.radius = '0.625';
        this.font = 'sans';
        this.shadow = 'default';
        this.spacing = 'default';
        this.tracking = 'normal';
        this.inputStyle = 'outline';
        this.fontHeading = 'sans';
        this.apply();
    },

    apply() {
        const root = document.documentElement;
        // darkMode:false means "hands off dark mode" — never touch the `dark` class, so apps that
        // drive their own dark mode (e.g. Flux) aren't stripped back to light on every page load.
        // (Previously this force-removed `dark`, causing a dark→light flash on full refresh.)
        if (this.darkMode !== false) {
            root.classList.toggle('dark', this.isDark);
        }
        // Attributes only emitted when non-default, so :root keeps the defaults.
        this.attr(root, 'data-base', this.base, 'neutral');
        this.attr(root, 'data-theme', this.preset, 'default');
        this.attr(root, 'data-font', this.font, 'sans');
        this.attr(root, 'data-shadow', this.shadow, 'default');
        this.attr(root, 'data-spacing', this.spacing, 'default');
        this.attr(root, 'data-tracking', this.tracking, 'normal');
        this.attr(root, 'data-input-style', this.inputStyle, 'outline');
        this.attr(root, 'data-font-heading', this.fontHeading, 'sans');
        root.setAttribute('data-radius', this.radius);
    },

    attr(root, name, value, fallback) {
        if (value && value !== fallback) root.setAttribute(name, value);
        else root.removeAttribute(name);
    },
};

// ---------------------------------------------------------------------------
// Toast helper — dispatches an event the <x-ui.sonner /> Toaster listens for.
//   toast('Saved')                       → simple message
//   toast({ title, description, type })  → full control (type: default|success|error|warning|info)
// ---------------------------------------------------------------------------
window.toast = (opts) => {
    const detail = typeof opts === 'string' ? { title: opts } : (opts || {});
    window.dispatchEvent(new CustomEvent('toast', { detail }));
};
['success', 'error', 'warning', 'info'].forEach((type) => {
    window.toast[type] = (opts) => {
        const detail = typeof opts === 'string' ? { title: opts } : (opts || {});
        window.dispatchEvent(new CustomEvent('toast', { detail: { ...detail, type } }));
    };
});
// A persistent loading toast (no auto-dismiss).
window.toast.loading = (opts) => {
    const detail = typeof opts === 'string' ? { title: opts } : (opts || {});
    window.dispatchEvent(new CustomEvent('toast', { detail: { duration: Infinity, ...detail, type: 'loading' } }));
};
// Promise toast: shows `loading`, then swaps to `success`/`error` on settle.
//   toast.promise(p, { loading, success, error }) — success/error may be functions of the value.
let _toastPromiseId = 0;
window.toast.promise = (promise, msgs = {}) => {
    const id = 'tp-' + (++_toastPromiseId);
    const text = (m, v) => (typeof m === 'function' ? m(v) : m);
    window.dispatchEvent(new CustomEvent('toast', { detail: { id, type: 'loading', title: text(msgs.loading) || 'Loading…', duration: Infinity } }));
    Promise.resolve(typeof promise === 'function' ? promise() : promise)
        .then((data) => window.dispatchEvent(new CustomEvent('toast-update', { detail: { id, type: 'success', title: text(msgs.success, data) || 'Success', duration: 4000 } })))
        .catch((err) => window.dispatchEvent(new CustomEvent('toast-update', { detail: { id, type: 'error', title: text(msgs.error, err) || 'Error', duration: 4000 } })));
    return promise;
};

// ---------------------------------------------------------------------------
// Calendar — Alpine day-picker mirroring shadcn/react-day-picker's <Calendar>.
//   Supports: mode single|multiple|range, numberOfMonths, captionLayout
//   label|dropdown, showWeekNumber, disabled matchers (array | {before,after,
//   dayOfWeek}), min/max, required, startMonth/endMonth, disableNavigation,
//   modifiers + modifier classNames, weekStart.
// ---------------------------------------------------------------------------
function _ymd(d) {
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
}
function _parse(s) {
    if (!s) return null;
    if (s instanceof Date) return new Date(s.getFullYear(), s.getMonth(), s.getDate());
    const p = String(s).split('-').map(Number);
    return new Date(p[0], (p[1] || 1) - 1, p[2] || 1);
}
function _sameDay(a, b) { return a && b && _ymd(a) === _ymd(b); }
function _addMonths(d, n) { return new Date(d.getFullYear(), d.getMonth() + n, 1); }

const calendar = (cfg = {}) => ({
    // Instance handle. Set it with the `calendar-id` prop (or a plain `id`) and every
    // incoming hook can be aimed at THIS calendar even when broadcast on `window`, and
    // every outgoing `calendar:updated` says which calendar it came from. Pages that run
    // several calendars at once (a range picker in a sticky sidebar + one in a mobile
    // sheet, say) need this: an un-targeted window dispatch reaches all of them.
    calendarId: cfg.calendarId || null,
    mode: cfg.mode || 'single',
    locale: cfg.locale || 'en-US',
    // aria-label fragments, localised by the Blade layer (never hardcode English here).
    todayLabel: cfg.todayLabel || 'Today, :date',
    selectedLabel: cfg.selectedLabel || 'selected',
    numberOfMonths: cfg.numberOfMonths || 1,
    weekStart: cfg.weekStart || 0,
    captionLayout: cfg.captionLayout || 'label',
    showWeekNumber: !!cfg.showWeekNumber,
    disableNavigation: !!cfg.disableNavigation,
    minDays: cfg.minDays ?? cfg.min ?? null,   // explicit minDays/maxDays preferred; min/max kept for back-compat
    maxDays: cfg.maxDays ?? cfg.max ?? null,
    disabledCfg: cfg.disabled || null,
    minDate: cfg.minDate ? _parse(cfg.minDate) : null,
    maxDate: cfg.maxDate ? _parse(cfg.maxDate) : null,
    outOfRange: cfg.outOfRange || 'disable',  // 'disable' (prevent) | 'flag' (allow + red)
    modifiers: cfg.modifiers || {},
    modifiersClass: cfg.modifiersClass || {},
    startMonth: null,
    endMonth: null,
    view: null,
    weekdays: [],
    // selection state
    single: null,
    multiple: [],
    rangeFrom: null,
    rangeTo: null,
    hover: null,
    focusedDate: null,

    init() {
        this.startMonth = cfg.startMonth ? _parse(cfg.startMonth) : null;
        this.endMonth = cfg.endMonth ? _parse(cfg.endMonth) : null;
        // seed selection
        if (this.mode === 'single') this.single = cfg.value ? _parse(cfg.value) : null;
        else if (this.mode === 'multiple') this.multiple = (cfg.value || []).map(_parse);
        else if (this.mode === 'range') {
            this.rangeFrom = cfg.value && cfg.value.from ? _parse(cfg.value.from) : null;
            this.rangeTo = cfg.value && cfg.value.to ? _parse(cfg.value.to) : null;
        }
        // default month
        let base = null;
        if (cfg.defaultMonth) base = _parse(cfg.defaultMonth.length === 7 ? cfg.defaultMonth + '-01' : cfg.defaultMonth);
        else base = this.single || this.rangeFrom || (this.multiple && this.multiple[0]) || this.startMonth || new Date();
        this.view = new Date(base.getFullYear(), base.getMonth(), 1);
        // Roving focus anchor for the grid (APG date-picker keyboard pattern).
        this.focusedDate = this.single || this.rangeFrom || (this.multiple && this.multiple[0]) || new Date(base.getFullYear(), base.getMonth(), base.getDate());
        // weekday headers
        const ref = new Date(2023, 0, 1);
        for (let i = 0; i < 7; i++) {
            const d = new Date(ref);
            d.setDate(ref.getDate() + ((this.weekStart + i + 7 - ref.getDay()) % 7));
            this.weekdays.push(d.toLocaleString(this.locale, { weekday: 'narrow' }));
        }
        // ---- External control hooks --------------------------------------------------
        // CONTRACT — an incoming hook NEVER emits `calendar-change`. That event means "the
        // user picked a day"; seeding is not a pick. Emitting it on a seed is what made a
        // popover that seeds itself on open close again on the very click that opened it,
        // and forced every consumer to carry a `syncing` flag. Programmatic changes are
        // observable through `calendar:updated` (source: 'set' | 'set-range' | 'today' |
        // 'clear' | 'value'), which never trips a "close when complete" handler.
        //
        // MODE — every hook tests the mode FIRST, before touching the view. A `calendar:set`
        // meant for a birthday picker must not drag an unrelated range calendar 30 years back.
        //
        // TARGETING — each hook is bound to BOTH this calendar's root element and `window`:
        //   • dispatch on the element (`bubbles: false`)  → this instance only;
        //   • dispatch on `window` with `detail.id`       → the instance whose calendarId matches;
        //   • dispatch on `window` without an id          → every calendar on the page (legacy).
        const mine = (e) => {
            const d = e.detail;
            const id = d && typeof d === 'object' && !(d instanceof Date) ? d.id : null;
            return !id || id === this.calendarId;
        };
        // Detail may be a bare 'YYYY-MM-DD' / Date, or { date, id } to target an instance.
        const payload = (e) => {
            const d = e.detail;
            return d && typeof d === 'object' && !(d instanceof Date) ? (d.date ?? d.month ?? null) : d;
        };
        const onToday = (e) => {
            if (this.mode !== 'single' || !mine(e)) return;
            if (this.setValue(_ymd(new Date()))) this.notify('today');
        };
        const onSet = (e) => {
            if (this.mode !== 'single' || !mine(e)) return;
            const t = _parse(payload(e));
            if (t && this.setValue(_ymd(t))) this.notify('set');
        };
        // Detail: { from: 'YYYY-MM-DD'|Date|null, to: 'YYYY-MM-DD'|Date|null, id? }.
        const onSetRange = (e) => {
            if (this.mode !== 'range' || !mine(e)) return;
            const d = e.detail || {};
            if (this.setValue({ from: d.from ?? null, to: d.to ?? null })) this.notify('set-range');
        };
        // View-only navigation — no selection, so it is the one hook that works in every mode.
        // Detail: 'YYYY-MM' | 'YYYY-MM-DD' | Date | { month, id? }.
        const onGoto = (e) => {
            if (!mine(e)) return;
            const raw = payload(e);
            const m = _parse(typeof raw === 'string' && raw.length === 7 ? raw + '-01' : raw);
            if (m) this.view = new Date(m.getFullYear(), m.getMonth(), 1);
        };
        const onClear = (e) => {
            if (!mine(e)) return;
            if (this.setValue(this.mode === 'single' ? null : this.mode === 'multiple' ? [] : { from: null, to: null })) {
                this.notify('clear');
            }
        };
        this._hooks = {
            'calendar:today': onToday,
            'calendar:set': onSet,
            'calendar:set-range': onSetRange,
            'calendar:goto': onGoto,
            'calendar:clear': onClear,
        };
        this._rootEl = this.$root;
        for (const target of [window, this._rootEl]) {
            for (const name in this._hooks) target.addEventListener(name, this._hooks[name]);
        }
    },

    // Alpine calls this when the component leaves the DOM. The window-level hooks would
    // otherwise outlive it (and keep firing) across Livewire re-renders / SPA navigations.
    destroy() {
        if (!this._hooks) return;
        for (const target of [window, this._rootEl]) {
            for (const name in this._hooks) target.removeEventListener(name, this._hooks[name]);
        }
    },

    // ---- controlled value -------------------------------------------------------------
    // `value` is the whole selection in one reactive property, so the calendar can be driven
    // from outside with Alpine's own two-way binding (the root carries x-modelable="value"):
    //
    //     <div x-data="{ stay: { from: null, to: null } }">
    //         <x-ui.calendar mode="range" x-model="stay" />
    //
    // The parent's value wins on mount; from then on the binding is live in both directions,
    // so a popover no longer has to stay mounted just to be re-seeded on every open.
    // Shape per mode: single 'Y-m-d'|null · multiple ['Y-m-d', …] · range { from, to }.
    get value() {
        if (this.mode === 'single') return this.single ? _ymd(this.single) : null;
        if (this.mode === 'multiple') return this.multiple.map(_ymd);
        return { from: this.rangeFrom ? _ymd(this.rangeFrom) : null, to: this.rangeTo ? _ymd(this.rangeTo) : null };
    },
    set value(v) {
        if (this.setValue(v)) this.notify('value');
    },
    // Seed the selection and scroll it into view. Returns whether anything actually changed —
    // the caller uses that both to skip a pointless event and to keep the two-way binding
    // from ping-ponging (a write of the value we already hold is a no-op, not a new change).
    setValue(v) {
        if (this.mode === 'single') {
            const next = v ? _ymd(_parse(v)) : null;
            const changed = next !== (this.single ? _ymd(this.single) : null);
            this.single = next ? _parse(next) : null;
            if (this.single) this.reveal(this.single);
            return changed;
        }
        if (this.mode === 'multiple') {
            const list = (Array.isArray(v) ? v : v ? [v] : []).filter(Boolean).map(_parse);
            const changed = list.map(_ymd).join(',') !== this.multiple.map(_ymd).join(',');
            this.multiple = list;
            if (list[0]) this.reveal(list[0]);
            return changed;
        }
        const d = v || {};
        const from = d.from ? _parse(d.from) : null;
        const to = d.to ? _parse(d.to) : null;
        const key = (a, b) => this.fmt(a) + '/' + this.fmt(b);
        const changed = key(from, to) !== key(this.rangeFrom, this.rangeTo);
        this.rangeFrom = from;
        this.rangeTo = to;
        this.hover = null;
        if (from) this.reveal(from);
        return changed;
    },
    // Bring a date into the visible month(s) — but only when it isn't already there, so
    // re-seeding the current selection never yanks a calendar the user just navigated.
    // Also re-anchors roving focus (APG: Tab lands on the selected day, not on day 1).
    reveal(d) {
        if (!d) return;
        if (!this._viewContains(d)) this.view = new Date(d.getFullYear(), d.getMonth(), 1);
        this.focusedDate = d;
    },

    // ---- grid building ----
    get months() {
        return Array.from({ length: this.numberOfMonths }, (_, i) => _addMonths(this.view, i));
    },
    monthLabel(m) { return m.toLocaleString(this.locale, { month: 'long', year: 'numeric' }); },
    weeksFor(m) {
        const year = m.getFullYear(), month = m.getMonth();
        const first = new Date(year, month, 1);
        const offset = (first.getDay() - this.weekStart + 7) % 7;
        const start = new Date(year, month, 1 - offset);
        const weeks = [];
        for (let w = 0; w < 6; w++) {
            const days = [];
            for (let d = 0; d < 7; d++) {
                const day = new Date(start);
                day.setDate(start.getDate() + w * 7 + d);
                // Stamp prev/next-month status here, where the panel month is correct. The
                // template reads day.__outside instead of isOutside(day, m): the outer-loop `m`
                // goes stale in the nested per-cell bindings after a month navigation, which
                // mislabels outside days (only visible once outside days are hidden/collapsed).
                day.__outside = day.getMonth() !== month;
                days.push(day);
            }
            weeks.push(days);
        }
        return weeks;
    },
    weekNumber(week) {
        const d = new Date(week[0]);
        d.setDate(d.getDate() + 3 - ((d.getDay() + 6) % 7));
        const firstThu = new Date(d.getFullYear(), 0, 4);
        return 1 + Math.round(((d - firstThu) / 86400000 - 3 + ((firstThu.getDay() + 6) % 7)) / 7);
    },

    // ---- predicates ----
    isOutside(d, m) { return d.getMonth() !== m.getMonth(); },
    isToday(d) { return _sameDay(d, new Date()); },
    isOutOfRange(d) {
        return !!((this.minDate && d < this.minDate) || (this.maxDate && d > this.maxDate));
    },
    isDisabled(d) {
        // Out-of-range dates are disabled UNLESS outOfRange === 'flag' (then they stay selectable
        // but are flagged red via data-out-of-range + the picker's invalid state).
        if (this.outOfRange !== 'flag' && this.isOutOfRange(d)) return true;
        if (this.startMonth && d < new Date(this.startMonth.getFullYear(), this.startMonth.getMonth(), 1)) return true;
        if (this.endMonth && d > new Date(this.endMonth.getFullYear(), this.endMonth.getMonth() + 1, 0)) return true;
        const c = this.disabledCfg;
        if (!c) return false;
        if (Array.isArray(c)) return c.some((x) => _sameDay(_parse(x), d));
        if (typeof c === 'object') {
            if (c.before && d < _parse(c.before)) return true;
            if (c.after && d > _parse(c.after)) return true;
            if (c.dayOfWeek && c.dayOfWeek.includes(d.getDay())) return true;
        }
        return false;
    },
    isSelected(d) {
        if (this.mode === 'single') return _sameDay(this.single, d);
        if (this.mode === 'multiple') return this.multiple.some((x) => _sameDay(x, d));
        return this.rangeIs(d).selected;
    },
    rangeIs(d) {
        const from = this.rangeFrom, to = this.rangeTo || (this.rangeFrom && this.hover);
        if (!from) return {};
        const lo = to && to < from ? to : from;
        const hi = to && to < from ? from : to;
        const isStart = _sameDay(d, lo);
        const isEnd = hi ? _sameDay(d, hi) : isStart;
        const inMid = hi && d > lo && d < hi;
        return { selected: isStart || isEnd || inMid, start: isStart, end: isEnd, middle: inMid };
    },
    modifierClass(d) {
        let cls = '';
        for (const name in this.modifiers) {
            const list = this.modifiers[name] || [];
            if (list.some((x) => _sameDay(_parse(x), d))) cls += ' ' + (this.modifiersClass[name] || '');
        }
        return cls;
    },

    // ---- interaction ----
    select(d) {
        if (this.isDisabled(d)) return;
        if (this.mode === 'single') {
            this.single = _sameDay(this.single, d) && !cfg.required ? null : d;
        } else if (this.mode === 'multiple') {
            const i = this.multiple.findIndex((x) => _sameDay(x, d));
            if (i >= 0) this.multiple.splice(i, 1);
            else if (!this.maxDays || this.multiple.length < this.maxDays) this.multiple.push(d);
        } else {
            if (!this.rangeFrom || (this.rangeFrom && this.rangeTo)) {
                this.rangeFrom = d; this.rangeTo = null;
            } else {
                let from = this.rangeFrom, to = d;
                if (to < from) [from, to] = [to, from];
                const span = Math.round((to - from) / 86400000) + 1;
                if (this.minDays && span < this.minDays) { this.rangeFrom = d; this.rangeTo = null; }
                else if (this.maxDays && span > this.maxDays) { this.rangeFrom = d; this.rangeTo = null; }
                else { this.rangeFrom = from; this.rangeTo = to; }
            }
        }
        this.notify('select');
    },

    // ---- events -----------------------------------------------------------------------
    // Two events, on purpose:
    //   `calendar-change`   — the user picked a day. Detail is the bare value (unchanged
    //                         since 1.0). Programmatic seeds do NOT fire it, which is what
    //                         lets "close the popover when the range is complete" be written
    //                         without a re-entrancy flag.
    //   `calendar:updated`  — ANY change, including seeds. Detail is { id, mode, value,
    //                         source }, so a listener can tell a pick from a seed and can
    //                         tell which calendar spoke.
    // The names are deliberately NOT near-twins: `calendar:change` would have differed from
    // `calendar-change` by a single character, which reads as a typo and greps as one string.
    // `calendar:*` is the structured API (in: set / set-range / today / goto / clear —
    // out: updated); `calendar-change` is the historical user-pick event.
    // Both bubble and are `composed` (Alpine's $dispatch), so they reach `window` even from
    // a popover teleported into <body> — listen inside the popover, or on `.window` and
    // filter on `$event.detail.id`.
    notify(source) {
        const value = this.value;
        this.$dispatch('calendar:updated', { id: this.calendarId, mode: this.mode, value, source });
        if (source === 'select') this.$dispatch('calendar-change', value);
    },
    /** @deprecated since 1.20 — use notify(source). Kept so overrides keep working. */
    emit(value) { this.$dispatch('calendar-change', value); },

    // ---- navigation ----
    get canPrev() {
        if (this.disableNavigation) return false;
        if (!this.startMonth) return true;
        return _addMonths(this.view, -1) >= new Date(this.startMonth.getFullYear(), this.startMonth.getMonth(), 1);
    },
    get canNext() {
        if (this.disableNavigation) return false;
        if (!this.endMonth) return true;
        return _addMonths(this.view, this.numberOfMonths) <= new Date(this.endMonth.getFullYear(), this.endMonth.getMonth(), 1);
    },
    prev() {
        if (!this.canPrev) return;
        this.view = _addMonths(this.view, -1);
        this.focusedDate = new Date(this.focusedDate.getFullYear(), this.focusedDate.getMonth() - 1, this.focusedDate.getDate());
    },
    next() {
        if (!this.canNext) return;
        this.view = _addMonths(this.view, 1);
        this.focusedDate = new Date(this.focusedDate.getFullYear(), this.focusedDate.getMonth() + 1, this.focusedDate.getDate());
    },

    // ---- grid roving focus + keyboard (APG date-picker dialog grid) ----
    isFocused(d) { return _sameDay(d, this.focusedDate); },
    dayLabel(d) {
        const base = d.toLocaleDateString(this.locale, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        let label = this.isToday(d) ? this.todayLabel.replace(':date', base) : base;
        if (this.isSelected(d)) label += ', ' + this.selectedLabel;
        return label;
    },
    _viewContains(d) {
        const start = new Date(this.view.getFullYear(), this.view.getMonth(), 1);
        const end = new Date(this.view.getFullYear(), this.view.getMonth() + this.numberOfMonths, 0);
        return d >= start && d <= end;
    },
    _focus(d) {
        this.focusedDate = d;
        if (!this._viewContains(d)) this.view = new Date(d.getFullYear(), d.getMonth(), 1);
        // Use $root (not $el): when invoked from a day button's @keydown, $el is the
        // button, but $root is always the calendar component element. Defer to a
        // macrotask so the moved focus isn't reverted to the event target.
        const key = _ymd(d);
        setTimeout(() => {
            const el = this.$root.querySelector('[data-day="' + key + '"]');
            if (el) el.focus();
        }, 0);
    },
    moveFocus(days) {
        const d = new Date(this.focusedDate);
        d.setDate(d.getDate() + days);
        this._focus(d);
    },
    moveFocusMonths(n) {
        const d = new Date(this.focusedDate);
        d.setMonth(d.getMonth() + n);
        this._focus(d);
    },
    focusWeekEdge(end) {
        const d = new Date(this.focusedDate);
        const offset = (d.getDay() - this.weekStart + 7) % 7;
        d.setDate(d.getDate() + (end ? 6 - offset : -offset));
        this._focus(d);
    },
    // The grid is laid out with logical properties, so under `dir="rtl"` it renders mirrored:
    // the next day sits to the LEFT of the current one. Arrow keys are visual in the APG grid
    // pattern, so they have to mirror with it — Home/End do not (they are "first/last day of
    // the week", which is a logical position, not a screen side).
    isRtl() {
        const el = this._rootEl;
        return !!(el && el.nodeType === 1 && typeof getComputedStyle === 'function' && getComputedStyle(el).direction === 'rtl');
    },
    onDayKeydown(e, d) {
        const k = e.key;
        const step = this.isRtl() ? -1 : 1;
        if (k === 'ArrowLeft') this.moveFocus(-step);
        else if (k === 'ArrowRight') this.moveFocus(step);
        else if (k === 'ArrowUp') this.moveFocus(-7);
        else if (k === 'ArrowDown') this.moveFocus(7);
        else if (k === 'Home') this.focusWeekEdge(false);
        else if (k === 'End') this.focusWeekEdge(true);
        else if (k === 'PageUp') this.moveFocusMonths(-1);
        else if (k === 'PageDown') this.moveFocusMonths(1);
        else if (k === 'Enter' || k === ' ') { this.select(d); this.focusedDate = d; }
        else return;
        e.preventDefault();
    },

    // ---- dropdown caption ----
    get years() {
        const start = this.startMonth ? this.startMonth.getFullYear() : new Date().getFullYear() - 100;
        const end = this.endMonth ? this.endMonth.getFullYear() : new Date().getFullYear() + 10;
        return Array.from({ length: end - start + 1 }, (_, i) => start + i);
    },
    get monthNames() {
        return Array.from({ length: 12 }, (_, i) => new Date(2023, i, 1).toLocaleString(this.locale, { month: 'long' }));
    },
    setMonth(m) { this.view = new Date(this.view.getFullYear(), Number(m), 1); },
    setYear(y) { this.view = new Date(Number(y), this.view.getMonth(), 1); },

    // ---- form value helpers ----
    fmt(d) { return d ? _ymd(d) : ''; },
    get multipleValue() { return this.multiple.map(_ymd).join(','); },
});

// Exported for the engine's unit tests (tests/js/calendar.test.mjs), which drive the
// factory directly with a stub Alpine scope. Apps use Alpine.data('calendar') instead.
export { calendar };

// ---------------------------------------------------------------------------
// Stepping arithmetic — shared by every component that adds a `step` to a value
// (number-input, slider, knob).
//
// `0.1 + 0.1 + 0.1` is 0.30000000000000004 in IEEE 754, so eight clicks of a
// +0.1 stepper starting at 1.1 land on 1.3666…, not 1.9, and the drift is then
// written straight into the consumer's Livewire property. Rounding the result
// back to the precision the inputs actually carry is what keeps a stepper on the
// values the author declared.
//
// The precision is max(step, current) rather than step alone, because a stepper
// with `step="1"` is still allowed to hold a hand-typed 1.32 — rounding that to
// the step's precision would silently truncate it to 2 on the next click.
// ---------------------------------------------------------------------------

/** Decimal places carried by a number, exponent notation (1e-7) included. */
function decimalsOf(n) {
    if (n === null || n === undefined || !isFinite(n)) return 0;
    const s = String(n);
    const exp = s.indexOf('e-');
    if (exp !== -1) {
        const mantissa = s.slice(0, exp);
        const dot = mantissa.indexOf('.');
        return Number(s.slice(exp + 2)) + (dot === -1 ? 0 : mantissa.length - dot - 1);
    }
    const dot = s.indexOf('.');
    return dot === -1 ? 0 : s.length - dot - 1;
}

/** Round to `decimals` places. */
function roundTo(v, decimals) {
    if (v === null || v === undefined || !isFinite(v)) return v;
    // Past 1e15 the scaling itself loses precision, so cap it rather than make things worse.
    const d = Math.min(Math.max(decimals, 0), 15);
    const s = String(v);
    // Shift the decimal point in the exponent rather than by multiplying: `1.005 * 100` is
    // 100.49999999999999, so the obvious version rounds 1.005 DOWN to 1. Values already in
    // exponent notation cannot be shifted this way, and are small enough not to need it.
    if (s.includes('e') || s.includes('E')) {
        const p = Math.pow(10, d);

        return Math.round(v * p) / p;
    }

    return Number(`${Math.round(Number(`${s}e${d}`))}e-${d}`);
}

/** `current + delta`, kept at the precision current and step imply. */
function stepBy(current, delta, step) {
    const from = Number(current) || 0;
    return roundTo(from + delta, Math.max(decimalsOf(step), decimalsOf(from)));
}

/** Nearest multiple of `step` counted from `origin`, at the step's precision. */
function snapTo(raw, step, origin = 0) {
    if (!isFinite(raw) || !step) return raw;
    return roundTo(Math.round((raw - origin) / step) * step + origin, decimalsOf(step) + decimalsOf(origin));
}

/**
 * `$blatNumber` — the stepping helpers above, for a component's x-data:
 *
 *     inc() { this.value = $blatNumber.step(this.value, this.step, this.step) }
 *
 * Exported as plain functions too, for the engine tests.
 */
const blatNumber = { decimals: decimalsOf, round: roundTo, step: stepBy, snap: snapTo };

export { blatNumber, decimalsOf, roundTo, stepBy, snapTo };

// ---------------------------------------------------------------------------
// Theme export — resolve the CURRENT customization (base color, accent, radius,
// font, shadow, spacing, tracking) into a COMPLETE, self-contained
// resources/css/app.css the user can paste as-is.
//
// We emit the full foundations scaffold (the Tailwind import, the @source globs
// and the @theme inline mapping) followed by the live
// :root/.dark tokens. The @theme inline mapping is what turns the tokens into
// utilities (bg-background, shadow-md, tracking-wide, …) — without it Tailwind
// generates nothing and a pasted theme renders unstyled. The :root block also
// carries every token the mapping references (spacing, fonts, shadows…), so
// nothing resolves to `var(--undefined)`.
//
// NOTE: THEME_SCAFFOLD mirrors the head of resources/css/app.css. If you change
// the @theme inline mapping or @source globs there, mirror them here too.
// ---------------------------------------------------------------------------
const THEME_SCAFFOLD = `@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../vendor/mallardduck/blade-lucide-icons/resources/svg/*.svg';
@source '../../storage/framework/views/*.php';
@source '../views';

@custom-variant dark (&:is(.dark *));

@theme inline {
    /* Fonts */
    --font-sans: var(--font-sans);
    --font-serif: var(--font-serif);
    --font-mono: var(--font-mono);
    --font-heading: var(--font-heading);

    /* Radius scale (derived from --radius) */
    --radius-sm: calc(var(--radius) - 4px);
    --radius-md: calc(var(--radius) - 2px);
    --radius-lg: var(--radius);
    --radius-xl: calc(var(--radius) + 4px);

    /* Spacing base (every p-*, gap-*, w-*… derives from this) */
    --spacing: var(--spacing);

    /* Letter-spacing scale (derived from --tracking-normal) */
    --tracking-tighter: calc(var(--tracking-normal) - 0.05em);
    --tracking-tight: calc(var(--tracking-normal) - 0.025em);
    --tracking-normal: var(--tracking-normal);
    --tracking-wide: calc(var(--tracking-normal) + 0.025em);
    --tracking-wider: calc(var(--tracking-normal) + 0.05em);
    --tracking-widest: calc(var(--tracking-normal) + 0.1em);

    /* Box-shadow scale */
    --shadow-2xs: var(--shadow-2xs);
    --shadow-xs: var(--shadow-xs);
    --shadow-sm: var(--shadow-sm);
    --shadow: var(--shadow);
    --shadow-md: var(--shadow-md);
    --shadow-lg: var(--shadow-lg);
    --shadow-xl: var(--shadow-xl);
    --shadow-2xl: var(--shadow-2xl);

    /* Colors */
    --color-background: var(--background);
    --color-foreground: var(--foreground);
    --color-card: var(--card);
    --color-card-foreground: var(--card-foreground);
    --color-popover: var(--popover);
    --color-popover-foreground: var(--popover-foreground);
    --color-primary: var(--primary);
    --color-primary-foreground: var(--primary-foreground);
    --color-secondary: var(--secondary);
    --color-secondary-foreground: var(--secondary-foreground);
    --color-muted: var(--muted);
    --color-muted-foreground: var(--muted-foreground);
    --color-accent: var(--accent);
    --color-accent-foreground: var(--accent-foreground);
    --color-destructive: var(--destructive);
    --color-destructive-foreground: var(--destructive-foreground);
    --color-success: var(--success);
    --color-success-foreground: var(--success-foreground);
    --color-warning: var(--warning);
    --color-warning-foreground: var(--warning-foreground);
    --color-info: var(--info);
    --color-info-foreground: var(--info-foreground);
    --color-border: var(--border);
    --color-input: var(--input);
    --color-ring: var(--ring);
    --color-chart-1: var(--chart-1);
    --color-chart-2: var(--chart-2);
    --color-chart-3: var(--chart-3);
    --color-chart-4: var(--chart-4);
    --color-chart-5: var(--chart-5);
    --color-sidebar: var(--sidebar);
    --color-sidebar-foreground: var(--sidebar-foreground);
    --color-sidebar-primary: var(--sidebar-primary);
    --color-sidebar-primary-foreground: var(--sidebar-primary-foreground);
    --color-sidebar-accent: var(--sidebar-accent);
    --color-sidebar-accent-foreground: var(--sidebar-accent-foreground);
    --color-sidebar-border: var(--sidebar-border);
    --color-sidebar-ring: var(--sidebar-ring);

    --animate-caret-blink: caret-blink 1.25s ease-out infinite;

    @keyframes caret-blink {
        0%,
        70%,
        100% {
            opacity: 1;
        }
        20%,
        50% {
            opacity: 0;
        }
    }

    --animate-progress-indeterminate: progress-indeterminate 1.4s ease-in-out infinite;

    @keyframes progress-indeterminate {
        0% {
            left: -40%;
        }
        100% {
            left: 100%;
        }
    }
}`;

// Every token the @theme inline mapping references — all must land in :root so
// the pasted file is fully self-contained.
const THEME_TOKENS = [
    // Geometry / rhythm
    '--radius', '--spacing', '--tracking-normal',
    // Fonts
    '--font-sans', '--font-serif', '--font-mono', '--font-heading',
    // Shadows
    '--shadow-2xs', '--shadow-xs', '--shadow-sm', '--shadow',
    '--shadow-md', '--shadow-lg', '--shadow-xl', '--shadow-2xl',
    // Colors
    '--background', '--foreground',
    '--card', '--card-foreground',
    '--popover', '--popover-foreground',
    '--primary', '--primary-foreground',
    '--secondary', '--secondary-foreground',
    '--muted', '--muted-foreground',
    '--accent', '--accent-foreground',
    '--destructive', '--destructive-foreground',
    '--success', '--success-foreground',
    '--warning', '--warning-foreground',
    '--info', '--info-foreground',
    '--border', '--input', '--ring',
    '--chart-1', '--chart-2', '--chart-3', '--chart-4', '--chart-5',
    '--sidebar', '--sidebar-foreground', '--sidebar-primary', '--sidebar-primary-foreground',
    '--sidebar-accent', '--sidebar-accent-foreground', '--sidebar-border', '--sidebar-ring',
];

function _readTokens() {
    const cs = getComputedStyle(document.documentElement);
    const out = {};
    THEME_TOKENS.forEach((t) => {
        const v = cs.getPropertyValue(t).trim();
        if (v) out[t] = v;
    });
    // getComputedStyle resolves var() references, so an untouched heading font
    // exports as a frozen copy of the body stack. Re-tie it, otherwise editing
    // --font-sans in the exported file silently stops moving the headings.
    if (out['--font-heading'] && out['--font-heading'] === out['--font-sans']) {
        out['--font-heading'] = 'var(--font-sans)';
    }
    return out;
}

window.exportTheme = function () {
    const root = document.documentElement;
    const wasDark = root.classList.contains('dark');
    root.classList.remove('dark');
    const light = _readTokens();
    root.classList.add('dark');
    const dark = _readTokens();
    root.classList.toggle('dark', wasDark);
    const fmt = (o, keys) => keys.filter((t) => o[t]).map((t) => `  ${t}: ${o[t]};`).join('\n');
    // :root carries the full token set; .dark only overrides what actually differs.
    const darkKeys = THEME_TOKENS.filter((t) => dark[t] && dark[t] !== light[t]);
    return `${THEME_SCAFFOLD}\n\n:root {\n${fmt(light, THEME_TOKENS)}\n}\n\n.dark {\n${fmt(dark, darkKeys)}\n}\n`;
};

// ---------------------------------------------------------------------------
// Accessibility primitives
//
// shadcn/ui inherits a flawless ARIA layer from Radix' `asChild` slot-merging:
// the popup ARIA (haspopup/expanded/controls) lands on the *real* focusable
// control, never on a wrapper. Our Blade triggers wrap a real <button> in a
// `display:contents` span, so without help the ARIA would sit on the
// non-focusable span. These directives port Radix' behaviour to Alpine.
// ---------------------------------------------------------------------------

// Find the genuine focusable control a trigger wrapper stands for. The wrapper
// is usually a `display:contents` span whose first interactive descendant is the
// actual <button>/<a>; fall back to the element itself when it is focusable.
function resolveControl(el) {
    const focusableSel = 'button, [href], input, select, textarea, [tabindex]';
    if (el.matches(focusableSel) && el.getAttribute('tabindex') !== '-1') return el;
    return (
        el.querySelector('button:not([tabindex="-1"]), a[href]:not([tabindex="-1"]), input:not([type="hidden"]), select, textarea, [tabindex]:not([tabindex="-1"])') ||
        el.firstElementChild ||
        el
    );
}

let _blatId = 0;
function mintId(prefix = 'blat') {
    return `${prefix}-${++_blatId}-${Math.random().toString(36).slice(2, 7)}`;
}

function ensureId(node, prefix = 'blat') {
    if (!node.id) node.id = mintId(prefix);
    return node.id;
}

/**
 * ensureId, but remembering the id it handed out for a given role.
 *
 * A morph strips ids we generated (the server's markup has never had them), and the re-wire
 * that follows would otherwise mint a brand new one on every single re-render — an idref that
 * keeps changing under a screen reader, and a counter that climbs forever on a page that polls.
 *
 * The memo lives on `owner` — the container the directive is bound to — rather than in the
 * directive's closure, because the closure is not guaranteed to outlive a re-render either.
 * Whether Alpine re-initialises a directive after one is decided by how the framework diffed
 * that particular subtree: measured on Livewire 4, a field that merely gained an error message
 * is not re-initialised, while a field whose child nodes were replaced is.
 *
 * Best-effort, deliberately. When a morph replaces the container itself the memo goes with it
 * and the next id is a fresh one — measured on Livewire 4, that costs one re-mint per morph
 * for a replaced description node, while the control's id (the one `label[for]` points at)
 * stays put. Guaranteeing more would mean a global registry keyed by something that survives
 * an arbitrary framework's diffing, and there is no such key. Correct wiring does not depend
 * on this: the ids are internal, and every idref is rewritten in the same pass that mints them.
 */
function stableIds(owner) {
    const minted = (owner._blatIds ??= {});

    return (node, role, prefix) => {
        if (node.id) return (minted[role] = node.id);
        node.id = minted[role] || mintId(prefix);

        return (minted[role] = node.id);
    };
}

/**
 * Write an attribute only when it would actually change, and treat null/'' as "remove".
 *
 * The "only when it would change" half is not a micro-optimisation — it is what makes
 * keepWired() below terminate. See there.
 */
function setAttr(node, name, value) {
    if (value === null || value === undefined || value === '') {
        if (node.hasAttribute(name)) node.removeAttribute(name);
    } else if (node.getAttribute(name) !== value) {
        node.setAttribute(name, String(value));
    }
}

/**
 * Attributes BlatUI derives from the DOM rather than from the server's markup. Narrowing the
 * observer to these is what keeps it quiet: a component that writes `style` or `class` on
 * every animation frame must not wake every field on the page.
 */
const WIRED_ATTRS = ['id', 'for', 'aria-describedby', 'aria-labelledby', 'aria-invalid', 'aria-controls', 'aria-haspopup', 'aria-expanded', 'data-invalid', 'data-state'];

/**
 * Keep DOM-derived wiring applied, instead of applying it once and hoping.
 *
 * Several directives here resolve a fact out of the rendered DOM — is there an error slot,
 * which node is the title, where is the real control — and write ARIA onto it. Doing that in
 * a `queueMicrotask` at init assumes two things that are only true on a static page:
 *
 *   1. the DOM it read is the DOM forever. A Livewire morph adds the error message to a field
 *      that rendered valid, and the field element itself is never recreated — so Alpine never
 *      re-runs the directive and the wiring is simply never applied (issue #19).
 *   2. what we wrote stays written. It does not: these attributes exist only in the browser,
 *      the server's HTML has never heard of them, and a morph syncs attributes against the
 *      server's version — so it strips `id`, `for` and `aria-describedby` back off on the next
 *      unrelated re-render, and the field silently loses its accessible name.
 *
 * So the wiring is re-derived whenever the subtree changes. This is a MutationObserver rather
 * than a Livewire hook on purpose: the same thing happens under Turbo, htmx, or a plain
 * `el.replaceWith(el.cloneNode(true))`, and none of those would fire a Livewire hook.
 *
 * `sync` MUST be idempotent — write only through setAttr(), and only values it would compute
 * again next pass. The observer sees our own writes: an idempotent sync makes the second pass
 * a no-op and the loop stops, a sync that writes unconditionally never stops.
 */
function keepWired(el, sync, cleanup) {
    sync();
    const observer = new MutationObserver(sync);
    observer.observe(el, { childList: true, subtree: true, attributes: true, attributeFilter: WIRED_ATTRS });
    cleanup?.(() => observer.disconnect());
}

// x-blat-trigger="{ haspopup: 'menu', controls: $id('blat-content'), state: 'open' }"
//   Mirrors disclosure/popup ARIA onto the real control inside the wrapper.
//   `haspopup` → aria-haspopup (dialog|menu|listbox|tree|grid|true)
//   `controls` → aria-controls (the popup content id)
//   `state`    → reactive boolean expression for expanded state (default 'open')
//   `labelledby`/`describedby` → static id refs (e.g. tooltip describing a trigger)
function blatTriggerDirective(el, { expression }, { evaluate, effect, cleanup }) {
    const cfg = expression ? evaluate(expression) : {};
    const tracksState = cfg.state !== null; // null → opt out (plain tooltip/hovercard target)
    const stateExpr = cfg.state || 'open';
    let open = false;

    // The control is re-resolved on every pass, not captured: a morph can replace the wrapper's
    // <button> with a fresh node, and wiring the old one leaves the live trigger bare.
    const sync = () => {
        const control = resolveControl(el);
        if (!control) return;
        // For triggers whose slot may be plain text (tooltip/hover-card), make the
        // resolved control keyboard-focusable when it isn't already.
        if (cfg.focusable && !control.matches('button, a[href], input, select, textarea, [tabindex]')) {
            control.tabIndex = 0;
        }
        if (cfg.id && !control.id) control.id = cfg.id;
        if (cfg.haspopup) setAttr(control, 'aria-haspopup', cfg.haspopup === true ? 'true' : cfg.haspopup);
        if (cfg.controls) setAttr(control, 'aria-controls', cfg.controls);
        if (cfg.labelledby) setAttr(control, 'aria-labelledby', cfg.labelledby);
        if (cfg.describedby) setAttr(control, 'aria-describedby', cfg.describedby);
        if (!tracksState) return;
        setAttr(control, 'aria-expanded', open ? 'true' : 'false');
        setAttr(control, 'data-state', open ? 'open' : 'closed');
    };

    if (tracksState) {
        // Alpine's effect covers "the state changed"; keepWired covers "the DOM changed under
        // us". Both funnel through the same idempotent sync, so they cannot disagree.
        effect(() => {
            try {
                open = !!evaluate(stateExpr);
            } catch (e) {
                /* state not yet in scope */
            }
            sync();
        });
    }

    keepWired(el, sync, cleanup);
}

// x-blat-labelledby="{ label: '[data-slot=dialog-title]', description: '[data-slot=dialog-description]' }"
//   Wires aria-labelledby / aria-describedby on a dialog/popover container from
//   whichever title/description slots the author actually rendered (Radix does
//   this through context; we resolve it from the DOM so absent slots add no
//   dangling idref).
function blatLabelledByDirective(el, { expression }, { evaluate, cleanup }) {
    const cfg = expression ? evaluate(expression) : {};
    // Only ever withdraw an idref we put there ourselves. An author who sets aria-describedby
    // on the container by hand keeps it, title slot or no title slot.
    const ours = {};
    const idFor = stableIds(el);

    const wire = (sel, attr) => {
        if (!sel) return;
        const node = el.querySelector(sel);
        if (node) {
            setAttr(el, attr, (ours[attr] = idFor(node, attr, 'blat-label')));
        } else if (ours[attr] && el.getAttribute(attr) === ours[attr]) {
            setAttr(el, attr, null);
            delete ours[attr];
        }
    };

    // Re-derived on every change rather than once: the title of a dialog is routinely
    // rendered by the framework that owns the page, so the slot can arrive, change or leave
    // long after this container was created.
    keepWired(
        el,
        () => {
            wire(cfg.label, 'aria-labelledby');
            wire(cfg.description, 'aria-describedby');
        },
        cleanup,
    );
}

// x-blat-dialog-layer — put a teleported popover in the right stacking layer. When its
// home (the <template x-teleport> Alpine left behind, `el._x_teleportBack`) sits inside a
// native <dialog> — e.g. a Flux <flux:modal>, opened with showModal() — move the popover
// into that dialog. A modal <dialog> lives in the browser's *top layer*, which paints above
// everything in <body> regardless of z-index; a popover teleported to <body> would otherwise
// render behind the modal (and be inert). Inside the dialog it shares the top layer and stays
// interactive. With no native <dialog> ancestor it stays in <body> (the teleport default),
// still escaping any overflow-clipping ancestor. Pure DOM — works in any component's scope.
// x-blat-anchor — positions a teleported popover at its trigger, like Alpine's x-anchor,
// but built for popovers that live in a modal's top layer and can be tall:
//   • strategy: 'fixed' — viewport-relative coords. x-anchor's default 'absolute' strategy
//     mis-positions a popover once it's relocated into a native <dialog> (Flux modal), because
//     the offsetParent math is wrong for the top layer; 'fixed' is correct there and in <body>.
//   • size() — caps the popover's height to the space actually available and lets it scroll,
//     so a calendar / long menu can't overflow off-screen when the trigger sits low (the exact
//     failure inside a centered modal). flip()+shift() keep it pointed at and within the viewport.
//   • `.no-size` — opt out of the height cap for a popover that cannot scroll (see below).
//   • `.pad N` — the gutter kept between popover and viewport edge (default 8). Only visible when
//     the popover would otherwise overflow and shift() clamps it, which is why the two components
//     that came from Alpine's x-anchor keep its 5: the migration should not move them 3px.
//   • `.absolute` — position in DOCUMENT coords instead. For a page-level popover that can stay
//     open while its trigger scrolls away (mini-cart, notification-center), 'fixed' + shift() pins
//     it to the viewport edge once the trigger has left the screen, so it hangs there detached from
//     what it belongs to. Those want to scroll with the page. Anything that can appear over a modal
//     wants the default.
//   • `.match-width` — the popover is never narrower than its trigger. It reads the width from
//     floating-ui's measured reference rect on every reposition, so a trigger that was 0px wide
//     at init (a popover inside a dialog that starts closed) still widens the moment it is shown.
//     A one-shot `offsetWidth` read in a Blade binding cannot: nothing there is reactive, so the
//     0px it measured behind `display:none` would stick for the component's lifetime (issue #18).
// Modifiers mirror x-anchor: a placement (e.g. bottom-start), `.offset N`, `.no-flip`, plus the
// `.match-width` and `.no-size` above.
const BLAT_ANCHOR_PLACEMENTS = ['top', 'top-start', 'top-end', 'right', 'right-start', 'right-end', 'bottom', 'bottom-start', 'bottom-end', 'left', 'left-start', 'left-end'];
function blatAnchorDirective(el, { modifiers, expression }, { evaluateLater, cleanup }) {
    const placement = BLAT_ANCHOR_PLACEMENTS.find((p) => modifiers.includes(p)) || 'bottom';
    let offsetValue = 0;
    if (modifiers.includes('offset')) {
        const i = modifiers.indexOf('offset');
        offsetValue = modifiers[i + 1] !== undefined ? Number(modifiers[i + 1]) : 0;
    }
    const allowFlip = !modifiers.includes('no-flip');
    const matchWidth = modifiers.includes('match-width');
    // `.no-size` — do not cap the height. The cap only works on a popover that can scroll at its
    // capped height: either the popover itself (select-content, dropdown-menu-content: max-h +
    // overflow-y-auto) or, for one that is overflow-hidden, an inner scroller it can shrink
    // (date-picker, combobox). On anything else — a tooltip, a hover card, a menu that sizes to
    // its items — a cap only cuts content off, so those opt out and keep flip/shift instead.
    const capHeight = !modifiers.includes('no-size');
    const strategy = modifiers.includes('absolute') ? 'absolute' : 'fixed';
    const PAD = modifiers.includes('pad') ? Number(modifiers[modifiers.indexOf('pad') + 1]) || 8 : 8;
    // The popover's own design cap (e.g. `max-h-96`), read once before we set anything inline.
    // size() only shrinks *below* this when the viewport is tight — it never makes the popover
    // taller than the component intended. Infinity when the component sets no cap (e.g. calendars).
    const designMax = parseFloat(getComputedStyle(el).maxHeight) || Infinity;
    const getReference = evaluateLater(expression);

    let stop = null;
    let reference = null;

    // Re-resolve the trigger when the one we are anchored to leaves the document.
    //
    // autoUpdate watches a NODE, not an expression. A framework that morphs the DOM — Livewire,
    // Turbo, htmx — can decide the trigger is not the same element and swap it for a fresh one
    // (Livewire's morph does exactly this when a re-render lands on a subtree it walks into). The
    // popover then holds a detached node: every measurement is 0x0, so it parks in the viewport
    // corner and stays there for the life of the page, because the observers that would correct it
    // are watching a node nothing will ever resize or scroll again. Alpine's x-ref registry follows
    // the swap, so re-evaluating the expression yields the live node; the popover itself is still
    // observed either way, which is what gets us back here when it is next shown.
    const rebind = (next) => {
        if (!next) return;
        if (stop) stop();
        reference = next;
        stop = autoUpdate(reference, el, update);
    };

    function update() {
        // Strict false: a virtual reference (a plain getBoundingClientRect object) has no
        // isConnected and must not be treated as detached.
        if (reference?.isConnected === false) {
            let fresh = null;
            getReference((r) => (fresh = r));
            if (fresh && fresh !== reference) return rebind(fresh); // rebinding measures immediately
        }

        return position();
    }

    const position = () =>
    computePosition(reference, el, {
        strategy,
        placement,
        middleware: [
            flOffset(offsetValue),
            allowFlip && flip({ padding: PAD }),
            shift({ padding: PAD }),
            size({
                padding: PAD,
                apply({ availableHeight, rects }) {
                    // Fit the available space but never exceed the design cap (min 140px so it
                    // never collapses). The popover carries overflow-y-auto, so it scrolls.
                    if (capHeight) {
                        const h = Math.min(designMax, Math.max(140, Math.floor(availableHeight)));
                        el.style.maxHeight = Number.isFinite(h) ? `${h}px` : '';
                    }
                    // Skip a 0-wide reference (the trigger is still hidden): writing 0px would
                    // read as "no minimum" and we'd never know to widen it later. Write only on
                    // change — setting the width resizes the popover, which autoUpdate observes.
                    if (matchWidth && rects.reference.width > 0) {
                        const w = `${Math.round(rects.reference.width)}px`;
                        if (el.style.minWidth !== w) el.style.minWidth = w;
                    }
                },
            }),
        ].filter(Boolean),
    }).then(({ x, y }) => {
        Object.assign(el.style, { position: strategy, left: `${x}px`, top: `${y}px` });
    });

    getReference((initial) => rebind(initial));

    cleanup(() => stop && stop());
}

// Left one-shot on purpose, unlike its neighbours. The others were re-derived because a
// re-render demonstrably breaks them; this one resolves a structural fact — is the popover's
// home inside a native <dialog> — that is fixed by the time the popover exists, and it was
// measured still correct in a real native modal (apps/livewire /native-dialog). It is not
// re-derived across a morph because no morph could be made to break it: re-rendering a native
// <dialog>'s subtree closes the modal outright, which is the app's problem long before it is
// this directive's. Change that if a report ever shows otherwise — not before.
function blatDialogLayerDirective(el) {
    queueMicrotask(() => {
        const home = el._x_teleportBack;
        if (!home) return;
        const target = home.closest('dialog') || document.body;
        if (el.parentElement !== target) {
            target.appendChild(el);
            // Reparenting changes the popover's offsetParent. x-anchor (floating-ui) positions
            // it as `position:absolute; top/left` computed against the *old* parent — stale after
            // the move. Nudge floating-ui's autoUpdate (it listens for resize/scroll) to recompute
            // against the new containing block so the popover re-anchors to its trigger.
            requestAnimationFrame(() => window.dispatchEvent(new Event('resize')));
        }
    });
}

// ---------------------------------------------------------------------------
// $blatModel — the Livewire bridge behind every component that carries a value.
//
// The obvious way to write this is `@entangle`, and that is what BlatUI did. It has three
// problems, all structural rather than incidental:
//
//   1. It keeps a SECOND copy of the value in the component's Alpine data and syncs the two
//      with effects. Two sources of truth for one field is the shape of every "the input says
//      10, the property says 0" bug.
//   2. The Blade directive compiles to `window.Livewire.find('<id>').entangle('price')` — the
//      component id is baked into the x-data string, and Alpine evaluates x-data exactly once.
//      Wiring derived from the page has to be re-derived rather than remembered (that is the
//      rule keepWired() exists for); a frozen component id is the same mistake one layer up.
//   3. `component.entangle()` is created WITHOUT a cleanup callback (only the `$wire.$entangle`
//      magic passes one), so the sync effects are never released when the element leaves the
//      DOM. That is what Livewire's own docs mean by "@entangle … causes issues when removing
//      DOM elements", and why the directive is deprecated as of Livewire 4.
//
// So there is no second copy here. When a consumer passed `wire:model`, the Livewire property
// IS the state: reads go through `$wire.$get`, writes through `$wire.$set` — deferred by
// default, immediate for `wire:model.live`, exactly as the modifier asks. Both the property
// path and the owning component are read back out of the DOM on every access, so a morph that
// re-points or re-mounts the component is followed instead of missed.
//
// Without Livewire — a plain Blade page, or the docs site — there is no `wire:model` attribute,
// and the same object simply holds the value locally, so a component behaves identically.
//
// Usage, in a component's x-data (the Blade bridge renders the data attributes):
//     _model: $blatModel(@js($default)),
//     get value() { return this._model.value },
//     set value(v) { this._model.value = v },
// ---------------------------------------------------------------------------

/**
 * `closest`, climbing THROUGH a teleport rather than stopping at it.
 *
 * A dialog, sheet, drawer or popover renders its content inside `<template x-teleport="body">`, so
 * at runtime that content is a child of <body> and a plain closest() from a control inside it
 * finds no Livewire root at all. The control is a child of <body> in the DOM and a child of the
 * component in the markup, and for "who owns this value" the markup is the truth: a bound control
 * in a dialog otherwise runs in local-only mode, silently losing its prefill and every write
 * (issue #25). Alpine has the same problem to solve for its own scopes and solves it the same way
 * — `_x_teleportBack` is the link it leaves from teleported content back to its template.
 */
function blatClosest(el, selector) {
    let node = el;
    while (node) {
        if (node.matches && node.matches(selector)) return node;
        node = node._x_teleportBack || node.parentElement;
    }

    return null;
}

/**
 * The `$wire` of the Livewire component that currently owns `el`, or null outside Livewire.
 * Also exposed as the `$blatWire` magic, for components that talk to Livewire directly (
 * file-upload drives its progress bar off `$wire`'s upload API) and must stay inert without it —
 * Alpine's own `$wire` magic does not exist at all in an app that has no Livewire.
 */
function blatWire(el) {
    if (! window.Livewire) return null;
    // Livewire.find() already hands back the component's $wire, and returns undefined for an id
    // it no longer knows — which is the case worth handling: the component was re-mounted.
    const root = blatClosest(el, '[wire\\:id]');

    return (root && window.Livewire.find(root.getAttribute('wire:id'))) || null;
}

function blatModelMagic(el) {
    return (fallback = null) => ({
        // Where the value lives when nothing is bound. Declared here (rather than captured
        // in a closure) so Alpine's reactivity covers it and a local component still re-renders.
        local: fallback,

        get path() { return el.dataset.blatModel || null; },

        get value() {
            const wire = this.path ? blatWire(el) : null;
            if (! wire) return this.local;
            // $get reads Livewire's reactive data, so an effect that reads through here is
            // re-run when the server sends a new value — no watcher of our own needed.
            const value = wire.$get(this.path);

            return value === undefined ? this.local : value;
        },

        set value(next) {
            this.write(next);
            this.commit();
        },

        /**
         * Update the value without sending anything. The Livewire property still moves — Alpine
         * and the server read the same object — so nothing goes out of step; only the request is
         * withheld. A slider drag is what this exists for: sixty writes a second must not be
         * sixty requests, and holding the value locally until pointerup would mean the property
         * disagreed with the thumb for the length of the drag.
         */
        write(next) {
            const wire = this.path ? blatWire(el) : null;
            if (! wire) { this.local = next; return; }
            wire.$set(this.path, next, false);
        },

        /** Send what is there — if this binding asked to be live. Deferred bindings ride along
         *  with the next request, so for them this is deliberately a no-op. */
        commit() {
            const wire = this.path && el.dataset.blatModelLive === '1' ? blatWire(el) : null;
            if (wire) wire.$set(this.path, wire.$get(this.path), true);
        },
    });
}


// x-blat-field — wires a form field's control to its label/description/error:
//   aria-describedby ← description + error ids, aria-invalid + data-invalid when
//   an error is present, and label[for] ← control id when not already set. Radix/
//   Base UI do this through React context; we resolve it from the rendered DOM.
const FIELD_CONTROL =
    'input:not([type=hidden]), textarea, select, [role="checkbox"], [role="switch"], [role="radiogroup"], [role="combobox"], [role="slider"], [role="spinbutton"]';

function blatFieldDirective(el, _directive, { cleanup }) {
    // The idrefs and the aria-invalid we contributed last pass. Tracked so a later pass can
    // withdraw exactly what we added — a field that goes valid again has to lose data-invalid,
    // and an aria-describedby the app wrote itself has to survive us.
    let ourIds = [];
    let ourInvalid = false;
    const idFor = stableIds(el);

    const sync = () => {
        const control = el.querySelector(FIELD_CONTROL);
        if (!control) return;

        const desc = el.querySelector('[data-slot="field-description"]');
        const err = el.querySelector('[data-slot="field-error"]');

        const ids = [];
        if (desc) ids.push(idFor(desc, 'desc', 'field-desc'));
        if (err) ids.push(idFor(err, 'err', 'field-err'));

        // Rebuild rather than append: appending is what made the original one-shot version
        // safe only because it ran once. Anything on the control that is neither ours-from-last
        // -pass nor ours-this-pass is the author's and is kept, in its original order.
        const present = (control.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean);
        const theirs = present.filter((id) => !ourIds.includes(id) && !ids.includes(id));
        setAttr(control, 'aria-describedby', [...theirs, ...ids].join(' '));
        ourIds = ids;

        // aria-invalid is the one attribute an app commonly drives itself (it was the
        // documented workaround for #19), so we add ours and remove only ours.
        if (err) {
            setAttr(control, 'aria-invalid', 'true');
            ourInvalid = true;
        } else if (ourInvalid) {
            setAttr(control, 'aria-invalid', null);
            ourInvalid = false;
        }

        setAttr(el, 'data-invalid', err ? 'true' : null);

        const label = el.querySelector('[data-slot="field-label"]');
        if (label && !label.getAttribute('for')) {
            setAttr(label, 'for', idFor(control, 'control', 'field-control'));
        }
    };

    keepWired(el, sync, cleanup);
}

// $blatNav(event[, opts]) — APG roving focus for composite widgets (menus,
// listboxes, toolbars). Moves DOM focus among the matching items in response to
// arrow / Home / End keys, wrapping by default. Does not change selection — that
// is the widget's job. opts: { selector, orientation: 'vertical'|'horizontal'|
// 'both', loop: true }.
function blatNavMagic(el) {
    return (e, opts = {}) => {
        if (!e || !e.key) return;
        const selector = opts.selector || '[role^="menuitem"], [role="option"]';
        const orientation = opts.orientation || 'vertical';
        const loop = opts.loop !== false;
        const horiz = orientation === 'horizontal' || orientation === 'both';
        const vert = orientation === 'vertical' || orientation === 'both';
        const items = Array.from(el.querySelectorAll(selector)).filter(
            (i) => i.offsetParent !== null && i.getAttribute('aria-disabled') !== 'true' && !i.hasAttribute('disabled'),
        );
        if (!items.length) return;
        const cur = items.indexOf(document.activeElement);
        // When the focus isn't already on one of the items, opt out (used by
        // accordion, whose panels contain their own focusable content).
        if (opts.requireMatch && cur < 0) return;
        let idx = null;
        if ((vert && e.key === 'ArrowDown') || (horiz && e.key === 'ArrowRight')) idx = cur < 0 ? 0 : cur + 1;
        else if ((vert && e.key === 'ArrowUp') || (horiz && e.key === 'ArrowLeft')) idx = cur < 0 ? items.length - 1 : cur - 1;
        else if (e.key === 'Home' || e.key === 'PageUp') idx = 0;
        else if (e.key === 'End' || e.key === 'PageDown') idx = items.length - 1;
        else return;
        if (loop) idx = (idx + items.length) % items.length;
        else idx = Math.max(0, Math.min(items.length - 1, idx));
        const target = items[idx];
        if (target) {
            target.focus();
            e.preventDefault();
            e.stopPropagation();
        }
    };
}

// $blatType(event[, selector]) — APG typeahead for menus/listboxes. Buffers the
// keys typed within 500ms and moves focus to the next matching item, wrapping.
function blatTypeMagic(el) {
    const DEFAULT = '[role^="menuitem"], [role="option"], [role="menuitemradio"], [role="menuitemcheckbox"]';
    return (e, selector = DEFAULT) => {
        if (!e || e.key == null || e.key.length !== 1 || e.ctrlKey || e.metaKey || e.altKey) return;
        const items = Array.from(el.querySelectorAll(selector)).filter(
            (i) => !i.hasAttribute('disabled') && i.getAttribute('aria-disabled') !== 'true' && i.offsetParent !== null,
        );
        if (!items.length) return;
        el._blatBuf = (el._blatBuf || '') + e.key.toLowerCase();
        clearTimeout(el._blatBufT);
        el._blatBufT = setTimeout(() => (el._blatBuf = ''), 500);
        const start = items.indexOf(document.activeElement);
        const ordered = items.slice(start + 1).concat(items.slice(0, start + 1));
        const match = ordered.find((i) => (i.textContent || '').trim().toLowerCase().startsWith(el._blatBuf));
        if (match) {
            match.focus();
            e.preventDefault();
        }
    };
}

// blatMenu — shared disclosure + roving-focus engine for menu widgets
// (dropdown-menu, context-menu). Mirrors the WAI-ARIA menu-button pattern:
//   * openMenu('first'|'last'|undefined) — open and place focus (a keyboard open
//     lands on the first/last item; a pointer open lands on the menu container).
//   * closeMenu(returnFocus) — close and (by default) restore focus to trigger.
// Item-to-item arrow navigation is handled by $blatNav on the content element;
// this object owns open state + initial/closing focus only.
const blatMenu = (config = {}) => ({
    open: config.open ?? false,
    x: 0,
    y: 0,
    _menu: null,
    _trigger: null,
    _closeTimer: null,
    // Context-menu entry point: open at the pointer position with first item focused.
    openAt(ev) {
        if (ev) {
            ev.preventDefault();
            this.x = ev.clientX;
            this.y = ev.clientY;
            this._trigger = ev.currentTarget || this._trigger;
        }
        this.openMenu('first');
    },
    get _items() {
        if (!this._menu) return [];
        return Array.from(this._menu.querySelectorAll('[role="menuitem"], [role="menuitemcheckbox"], [role="menuitemradio"]')).filter(
            (i) => i.getAttribute('aria-disabled') !== 'true' && !i.hasAttribute('disabled') && i.offsetParent !== null,
        );
    },
    openMenu(focus) {
        this.cancelClose();
        this.open = true;
        this.$nextTick(() => {
            if (!this._menu) return;
            const items = this._items;
            if (focus === 'first') (items[0] || this._menu).focus();
            else if (focus === 'last') (items[items.length - 1] || this._menu).focus();
            else this._menu.focus();
        });
    },
    toggleMenu() {
        this.open ? this.closeMenu(false) : this.openMenu();
    },
    closeMenu(returnFocus = true) {
        this.cancelClose();
        if (!this.open) return;
        this.open = false;
        if (returnFocus && this._trigger) this.$nextTick(() => this._trigger.focus());
    },
    // Submenus portal their content to <body> (to escape the scrolling parent
    // panel), so the wrapper's hover no longer covers the flyout. A short,
    // cancellable close lets the pointer cross the gap from trigger to submenu
    // without it snapping shut.
    closeSoon(delay = 120) {
        clearTimeout(this._closeTimer);
        this._closeTimer = setTimeout(() => this.closeMenu(false), delay);
    },
    cancelClose() {
        clearTimeout(this._closeTimer);
    },
});

// blatMenubar — WAI-ARIA menubar engine. Triggers share one `active` (the open
// menu's id) and a roving tabindex (`rovingId`); ArrowLeft/Right move between
// triggers (and switch the open menu when one is open); Down/Enter open a menu
// with its first item focused. Each menu's content keys off `active === id`.
const blatMenubar = () => ({
    active: null,
    rovingId: null,
    triggers: [],
    register(id, el) {
        this.triggers.push({ id, el });
        if (this.rovingId === null) this.rovingId = id;
    },
    _idx(id) {
        return this.triggers.findIndex((t) => t.id === id);
    },
    _items(id) {
        const m = document.getElementById(id);
        return m
            ? Array.from(m.querySelectorAll('[role="menuitem"], [role="menuitemcheckbox"], [role="menuitemradio"]')).filter(
                  (x) => x.getAttribute('aria-disabled') !== 'true' && x.offsetParent !== null,
              )
            : [];
    },
    focusTrigger(id) {
        const t = this.triggers.find((x) => x.id === id);
        if (t) {
            this.rovingId = id;
            t.el.focus();
        }
    },
    openMenu(id, focusFirst = true) {
        this.active = id;
        this.rovingId = id;
        if (focusFirst) this.$nextTick(() => (this._items(id)[0] || document.getElementById(id))?.focus());
    },
    toggleMenu(id) {
        this.active === id ? this.closeMenu(false) : this.openMenu(id);
    },
    moveTrigger(dir, fromId) {
        if (!this.triggers.length) return;
        const i = this._idx(fromId);
        if (i < 0) return;
        const next = this.triggers[(i + dir + this.triggers.length) % this.triggers.length].id;
        this.active !== null ? this.openMenu(next) : this.focusTrigger(next);
    },
    closeMenu(returnFocus = true) {
        const id = this.active;
        this.active = null;
        if (returnFocus && id) this.focusTrigger(id);
    },
});

// blatSelect — WAI-ARIA listbox/combobox engine for the <Select>. Opening focuses
// the selected option (or the first); arrow/typeahead navigation is handled by
// $blatNav/$blatType on the listbox; Enter/Space on a focused option selects and
// closes, restoring focus to the trigger.
/** Coerce a select's own initial value to string(s), in place, and hand the holder back. */
function normaliseSelectValue(model, multiple) {
    const v = model.value;
    model.value = multiple
        ? (Array.isArray(v) ? v.map(String) : v != null && v !== '' ? [String(v)] : [])
        : (v != null ? String(v) : '');

    return model;
}

const blatSelect = (config = {}) => ({
    multiple: !!config.multiple,
    open: false,
    // The value lives in the caller's model (a $blatModel — bound to wire:model, or holding it
    // locally when there is none). A wired value passes through verbatim: coercing what the
    // server sent would fight its own type. A local one is normalised to string(s), as before.
    _model: config.wired ? config.model : normaliseSelectValue(config.model, !!config.multiple),
    get value() { return this._model.value; },
    set value(v) { this._model.value = v; },
    // value -> label, registered by every <select-item> as it initialises. What the trigger shows
    // is DERIVED from this and the current value, not written down when the user picks: a value
    // the server assigns has to move the trigger too, and a `label` remembered at selection time
    // leaves it reading "Pick a plan" over a plan that is already chosen.
    _labels: {},
    get label() {
        if (this.multiple) return '';
        const v = this.value;

        return v === null || v === undefined || v === '' ? '' : (this._labels[String(v)] ?? '');
    },
    get selected() {
        const vs = this.multiple && Array.isArray(this.value) ? this.value : [];

        return vs.map((v) => ({ value: String(v), label: this._labels[String(v)] ?? String(v) }));
    },
    _list: null,
    _trigger: null,
    get _options() {
        return this._list
            ? Array.from(this._list.querySelectorAll('[role="option"]')).filter(
                  (o) => o.getAttribute('aria-disabled') !== 'true' && o.offsetParent !== null,
              )
            : [];
    },
    isSelected(val) {
        val = String(val);
        // Both sides through String(): a wire:model'd property arrives with the server's own
        // type, and a numeric 2 must still match the option that rendered value="2".
        return this.multiple
            ? (Array.isArray(this.value) ? this.value : []).map(String).includes(val)
            : String(this.value ?? '') === val;
    },
    openList() {
        this.open = true;
        this.$nextTick(() => {
            if (!this._list) return;
            const opts = this._options;
            (opts.find((o) => this.isSelected(o.dataset.value)) || opts[0] || this._list).focus();
        });
    },
    toggleList() {
        this.open ? this.close(false) : this.openList();
    },
    close(returnFocus = true) {
        if (!this.open) return;
        this.open = false;
        if (returnFocus && this._trigger) this.$nextTick(() => this._trigger.focus());
    },
    selectOption(val, lbl) {
        val = String(val);
        this._labels[val] = lbl;
        if (this.multiple) {
            // Assign rather than push/splice: `value` may BE the bound Livewire property, and a
            // mutation nobody assigned is a change wire:model.live never hears about.
            const current = (Array.isArray(this.value) ? this.value : []).map(String);
            this.value = current.includes(val) ? current.filter((v) => v !== val) : [...current, val];

            return; // keep the list open for further picks
        }
        this.value = val;
        this.close();
    },
    // Each <select-item> calls this on init, so the trigger can name a value that was already
    // set — by the server, by a morph, or by the page's initial render.
    seedSelected(val, lbl) {
        this._labels[String(val)] = lbl;
    },
    remove(val) {
        val = String(val);
        this.value = (Array.isArray(this.value) ? this.value : []).map(String).filter((v) => v !== val);
    },
});

// blatCommand — command-palette engine implementing the editable-combobox +
// listbox APG pattern with aria-activedescendant. Focus stays on the input; the
// "active" option is tracked by id and exposed via aria-activedescendant, moved
// with Arrow/Home/End, and triggered with Enter. Items register themselves and
// filter on the query.
const blatCommand = () => ({
    query: '',
    activeId: null,
    _entries: [],
    registerItem(el, keyword, disabled) {
        const id = ensureId(el, 'blat-cmd-item');
        this._entries.push({ id, el, keyword: (keyword || '').toLowerCase(), disabled: !!disabled });
        return id;
    },
    matches(kw) {
        return (kw || '').toLowerCase().includes(this.query.toLowerCase());
    },
    get _visible() {
        return this._entries.filter((i) => !i.disabled && this.matches(i.keyword) && i.el.offsetParent !== null);
    },
    get visibleCount() {
        return this._entries.filter((i) => this.matches(i.keyword)).length;
    },
    ensureActive() {
        const vis = this._visible;
        if (!vis.length) {
            this.activeId = null;
        } else if (!vis.some((i) => i.id === this.activeId)) {
            this.activeId = vis[0].id;
        }
    },
    move(dir) {
        const vis = this._visible;
        if (!vis.length) return;
        let idx = vis.findIndex((i) => i.id === this.activeId);
        idx = idx < 0 ? (dir > 0 ? 0 : vis.length - 1) : (idx + dir + vis.length) % vis.length;
        this.activeId = vis[idx].id;
        vis[idx].el.scrollIntoView({ block: 'nearest' });
    },
    edge(pos) {
        const vis = this._visible;
        if (!vis.length) return;
        const it = pos === 'last' ? vis[vis.length - 1] : vis[0];
        this.activeId = it.id;
        it.el.scrollIntoView({ block: 'nearest' });
    },
    selectActive() {
        const it = this._entries.find((i) => i.id === this.activeId);
        if (it && !it.disabled) it.el.click();
    },
});

// blatListbox — shared filtered-listbox engine behind <x-ui.combobox> (and its
// inline-input variant, formerly <x-ui.autocomplete>). Both render the SAME
// option list + selection model; they only differ in how the list is opened:
//
//   trigger: 'button' — a button opens a popover with the search input INSIDE it
//            (the classic shadcn Combobox). `query` resets on open; filtering is
//            always on; focus returns to the trigger on close.
//   trigger: 'input'  — the field itself IS the text input (the "autocomplete"
//            shape). `query` mirrors the selected label (single select); typing
//            filters; picking a single value fills the input and closes.
//
// Shared primitives (single-sourced here): isSelected, selected, matches,
// visible, visibleCount, ensureActive, move, edge, selectActive, select, remove.
const blatListbox = (config = {}) => ({
    trigger: config.trigger || 'button', // 'button' | 'input'
    open: false,
    // `filtering` only matters for the input trigger: the list shows ALL options
    // until the user actually types, so opening a field with a value shows siblings.
    filtering: false,
    multiple: !!config.multiple,
    // The value lives in the caller's model — a $blatModel bound to wire:model, or the same
    // object holding it locally when there is none. One source of truth either way.
    _model: config.model ?? { value: config.value ?? (config.multiple ? [] : '') },
    get value() { const v = this._model.value; return v ?? (this.multiple ? [] : ''); },
    set value(v) { this._model.value = v; },
    activeValue: null,
    options: config.options || [],
    // Seed the query: input/single starts showing the selected label.
    query: config.query ?? '',

    isSelected(v) { return this.multiple ? this.value.includes(v) : this.value === v; },
    get selected() { return this.options.filter((o) => this.isSelected(o.value)); },
    // Single-select trigger label (button variant renders this).
    get label() { const o = this.options.find((o) => o.value === this.value); return o ? o.label : ''; },
    matches(label) { return label.toLowerCase().includes(this.query.toLowerCase()); },
    get visible() {
        // Input trigger only filters once the user types; button always filters by query.
        if (this.trigger === 'input' && !this.filtering) return this.options;
        return this.options.filter((o) => this.matches(o.label));
    },
    get visibleCount() { return this.visible.length; },
    ensureActive() {
        const v = this.visible;
        if (!v.length) { this.activeValue = null; return; }
        if (!v.some((o) => o.value === this.activeValue)) {
            this.activeValue = (v.find((o) => this.isSelected(o.value)) || v[0]).value;
        }
    },
    move(dir) {
        if (this.trigger === 'input' && !this.open) { this.openList(); return; }
        const v = this.visible;
        if (!v.length) return;
        let i = v.findIndex((o) => o.value === this.activeValue);
        i = i < 0 ? 0 : (i + dir + v.length) % v.length;
        this.activeValue = v[i].value;
    },
    edge(pos) {
        const v = this.visible;
        if (!v.length) return;
        this.activeValue = (pos === 'last' ? v[v.length - 1] : v[0]).value;
    },
    openList() {
        this.open = true;
        if (this.trigger === 'button') {
            this.query = '';
            this.$nextTick(() => { this.ensureActive(); (this.$refs.search || this.$refs.list)?.focus(); });
        } else {
            this.filtering = false;
            this.$nextTick(() => this.ensureActive());
        }
    },
    // Input trigger: typing opens + filters; single select clears the committed value.
    onInput() {
        if (!this.multiple) this.value = '';
        this.open = true;
        this.filtering = true;
        this.$nextTick(() => this.ensureActive());
    },
    toggle() { this.open ? this.close(false) : this.openList(); },
    close(returnFocus = true) {
        if (!this.open) return;
        this.open = false;
        this.filtering = false;
        if (this.trigger === 'button' && returnFocus) this.$nextTick(() => this.$refs.trigger?.focus());
    },
    selectActive() { if (this.activeValue != null) this.select(this.activeValue); },
    select(v) {
        if (this.multiple) {
            // Assign rather than push/splice: `value` may BE the bound Livewire property, and a
            // mutation nobody assigned is a change wire:model.live never hears about.
            const current = this.value;
            this.value = current.includes(v) ? current.filter((x) => x !== v) : [...current, v];
            if (this.trigger === 'input') {
                this.query = '';
                this.filtering = false;
                this.$nextTick(() => this.$refs.input?.focus());
            }
            return; // keep the list open for further picks
        }
        if (this.trigger === 'input') {
            const o = this.options.find((x) => x.value === v);
            if (o) { this.value = o.value; this.query = o.label; }
            this.close();
            return;
        }
        this.value = this.value === v ? '' : v; // toggle off in single button mode (matches shadcn)
        this.close();
        this.query = '';
    },
    remove(v) { this.value = this.value.filter((x) => x !== v); },
    // Input trigger: Backspace on an empty query pops the last chip.
    backspace() { if (this.multiple && this.query === '' && this.value.length) this.value = this.value.slice(0, -1); },
});

// ---------------------------------------------------------------------------
// Register every BlatUI Alpine piece — plugins, the theme store, the calendar
// component and the a11y primitives — into an Alpine instance. (Charts are
// opt-in: see blatui-charts.js / registerCharts.)
// Call this BEFORE Alpine.start().
//
//   Greenfield (no Alpine yet): the published blatui.js does this for you.
//   Existing Alpine app:        import { registerBlatUI } from './blatui-core.js'
//                               and call registerBlatUI(Alpine) before your start.
// ---------------------------------------------------------------------------
export function registerBlatUI(Alpine, options = {}) {
    // darkMode: 'class' (default — light until an explicit toggle), 'system' (follow the OS
    // prefers-color-scheme), or false (hard light-only). The default does NOT auto-apply the
    // OS dark preference, so a light-only app never flips to an unreadable dark on a dark OS.
    if (options.darkMode !== undefined) themeStore.darkMode = options.darkMode;
    Alpine.plugin(anchor);
    Alpine.plugin(focus);
    Alpine.plugin(collapse);
    Alpine.store('theme', themeStore);
    Alpine.data('calendar', calendar);
    Alpine.data('blatMenu', blatMenu);
    Alpine.data('blatMenubar', blatMenubar);
    Alpine.data('blatSelect', blatSelect);
    Alpine.data('blatListbox', blatListbox);
    Alpine.data('blatCommand', blatCommand);
    Alpine.directive('blat-trigger', blatTriggerDirective);
    Alpine.directive('blat-labelledby', blatLabelledByDirective);
    Alpine.directive('blat-anchor', blatAnchorDirective);
    Alpine.directive('blat-dialog-layer', blatDialogLayerDirective);
    Alpine.directive('blat-field', blatFieldDirective);
    Alpine.magic('blatModel', blatModelMagic);
    Alpine.magic('blatWire', blatWire);
    Alpine.magic('blatNumber', () => blatNumber);
    Alpine.magic('blatNav', blatNavMagic);
    Alpine.magic('blatType', blatTypeMagic);
}
