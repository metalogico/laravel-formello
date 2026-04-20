(function () {
    'use strict';

    // ── Client State (mirrors PHP FormelloState API) ─────────────
    class FormelloClientState {
        constructor(form_el) {
            this._form = form_el;
            this._value_changes = {};
            this._options_changes = {};
            this._attributes_changes = {};
        }

        get(field) {
            const el = this._findInput(field);
            if (!el) return null;
            if (el.type === 'checkbox') return el.checked;
            if (el.type === 'radio') {
                const checked = this._form.querySelector(
                    `input[type="radio"][name="${CSS.escape(field)}"]:checked`
                );
                return checked ? checked.value : null;
            }
            return el.value;
        }

        set(field, value) {
            this._value_changes[field] = value;
        }

        setOptions(field, options) {
            this._options_changes[field] = options;
        }

        setAttributes(field, attrs) {
            this._attributes_changes[field] = Object.assign(
                this._attributes_changes[field] || {}, attrs
            );
        }

        getChanges() {
            return {
                values: this._value_changes,
                options: this._options_changes,
                attributes: this._attributes_changes,
            };
        }

        _findInput(field) {
            return this._form.querySelector(`#${CSS.escape(field)}`)
                || this._form.querySelector(`[name="${CSS.escape(field)}"]`);
        }
    }

    // ── Reactive Engine ──────────────────────────────────────────
    const engine = {
        form_el: null,
        reactive_map: {},
        compute_url: null,
        model_class: null,
        model_id: null,
        debounce_ms: 300,
        timers: {},

        init() {
            this.form_el = document.querySelector('[data-formello-reactive]');
            if (!this.form_el) return;

            try {
                this.reactive_map = JSON.parse(
                    this.form_el.getAttribute('data-formello-reactive') || '{}'
                );
            } catch (e) { this.reactive_map = {}; }

            this.compute_url = this.form_el.dataset.formelloCompute || null;
            this.model_class = this.form_el.dataset.formelloModelClass || null;
            this.model_id = this.form_el.dataset.formelloModelId || null;

            this.setupListeners();
            this.runInitialClientCallbacks();
        },

        // Run all client callbacks once on load (e.g. to hide fields)
        runInitialClientCallbacks() {
            const seen = new Set();
            for (const [field, config] of Object.entries(this.reactive_map)) {
                const callbacks = this.normalizeCallbacks(config.client);
                for (const cb of callbacks) {
                    if (!seen.has(cb)) {
                        seen.add(cb);
                        this.executeClientCallback(cb);
                    }
                }
            }
        },

        setupListeners() {
            // Only listen on fields that have reactive config
            for (const field_name of Object.keys(this.reactive_map)) {
                const wrapper = this.form_el.querySelector(
                    `[data-formello-field="${CSS.escape(field_name)}"]`
                );
                if (!wrapper) continue;

                // Text/number/email/range (debounced)
                wrapper.querySelectorAll('input[type="text"],input[type="number"],input[type="email"],input[type="hidden"],input[type="range"],textarea')
                    .forEach(el => {
                        el.addEventListener('input', () => this.debounce(field_name));
                        el.addEventListener('change', () => this.trigger(field_name));
                    });

                // Select
                wrapper.querySelectorAll('select')
                    .forEach(el => el.addEventListener('change', () => this.trigger(field_name)));

                // Checkbox
                wrapper.querySelectorAll('input[type="checkbox"]')
                    .forEach(el => el.addEventListener('change', () => this.trigger(field_name)));

                // Radio
                wrapper.querySelectorAll('input[type="radio"]')
                    .forEach(el => el.addEventListener('change', (e) => {
                        if (e.target.checked) this.trigger(field_name);
                    }));
            }
        },

        debounce(field_name) {
            clearTimeout(this.timers[field_name]);
            this.timers[field_name] = setTimeout(
                () => this.trigger(field_name), this.debounce_ms
            );
        },

        trigger(field_name) {
            const config = this.reactive_map[field_name];
            if (!config) return;

            // 1. Client callbacks (instant)
            const client_callbacks = this.normalizeCallbacks(config.client);
            for (const cb of client_callbacks) {
                this.executeClientCallback(cb);
            }

            // 2. Server callbacks (async)
            const server_callbacks = this.normalizeCallbacks(config.server);
            if (server_callbacks.length > 0 && this.compute_url) {
                this.executeServerCallbacks(field_name);
            }
        },

        executeClientCallback(callback_name) {
            const fn = window.FormelloReactive && window.FormelloReactive[callback_name];
            if (typeof fn !== 'function') {
                console.warn(`FormelloReactive: callback '${callback_name}' not found`);
                return;
            }

            const state = new FormelloClientState(this.form_el);
            fn(state);
            this.applyChanges(state.getChanges());
        },

        async executeServerCallbacks(changed_field) {
            // Collect all current form values
            const form_data = {};
            const inputs = this.form_el.querySelectorAll('[name]');
            inputs.forEach(el => {
                const name = el.name.replace(/\[\]$/, '');
                if (el.type === 'checkbox') {
                    form_data[name] = el.checked;
                } else if (el.type === 'radio') {
                    if (el.checked) form_data[name] = el.value;
                } else {
                    form_data[name] = el.value;
                }
            });

            const csrf = document.querySelector('meta[name="csrf-token"]');

            try {
                const resp = await fetch(this.compute_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf ? csrf.content : '',
                    },
                    body: JSON.stringify({
                        form_class: this.form_el.dataset.formelloClass,
                        changed_field: changed_field,
                        form_data: form_data,
                        model_class: this.model_class,
                        model_id: this.model_id,
                    }),
                });

                if (!resp.ok) {
                    console.error('FormelloReactive server error:', resp.status);
                    return;
                }

                const changes = await resp.json();
                this.applyChanges(changes);
            } catch (e) {
                console.error('FormelloReactive server error:', e);
            }
        },

        // ── Apply changes to DOM ─────────────────────────────────
        applyChanges(changes) {
            // Values
            for (const [field, value] of Object.entries(changes.values || {})) {
                this.setFieldValue(field, value);
            }
            // Options
            for (const [field, options] of Object.entries(changes.options || {})) {
                this.setFieldOptions(field, options);
            }
            // Attributes
            for (const [field, attrs] of Object.entries(changes.attributes || {})) {
                this.setFieldAttributes(field, attrs);
            }
        },

        setFieldValue(field, value) {
            const el = this.form_el.querySelector(`#${CSS.escape(field)}`)
                || this.form_el.querySelector(`[name="${CSS.escape(field)}"]`);
            if (!el) return;

            // Widget-aware setters
            if (el.tomselect) { el.tomselect.setValue(value, true); return; }
            if (el._flatpickr) { el._flatpickr.setDate(value, true); return; }
            if (el._jodit) { el._jodit.value = value; return; }

            if (el.type === 'checkbox') { el.checked = !!value; }
            else if (el.type === 'radio') {
                this.form_el.querySelectorAll(`input[type="radio"][name="${CSS.escape(field)}"]`)
                    .forEach(r => r.checked = (r.value == value));
            }
            else { el.value = value ?? ''; }

            el.dispatchEvent(new Event('change', { bubbles: true }));
        },

        setFieldOptions(field, options) {
            const el = this.form_el.querySelector(`select#${CSS.escape(field)}`)
                || this.form_el.querySelector(`select[name="${CSS.escape(field)}"]`);
            if (!el) return;

            if (el.tomselect) {
                el.tomselect.clear(true);
                el.tomselect.clearOptions();
                for (const [v, t] of Object.entries(options)) {
                    el.tomselect.addOption({ id: v, text: t });
                }
                el.tomselect.refreshOptions(false);
                return;
            }

            // Native select
            const placeholder = el.querySelector('option[value=""]');
            el.innerHTML = '';
            if (placeholder) el.appendChild(placeholder);
            else { const o = document.createElement('option'); o.value = ''; el.appendChild(o); }

            for (const [v, t] of Object.entries(options)) {
                const o = document.createElement('option');
                o.value = v;
                o.textContent = t;
                el.appendChild(o);
            }
        },

        setFieldAttributes(field, attrs) {
            const el = this.form_el.querySelector(`#${CSS.escape(field)}`)
                || this.form_el.querySelector(`[name="${CSS.escape(field)}"]`);
            if (!el) return;

            // hidden applies to the wrapper, not the input
            if ('hidden' in attrs) {
                const wrapper = this.form_el.querySelector(
                    `[data-formello-field="${CSS.escape(field)}"]`
                );
                if (wrapper) {
                    wrapper.style.display = attrs.hidden ? 'none' : '';
                }
            }

            // Boolean attributes on the input
            for (const key of ['disabled', 'readonly', 'required']) {
                if (key in attrs) {
                    attrs[key] ? el.setAttribute(key, '') : el.removeAttribute(key);
                    // Sync TomSelect disabled state
                    if (key === 'disabled' && el.tomselect) {
                        attrs[key] ? el.tomselect.disable() : el.tomselect.enable();
                    }
                }
            }

            if ('placeholder' in attrs) {
                el.setAttribute('placeholder', attrs.placeholder ?? '');
            }

            if ('class' in attrs) {
                const classes = Array.isArray(attrs.class)
                    ? attrs.class
                    : String(attrs.class || '').split(/\s+/).filter(Boolean);
                classes.forEach(c => el.classList.add(c));
            }

            if ('removeClass' in attrs) {
                const classes = Array.isArray(attrs.removeClass)
                    ? attrs.removeClass
                    : String(attrs.removeClass || '').split(/\s+/).filter(Boolean);
                classes.forEach(c => el.classList.remove(c));
            }
        },

        // ── Helpers ──────────────────────────────────────────────
        normalizeCallbacks(val) {
            if (!val) return [];
            return Array.isArray(val) ? val : [val];
        },
    };

    // Boot
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => engine.init());
    } else {
        engine.init();
    }
})();
