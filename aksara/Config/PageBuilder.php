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
 * When the signs come, those who do not believe at that time
 * will have only two choices: commit suicide or become brutal.
 */

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Page Builder Configuration.
 *
 * Defines CSS framework mappings and available components for the
 * drag-and-drop page builder. The default framework is Bootstrap 5,
 * but any CSS framework can be configured by adding its class mappings.
 */
class PageBuilder extends BaseConfig
{
    /**
     * Active CSS framework preset key.
     */
    public string $framework = 'bootstrap5';

    /**
     * CSS class mappings per framework.
     *
     * Each framework entry maps abstract component concepts to concrete
     * CSS classes. The renderer uses these mappings to produce framework-
     * specific HTML output.
     *
     * @var array<string, array<string, mixed>>
     */
    public array $frameworks = [
        'bootstrap5' => [
            'section'       => '',
            'container'     => 'container',
            'container_fluid' => 'container-fluid',
            'row'           => 'row',
            'col_prefix'    => 'col',
            'breakpoints'   => ['', 'sm', 'md', 'lg', 'xl', 'xxl'],
            'grid_columns'  => 12,
            'card'          => 'card',
            'card_body'     => 'card-body',
            'card_title'    => 'card-title',
            'card_text'     => 'card-text',
            'btn'           => 'btn',
            'btn_primary'   => 'btn btn-primary',
            'btn_secondary' => 'btn btn-secondary',
            'btn_outline_primary' => 'btn btn-outline-primary',
            'btn_lg'        => 'btn-lg',
            'btn_sm'        => 'btn-sm',
            'alert'         => 'alert',
            'accordion'     => 'accordion',
            'accordion_item' => 'accordion-item',
            'accordion_header' => 'accordion-header',
            'accordion_button' => 'accordion-button',
            'accordion_collapse' => 'accordion-collapse collapse',
            'accordion_body' => 'accordion-body',
            'img_fluid'     => 'img-fluid',
            'rounded'       => 'rounded-4',
            'text_center'   => 'text-center',
            'text_start'    => 'text-start',
            'text_end'      => 'text-end',
            'fw_bold'       => 'fw-bold',
            'mb_3'          => 'mb-3',
            'mb_4'          => 'mb-4',
            'mb_5'          => 'mb-5',
            'py_3'          => 'py-3',
            'py_5'          => 'py-5',
            'display_4'     => 'display-4',
            'lead'          => 'lead',
            'text_muted'    => 'text-muted',
        ],
    ];

    /**
     * Component categories shown in the builder palette.
     *
     * @var array<string, string>
     */
    public array $componentCategories = [
        'layout'      => 'Layout',
        'typography'  => 'Typography',
        'media'       => 'Media',
        'interactive' => 'Interactive',
        'content'     => 'Content',
    ];

    /**
     * Available components for the page builder.
     *
     * Each component defines its type, label, icon, category, and
     * default properties. The 'children' flag indicates whether the
     * component can contain nested components.
     *
     * @var array<string, array<string, mixed>>
     */
    public array $components = [
        // --- Layout ---
        'section' => [
            'label'    => 'Section',
            'icon'     => 'mdi mdi-page-layout-body',
            'category' => 'layout',
            'children' => true,
            'defaults' => [
                'class'       => 'section-padding',
                'id'          => '',
                'background'  => '',
            ],
        ],
        'container' => [
            'label'    => 'Container',
            'icon'     => 'mdi mdi-border-all-variant',
            'category' => 'layout',
            'children' => true,
            'defaults' => [
                'fluid' => false,
            ],
        ],
        'row' => [
            'label'    => 'Row',
            'icon'     => 'mdi mdi-view-headline',
            'category' => 'layout',
            'children' => true,
            'defaults' => [
                'class'          => '',
                'align_items'    => '',
                'justify_content' => '',
            ],
            'options'  => [
                'align_items'    => [
                    '' => 'Default',
                    'align-items-start' => 'Top',
                    'align-items-center' => 'Middle',
                    'align-items-end' => 'Bottom',
                ],
                'justify_content' => [
                    '' => 'Default',
                    'justify-content-start' => 'Start',
                    'justify-content-center' => 'Center',
                    'justify-content-end' => 'End',
                    'justify-content-between' => 'Space Between',
                    'justify-content-around' => 'Space Around',
                ]
            ]
        ],
        'column' => [
            'label'    => 'Column',
            'icon'     => 'mdi mdi-view-column',
            'category' => 'layout',
            'children' => true,
            'defaults' => [
                'size'   => ['md' => 12],
                'class'  => '',
                'align_self' => '',
            ],
            'options' => [
                'align_self' => [
                    '' => 'Default',
                    'align-self-start' => 'Top',
                    'align-self-center' => 'Middle',
                    'align-self-end' => 'Bottom',
                    'align-self-stretch' => 'Stretch',
                ]
            ]
        ],

        // --- Typography ---
        'heading' => [
            'label'    => 'Heading',
            'icon'     => 'mdi mdi-format-header-1',
            'category' => 'typography',
            'children' => false,
            'defaults' => [
                'level'     => 2,
                'text'      => 'Heading text',
                'alignment' => 'left',
                'class'     => '',
            ],
            'options'  => [
                'level'     => [1=>'H1', 2=>'H2', 3=>'H3', 4=>'H4', 5=>'H5', 6=>'H6'],
                'alignment' => ['left' => 'Left', 'center' => 'Center', 'right' => 'Right']
            ]
        ],
        'paragraph' => [
            'label'    => 'Paragraph',
            'icon'     => 'mdi mdi-format-paragraph',
            'category' => 'typography',
            'children' => false,
            'defaults' => [
                'text'      => 'Paragraph text goes here.',
                'alignment' => 'left',
                'class'     => '',
            ],
            'options'  => [
                'alignment' => ['left' => 'Left', 'center' => 'Center', 'right' => 'Right']
            ]
        ],
        'divider' => [
            'label'    => 'Divider',
            'icon'     => 'mdi mdi-minus',
            'category' => 'typography',
            'children' => false,
            'defaults' => [
                'class' => '',
            ],
        ],

        // --- Media ---
        'image' => [
            'label'    => 'Image',
            'icon'     => 'mdi mdi-image',
            'category' => 'media',
            'children' => false,
            'defaults' => [
                'src'   => '',
                'alt'   => '',
                'class' => 'img-fluid',
                'width' => '',
            ],
        ],
        'video' => [
            'label'    => 'Video Embed',
            'icon'     => 'mdi mdi-video',
            'category' => 'media',
            'children' => false,
            'defaults' => [
                'url'    => '',
                'ratio'  => '16x9',
                'class'  => '',
            ],
            'options'  => [
                'ratio' => ['21x9'=>'21:9','16x9'=>'16:9','4x3'=>'4:3','1x1'=>'1:1']
            ]
        ],

        // --- Interactive ---
        'carousel' => [
            'label'    => 'Carousel',
            'icon'     => 'mdi mdi-view-carousel',
            'category' => 'interactive',
            'children' => false,
            'defaults' => [
                'items' => [
                    ['src' => '', 'title' => 'Slide 1', 'subtitle' => 'Description for slide 1'],
                    ['src' => '', 'title' => 'Slide 2', 'subtitle' => 'Description for slide 2'],
                ],
                'indicators' => true,
                'controls'   => true,
                'interval'   => 5000,
                'class'      => '',
            ],
        ],
        'accordion' => [
            'label'    => 'Accordion',
            'icon'     => 'mdi mdi-view-sequential',
            'category' => 'interactive',
            'children' => false,
            'defaults' => [
                'items' => [
                    ['title' => 'Item 1', 'body' => 'Content for item 1.'],
                    ['title' => 'Item 2', 'body' => 'Content for item 2.'],
                ],
                'class' => '',
            ],
        ],
        'tabs' => [
            'label'    => 'Nav Tabs',
            'icon'     => 'mdi mdi-tab',
            'category' => 'interactive',
            'children' => false,
            'defaults' => [
                'items' => [
                    ['title' => 'Tab 1', 'content' => 'Content for tab 1'],
                    ['title' => 'Tab 2', 'content' => 'Content for tab 2'],
                ],
                'style'     => 'tabs',
                'alignment' => 'horizontal',
                'class'     => '',
            ],
            'options' => [
                'style'     => ['tabs' => 'Tabs', 'pills' => 'Pills'],
                'alignment' => ['horizontal' => 'Horizontal', 'vertical' => 'Vertical']
            ]
        ],
        'alert' => [
            'label'    => 'Alert',
            'icon'     => 'mdi mdi-alert-circle-outline',
            'category' => 'interactive',
            'children' => false,
            'defaults' => [
                'text'  => 'This is an alert.',
                'style' => 'info',
                'class' => '',
            ],
            'options'  => [
                'style' => [
                    'primary' => 'Primary',
                    'secondary' => 'Secondary',
                    'success' => 'Success',
                    'danger' => 'Danger',
                    'warning' => 'Warning',
                    'info' => 'Info',
                    'light' => 'Light',
                    'dark' => 'Dark'
                ]
            ]
        ],
        'button' => [
            'label'    => 'Button',
            'icon'     => 'mdi mdi-gesture-tap-button',
            'category' => 'interactive',
            'children' => false,
            'defaults' => [
                'text'    => 'Click me',
                'url'     => '#',
                'icon'    => '',
                'icon_placement' => 'prefix',
                'style'   => 'primary',
                'size'    => '',
                'class'   => '',
                'target'  => '_self',
                'rounded' => true,
            ],
            'options'  => [
                'icon'  => 'iconpicker',
                'icon_placement' => ['prefix' => 'Before Text', 'suffix' => 'After Text'],
                'style' => [
                    'primary' => 'Primary',
                    'secondary' => 'Secondary',
                    'success' => 'Success',
                    'danger' => 'Danger',
                    'warning' => 'Warning',
                    'info' => 'Info',
                    'light' => 'Light',
                    'dark' => 'Dark',
                    'link' => 'Link',
                    'outline-primary' => 'Outline Primary',
                    'outline-secondary' => 'Outline Secondary'
                ],
                'size' => [
                    '' => 'Default',
                    'btn-sm' => 'Small',
                    'btn-lg' => 'Large'
                ],
                'target' => [
                    '_self' => 'Same Tab',
                    '_blank' => 'New Tab'
                ]
            ]
        ],

        // --- Content ---
        'hero' => [
            'label'    => 'Hero Section',
            'icon'     => 'mdi mdi-presentation',
            'category' => 'content',
            'children' => false,
            'defaults' => [
                'title'       => 'Hero Title',
                'subtitle'    => 'Subtitle text goes here.',
                'button_text' => 'Get Started',
                'button_url'  => '#',
                'background'  => '',
                'alignment'   => 'center',
                'overlay'     => true,
            ],
            'options' => [
                'alignment'  => [
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right'
                ],
                'overlay' => 'boolean'
            ],
            'grouping' => []
        ],
        'card' => [
            'label'    => 'Card',
            'icon'     => 'mdi mdi-card-outline',
            'category' => 'content',
            'children' => true,
            'defaults' => [
                'title' => 'Card Title',
                'text'  => 'Card content goes here.',
                'image' => '',
                'class' => '',
            ],
        ],
        'feature_box' => [
            'label'    => 'Feature Box',
            'icon'     => 'mdi mdi-star-box-outline',
            'category' => 'content',
            'children' => false,
            'defaults' => [
                'icon'  => 'mdi mdi-star',
                'title' => 'Feature Title',
                'text'  => 'Feature description goes here.',
                'class' => '',
            ],
            'options' => [
                'icon' => 'iconpicker'
            ]
        ],
        'pricing' => [
            'label'    => 'Pricing Table',
            'icon'     => 'mdi mdi-currency-usd',
            'category' => 'content',
            'children' => false,
            'defaults' => [
                'title'     => 'Pro Plan',
                'price'     => '$29',
                'period'    => '/month',
                'features'  => "Feature 1\nFeature 2\nFeature 3",
                'btn_text'  => 'Get Started',
                'btn_url'   => '#',
                'featured'  => false,
                'class'     => '',
            ],
            'options' => [
                'features' => 'textarea',
                'featured' => 'boolean'
            ]
        ],
        'testimonial' => [
            'label'    => 'Testimonial',
            'icon'     => 'mdi mdi-comment-quote-outline',
            'category' => 'content',
            'children' => false,
            'defaults' => [
                'quote'  => 'The best CMS I have ever used!',
                'author' => 'John Doe',
                'role'   => 'CEO at Tech Corp',
                'image'  => '',
                'class'  => '',
            ],
        ],
        'team_member' => [
            'label'    => 'Team Member',
            'icon'     => 'mdi mdi-account-box-outline',
            'category' => 'content',
            'children' => false,
            'defaults' => [
                'name'  => 'Jane Smith',
                'role'  => 'Designer',
                'image' => '',
                'bio'   => 'Creative mind behind our beautiful interfaces.',
                'class' => '',
            ],
        ],
        'cta' => [
            'label'    => 'CTA Section',
            'icon'     => 'mdi mdi-bullhorn-outline',
            'category' => 'content',
            'children' => false,
            'defaults' => [
                'title'       => 'Ready to get started?',
                'text'        => 'Join thousands of satisfied customers today.',
                'button_text' => 'Join Now',
                'button_url'  => '#',
                'background'  => 'primary',
                'class'       => '',
            ],
            'options' => [
                'background' => ['primary' => 'Primary', 'dark' => 'Dark', 'light' => 'Light']
            ]
        ],
        'spacer' => [
            'label'    => 'Spacer',
            'icon'     => 'mdi mdi-arrow-expand-vertical',
            'category' => 'layout',
            'children' => false,
            'defaults' => [
                'height' => '40',
            ],
        ],
    ];

    /**
     * Page templates with pre-built layouts.
     *
     * @var array<string, array<string, mixed>>
     */
    public array $templates = [
        'blank' => [
            'label'       => 'Blank Page',
            'description' => 'Start from scratch with an empty canvas.',
            'icon'        => 'mdi mdi-file-outline',
            'layout'      => [],
        ],
        'landing' => [
            'label'       => 'Landing Page',
            'description' => 'Hero section with features and call to action.',
            'icon'        => 'mdi mdi-rocket-launch',
            'layout'      => [],
        ],
        'services' => [
            'label'       => 'Service Page',
            'description' => 'Display your services with tabs and features.',
            'icon'        => 'mdi mdi-cog-outline',
            'layout'      => [],
        ],
        'pricing' => [
            'label'       => 'Pricing Page',
            'description' => 'Clear pricing tables for your products.',
            'icon'        => 'mdi mdi-currency-usd',
            'layout'      => [],
        ],
        'about' => [
            'label'       => 'About Us',
            'description' => 'Introduce your organization with team and info.',
            'icon'        => 'mdi mdi-account-group',
            'layout'      => [],
        ],
        'contact' => [
            'label'       => 'Contact Page',
            'description' => 'Simple contact information layout.',
            'icon'        => 'mdi mdi-email-outline',
            'layout'      => [],
        ],
    ];

    /**
     * Get a pre-built template layout by key.
     *
     * @param string $key Template key.
     *
     * @return array<string, mixed> JSON-encodable layout array.
     */
    public function getTemplate(string $key): array
    {
        return match ($key) {
            'landing'  => $this->_landingTemplate(),
            'services' => $this->_servicesTemplate(),
            'pricing'  => $this->_pricingTemplate(),
            'about'    => $this->_aboutTemplate(),
            'contact'  => $this->_contactTemplate(),
            default   => [
                'version'    => '1.0',
                'framework'  => $this->framework,
                'components' => [],
            ],
        };
    }

    /**
     * Landing page template.
     */
    private function _landingTemplate(): array
    {
        return [
            'version'    => '1.0',
            'framework'  => $this->framework,
            'components' => [
                [
                    'type'     => 'hero',
                    'id'       => 'hero_1',
                    'props'    => [
                        'title'       => 'Build Something Amazing',
                        'subtitle'    => 'Create beautiful, responsive pages with our drag and drop page builder.',
                        'button_text' => 'Get Started',
                        'button_url'  => '#features',
                        'alignment'   => 'center',
                        'overlay'     => true,
                    ],
                ],
                [
                    'type'     => 'section',
                    'id'       => 'features',
                    'props'    => ['class' => 'section-padding', 'id' => 'features'],
                    'children' => [
                        [
                            'type' => 'container',
                            'id'   => 'features_container',
                            'children' => [
                                [
                                    'type'  => 'heading',
                                    'id'    => 'features_heading',
                                    'props' => ['level' => 2, 'text' => 'Our Features', 'class' => 'text-center mb-5'],
                                ],
                                [
                                    'type' => 'row',
                                    'id'   => 'features_row',
                                    'children' => [
                                        [
                                            'type'  => 'column',
                                            'id'    => 'feat_col_1',
                                            'props' => ['size' => ['md' => 4]],
                                            'children' => [
                                                [
                                                    'type'  => 'feature_box',
                                                    'id'    => 'feat_1',
                                                    'props' => [
                                                        'icon'  => 'mdi mdi-lightning-bolt',
                                                        'title' => 'Fast Performance',
                                                        'text'  => 'Optimized for speed and reliability.',
                                                    ],
                                                ],
                                            ],
                                        ],
                                        [
                                            'type'  => 'column',
                                            'id'    => 'feat_col_2',
                                            'props' => ['size' => ['md' => 4]],
                                            'children' => [
                                                [
                                                    'type'  => 'feature_box',
                                                    'id'    => 'feat_2',
                                                    'props' => [
                                                        'icon'  => 'mdi mdi-shield-check',
                                                        'title' => 'Secure by Default',
                                                        'text'  => 'Built with security best practices.',
                                                    ],
                                                ],
                                            ],
                                        ],
                                        [
                                            'type'  => 'column',
                                            'id'    => 'feat_col_3',
                                            'props' => ['size' => ['md' => 4]],
                                            'children' => [
                                                [
                                                    'type'  => 'feature_box',
                                                    'id'    => 'feat_3',
                                                    'props' => [
                                                        'icon'  => 'mdi mdi-cellphone-link',
                                                        'title' => 'Fully Responsive',
                                                        'text'  => 'Looks great on any device.',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Services page template.
     */
    private function _servicesTemplate(): array
    {
        return [
            'version'    => '1.0',
            'framework'  => $this->framework,
            'components' => [
                [
                    'type'     => 'section',
                    'id'       => 'sec_serv_1',
                    'props'    => ['class' => 'section-padding'],
                    'children' => [
                        [
                            'type' => 'container',
                            'id'   => 'con_serv_1',
                            'children' => [
                                [
                                    'type'  => 'heading',
                                    'id'    => 'h_serv_1',
                                    'props' => ['level' => 1, 'text' => 'Our Expertise', 'class' => 'text-center mb-5'],
                                ],
                                [
                                    'type'  => 'tabs',
                                    'id'    => 'tab_serv_1',
                                    'props' => [
                                        'items' => [
                                            ['title' => 'Web Design', 'content' => 'Crafting beautiful and functional websites.'],
                                            ['title' => 'Mobile Apps', 'content' => 'Developing high-performance mobile applications.'],
                                            ['title' => 'SEO', 'content' => 'Improving your online visibility.'],
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Pricing page template.
     */
    private function _pricingTemplate(): array
    {
        return [
            'version'    => '1.0',
            'framework'  => $this->framework,
            'components' => [
                [
                    'type'     => 'section',
                    'id'       => 'sec_price_1',
                    'props'    => ['class' => 'section-padding'],
                    'children' => [
                        [
                            'type' => 'container',
                            'id'   => 'con_price_1',
                            'children' => [
                                [
                                    'type'  => 'heading',
                                    'id'    => 'h_price_1',
                                    'props' => ['level' => 1, 'text' => 'Choose Your Plan', 'class' => 'text-center mb-5'],
                                ],
                                [
                                    'type' => 'row',
                                    'id'   => 'row_price_1',
                                    'children' => [
                                        [
                                            'type' => 'column',
                                            'id'   => 'col_price_1',
                                            'props' => ['size' => ['md' => 4]],
                                            'children' => [
                                                ['type' => 'pricing', 'id' => 'p_1', 'props' => ['title' => 'Basic', 'price' => '$0', 'period' => '/forever', 'features' => "1 Project\nBasic Support"]]
                                            ]
                                        ],
                                        [
                                            'type' => 'column',
                                            'id'   => 'col_price_2',
                                            'props' => ['size' => ['md' => 4]],
                                            'children' => [
                                                ['type' => 'pricing', 'id' => 'p_2', 'props' => ['title' => 'Pro', 'price' => '$29', 'featured' => true, 'features' => "10 Projects\nPriority Support\nAPI Access"]]
                                            ]
                                        ],
                                        [
                                            'type' => 'column',
                                            'id'   => 'col_price_3',
                                            'props' => ['size' => ['md' => 4]],
                                            'children' => [
                                                ['type' => 'pricing', 'id' => 'p_3', 'props' => ['title' => 'Enterprise', 'price' => '$99', 'features' => "Unlimited Projects\n24/7 Support\nCustom Solutions"]]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * About page template.
     */
    private function _aboutTemplate(): array
    {
        return [
            'version'    => '1.0',
            'framework'  => $this->framework,
            'components' => [
                [
                    'type'     => 'section',
                    'id'       => 'about_hero',
                    'props'    => ['class' => 'section-padding'],
                    'children' => [
                        [
                            'type' => 'container',
                            'id'   => 'about_container',
                            'children' => [
                                [
                                    'type'  => 'heading',
                                    'id'    => 'about_heading',
                                    'props' => ['level' => 1, 'text' => 'About Us', 'class' => 'display-4 fw-bold text-center'],
                                ],
                                [
                                    'type'  => 'paragraph',
                                    'id'    => 'about_text',
                                    'props' => ['text' => 'We are a team of passionate individuals building amazing products.', 'class' => 'lead text-muted text-center mb-5'],
                                ],
                                [
                                    'type' => 'row',
                                    'id'   => 'about_row',
                                    'children' => [
                                        [
                                            'type' => 'column',
                                            'id'   => 'about_col_1',
                                            'props' => ['size' => ['md' => 6]],
                                            'children' => [
                                                ['type' => 'image', 'id' => 'about_img', 'props' => ['src' => 'https://via.placeholder.com/600x400']]
                                            ]
                                        ],
                                        [
                                            'type' => 'column',
                                            'id'   => 'about_col_2',
                                            'props' => ['size' => ['md' => 6]],
                                            'children' => [
                                                ['type' => 'heading', 'id' => 'about_h2', 'props' => ['level' => 2, 'text' => 'Our Mission']],
                                                ['type' => 'paragraph', 'id' => 'about_p2', 'props' => ['text' => 'To empower creators and businesses with the most intuitive and powerful tools.']]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'section',
                    'id' => 'sec_team',
                    'props' => ['class' => 'section-padding bg-light'],
                    'children' => [
                        [
                            'type' => 'container',
                            'id' => 'con_team',
                            'children' => [
                                ['type' => 'heading', 'id' => 'h_team', 'props' => ['level' => 2, 'text' => 'Meet Our Team', 'class' => 'text-center mb-5']],
                                [
                                    'type' => 'row',
                                    'id' => 'row_team',
                                    'children' => [
                                        ['type' => 'column', 'id' => 'c_t1', 'props' => ['size' => ['md' => 4]], 'children' => [['type' => 'team_member', 'id' => 't1', 'props' => ['name' => 'Aby Dahana', 'role' => 'Founder & CEO']]]],
                                        ['type' => 'column', 'id' => 'c_t2', 'props' => ['size' => ['md' => 4]], 'children' => [['type' => 'team_member', 'id' => 't2', 'props' => ['name' => 'Jane Smith', 'role' => 'Lead Designer']]]],
                                        ['type' => 'column', 'id' => 'c_t3', 'props' => ['size' => ['md' => 4]], 'children' => [['type' => 'team_member', 'id' => 't3', 'props' => ['name' => 'John Wilson', 'role' => 'Lead Developer']]]],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

    /**
     * Contact page template.
     */
    private function _contactTemplate(): array
    {
        return [
            'version'    => '1.0',
            'framework'  => $this->framework,
            'components' => [
                [
                    'type'     => 'section',
                    'id'       => 'contact_section',
                    'props'    => ['class' => 'section-padding'],
                    'children' => [
                        [
                            'type' => 'container',
                            'id'   => 'contact_container',
                            'children' => [
                                [
                                    'type'  => 'heading',
                                    'id'    => 'contact_heading',
                                    'props' => ['level' => 1, 'text' => 'Contact Us', 'class' => 'display-4 fw-bold text-center mb-4'],
                                ],
                                [
                                    'type'  => 'paragraph',
                                    'id'    => 'contact_text',
                                    'props' => ['text' => 'Have questions? We would love to hear from you.', 'class' => 'lead text-muted text-center mb-5'],
                                ],
                                [
                                    'type' => 'row',
                                    'id'   => 'contact_row',
                                    'children' => [
                                        [
                                            'type'  => 'column',
                                            'id'    => 'contact_col_1',
                                            'props' => ['size' => ['md' => 4]],
                                            'children' => [
                                                [
                                                    'type'  => 'feature_box',
                                                    'id'    => 'contact_email',
                                                    'props' => ['icon' => 'mdi mdi-email', 'title' => 'Email', 'text' => 'info@example.com'],
                                                ],
                                            ],
                                        ],
                                        [
                                            'type'  => 'column',
                                            'id'    => 'contact_col_2',
                                            'props' => ['size' => ['md' => 4]],
                                            'children' => [
                                                [
                                                    'type'  => 'feature_box',
                                                    'id'    => 'contact_phone',
                                                    'props' => ['icon' => 'mdi mdi-phone', 'title' => 'Phone', 'text' => '+62 812 3456 7890'],
                                                ],
                                            ],
                                        ],
                                        [
                                            'type'  => 'column',
                                            'id'    => 'contact_col_3',
                                            'props' => ['size' => ['md' => 4]],
                                            'children' => [
                                                [
                                                    'type'  => 'feature_box',
                                                    'id'    => 'contact_address',
                                                    'props' => ['icon' => 'mdi mdi-map-marker', 'title' => 'Address', 'text' => 'Jakarta, Indonesia'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
