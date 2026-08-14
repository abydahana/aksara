(function() {
    const baseUrl = () => (typeof config !== 'undefined' && config.base_url ? config.base_url : ((window.config && window.config.base_url) ? window.config.base_url : '/'));
    const endpoint = (window.AksaraAI && window.AksaraAI.endpoint)
        ? window.AksaraAI.endpoint
        : (baseUrl() + 'xhr/ai');
    const token = () => document.querySelector('meta[name="_token"]')?.getAttribute('content') || '';
    const phrase = window.phrase || ((value) => value);
    let activeForm = null;
    let generatedFields = {};
    let generatedLabels = {};
    let loadingTimer = null;
    let dotTimer = null;
    const hiddenPreviewNames = ['headline', 'featured', 'featured_image', 'status'];
    const loadingTexts = [
        'The assistant is reading the form...',
        'The assistant is preparing your request...',
        'The assistant is arranging the content...',
        'The assistant is matching fields carefully...',
        'The assistant is almost done...'
    ];

    function fieldValue(field) {
        if (window.jQuery && window.jQuery(field).next('.note-editor').length && window.jQuery(field).summernote) {
            return window.jQuery(field).summernote('code');
        }

        if ('checkbox' === field.type) {
            return field.checked ? (field.value || '1') : '';
        }

        if ('radio' === field.type) {
            const checked = field.form.querySelector('[name="' + cssEscape(field.name) + '"]:checked');

            return checked ? checked.value : '';
        }

        return field.value || '';
    }

    function setFieldValue(field, value, label) {
        if (field.name === 'page_content' && applyPageBuilderLayout(value)) {
            field.value = stringifyPageBuilderValue(value);
        } else if (window.jQuery && window.jQuery(field).next('.note-editor').length && window.jQuery(field).summernote) {
            window.jQuery(field).summernote('code', value);
        } else if (field.type === 'file' && String(value || '').startsWith('data:image/')) {
            setFileInput(field, value, label);
        } else if ('SELECT' === field.tagName && window.jQuery) {
            ensureSelectOption(field, value, label);
            window.jQuery(field).val(String(value)).trigger('change').trigger('change.select2');
            setTimeout(() => window.jQuery(field).trigger('change').trigger('change.select2'), 50);
        } else if ('checkbox' === field.type) {
            field.checked = Array.isArray(value) ? value.map(String).includes(String(field.value)) : !!value && String(value) !== '0';
        } else if ('radio' === field.type) {
            const target = field.form.querySelector('[name="' + cssEscape(field.name) + '"][value="' + cssEscape(String(value)) + '"]');

            if (target) {
                target.checked = true;
            }
        } else {
            field.value = value == null ? '' : value;
        }

        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));

        if ('TEXTAREA' === field.tagName) {
            field.dispatchEvent(new Event('keyup', { bubbles: true }));
        }

        if (window.jQuery && 'SELECT' !== field.tagName) {
            window.jQuery(field).trigger('input').trigger('change').trigger('keyup').trigger('change.select2');
        }
    }

    function applyPageBuilderLayout(value) {
        if (! window._pageBuilder || ! value) {
            return false;
        }

        let layout = value;

        if ('string' === typeof value) {
            try {
                layout = JSON.parse(value);
            } catch (error) {
                return false;
            }
        }

        if (! layout || ! Array.isArray(layout.components)) {
            return false;
        }

        layout.version = layout.version || '1.0';
        layout.framework = layout.framework || 'bootstrap5';
        normalizePageBuilderImages(layout.components);
        window._pageBuilder.layout = layout;
        window._pageBuilder.selectedId = null;

        if (typeof window._pageBuilder.saveToHistory === 'function') {
            window._pageBuilder.saveToHistory();
        }

        if (typeof window._pageBuilder.render === 'function') {
            window._pageBuilder.render();
        }

        if (typeof window._pageBuilder.renderProperties === 'function') {
            window._pageBuilder.renderProperties();
        }

        if (typeof window._pageBuilder.syncToInput === 'function') {
            window._pageBuilder.syncToInput();
        }

        return true;
    }

    function stringifyPageBuilderValue(value) {
        if ('string' === typeof value) {
            return value;
        }

        return JSON.stringify(value || { version: '1.0', framework: 'bootstrap5', components: [] });
    }

    function normalizePageBuilderImages(components) {
        const placeholder = baseUrl() + 'uploads/pages/placeholder.png';

        walkComponents(components, (component) => {
            if (! component.props) {
                return;
            }

            imagePropKeys(component.type).forEach((key) => {
                if (Object.prototype.hasOwnProperty.call(component.props, key) && ! component.props[key]) {
                    component.props[key] = placeholder;
                }
            });

            if (Array.isArray(component.props.items)) {
                component.props.items.forEach((item) => {
                    ['src', 'image'].forEach((key) => {
                        if (Object.prototype.hasOwnProperty.call(item, key) && ! item[key]) {
                            item[key] = placeholder;
                        }
                    });
                });
            }
        });
    }

    function imagePropKeys(type) {
        return ['section', 'container', 'row', 'column'].includes(type)
            ? []
            : ('hero' === type ? ['background'] : ['src', 'image']);
    }

    function setFileInput(field, dataUrl, label) {
        const file = dataUrlToFile(dataUrl, slugify(label || field.name || 'ai-image') + '.png');

        if (! file || ! window.DataTransfer) {
            return;
        }

        const transfer = new DataTransfer();

        transfer.items.add(file);
        field.files = transfer.files;

        const preview = field.closest('.fileupload')?.querySelector('img.upload_preview, img');

        if (preview) {
            preview.src = dataUrl;
        }
    }

    function dataUrlToFile(dataUrl, filename) {
        const parts = String(dataUrl).split(',');
        const mime = (parts[0].match(/:(.*?);/) || [])[1] || 'image/png';
        const binary = atob(parts[1] || '');
        const bytes = new Uint8Array(binary.length);

        for (let index = 0; index < binary.length; index++) {
            bytes[index] = binary.charCodeAt(index);
        }

        return new File([bytes], filename, {
            type: mime
        });
    }

    function slugify(value) {
        return String(value).toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'ai-image';
    }

    function ensureSelectOption(field, value, label) {
        const values = Array.isArray(value) ? value : [value];

        values.forEach((item) => {
            const optionValue = item == null ? '' : String(item);

            if (optionValue === '') {
                return;
            }

            if (! Array.from(field.options).some((option) => option.value === optionValue)) {
                const option = new Option(label || optionValue, optionValue, true, true);

                field.add(option);
            }
        });
    }

    function cssEscape(value) {
        return window.CSS && window.CSS.escape ? window.CSS.escape(value) : value.replace(/["\\]/g, '\\$&');
    }

    function fieldLabel(field) {
        const id = field.getAttribute('id');
        const label = id ? field.form.querySelector('label[for="' + cssEscape(id) + '"]') : null;

        if (label) {
            return label.textContent.replace(/\s+/g, ' ').trim();
        }

        return field.name.replace(/[_-]+/g, ' ');
    }

    function fieldOptions(field) {
        if ('SELECT' === field.tagName) {
            return Array.from(field.options).map((option) => ({
                value: option.value,
                label: option.textContent.trim()
            }));
        }

        if ('radio' === field.type || 'checkbox' === field.type) {
            return Array.from(field.form.querySelectorAll('[name="' + cssEscape(field.name) + '"]')).map((option) => ({
                value: option.value,
                label: fieldLabel(option)
            }));
        }

        return [];
    }

    function shouldPreview(name, field, value) {
        const lowered = name.toLowerCase();

        if ('page_content' === lowered || 'pagebuilder' === String(field.type || '').toLowerCase()) {
            return true;
        }

        if (String(value || '').startsWith('data:image/')) {
            return true;
        }

        if (hiddenPreviewNames.includes(lowered) || lowered.includes('featured') || lowered.includes('status') || lowered.includes('headline')) {
            return false;
        }

        return !['boolean', 'checkbox', 'radio', 'files'].includes(field.type);
    }

    function collectFields(form) {
        const fields = [];
        const seen = new Set();

        form.querySelectorAll('input[name], textarea[name], select[name]').forEach((field) => {
            const isPageBuilderContent = field.name === 'page_content' && window._pageBuilder;

            if (field.name === '_token' || (! isPageBuilderContent && field.type === 'hidden') || field.type === 'password' || field.disabled || field.readOnly) {
                return;
            }

            if (field.type === 'file' && (! window.AksaraAI || ! window.AksaraAI.image || (field.dataset.role !== 'image-upload' && ! String(field.getAttribute('accept') || '').includes('image')))) {
                return;
            }

            if (seen.has(field.name)) {
                return;
            }

            seen.add(field.name);
            fields.push({
                name: field.name,
                label: fieldLabel(field),
                type: isPageBuilderContent ? 'pagebuilder' : (field.dataset.role || field.type || field.tagName.toLowerCase()),
                required: field.required || !!field.closest('.mb-3')?.querySelector('.text-danger'),
                value: isPageBuilderContent ? JSON.stringify(window._pageBuilder.layout || { components: [] }) : (Object.prototype.hasOwnProperty.call(generatedFields, field.name) ? generatedFields[field.name] : fieldValue(field)),
                options: isPageBuilderContent ? pageBuilderSchema() : fieldOptions(field)
            });
        });

        return fields;
    }

    function pageBuilderSchema() {
        if (! window._pageBuilder || ! window._pageBuilder.componentDefinitions) {
            return {};
        }

        const schema = {};
        const fullPlaceholder = baseUrl() + 'uploads/pages/placeholder.png';
        const thumbPlaceholder = baseUrl() + 'uploads/pages/thumbs/placeholder.png';

        Object.keys(window._pageBuilder.componentDefinitions).forEach((type) => {
            const definition = window._pageBuilder.componentDefinitions[type] || {};
            const defaults = JSON.parse(JSON.stringify(definition.defaults || {}));

            imagePropKeys(type).forEach((key) => {
                if (Object.prototype.hasOwnProperty.call(defaults, key) && ! defaults[key]) {
                    defaults[key] = fullPlaceholder;
                }
            });

            schema[type] = {
                label: definition.label || type,
                category: definition.category || '',
                children: !!definition.children,
                defaults: defaults,
                options: definition.options || {}
            };
        });

        return {
            contract: {
                type: 'pagebuilder',
                value: 'JSON string',
                required_shape: {
                    version: '1.0',
                    framework: 'bootstrap5',
                    components: [
                        {
                            type: 'component_type',
                            id: 'unique_ascii_id',
                            props: {},
                            children: []
                        }
                    ]
                },
                nesting_rules: [
                    'Top-level components may be section, hero, or carousel/slideshow.',
                    'hero and carousel/slideshow may be full-bleed top-level components.',
                    'CTA, pricing, features, testimonials, media, text, cards, accordions, and similar content must be wrapped inside section > container > row > column.',
                    'section may contain container only.',
                    'container may contain row only.',
                    'row may contain column only.',
                    'column may contain row or non-structural content components.',
                    'Only components with children=true may have children.',
                    'Use unique id values for every component.'
                ],
                invalid_response_rules: [
                    'Do not return HTML for page_content.',
                    'Do not use component types outside components schema.',
                    'Do not leave required text, image, src, or hero background props empty.',
                    'Leave section, container, row, and column background props empty unless an actual layout background image is explicitly requested.',
                    'Do not put structural children into components with children=false.'
                ]
            },
            asset_samples: {
                image_full: fullPlaceholder,
                image_thumbnail: thumbPlaceholder
            },
            layout_shape: {
                version: '1.0',
                framework: 'bootstrap5',
                components: []
            },
            components: schema,
            sample_layout: pageBuilderSampleLayout(fullPlaceholder)
        };
    }

    function pageBuilderSampleLayout(placeholder) {
        return {
            version: '1.0',
            framework: 'bootstrap5',
            components: [
                {
                    type: 'hero',
                    id: 'sample_hero',
                    props: {
                        title: 'Sample Hero',
                        subtitle: 'This demonstrates a complete PageBuilder JSON layout.',
                        button_text: 'Learn More',
                        button_url: '#content',
                        background: placeholder,
                        alignment: 'center',
                        overlay: true
                    }
                },
                {
                    type: 'section',
                    id: 'sample_content',
                    props: {
                        class: 'section-padding',
                        id: 'content'
                    },
                    children: [
                        {
                            type: 'container',
                            id: 'sample_container',
                            props: {
                                fluid: false
                            },
                            children: [
                                {
                                    type: 'row',
                                    id: 'sample_row',
                                    props: {
                                        class: 'g-3'
                                    },
                                    children: [
                                        {
                                            type: 'column',
                                            id: 'sample_col_1',
                                            props: {
                                                size: {
                                                    md: 6
                                                }
                                            },
                                            children: [
                                                {
                                                    type: 'heading',
                                                    id: 'sample_heading',
                                                    props: {
                                                        level: 2,
                                                        text: 'Sample Heading',
                                                        alignment: 'left',
                                                        class: ''
                                                    }
                                                },
                                                {
                                                    type: 'paragraph',
                                                    id: 'sample_paragraph',
                                                    props: {
                                                        text: 'Sample paragraph content.',
                                                        alignment: 'left',
                                                        class: ''
                                                    }
                                                }
                                            ]
                                        },
                                        {
                                            type: 'column',
                                            id: 'sample_col_2',
                                            props: {
                                                size: {
                                                    md: 6
                                                }
                                            },
                                            children: [
                                                {
                                                    type: 'image',
                                                    id: 'sample_image',
                                                    props: {
                                                        src: placeholder,
                                                        alt: 'Sample image',
                                                        class: 'img-fluid',
                                                        width: ''
                                                    }
                                                }
                                            ]
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                }
            ]
        };
    }

    function formTitle(form) {
        return document.querySelector('[data-role="title"]')?.textContent.trim()
            || form.closest('.modal')?.querySelector('.modal-title')?.textContent.trim()
            || document.title;
    }

    function ensureModal() {
        let modal = document.getElementById('aksara-ai-modal');

        if (modal) {
            return modal;
        }

        modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'aksara-ai-modal';
        modal.tabIndex = -1;
        modal.innerHTML = `
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="mdi mdi-creation"></i> ${escapeHtml(phrase('AI Assistant'))}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="${escapeHtml(phrase('Close'))}"></button>
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control mb-3" data-ai-prompt rows="1" placeholder="${escapeHtml(phrase('Describe what should be filled or improved in this form'))}"></textarea>
                        <div class="list-group list-group-flush border rounded" style="border-style:dashed !important" data-ai-result></div>
                    </div>
                    <div class="modal-footer">
                        <span class="small text-muted me-auto" data-ai-status></span>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">${escapeHtml(phrase('Close'))}</button>
                        <button type="button" class="btn btn-primary" data-ai-use><i class="mdi mdi-check"></i> ${escapeHtml(phrase('Use This'))}</button>
                    </div>
                </div>
            </div>`;
        document.body.appendChild(modal);

        return modal;
    }

    function autogrow(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = Math.min((textarea.scrollHeight ? (textarea.scrollHeight + 2) : 38), 240) + 'px';
    }

    function markdownPreview(value) {
        const source = document.createElement('div');

        source.innerHTML = value || '';

        if (! source.children.length) {
            return source.textContent || value || '';
        }

        return Array.from(source.childNodes).map((node) => nodeToMarkdown(node, 0)).join('').replace(/\n{3,}/g, '\n\n').trim();
    }

    function nodeToMarkdown(node, depth) {
        if (node.nodeType === Node.TEXT_NODE) {
            return node.textContent;
        }

        if (node.nodeType !== Node.ELEMENT_NODE) {
            return '';
        }

        const tag = node.tagName.toLowerCase();
        const content = Array.from(node.childNodes).map((child) => nodeToMarkdown(child, depth + 1)).join('').trim();

        if (! content && ! ['br', 'img'].includes(tag)) {
            return '';
        }

        switch (tag) {
            case 'h1':
                return '# ' + content + '\n\n';
            case 'h2':
                return '## ' + content + '\n\n';
            case 'h3':
                return '### ' + content + '\n\n';
            case 'h4':
                return '#### ' + content + '\n\n';
            case 'p':
                return content + '\n\n';
            case 'br':
                return '\n';
            case 'strong':
            case 'b':
                return '**' + content + '**';
            case 'em':
            case 'i':
                return '*' + content + '*';
            case 'ul':
            case 'ol':
                return Array.from(node.children).map((child, index) => listItemToMarkdown(child, tag, index, depth)).join('') + '\n';
            case 'li':
                return '- ' + content + '\n';
            case 'a':
                return content + (node.getAttribute('href') ? ' (' + node.getAttribute('href') + ')' : '');
            case 'blockquote':
                return content.split('\n').map((line) => line ? '> ' + line : line).join('\n') + '\n\n';
            case 'pre':
            case 'code':
                return content + '\n\n';
            case 'img':
                return node.getAttribute('alt') || node.getAttribute('src') || '';
            default:
                return content + (['div', 'section', 'article'].includes(tag) ? '\n\n' : '');
        }
    }

    function listItemToMarkdown(node, listTag, index, depth) {
        if (node.tagName.toLowerCase() !== 'li') {
            return '';
        }

        const marker = listTag === 'ol' ? (index + 1) + '. ' : '- ';
        const content = Array.from(node.childNodes).map((child) => nodeToMarkdown(child, depth + 1)).join('').trim();

        return '  '.repeat(Math.max(0, depth - 1)) + marker + content.replace(/\n/g, '\n  '.repeat(depth) + '  ') + '\n';
    }

    function safeMarkdownHtml(markdown) {
        if (window.marked && window.DOMPurify) {
            window.marked.setOptions({
                breaks: true,
                gfm: true,
                headerIds: false,
                mangle: false
            });

            return window.DOMPurify.sanitize(window.marked.parse(markdown || ''), {
                USE_PROFILES: {
                    html: true
                }
            }) || '<p class="mb-0 text-muted">' + escapeHtml(phrase('No preview.')) + '</p>';
        }

        const escaped = escapeHtml(markdown || '');
        const lines = escaped.split(/\n/);
        let html = '';
        let listOpen = false;

        lines.forEach((line) => {
            const trimmed = line.trim();

            if (! trimmed) {
                if (listOpen) {
                    html += '</ul>';
                    listOpen = false;
                }

                return;
            }

            if (/^###\s+/.test(trimmed)) {
                if (listOpen) {
                    html += '</ul>';
                    listOpen = false;
                }
                html += '<h6 class="fw-bold mt-2 mb-1">' + inlineMarkdown(trimmed.replace(/^###\s+/, '')) + '</h6>';
            } else if (/^##\s+/.test(trimmed)) {
                if (listOpen) {
                    html += '</ul>';
                    listOpen = false;
                }
                html += '<h6 class="fw-bold mt-2 mb-1">' + inlineMarkdown(trimmed.replace(/^##\s+/, '')) + '</h6>';
            } else if (/^#\s+/.test(trimmed)) {
                if (listOpen) {
                    html += '</ul>';
                    listOpen = false;
                }
                html += '<h6 class="fw-bold mt-2 mb-1">' + inlineMarkdown(trimmed.replace(/^#\s+/, '')) + '</h6>';
            } else if (/^[-*]\s+/.test(trimmed)) {
                if (! listOpen) {
                    html += '<ul class="mb-2 ps-3">';
                    listOpen = true;
                }
                html += '<li>' + inlineMarkdown(trimmed.replace(/^[-*]\s+/, '')) + '</li>';
            } else if (/^\d+\.\s+/.test(trimmed)) {
                if (! listOpen) {
                    html += '<ul class="mb-2 ps-3">';
                    listOpen = true;
                }
                html += '<li>' + inlineMarkdown(trimmed.replace(/^\d+\.\s+/, '')) + '</li>';
            } else {
                if (listOpen) {
                    html += '</ul>';
                    listOpen = false;
                }
                html += '<p class="mb-2">' + inlineMarkdown(trimmed) + '</p>';
            }
        });

        if (listOpen) {
            html += '</ul>';
        }

        return html || '<p class="mb-0 text-muted">' + escapeHtml(phrase('No preview.')) + '</p>';
    }

    function inlineMarkdown(value) {
        return value
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>');
    }

    function renderResult(form, fields, labels) {
        const result = document.querySelector('#aksara-ai-modal [data-ai-result]');
        const schema = collectFields(form).reduce((carry, field) => {
            carry[field.name] = field;

            return carry;
        }, {});

        result.innerHTML = '';

        Object.keys(fields).forEach((name) => {
            const field = schema[name] || { name, label: name, type: 'text' };

            const value = fields[name] == null ? '' : String(fields[name]);

            if (! shouldPreview(name, field, value)) {
                return;
            }

            const item = document.createElement('div');
            const preview = labels && labels[name] ? labels[name] : (field.type === 'wysiwyg' ? markdownPreview(value) : value);

            item.className = 'list-group-item';
            item.innerHTML = `
                <div class="mb-2">
                    <strong>${escapeHtml(field.label || name)}</strong>
                </div>
                <div class="small" data-ai-field-preview></div>
            `;

            if (field.type === 'pagebuilder') {
                item.querySelector('[data-ai-field-preview]').innerHTML = pageBuilderPreviewHtml(value);
            } else if (value.startsWith('data:image/')) {
                item.querySelector('[data-ai-field-preview]').innerHTML = '<img src="' + escapeHtml(value) + '" class="img-fluid rounded border" alt="' + escapeHtml(preview || field.label || name) + '" />';
            } else if (field.type === 'wysiwyg') {
                item.querySelector('[data-ai-field-preview]').innerHTML = safeMarkdownHtml(preview);
            } else {
                item.querySelector('[data-ai-field-preview]').textContent = preview;
            }

            result.appendChild(item);
        });
    }

    function normalizeAiResponse(response) {
        const normalized = {
            fields: response && response.fields && typeof response.fields === 'object' ? response.fields : {},
            labels: response && response.labels && typeof response.labels === 'object' ? response.labels : {}
        };

        if (normalized.fields && normalized.fields.fields && typeof normalized.fields.fields === 'object') {
            normalized.fields = normalized.fields.fields;
        }

        if (! normalized.fields.page_content && looksLikePageBuilderLayout(normalized.fields)) {
            normalized.fields = {
                page_content: JSON.stringify(normalized.fields)
            };
        }

        if (normalized.fields.layout && looksLikePageBuilderLayout(normalized.fields.layout)) {
            normalized.fields.page_content = JSON.stringify(normalized.fields.layout);
            delete normalized.fields.layout;
        }

        if (normalized.fields.page_content && typeof normalized.fields.page_content === 'object' && looksLikePageBuilderLayout(normalized.fields.page_content)) {
            normalized.fields.page_content = JSON.stringify(normalized.fields.page_content);
        }

        if (Object.keys(normalized.fields).length || ! response || ! response.content) {
            return normalized;
        }

        try {
            const content = JSON.parse(response.content);

            if (content && typeof content === 'object' && ! Array.isArray(content)) {
                normalized.fields = looksLikePageBuilderLayout(content)
                    ? { page_content: JSON.stringify(content) }
                    : content;
            }
        } catch (error) {
            normalized.fields = {};
        }

        return normalized;
    }

    function looksLikePageBuilderLayout(value) {
        return value && typeof value === 'object' && ! Array.isArray(value) && Array.isArray(value.components);
    }

    function pageBuilderPreviewHtml(value) {
        try {
            const layout = JSON.parse(value);
            const components = Array.isArray(layout.components) ? layout.components : [];
            const counts = {};

            walkComponents(components, (component) => {
                counts[component.type] = (counts[component.type] || 0) + 1;
            });

            const summary = Object.keys(counts).map((type) => type + ': ' + counts[type]).join(', ');
            let preview = '';

            if (window._pageBuilder && typeof window._pageBuilder.renderBlocks === 'function' && components.length) {
                preview = '<div class="border rounded bg-body-tertiary p-2 mt-2" style="max-height:420px;overflow:auto" data-ai-pagebuilder-preview>'
                    + window._pageBuilder.renderBlocks(components)
                    + '</div>';
            }

            return '<div class="text-muted mb-2">'
                + escapeHtml(phrase('{{count}} top-level component(s)').replace('{{count}}', components.length || 0) + (summary ? ' | ' + summary : ''))
                + '</div>'
                + (window.DOMPurify ? window.DOMPurify.sanitize(preview) : preview);
        } catch (error) {
            return '<span class="text-muted">' + escapeHtml(phrase('Page builder layout is ready.')) + '</span>';
        }
    }

    function walkComponents(components, callback) {
        components.forEach((component) => {
            if (! component || ! component.type) {
                return;
            }

            callback(component);

            if (Array.isArray(component.children)) {
                walkComponents(component.children, callback);
            }
        });
    }

    function startLoading(result, status) {
        let index = 0;
        let activeDot = 0;

        stopLoading();
        result.innerHTML = `
            <div class="list-group-item text-center py-5">
                <div class="mb-3" data-ai-dots>
                    <span class="d-inline-block rounded-circle bg-primary mx-1" style="width:8px;height:8px;opacity:.35;transition:all .25s ease"></span>
                    <span class="d-inline-block rounded-circle bg-primary mx-1" style="width:8px;height:8px;opacity:.35;transition:all .25s ease"></span>
                    <span class="d-inline-block rounded-circle bg-primary mx-1" style="width:8px;height:8px;opacity:.35;transition:all .25s ease"></span>
                </div>
                <div data-ai-loading-text>${escapeHtml(phrase(loadingTexts[index]))}</div>
            </div>
        `;
        status.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> ' + escapeHtml(phrase('Generating')) + '...';
        loadingTimer = setInterval(() => {
            index = (index + 1) % loadingTexts.length;
            const target = result.querySelector('[data-ai-loading-text]');

            if (target) {
                target.textContent = phrase(loadingTexts[index]);
            }
        }, 5000);
        dotTimer = setInterval(() => {
            const dots = result.querySelectorAll('[data-ai-dots] span');

            activeDot = (activeDot + 1) % dots.length;
            dots.forEach((dot, dotIndex) => {
                dot.style.opacity = dotIndex === activeDot ? '1' : '.35';
                dot.style.transform = dotIndex === activeDot ? 'translateY(-5px)' : 'translateY(0)';
            });
        }, 360);
    }

    function stopLoading() {
        if (loadingTimer) {
            clearInterval(loadingTimer);
            loadingTimer = null;
        }

        if (dotTimer) {
            clearInterval(dotTimer);
            dotTimer = null;
        }
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, (character) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[character]);
    }

    function openAssistant(form) {
        const modal = ensureModal();
        const modalApi = bootstrap.Modal.getOrCreateInstance(modal);
        const prompt = modal.querySelector('[data-ai-prompt]');
        const result = modal.querySelector('[data-ai-result]');
        const status = modal.querySelector('[data-ai-status]');
        const use = modal.querySelector('[data-ai-use]');

        activeForm = form;
        generatedFields = {};
        generatedLabels = {};
        prompt.value = '';
        result.innerHTML = '<p class="text-center text-secondary py-4 mb-0">' + escapeHtml(phrase('Type your instruction in the field above, then press Enter to process.')) + '</p>';
        status.textContent = '';
        use.disabled = true;
        prompt.oninput = () => autogrow(prompt);
        prompt.onkeydown = function(event) {
            if (event.key === 'Enter' && ! event.shiftKey) {
                event.preventDefault();
                runRequest();
            }
        };
        modalApi.show();
        setTimeout(() => {
            prompt.focus();
            autogrow(prompt);
        }, 150);

        function runRequest() {
            startLoading(result, status);
            use.disabled = true;

            Promise.resolve()
            .then(() => {
                const fields = collectFields(form);
                const formData = new FormData();

                formData.append('_token', token());
                formData.append('action', 'form_fill');
                formData.append('instruction', prompt.value || '');
                formData.append('title', formTitle(form));
                formData.append('content_type', form.getAttribute('action') || window.location.pathname);
                formData.append('context_id', aiContextId(form));
                formData.append('fields', JSON.stringify(fields));

                return fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
            })
            .then((response) => response.json())
            .then((response) => {
                if (! response || 200 !== response.status) {
                    throw new Error(response && response.message ? response.message : phrase('AI request failed.'));
                }

                const normalized = normalizeAiResponse(response);

                generatedFields = normalized.fields;
                generatedLabels = normalized.labels;
                renderResult(form, generatedFields, generatedLabels);
                if (response.image_errors && Object.keys(response.image_errors).length) {
                    status.textContent = phrase('Image generation failed.') + ' ' + Object.values(response.image_errors).join(' ');
                } else {
                    status.textContent = Object.keys(generatedFields).length ? phrase('Ready') : phrase('AI returned no field values.');
                }
                use.disabled = ! Object.keys(generatedFields).length;
            })
            .catch((error) => {
                status.textContent = error.message;
            })
            .finally(() => {
                stopLoading();
            });
        }

        use.onclick = function() {
            if (! activeForm || ! generatedFields) {
                return;
            }

            Object.keys(generatedFields).forEach((name) => {
                const field = activeForm.querySelector('[name="' + cssEscape(name) + '"]');

                if (field) {
                    setFieldValue(field, generatedFields[name], generatedLabels[name]);
                }
            });

            modalApi.hide();
        };
    }

    function formFromButton(button) {
        return button.closest('form')
            || button.closest('.modal, .container-fluid, article')?.querySelector('form.--validate-form')
            || document.querySelector('form.--validate-form');
    }

    function aiContextId(form) {
        if (! form.dataset.aiContextId) {
            form.dataset.aiContextId = Date.now().toString(36) + '-' + Math.random().toString(36).slice(2);
        }

        return form.dataset.aiContextId;
    }

    function attach(scope) {
        scope.querySelectorAll('.--ai-assistant').forEach((button) => {
            if (button.dataset.aiAttached) {
                return;
            }

            const form = formFromButton(button);

            if (! form || ! form.querySelector('input[name], textarea[name], select[name]')) {
                return;
            }

            button.addEventListener('click', () => openAssistant(form));
            button.dataset.aiAttached = '1';
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        attach(document);
        setTimeout(() => attach(document), 500);
        setTimeout(() => attach(document), 1500);
    });

    if (window.jQuery) {
        window.jQuery(document).ajaxComplete(() => attach(document));
    }

    if (window.MutationObserver) {
        new MutationObserver((mutations) => {
            if (mutations.some((mutation) => mutation.addedNodes.length)) {
                attach(document);
            }
        }).observe(document.body, {
            childList: true,
            subtree: true
        });
    }
})();
