<?php

$theme = $theme ?? 'default';
$themeConfigPath = ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'theme.json';
$fallbackConfigPath = ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'theme.json';
$themeConfig = is_file($themeConfigPath) ? json_decode(file_get_contents($themeConfigPath), true) : [];

if (empty($themeConfig['presets']) && is_file($fallbackConfigPath)) {
    $themeConfig = json_decode(file_get_contents($fallbackConfigPath), true) ?: [];
}

$presets = $themeConfig['presets'] ?? [];
?>

<div class="theme-editor" data-theme-editor>
    <div class="d-flex align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom">
        <h6 class="fw-bold mb-0 me-auto"><?= phrase('Theme Mode') ?></h6>
        <div class="btn-group btn-group-sm" role="group" aria-label="<?= phrase('Theme Mode'); ?>" data-theme-mode-toggle>
            <button type="button" class="btn btn-outline-secondary rounded-pill rounded-end-0" data-mode-val="light"><i class="mdi mdi-weather-sunny me-1"></i><?= phrase('Light') ?></button>
            <button type="button" class="btn btn-outline-secondary rounded-pill rounded-start-0" data-mode-val="dark"><i class="mdi mdi-weather-night me-1"></i><?= phrase('Dark') ?></button>
        </div>
    </div>
    <div class="row g-5">
        <div class="col-lg-5">
            <h6 class="fw-bold mb-2"><?= phrase('Presets') ?></h6>
            <div class="row g-2 mb-3" data-theme-presets></div>
        </div>
        <div class="col-lg-7">
            <h6 class="fw-bold mb-2"><?= phrase('Custom Colors') ?></h6>
            <div data-theme-fields></div>
        </div>
    </div>
    <hr class="border-secondary" style="margin-inline:-1rem" />
    <div class="d-flex gap-2 justify-content-end mt-3">
        <button type="button" class="btn btn-outline-secondary" data-theme-reset><i class="mdi mdi-restore"></i> <?= phrase('Reset') ?></button>
        <button type="button" class="btn btn-primary" data-theme-save><i class="mdi mdi-content-save-outline"></i> <?= phrase('Save') ?></button>
    </div>
</div>

<script type="text/javascript">
    (function() {
        const presets = <?= json_encode($presets, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) ?>;
        const tokens = ['background', 'surface', 'foreground', 'primary', 'secondary', 'accent', 'muted', 'secondaryBg', 'tertiaryBg', 'border', 'success', 'info', 'warning', 'danger'];
        const labels = {
            background: 'Background',
            surface: 'Surface',
            foreground: 'Text',
            primary: 'Primary',
            secondary: 'Secondary',
            accent: 'Accent',
            muted: 'Muted',
            secondaryBg: 'Secondary Background',
            tertiaryBg: 'Tertiary Background',
            border: 'Border',
            success: 'Success',
            info: 'Info',
            warning: 'Warning',
            danger: 'Danger'
        };
        const groups = [
            { label: 'Base', tokens: ['background', 'surface', 'foreground', 'muted', 'secondaryBg', 'tertiaryBg', 'border'] },
            { label: 'Brand', tokens: ['primary', 'secondary', 'accent'] },
            { label: 'Status', tokens: ['success', 'info', 'warning', 'danger'] }
        ];
        let mode = (window.AksaraTheme && window.AksaraTheme.initialTheme) || document.documentElement.getAttribute('data-bs-theme') || 'light';
        const state = window.AksaraTheme && window.AksaraTheme.config ? JSON.parse(JSON.stringify(window.AksaraTheme.config)) : { baseTheme: 'default', activeMode: mode, overrides: { light: {}, dark: {} } };
        const root = document.querySelector('[data-theme-editor]');
        const fields = root.querySelector('[data-theme-fields]');
        const presetWrap = root.querySelector('[data-theme-presets]');

        function hexToRgb(hex) {
            const v = hex.replace('#', '');
            return [
                parseInt(v.substr(0, 2), 16),
                parseInt(v.substr(2, 2), 16),
                parseInt(v.substr(4, 2), 16)
            ];
        }

        function preset() {
            return presets.find(item => item.id === state.baseTheme) || presets[0];
        }

        function color(token) {
            const presetColors = preset().colors[mode];
            return state.overrides[mode] && state.overrides[mode][token] ? state.overrides[mode][token] : (presetColors[token] || presetColors.muted);
        }

        function cssVars() {
            const values = {};
            tokens.forEach(token => {
                values[token] = color(token);
            });

            const primaryRgb = hexToRgb(values.primary).join(', ');
            const secondaryRgb = hexToRgb(values.secondary).join(', ');
            const accentRgb = hexToRgb(values.accent).join(', ');
            const successRgb = hexToRgb(values.success).join(', ');
            const infoRgb = hexToRgb(values.info).join(', ');
            const warningRgb = hexToRgb(values.warning).join(', ');
            const dangerRgb = hexToRgb(values.danger).join(', ');
            const backgroundRgb = hexToRgb(values.background).join(', ');
            const foregroundRgb = hexToRgb(values.foreground).join(', ');
            const secondaryBgRgb = hexToRgb(values.secondaryBg).join(', ');
            const tertiaryBgRgb = hexToRgb(values.tertiaryBg).join(', ');

            return [
                '--wg-primary:' + values.primary,
                '--wg-primary-hover:' + values.accent,
                '--wg-text:' + values.foreground,
                '--wg-text-light:' + values.secondary,
                '--wg-bg-light:' + values.muted,
                '--wg-border:' + values.border,
                '--bs-primary:' + values.primary,
                '--bs-primary-rgb:' + primaryRgb,
                '--bs-secondary:' + values.secondary,
                '--bs-secondary-rgb:' + secondaryRgb,
                '--bs-accent:' + values.accent,
                '--bs-accent-rgb:' + accentRgb,
                '--bs-success:' + values.success,
                '--bs-success-rgb:' + successRgb,
                '--bs-info:' + values.info,
                '--bs-info-rgb:' + infoRgb,
                '--bs-warning:' + values.warning,
                '--bs-warning-rgb:' + warningRgb,
                '--bs-danger:' + values.danger,
                '--bs-danger-rgb:' + dangerRgb,
                '--bs-body-bg:' + values.background,
                '--bs-body-bg-rgb:' + backgroundRgb,
                '--bs-body-color:' + values.foreground,
                '--bs-body-color-rgb:' + foregroundRgb,
                '--bs-emphasis-color:' + values.foreground,
                '--bs-emphasis-color-rgb:' + foregroundRgb,
                '--bs-link-color:' + values.primary,
                '--bs-link-hover-color:' + values.accent,
                '--bs-border-color:' + values.border,
                '--bs-surface-bg:' + values.surface,
                '--bs-tertiary-bg:' + values.tertiaryBg,
                '--bs-tertiary-bg-rgb:' + tertiaryBgRgb,
                '--bs-secondary-bg:' + values.secondaryBg,
                '--bs-secondary-bg-rgb:' + secondaryBgRgb,
                '--bs-breadcrumb-bg:' + values.muted,
                '--bs-sidebar-bg:' + values.surface,
                '--range-color:' + values.accent,
                '--range-track-color:' + values.surface,
                '--range-track-border:' + values.border
            ].join(';');
        }

        function renderModeToggle() {
            const toggleBtns = root.querySelectorAll('[data-mode-val]');
            toggleBtns.forEach(btn => {
                const val = btn.getAttribute('data-mode-val');
                if (val === mode) {
                    btn.classList.add('btn-primary', 'active');
                    btn.classList.remove('btn-outline-secondary');
                } else {
                    btn.classList.remove('btn-primary', 'active');
                    btn.classList.add('btn-outline-secondary');
                }
            });
        }

        function apply() {
            let style = document.getElementById('aksara-user-theme-vars');
            const activeMode = mode;
            const blocks = [];

            if (!style) {
                style = document.createElement('style');
                style.id = 'aksara-user-theme-vars';
                document.head.appendChild(style);
            }

            ['light', 'dark'].forEach(item => {
                if (item !== activeMode && state.overrides[item] && Object.keys(state.overrides[item]).length) {
                    mode = item;
                    blocks.push('[data-bs-theme="' + item + '"]{' + cssVars() + '}');
                }
            });

            mode = activeMode;
            blocks.push('[data-bs-theme="' + mode + '"]{' + cssVars() + '}');
            style.textContent = blocks.join('');
            renderModeToggle();

            if (typeof updateStatusBarColor === 'function') {
                updateStatusBarColor(color('primary'));
            }
        }

        function thumbnail(colors) {
            return '<svg viewBox="0 0 120 72" width="100%" height="72" role="img" aria-hidden="true"><rect width="120" height="72" rx="8" fill="' + colors.background + '"/><rect x="10" y="10" width="100" height="36" rx="6" fill="' + colors.surface + '" stroke="' + colors.border + '"/><circle cx="25" cy="28" r="8" fill="' + colors.primary + '"/><rect x="42" y="19" width="48" height="6" rx="3" fill="' + colors.foreground + '"/><rect x="42" y="32" width="34" height="5" rx="2.5" fill="' + colors.secondary + '"/><rect x="10" y="55" width="28" height="8" rx="4" fill="' + colors.primary + '"/><rect x="46" y="55" width="28" height="8" rx="4" fill="' + colors.accent + '"/><rect x="82" y="55" width="28" height="8" rx="4" fill="' + colors.muted + '"/></svg>';
        }

        function renderPresets() {
            presetWrap.innerHTML = presets.map(item => {
                return '<div class="col-6"><button type="button" class="btn btn-outline-secondary rounded-4 w-100 p-2 text-start ' + (item.id === state.baseTheme ? 'active' : '') + '" data-preset="' + item.id + '">' + thumbnail(item.colors[mode]) + '<span class="d-block mt-2 fw-semibold text-truncate text-center">' + item.name + '</span></button></div>';
            }).join('');
        }

        function renderFields() {
            fields.innerHTML = groups.map(group => {
                return '<div class="mb-3"><h6 class="fw-bold mb-2">' + group.label + '</h6><div class="row g-3">' + group.tokens.map(token => {
                    return '<div class="col-md-6"><label class="form-label small fw-semibold">' + labels[token] + '</label><div class="input-group"><input type="color" class="form-control form-control-color" data-token="' + token + '" value="' + color(token) + '"><input type="text" class="form-control" data-token-text="' + token + '" value="' + color(token) + '"></div></div>';
                }).join('') + '</div></div>';
            }).join('');
        }

        function currentPresetColors(targetMode) {
            const m = targetMode || mode;
            const output = {};
            const currentPreset = preset();

            if (currentPreset && currentPreset.colors && currentPreset.colors[m]) {
                tokens.forEach(token => {
                    const presetColors = currentPreset.colors[m];
                    output[token] = presetColors[token] || presetColors.muted;
                });
            }

            return output;
        }

        function saveTheme(silent) {
            state.activeMode = mode;
            ['light', 'dark'].forEach(m => {
                state.overrides[m] = state.overrides[m] || {};
                const currentPreset = preset();
                tokens.forEach(token => {
                    if (!state.overrides[m][token]) {
                        const presetColors = currentPreset && currentPreset.colors && currentPreset.colors[m] ? currentPreset.colors[m] : {};
                        state.overrides[m][token] = presetColors[token] || presetColors.muted;
                    }
                });
            });

            $.ajax({
                url: (typeof config !== 'undefined' && config.baseUrl ? config.baseUrl : '/') + 'xhr/theme/save',
                method: 'POST',
                data: { theme: JSON.stringify(state) },
                statusCode: {
                    301: function(response) {
                        response = response && response.responseJSON ? response.responseJSON : response;
                        const savedTheme = response && response.data && response.data.theme ? response.data.theme : state;

                        if (savedTheme) {
                            window.AksaraTheme.config = savedTheme;
                            window.AksaraTheme.hasUserTheme = true;
                            window.AksaraTheme.initialTheme = savedTheme.activeMode || mode;
                            document.documentElement.setAttribute('data-bs-theme', savedTheme.activeMode || mode);
                            try {
                                localStorage.setItem('bs-theme', savedTheme.activeMode || mode);
                                const encoded = encodeURIComponent(btoa(JSON.stringify(savedTheme)));
                                document.cookie = 'aksara_theme_config=' + encoded + '; path=/; max-age=31536000; SameSite=Lax';
                                document.cookie = 'aksara_theme=' + (savedTheme.activeMode || mode) + '; path=/; max-age=31536000; SameSite=Lax';
                            } catch (e) {}
                        }

                        if (!silent) {
                            $(root).closest('.modal').modal('hide');
                        }
                    }
                }
            });
        }

        renderPresets();
        renderFields();
        apply();

        root.addEventListener('click', function(event) {
            const modeBtn = event.target.closest('[data-mode-val]');
            if (modeBtn) {
                const selectedMode = modeBtn.getAttribute('data-mode-val');
                if (selectedMode !== mode) {
                    mode = selectedMode;
                    state.activeMode = mode;
                    document.documentElement.setAttribute('data-bs-theme', mode);
                    renderPresets();
                    renderFields();
                    apply();
                    saveTheme(true);
                }
            }

            const presetButton = event.target.closest('[data-preset]');
            if (presetButton) {
                state.baseTheme = presetButton.getAttribute('data-preset');
                ['light', 'dark'].forEach(m => {
                    state.overrides[m] = currentPresetColors(m);
                });
                renderPresets();
                renderFields();
                apply();
                saveTheme(true);
            }

            if (event.target.closest('[data-theme-reset]')) {
                ['light', 'dark'].forEach(m => {
                    state.overrides[m] = currentPresetColors(m);
                });
                renderFields();
                apply();
                saveTheme(true);
            }

            if (event.target.closest('[data-theme-save]')) {
                saveTheme(false);
            }
        });

        root.addEventListener('input', function(event) {
            const token = event.target.getAttribute('data-token') || event.target.getAttribute('data-token-text');
            if (!token || !/^#[0-9a-fA-F]{6}$/.test(event.target.value)) {
                return;
            }

            state.overrides[mode] = state.overrides[mode] || {};
            state.overrides[mode][token] = event.target.value.toLowerCase();

            root.querySelectorAll('[data-token="' + token + '"],[data-token-text="' + token + '"]').forEach(input => {
                if (input !== event.target) {
                    input.value = event.target.value.toLowerCase();
                }
            });

            apply();
        });
    })();
</script>
