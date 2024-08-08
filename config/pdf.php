<?php

return [
    'title' => 'Laravel PDF',

    'author' => '',

    'mode' => 'UTF-8',
    
    /*
     * Page size A4, A3, latter etc.
     */
    'format' => 'A4',

    /*
     * Default font zise for all text
     */
    'default_font_size' => '13',

    /*
     * Default font for all text
     */
    'default_font' => 'sans-serif',

    /*
     * Path for font folder
     *
     * don't forget the trailing slash!
     */
    'custom_font_path' => base_path('/resources/fonts/'),

    /*
     * Content direct ltr or rtl
     */
    'direction' => 'ltr',

    /*
     * Page left margin
     */
    'margin_left' => 10,

    /*
     * Page right margin
     */
    'margin_right' => 10,

    /*
     * Page top margin
     */
    'margin_top' => 10,

    /*
     * Page bottom margin
     */
    'margin_bottom' => 10,

    /*
     * Page header margin
     */
    'margin_header' => 0,

    /*
     * Page footer margin
     */
    'margin_footer' => 0,

    /*
     * Page orientation L - landscape, P - portrait
     */
    'orientation' => 'P',

    /*
     * Watermark
     */
    'show_watermark' => false,
    'watermark' => 'Document',
    'watermark_font' => 'sans-serif',
    'display_mode' => 'fullpage',
    'watermark_text_alpha' => 0.1
];
