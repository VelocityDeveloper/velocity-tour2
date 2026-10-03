<?php
/**
 * Pengaturan Tour 2 di Customizer bawaan WordPress (tanpa Kirki).
 *
 * Nama theme mod sama dengan versi Kirki (color_theme, background_themewebsite,
 * image_bannerheader) supaya nilai yang sudah tersimpan tetap terbaca.
 * background_themewebsite tetap satu array; tiap kontrol menyimpan satu kuncinya.
 *
 * @package justg
 */

defined('ABSPATH') || exit;

const VELOCITY_TOUR2_WARNA = '#176cb7';

/**
 * Nilai bawaan latar website (sama dengan default Kirki dulu).
 */
function velocity_tour2_latar_bawaan()
{
    return [
        'background-color'      => '#ffffff',
        'background-image'      => '',
        'background-repeat'     => 'repeat',
        'background-position'   => 'center center',
        'background-size'       => 'cover',
        'background-attachment' => 'scroll',
    ];
}

/**
 * Warna hex, atau rgb()/rgba() yang dulu bisa disimpan Kirki.
 */
function velocity_tour2_sanitize_warna($warna)
{
    $warna = trim((string) $warna);
    if (preg_match('/^rgba?\(\s*[\d.\s,%]+\)$/i', $warna)) {
        return $warna;
    }
    return sanitize_hex_color($warna) ?: '';
}

add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_panel('panel_velocity', [
        'priority' => 10,
        'title'    => __('Velocity Theme', 'justg'),
    ]);

    // Header
    $wp_customize->add_section('section_headervelocity', [
        'panel'       => 'panel_velocity',
        'title'       => __('Header', 'justg'),
        'description' => __('Gambar header utama diatur di bagian Header Image.', 'justg'),
        'priority'    => 20,
    ]);
    $wp_customize->add_setting('image_bannerheader', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'image_bannerheader', [
        'label'       => __('Banner Header', 'justg'),
        'description' => __('Banner selebar halaman di bawah menu. Kosongkan bila tidak dipakai.', 'justg'),
        'section'     => 'section_headervelocity',
    ]));

    // Warna & latar
    $wp_customize->add_section('section_colorvelocity', [
        'panel'    => 'panel_velocity',
        'title'    => __('Color & Background', 'justg'),
        'priority' => 30,
    ]);
    $wp_customize->add_setting('color_theme', [
        'default'           => VELOCITY_TOUR2_WARNA,
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_theme', [
        'label'       => __('Primary Color', 'justg'),
        'description' => __('Warna utama tema (--color-theme & --bs-primary): menu, judul widget, tombol, dan footer.', 'justg'),
        'section'     => 'section_colorvelocity',
    ]));

    $bawaan = velocity_tour2_latar_bawaan();
    $wp_customize->add_setting('background_themewebsite[background-color]', [
        'default'           => $bawaan['background-color'],
        'sanitize_callback' => 'velocity_tour2_sanitize_warna',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'background_themewebsite[background-color]', [
        'label'   => __('Website Background Color', 'justg'),
        'section' => 'section_colorvelocity',
    ]));
    $wp_customize->add_setting('background_themewebsite[background-image]', [
        'default'           => $bawaan['background-image'],
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'background_themewebsite[background-image]', [
        'label'   => __('Website Background Image', 'justg'),
        'section' => 'section_colorvelocity',
    ]));

    $pilihan = [
        'background-repeat'     => [__('Background Repeat', 'justg'), [
            'repeat'    => __('Repeat', 'justg'),
            'no-repeat' => __('No Repeat', 'justg'),
            'repeat-x'  => __('Repeat Horizontally', 'justg'),
            'repeat-y'  => __('Repeat Vertically', 'justg'),
        ]],
        'background-position'   => [__('Background Position', 'justg'), [
            'left top'      => __('Left Top', 'justg'),
            'left center'   => __('Left Center', 'justg'),
            'left bottom'   => __('Left Bottom', 'justg'),
            'center top'    => __('Center Top', 'justg'),
            'center center' => __('Center Center', 'justg'),
            'center bottom' => __('Center Bottom', 'justg'),
            'right top'     => __('Right Top', 'justg'),
            'right center'  => __('Right Center', 'justg'),
            'right bottom'  => __('Right Bottom', 'justg'),
        ]],
        'background-size'       => [__('Background Size', 'justg'), [
            'cover'   => __('Cover', 'justg'),
            'contain' => __('Contain', 'justg'),
            'auto'    => __('Auto', 'justg'),
        ]],
        'background-attachment' => [__('Background Attachment', 'justg'), [
            'scroll' => __('Scroll', 'justg'),
            'fixed'  => __('Fixed', 'justg'),
        ]],
    ];
    foreach ($pilihan as $kunci => [$label, $choices]) {
        $id = "background_themewebsite[$kunci]";
        $wp_customize->add_setting($id, [
            'default'           => $bawaan[$kunci],
            'sanitize_callback' => function ($nilai) use ($choices, $bawaan, $kunci) {
                return isset($choices[$nilai]) ? $nilai : $bawaan[$kunci];
            },
        ]);
        $wp_customize->add_control($id, [
            'type'    => 'select',
            'label'   => $label,
            'section' => 'section_colorvelocity',
            'choices' => $choices,
        ]);
    }
});

// Penyesuaian bagian bawaan WordPress & tema induk. Prioritas akhir supaya berjalan
// sesudah bagian itu terdaftar, termasuk panel Kirki tema induk lama bila Kirki masih aktif.
add_action('customize_register', function ($wp_customize) {
    // Identitas situs masuk panel; logo tidak dipakai (header memakai Header Image).
    $identitas = $wp_customize->get_section('title_tagline');
    if ($identitas) {
        $identitas->panel = 'panel_velocity';
        $identitas->priority = 10;
    }
    $wp_customize->remove_control('custom_logo');
    $wp_customize->remove_control('display_header_text');

    // Digantikan Primary Color & Website Background di atas.
    $wp_customize->remove_control('primary_color');
    $wp_customize->remove_section('velocity_section_background');
    foreach (['global_panel', 'panel_header', 'panel_footer', 'panel_antispam'] as $panel) {
        $wp_customize->remove_panel($panel);
    }
}, 1000);

/**
 * CSS dari pengaturan di atas. Dicetak di akhir <head> seperti Kirki dulu, supaya
 * menang atas CSS tema induk.
 */
add_action('wp_head', function () {
    $warna = sanitize_hex_color(get_theme_mod('color_theme', VELOCITY_TOUR2_WARNA)) ?: VELOCITY_TOUR2_WARNA;
    $rgb = implode(',', array_map('hexdec', str_split(ltrim(strlen($warna) === 4 ? preg_replace('/([0-9a-f])/i', '$1$1', $warna) : $warna, '#'), 2)));
    $css = ':root{--color-theme:' . $warna . ';--bs-primary:' . $warna . ';--bs-primary-rgb:' . $rgb . ';--primary:' . $warna . ';}'
        . '.border-color-theme{--bs-border-color:' . $warna . ';}';

    $latar = get_theme_mod('background_themewebsite', []);
    $latar = array_merge(velocity_tour2_latar_bawaan(), is_array($latar) ? $latar : []);
    $aturan = [];
    foreach ($latar as $prop => $nilai) {
        if (!array_key_exists($prop, velocity_tour2_latar_bawaan())) {
            continue;
        }
        // Kirki bisa menyimpan id lampiran, bukan URL.
        if ($prop === 'background-image' && is_numeric($nilai)) {
            $nilai = wp_get_attachment_url((int) $nilai);
        }
        $nilai = trim((string) $nilai);
        if ($nilai === '') {
            continue;
        }
        if ($prop === 'background-color') {
            $nilai = velocity_tour2_sanitize_warna($nilai);
        } elseif ($prop === 'background-image') {
            $nilai = 'url("' . esc_url($nilai) . '")';
        } else {
            $nilai = esc_attr($nilai);
        }
        if ($nilai !== '') {
            $aturan[] = $prop . ':' . $nilai;
        }
    }
    if ($aturan) {
        $css .= ':root[data-bs-theme=light] body,body{' . implode(';', $aturan) . ';}';
    }
    echo '<style id="velocity-tour2-customizer">' . wp_strip_all_tags($css) . '</style>' . "\n";
}, 100);
