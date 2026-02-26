<?php
/**
 * SITE THEME CONFIGURATION
 * 
 * You can customize the look and feel of the site here. 
 * These values are injected into the CSS variables across the entire platform.
 */

$THEME = [
    // Core Colors
    'bg_color'         => '#0c0a15',
    'card_bg'          => 'rgba(255, 255, 255, 0.04)',
    'glass_border'     => 'rgba(255, 255, 255, 0.08)',
    
    // Aesthetic Accents
    'accent_primary'   => '#e5a354', // Primary Pink/Neon
    'accent_secondary' => '#d91d5e', // Secondary Cyan/Blue
    
    // Typography Colors
    'text_main'        => '#f0f0f0',
    'text_muted'       => '#a0a0b0',
    
    // Rarity Color System
    'rarity_common'    => '#b0b0b0',
    'rarity_uncommon'  => '#4dfa7c',
    'rarity_rare'      => '#4e7cfe',
    'rarity_epic'      => '#a335ee',
    'rarity_legendary' => '#ff8000',
    'rarity_mythic'    => '#ff4e4e',
    'rarity_test_card' => '#ff00ea', // Developer/Test Magenta
    'rarity_relic' => '#ff0000ff', // Above Unique
    
    // Functional Colors
    'mana_color'       => '#ff4e4e',
    'success_color'    => '#4dfa7c',
    'error_color'      => '#ff4e4e',
    
    // Dashboard Specifics
    'sidebar_bg'       => '#08070b',
    'login_box_bg'     => 'rgba(8, 7, 15, 0.6)',
    
    // Fonts (Google Fonts names)
    'font_primary'     => "'Inter', sans-serif",
    'font_heading'     => "'Outfit', sans-serif",
];

/**
 * Injects the theme variables as CSS custom properties into the <head>
 */
function injectTheme($theme) {
    // 1. Generate core variables
    echo "<style>:root {";
    foreach ($theme as $key => $val) {
        if (strpos($key, 'rarity_') === 0) {
            $name = str_replace('rarity_', '', $key);
            $css_name = str_replace('_', '-', $name); // Always hyphenated for CSS
            echo "--$css_name: $val;";
        } else if (!is_array($val) && strpos($val, 'rgba') === false && strpos($val, '#') === 0) {
            $css_name = str_replace('_', '-', $key);
            echo "--$css_name: $val;";
        }
    }
    // Manual overrides for complex values
    echo "--bg-color: {$theme['bg_color']};";
    echo "--card-bg: {$theme['card_bg']};";
    echo "--glass-border: {$theme['glass_border']};";
    echo "--font-primary: {$theme['font_primary']};";
    echo "--font-heading: {$theme['font_heading']};";
    echo "}";

    // 2. Generate Dynamic Rarity Classes
    foreach ($theme as $key => $val) {
        if (strpos($key, 'rarity_') === 0) {
            $name = str_replace('rarity_', '', $key);
            $class = str_replace('_', '-', $name); // rarity_test_card -> .rarity-test-card
            
            echo "
            /* Dynamic Styles for $name */
            .rarity-$class.owned { border-color: var(--$class) !important; box-shadow: 0 0 15px var(--$class) !important; }
            .mini-card.rarity-$class::before { background: var(--$class) !important; }
            .mini-card.rarity-$class:hover { box-shadow: 0 10px 25px var(--$class) !important; }
            .$class .rarity-badge { color: var(--$class) !important; border-color: var(--$class) !important; }
            .lookup-result-item.rarity-$class .lookup-result-rarity { color: var(--$class) !important; }
            .card-entry.$class .rarity-badge { color: var(--$class) !important; border-color: var(--$class) !important; }
            ";
        }
    }

    echo "
        body { font-family: var(--font-primary); background-color: var(--bg-color); color: var(--text-main); }
        h1, h2, h3, .gradient-text { font-family: var(--font-heading); }
    </style>";
}
