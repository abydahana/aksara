<?php

/**
 * This file is part of Aksara CMS, both framework and publishing
 * platform.
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.txt file.
 *
 * When the signs come, those who don't believe at "that time"
 * will have only two choices, commit suicide or become brutal.
 */

namespace Aksara\Libraries\PageBuilder;

use Config\PageBuilder as PageBuilderConfig;

/**
 * Page Builder — JSON to HTML Renderer.
 *
 * Converts a JSON layout tree into HTML output using the configured
 * CSS framework class mappings. Each component type has a dedicated
 * render method that produces framework-specific markup.
 */
class Renderer
{
    private PageBuilderConfig $config;

    /** @var array<string, mixed> Active framework class mappings. */
    private array $classes;

    /** @var int Counter for generating unique IDs. */
    private int $idCounter = 0;

    public function __construct(PageBuilderConfig $config)
    {
        $this->config = $config;
        $fw = $config->framework;
        $this->classes = $config->frameworks[$fw] ?? $config->frameworks['bootstrap5'];
    }

    /**
     * Render a full layout.
     *
     * @param array $layout Layout array with 'version', 'framework', 'components'.
     *
     * @return string Rendered HTML.
     */
    public function render(array $layout): string
    {
        if (empty($layout['components'])) {
            return '';
        }

        $html = '';

        foreach ($layout['components'] as $component) {
            if (! is_array($component)) {
                continue;
            }

            $html .= $this->renderComponent($component);
        }

        return $html;
    }

    /**
     * Render a single component and its children recursively.
     *
     * @param array $component Component definition.
     *
     * @return string Rendered HTML.
     */
    public function renderComponent(?array $component): string
    {
        if (! is_array($component)) {
            return '';
        }
        $type = $component['type'] ?? '';
        $props = $component['props'] ?? [];
        $children = $component['children'] ?? [];
        $id = $component['id'] ?? '';

        return match ($type) {
            'section' => $this->renderSection($props, $children, $id),
            'container' => $this->renderContainer($props, $children, $id),
            'row' => $this->renderRow($props, $children, $id),
            'column' => $this->renderColumn($props, $children, $id),
            'heading' => $this->renderHeading($props, $id),
            'paragraph' => $this->renderParagraph($props, $id),
            'divider' => $this->renderDivider($props, $id),
            'image' => $this->renderImage($props, $id),
            'video' => $this->renderVideo($props, $id),
            'button' => $this->renderButton($props, $id),
            'accordion' => $this->renderAccordion($props, $id),
            'alert' => $this->renderAlert($props, $id),
            'carousel' => $this->renderCarousel($props, $id),
            'tabs' => $this->renderTabs($props, $id),
            'card' => $this->renderCard($props, $children, $id),
            'hero' => $this->renderHero($props, $id),
            'feature_box' => $this->renderFeatureBox($props, $id),
            'pricing' => $this->renderPricing($props, $id),
            'testimonial' => $this->renderTestimonial($props, $id),
            'team_member' => $this->renderTeamMember($props, $id),
            'cta' => $this->renderCta($props, $id),
            'spacer' => $this->renderSpacer($props, $id),
            default => "<!-- Unknown component: {$type} -->",
        };
    }

    /**
     * Render children array.
     */
    private function renderChildren(array $children): string
    {
        $html = '';

        foreach ($children as $child) {
            $html .= $this->renderComponent($child);
        }

        return $html;
    }

    /**
     * Build HTML attribute string from key-value pairs.
     */
    private function attrs(array $attributes): string
    {
        $parts = [];

        foreach ($attributes as $key => $value) {
            if ('' === $value || null === $value) {
                continue;
            }

            $parts[] = $key . '="' . htmlspecialchars((string) $value, ENT_QUOTES) . '"';
        }

        return $parts ? ' ' . implode(' ', $parts) : '';
    }

    // =========================================================================
    // Component Renderers
    // =========================================================================

    private function renderSection(array $props, array $children, string $id): string
    {
        $class = trim(($this->classes['section'] ?? '') . ' ' . ($props['class'] ?? ''));
        $style = '';

        if (! empty($props['background'])) {
            $style = "background-image:url('" . htmlspecialchars($props['background']) . "');background-size:cover;background-position:center;";
        }

        $attrStr = $this->attrs(array_filter([
            'class' => $class,
            'id' => $props['id'] ?? $id,
            'style' => $style,
        ]));

        return "<section{$attrStr}>\n" . $this->renderChildren($children) . "</section>\n";
    }

    private function renderContainer(array $props, array $children, string $id): string
    {
        $fluid = $props['fluid'] ?? false;
        $class = $fluid ? $this->classes['container_fluid'] : $this->classes['container'];

        return "<div class=\"{$class}\">\n" . $this->renderChildren($children) . "</div>\n";
    }

    private function renderRow(array $props, array $children, string $id): string
    {
        $classes = [$this->classes['row']];

        if (! empty($props['align_items'])) {
            $classes[] = $props['align_items'];
        }
        if (! empty($props['justify_content'])) {
            $classes[] = $props['justify_content'];
        }

        if (! empty($props['class'])) {
            $classes[] = $props['class'];
        }

        $class = implode(' ', array_filter($classes));

        return "<div class=\"{$class}\">\n" . $this->renderChildren($children) . "</div>\n";
    }

    private function renderColumn(array $props, array $children, string $id): string
    {
        $prefix = $this->classes['col_prefix'];
        $classes = [];

        // Build responsive column classes
        $size = $props['size'] ?? [];

        if (empty($size)) {
            $classes[] = $prefix;
        } else {
            foreach ($size as $bp => $cols) {
                if ('' === $bp || 'xs' === $bp) {
                    $classes[] = "{$prefix}-{$cols}";
                } else {
                    $classes[] = "{$prefix}-{$bp}-{$cols}";
                }
            }
        }

        // Build responsive offset classes
        $offset = $props['offset'] ?? [];

        foreach ($offset as $bp => $cols) {
            if ('' === $bp || 'xs' === $bp) {
                $classes[] = "offset-{$cols}";
            } else {
                $classes[] = "offset-{$bp}-{$cols}";
            }
        }
        if (! empty($props['align_self'])) {
            $classes[] = $props['align_self'];
        }

        if (! empty($props['class'])) {
            $classes[] = $props['class'];
        }

        $class = implode(' ', array_filter($classes));

        return "<div class=\"{$class}\">\n" . $this->renderChildren($children) . "</div>\n";
    }

    private function renderHeading(array $props, string $id): string
    {
        $level = max(1, min(6, (int) ($props['level'] ?? 2)));
        $text = $this->sanitizeHtml($props['text'] ?? 'Heading');
        $classes = [$props['class'] ?? ''];
        if (! empty($props['alignment'])) {
            $classes[] = 'text-' . $props['alignment'];
        }
        $class = trim(implode(' ', array_filter($classes)));
        $attrStr = $class ? " class=\"{$class}\"" : '';

        return "<h{$level}{$attrStr}>{$text}</h{$level}>\n";
    }

    private function renderParagraph(array $props, string $id): string
    {
        $text = $this->sanitizeHtml($props['text'] ?? '');
        $classes = [$props['class'] ?? ''];

        if (! empty($props['alignment'])) {
            $classes[] = 'text-' . $props['alignment'];
        }

        $class = trim(implode(' ', array_filter($classes)));
        $attrStr = $class ? " class=\"{$class}\"" : '';

        return "<p{$attrStr}>{$text}</p>\n";
    }

    private function renderDivider(array $props, string $id): string
    {
        $class = $props['class'] ?? ($this->classes['mb_3'] ?? 'mb-3');

        return "<hr class=\"{$class}\" />\n";
    }

    private function renderImage(array $props, string $id): string
    {
        $src = htmlspecialchars($props['src'] ?? '', ENT_QUOTES);
        $alt = htmlspecialchars($props['alt'] ?? '', ENT_QUOTES);
        $classes = [$this->classes['img_fluid'], $this->classes['rounded']];

        if (! empty($props['class'])) {
            $classes[] = $props['class'];
        }

        $class = implode(' ', array_filter($classes));
        $style = ! empty($props['width']) ? " style=\"max-width:{$props['width']}px\"" : '';

        return "<img src=\"{$src}\" alt=\"{$alt}\" class=\"{$class}\"{$style} />\n";
    }

    private function renderVideo(array $props, string $id): string
    {
        $url = $props['url'] ?? '';
        $ratio = $props['ratio'] ?? '16x9';

        // Convert YouTube/Vimeo URLs to embed URLs
        $embedUrl = $this->convertToEmbedUrl($url);
        $ratioClass = "ratio ratio-{$ratio}";
        $class = trim("{$ratioClass} " . ($this->classes['mb_3'] ?? 'mb-3') . ' ' . ($props['class'] ?? ''));
        $style = (strpos($class, 'rounded') !== false) ? ' style="overflow:hidden"' : '';

        return "<div class=\"{$class}\"{$style}>\n"
             . "  <iframe src=\"{$embedUrl}\" allowfullscreen></iframe>\n"
             . "</div>\n";
    }

    private function renderButton(array $props, string $id): string
    {
        $text = htmlspecialchars($props['text'] ?? 'Button', ENT_QUOTES);
        $url = htmlspecialchars($props['url'] ?? '#', ENT_QUOTES);
        $style = $props['style'] ?? 'primary';
        $size = $props['size'] ?? '';
        $target = $props['target'] ?? '_self';
        $rounded = $props['rounded'] ?? true;
        $icon = $props['icon'] ?? '';
        $icon_placement = $props['icon_placement'] ?? 'prefix';

        $classes = [$this->classes['btn']];

        // Map style to framework class
        $styleKey = "btn_{$style}";

        if (isset($this->classes[$styleKey])) {
            $classes = [$this->classes[$styleKey]];
        } else {
            $classes[] = "btn-{$style}";
        }

        if ($size && isset($this->classes["btn_{$size}"])) {
            $classes[] = $this->classes["btn_{$size}"];
        }

        if ($rounded) {
            $classes[] = 'rounded-pill';
        }

        if (! empty($props['class'])) {
            $classes[] = $props['class'];
        }

        $class = implode(' ', array_filter($classes));
        $targetAttr = '_self' !== $target ? " target=\"{$target}\"" : '';

        $iconHtml = '';

        if ($icon) {
            $marginClass = ($text ? ('suffix' === $icon_placement ? ' ms-2' : ' me-2') : '');
            $iconHtml = "<i class=\"{$icon}{$marginClass}\"></i>";
        }

        $content = ('suffix' === $icon_placement ? $text . $iconHtml : $iconHtml . $text);

        return "<a href=\"{$url}\" class=\"{$class}\"{$targetAttr}>{$content}</a>\n";
    }

    private function renderAccordion(array $props, string $id): string
    {
        $items = $props['items'] ?? [];
        $accordionId = $id ?: ('accordion_' . ++$this->idCounter);
        $html = "<div class=\"{$this->classes['accordion']}\" id=\"{$accordionId}\">\n";

        foreach ($items as $index => $item) {
            $itemId = "{$accordionId}_item_{$index}";
            $expanded = 0 === $index ? 'true' : 'false';
            $collapsed = 0 === $index ? '' : ' collapsed';
            $show = 0 === $index ? ' show' : '';

            $html .= "  <div class=\"{$this->classes['accordion_item']}\">\n"
                   . "    <div class=\"{$this->classes['accordion_header']}\" id=\"heading_{$itemId}\">\n"
                   . "      <button type=\"button\" class=\"{$this->classes['accordion_button']}{$collapsed}\" data-bs-toggle=\"collapse\" data-bs-target=\"#collapse_{$itemId}\" aria-expanded=\"{$expanded}\">\n"
                   . '        ' . htmlspecialchars($item['title'] ?? '', ENT_QUOTES) . "\n"
                   . "      </button>\n"
                   . "    </div>\n"
                   . "    <div id=\"collapse_{$itemId}\" class=\"{$this->classes['accordion_collapse']}{$show}\" data-bs-parent=\"#{$accordionId}\">\n"
                   . "      <div class=\"{$this->classes['accordion_body']}\">\n"
                   . '        ' . $this->sanitizeHtml($item['body'] ?? '') . "\n"
                   . "      </div>\n"
                   . "    </div>\n"
                   . "  </div>\n";
        }

        $html .= "</div>\n";

        return $html;
    }

    private function renderAlert(array $props, string $id): string
    {
        $text = $this->sanitizeHtml($props['text'] ?? '');
        $style = $props['style'] ?? 'info';
        $class = trim("{$this->classes['alert']} alert-{$style} " . ($props['class'] ?? ''));

        return "<div class=\"{$class}\" role=\"alert\">" . $this->removeLastMargin($text) . "</div>\n";
    }

    private function renderCard(array $props, array $children, string $id): string
    {
        $classes = [$this->classes['card'], $this->classes['rounded'], $this->classes['mb_3']];

        if (! empty($props['class'])) {
            $classes[] = $props['class'];
        }

        $class = implode(' ', array_filter($classes));
        $html = "<div class=\"{$class}\" style=\"overflow:hidden;position:relative\">\n";

        if (! empty($props['image'])) {
            $html .= "  <img src=\"" . htmlspecialchars($props['image'], ENT_QUOTES) . "\" class=\"card-img-top\" alt=\"\" />\n";
        }

        $html .= "  <div class=\"{$this->classes['card_body']}\">\n";

        if (! empty($props['title'])) {
            $html .= "    <h5 class=\"{$this->classes['card_title']}\">" . htmlspecialchars($props['title'], ENT_QUOTES) . "</h5>\n";
        }

        if (! empty($props['text'])) {
            $marginClass = empty($children) ? ' mb-0' : ' mb-3';
            $html .= "    <div class=\"{$this->classes['card_text']}{$marginClass}\">" . $this->removeLastMargin($this->sanitizeHtml($props['text'])) . "</div>\n";
        }

        if ($children) {
            $html .= $this->renderChildren($children);
        }

        $html .= "  </div>\n</div>\n";

        return $html;
    }

    private function renderHero(array $props, string $id): string
    {
        $title = $this->sanitizeHtml($props['title'] ?? 'Hero Title');
        $subtitle = $this->sanitizeHtml($props['subtitle'] ?? '');
        $btnText = htmlspecialchars($props['button_text'] ?? '', ENT_QUOTES);
        $btnUrl = htmlspecialchars($props['button_url'] ?? '#', ENT_QUOTES);
        $alignment = $props['alignment'] ?? 'center';
        $background = $props['background'] ?? '';
        $overlay = $props['overlay'] ?? true;

        $sectionStyle = '';

        if ($background) {
            $sectionStyle = " style=\"background-image:url('" . htmlspecialchars($background, ENT_QUOTES) . "');background-size:cover;background-position:center;\"";
        }

        $alignClass = "text-{$alignment}";
        $textColorClass = $background ? 'text-white' : '';

        $html = "<section class=\"section-padding position-relative {$alignClass}\"{$sectionStyle}>\n";

        if ($background && $overlay) {
            $html .= "  <div style=\"position:absolute;inset:0;background:rgba(0,0,0,0.5);\"></div>\n";
        }

        $html .= "  <div class=\"{$this->classes['container']} position-relative\">\n"
               . "    <h1 class=\"{$this->classes['display_4']} {$this->classes['fw_bold']} {$this->classes['mb_3']} {$textColorClass}\">{$title}</h1>\n";

        if ($subtitle) {
            $subtitleClass = "{$this->classes['lead']} {$this->classes['mb_4']} " . ($background ? $textColorClass : ($this->classes['text_muted'] ?? 'text-muted'));
            $html .= "    <div class=\"{$subtitleClass}\">{$subtitle}</div>\n";
        }

        if ($btnText) {
            $html .= "    <a href=\"{$btnUrl}\" class=\"{$this->classes['btn_primary']} {$this->classes['btn_lg']} rounded-pill px-4\">{$btnText}</a>\n";
        }

        $html .= "  </div>\n</section>\n";

        return $html;
    }

    private function renderFeatureBox(array $props, string $id): string
    {
        $icon = htmlspecialchars($props['icon'] ?? 'mdi mdi-star', ENT_QUOTES);
        $title = $this->sanitizeHtml($props['title'] ?? '');
        $text = $this->sanitizeHtml($props['text'] ?? '');
        $class = $props['class'] ?? '';

        return "<div class=\"text-center {$this->classes['mb_4']} {$class}\">\n"
             . "  <div class=\"d-inline-flex align-items-center justify-content-center rounded-circle {$this->classes['mb_3']}\" style=\"width:80px;height:80px;background-color:rgba(43,102,255,0.05)\">\n"
             . "    <i class=\"{$icon} mdi-3x\"></i>\n"
             . "  </div>\n"
             . "  <h5 class=\"{$this->classes['fw_bold']}\">{$title}</h5>\n"
             . "  <p class=\"{$this->classes['text_muted']}\">{$text}</p>\n"
             . "</div>\n";
    }

    private function renderSpacer(array $props, string $id): string
    {
        $height = (int) ($props['height'] ?? 40);

        return "<div style=\"height:{$height}px\"></div>\n";
    }

    private function renderCarousel(array $props, string $id): string
    {
        $items = $props['items'] ?? [];
        $carouselId = $id ?: ('carousel_' . ++$this->idCounter);
        $interval = $props['interval'] ?? 5000;
        $indicators = $props['indicators'] ?? true;
        $controls = $props['controls'] ?? true;

        $html = "<div id=\"{$carouselId}\" class=\"carousel slide " . ($props['class'] ?? '') . "\" data-bs-ride=\"carousel\" data-bs-interval=\"{$interval}\">\n";

        if ($indicators) {
            $html .= "  <div class=\"carousel-indicators\">\n";
            foreach ($items as $index => $item) {
                $active = 0 === $index ? ' class="active" aria-current="true"' : '';
                $html .= "    <button type=\"button\" data-bs-target=\"#{$carouselId}\" data-bs-slide-to=\"{$index}\"{$active} aria-label=\"Slide " . ($index + 1) . "\"></button>\n";
            }
            $html .= "  </div>\n";
        }

        $html .= "  <div class=\"carousel-inner rounded-4 shadow-sm\">\n";
        foreach ($items as $index => $item) {
            $active = 0 === $index ? ' active' : '';
            $src = htmlspecialchars($item['src'] ?? '', ENT_QUOTES);
            $title = $this->sanitizeHtml($item['title'] ?? '');
            $subtitle = $this->sanitizeHtml($item['subtitle'] ?? '');

            $html .= "    <div class=\"carousel-item{$active}\" style=\"height:450px; background:#f8f9fa\">\n";
            if ($src) {
                $html .= "      <img src=\"{$src}\" class=\"d-block w-100 h-100\" style=\"object-fit:cover\" alt=\"\">\n";
            }
            if ($title || $subtitle) {
                $html .= "      <div class=\"carousel-caption d-none d-md-block\" style=\"background:rgba(0,0,0,0.5); border-radius:1rem; padding:1.5rem\">\n";
                if ($title) {
                    $html .= "        <h3 class=\"fw-bold text-white\">{$title}</h3>\n";
                }
                if ($subtitle) {
                    $html .= "        <p class=\"mb-0 text-white-50\">{$subtitle}</p>\n";
                }
                $html .= "      </div>\n";
            }
            $html .= "    </div>\n";
        }
        $html .= "  </div>\n";

        if ($controls) {
            $html .= "  <button class=\"carousel-control-prev\" type=\"button\" data-bs-target=\"#{$carouselId}\" data-bs-slide=\"prev\">\n"
                   . "    <span class=\"carousel-control-prev-icon\" aria-hidden=\"true\"></span>\n"
                   . "    <span class=\"visually-hidden\">Previous</span>\n"
                   . "  </button>\n"
                   . "  <button class=\"carousel-control-next\" type=\"button\" data-bs-target=\"#{$carouselId}\" data-bs-slide=\"next\">\n"
                   . "    <span class=\"carousel-control-next-icon\" aria-hidden=\"true\"></span>\n"
                   . "    <span class=\"visually-hidden\">Next</span>\n"
                   . "  </button>\n";
        }

        $html .= "</div>\n";

        return $html;
    }

    private function renderTabs(array $props, string $id): string
    {
        $items = $props['items'] ?? [];
        $tabsId = $id ?: ('tabs_' . ++$this->idCounter);
        $style = $props['style'] ?? 'tabs';
        $alignment = $props['alignment'] ?? 'horizontal';
        $class = $props['class'] ?? '';

        $navClass = "nav nav-{$style}";
        $wrapperClass = "";
        $contentClass = "tab-content mt-3";

        if ('vertical' === $alignment) {
            $wrapperClass = "d-flex align-items-start";
            $navClass .= " flex-column nav-pills me-3";
            $contentClass = "tab-content flex-grow-1";
        }

        $html = "<div class=\"{$wrapperClass} {$class}\">\n";
        $html .= "  <ul class=\"{$navClass}\" id=\"{$tabsId}\" role=\"tablist\">\n";

        foreach ($items as $index => $item) {
            $active = 0 === $index ? ' active' : '';
            $itemId = "{$tabsId}_item_{$index}";
            $title = htmlspecialchars($item['title'] ?? "Tab " . ($index + 1), ENT_QUOTES);
            $alignmentClass = ('vertical' === $alignment ? ' w-100 text-start' : '');

            $html .= "    <li class=\"nav-item\" role=\"presentation\">\n"
                   . "      <button class=\"nav-link{$active}{$alignmentClass}\" id=\"tab-{$itemId}\" data-bs-toggle=\"pill\" data-bs-target=\"#content-{$itemId}\" type=\"button\" role=\"tab\">{$title}</button>\n"
                   . "    </li>\n";
        }

        $html .= "  </ul>\n";
        $html .= "  <div class=\"{$contentClass}\" id=\"{$tabsId}Content\">\n";

        foreach ($items as $index => $item) {
            $active = 0 === $index ? ' show active' : '';
            $itemId = "{$tabsId}_item_{$index}";
            $content = $this->sanitizeHtml($item['content'] ?? '');

            $html .= "    <div class=\"tab-pane fade{$active}\" id=\"content-{$itemId}\" role=\"tabpanel\" aria-labelledby=\"tab-{$itemId}\">\n"
                   . "      {$content}\n"
                   . "    </div>\n";
        }

        $html .= "  </div>\n</div>\n";

        return $html;
    }

    private function renderPricing(array $props, string $id): string
    {
        $title = $this->sanitizeHtml($props['title'] ?? 'Plan');
        $price = htmlspecialchars($props['price'] ?? '$0', ENT_QUOTES);
        $period = htmlspecialchars($props['period'] ?? '', ENT_QUOTES);
        $features = explode("\n", $props['features'] ?? '');
        $btnText = htmlspecialchars($props['btn_text'] ?? 'Get Started', ENT_QUOTES);
        $btnUrl = htmlspecialchars($props['btn_url'] ?? '#', ENT_QUOTES);
        $featured = $props['featured'] ?? false;

        $cardClass = $featured ? 'border-primary border-2 shadow' : 'shadow-sm';
        $btnClass = $featured ? 'btn-primary' : 'btn-outline-primary';

        $html = "<div class=\"card h-100 rounded-4 {$cardClass} " . ($props['class'] ?? '') . "\">\n";
        if ($featured) {
            $html .= "  <div class=\"badge bg-primary position-absolute top-0 start-50 translate-middle\">" . phrase('Most Popular') . "</div>\n";
        }
        $html .= "  <div class=\"card-body p-4 text-center\">\n"
               . "    <h4 class=\"fw-bold mb-3\">{$title}</h4>\n"
               . "    <h2 class=\"display-5 fw-bold mb-0\">{$price}</h2>\n"
               . "    <p class=\"text-muted mb-4\">{$period}</p>\n"
               . "    <ul class=\"list-unstyled mb-4 text-start\">\n";

        foreach ($features as $feature) {
            if (! trim($feature)) {
                continue;
            }
            $html .= "      <li class=\"mb-2\"><i class=\"mdi mdi-check text-primary me-2\"></i>" . htmlspecialchars(trim($feature), ENT_QUOTES) . "</li>\n";
        }

        $html .= "    </ul>\n"
               . "    <a href=\"{$btnUrl}\" class=\"btn {$btnClass} w-100 rounded-pill\">{$btnText}</a>\n"
               . "  </div>\n</div>\n";

        return $html;
    }

    private function renderTestimonial(array $props, string $id): string
    {
        $quote = $this->sanitizeHtml($props['quote'] ?? '');
        $author = htmlspecialchars($props['author'] ?? 'Anonymous', ENT_QUOTES);
        $role = htmlspecialchars($props['role'] ?? '', ENT_QUOTES);
        $image = $props['image'] ?? '';

        $html = "<div class=\"card border-0 bg-light rounded-4 " . ($props['class'] ?? '') . "\">\n"
               . "  <div class=\"card-body p-4\">\n"
               . "    <div class=\"mb-3 text-primary\"><i class=\"mdi mdi-format-quote-open mdi-3x opacity-25\"></i></div>\n"
               . "    <div class=\"fs-5 mb-4\">{$quote}</div>\n"
               . "    <div class=\"d-flex align-items-center\">\n";

        if ($image) {
            $html .= "      <img src=\"" . htmlspecialchars($image, ENT_QUOTES) . "\" class=\"rounded-circle me-3\" style=\"width:50px;height:50px;object-fit:cover\" alt=\"\">\n";
        } else {
            $html .= "      <div class=\"rounded-circle bg-secondary me-3\" style=\"width:50px;height:50px\"></div>\n";
        }

        $html .= "      <div>\n"
               . "        <h6 class=\"fw-bold mb-0\">{$author}</h6>\n"
               . "        <small class=\"text-muted\">{$role}</small>\n"
               . "      </div>\n"
               . "    </div>\n"
               . "  </div>\n</div>\n";

        return $html;
    }

    private function renderTeamMember(array $props, string $id): string
    {
        $name = htmlspecialchars($props['name'] ?? 'Team Member', ENT_QUOTES);
        $role = htmlspecialchars($props['role'] ?? '', ENT_QUOTES);
        $image = $props['image'] ?? '';
        $bio = $this->sanitizeHtml($props['bio'] ?? '');

        $html = "<div class=\"text-center " . ($props['class'] ?? '') . "\">\n";
        if ($image) {
            $html .= "  <img src=\"" . htmlspecialchars($image, ENT_QUOTES) . "\" class=\"rounded-circle mb-3 shadow-sm\" style=\"width:150px;height:150px;object-fit:cover\" alt=\"\">\n";
        }
        $html .= "  <h5 class=\"fw-bold mb-1\">{$name}</h5>\n"
               . "  <p class=\"text-primary mb-2\">{$role}</p>\n"
               . "  <p class=\"small text-muted\">{$bio}</p>\n"
               . "</div>\n";

        return $html;
    }

    private function renderCta(array $props, string $id): string
    {
        $bg = $props['background'] ?? 'primary';
        $bgClass = 'primary' === $bg ? 'bg-primary text-white' : ('dark' === $bg ? 'bg-dark text-white' : 'bg-light');
        $btnClass = 'primary' === $bg ? 'btn-light' : 'btn-primary';
        $title = $this->sanitizeHtml($props['title'] ?? '');
        $text = $this->sanitizeHtml($props['text'] ?? '');
        $btnText = htmlspecialchars($props['button_text'] ?? 'Get Started', ENT_QUOTES);
        $btnUrl = htmlspecialchars($props['button_url'] ?? '#', ENT_QUOTES);

        return "<div class=\"card border-0 {$bgClass} rounded-4 p-5 " . ($props['class'] ?? '') . "\">\n"
             . "  <div class=\"row align-items-center\">\n"
             . "    <div class=\"col-lg-8\">\n"
             . "      <h2 class=\"fw-bold mb-3\">{$title}</h2>\n"
             . "      <p class=\"lead mb-0 opacity-75\">{$text}</p>\n"
             . "    </div>\n"
             . "    <div class=\"col-lg-4 text-lg-end mt-4 mt-lg-0\">\n"
             . "      <a href=\"{$btnUrl}\" class=\"btn btn-lg {$btnClass} px-5 rounded-pill\">{$btnText}</a>\n"
             . "    </div>\n"
             . "  </div>\n"
             . "</div>\n";
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Convert a YouTube or Vimeo URL into an embed URL.
     */
    private function convertToEmbedUrl(string $url): string
    {
        // YouTube
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }

        return htmlspecialchars($url, ENT_QUOTES);
    }

    /**
     * Convert Markdown content to HTML.
     * Mirrors the JavaScript implementation in pagebuilder.min.js.
     */
    private function markdownToHtml(?string $md): string
    {
        if (! $md) {
            return '';
        }

        $html = $md;

        // Bold, Italic, Strikethrough
        $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/_(.*?)_/', '<em>$1</em>', $html);
        $html = preg_replace('/~~(.*?)~~/', '<del>$1</del>', $html);

        // Links (with protocol filtering for security)
        $html = preg_replace_callback('/\[(.*?)\]\((.*?)\)/', function ($matches) {
            $url = trim($matches[2]);
            // Block dangerous protocols
            if (preg_match('/^(javascript|data|vbscript|file):/i', $url)) {
                $url = '#';
            }

            return '<a href="' . htmlspecialchars($url, ENT_QUOTES) . '" target="_blank">' . $matches[1] . '</a>';
        }, $html);

        // Unordered Lists
        $html = preg_replace_callback('/(?:^\s?\*\s*(.*?)$\n?)+/m', function ($matches) {
            $items = preg_replace('/^\s?\*\s*(.*?)$/m', '<li>$1</li>', $matches[0]);

            return '<ul>' . str_replace("\n", '', $items) . '</ul>';
        }, $html);

        // Ordered Lists
        $html = preg_replace_callback('/(?:^\s?(\d+)\.\s*(.*?)$\n?)+/m', function ($matches) {
            $items = preg_replace('/^\s?(\d+)\.\s*(.*?)$/m', '<li>$2</li>', $matches[0]);

            return '<ol>' . str_replace("\n", '', $items) . '</ol>';
        }, $html);

        // Paragraphs and Newlines
        $html = str_replace("\n\n", '</p><p>', $html);
        $html = str_replace("\n", '<br>', $html);

        // Final wrap in <p> if no blocks exist
        if (strpos($html, '<p>') === false && strlen($html) > 0) {
            $html = '<p>' . $html . '</p>';
        }

        return $html;
    }

    /**
     * Sanitize HTML content — allow safe inline tags, strip dangerous ones.
     */
    private function sanitizeHtml($html = ''): string
    {
        if (! $html) {
            return '';
        }

        // Escape raw HTML first to prevent XSS via attributes or unallowed tags.
        // Since we only allow Markdown, any raw HTML will be treated as plain text.
        $html = htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        // Convert Markdown to HTML
        $html = $this->markdownToHtml($html);

        // We can safely return this because markdownToHtml only generates safe tags.
        return $html;
    }

    /**
     * Remove margin-bottom from the last paragraph of HTML content.
     */
    private function removeLastMargin(string $html): string
    {
        // Add mb-0 class to the last <p> tag
        if (preg_match('/<p([^>]*)>(.*?)<\/p>\s*$/is', $html, $matches)) {
            $attr = $matches[1];
            $content = $matches[2];

            if (strpos($attr, 'class=') !== false) {
                $attr = preg_replace('/class="([^"]*)"/i', 'class="$1 mb-0"', $attr);
            } else {
                $attr .= ' class="mb-0"';
            }

            return preg_replace('/<p([^>]*)>(.*?)<\/p>\s*$/is', '<p' . $attr . '>' . $content . '</p>', $html);
        }

        return $html;
    }
}
