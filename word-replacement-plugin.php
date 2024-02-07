<?php
/*
Plugin Name: Dynamic Word Replacement
Plugin URI: https://github.com/ahmedalimughal/Dynamic-word-replacement
Description: Replace a word throughout the site dynamically.
Author: Ahmed Ali Mughal
Author URI: https://ahmedalimughal.netlify.app/
License: GPL v2 or later
Version: 1.0.1
Requires at least: 6.2
Requires PHP: 7.4
GitHub Plugin URI: https://github.com/ahmedalimughal/Dynamic-word-replacement
*/

// Add a settings page to the admin menu
function word_replacement_menu() {
    add_options_page('Word Replacement Settings', 'Word Replacement', 'manage_options', 'word-replacement-settings', 'word_replacement_settings_page');
}

add_action('admin_menu', 'word_replacement_menu');

// Function to render the settings page
function word_replacement_settings_page() {
    ?>
    <div class="wrap">
        <h2>Word Replacement Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('word_replacement_options');
            do_settings_sections('word-replacement-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register and define settings
function word_replacement_settings_init() {
    register_setting('word_replacement_options', 'word_replacement_options', 'word_replacement_sanitize');

    add_settings_section('word_replacement_section', 'Word Replacement Settings', 'word_replacement_section_callback', 'word-replacement-settings');

    add_settings_field('original_word', 'Original Word', 'word_replacement_original_word_callback', 'word-replacement-settings', 'word_replacement_section');
    add_settings_field('replacement_word', 'Replacement Word', 'word_replacement_replacement_word_callback', 'word-replacement-settings', 'word_replacement_section');
}

add_action('admin_init', 'word_replacement_settings_init');

// Sanitize and validate input
function word_replacement_sanitize($input) {
    $sanitized_input = array();
    
    if (isset($input['original_word'])) {
        $sanitized_input['original_word'] = sanitize_text_field($input['original_word']);
    }

    if (isset($input['replacement_word'])) {
        $sanitized_input['replacement_word'] = sanitize_text_field($input['replacement_word']);
    }

    return $sanitized_input;
}

// Callback functions to render input fields
function word_replacement_original_word_callback() {
    $options = get_option('word_replacement_options');
    echo '<input type="text" id="original_word" name="word_replacement_options[original_word]" value="' . esc_attr($options['original_word']) . '" />';
}

function word_replacement_replacement_word_callback() {
    $options = get_option('word_replacement_options');
    echo '<input type="text" id="replacement_word" name="word_replacement_options[replacement_word]" value="' . esc_attr($options['replacement_word']) . '" />';
}

function word_replacement_section_callback() {
    echo '<p>Enter the words you want to replace below:</p>';
}

// Function to replace the word in content
function replace_word_content($content) {
    $options = get_option('word_replacement_options');
    $original_word = isset($options['original_word']) ? $options['original_word'] : '';
    $replacement_word = isset($options['replacement_word']) ? $options['replacement_word'] : '';

    // Replace the word in the content
    $content = str_replace($original_word, $replacement_word, $content);

    return $content;
}

// Hook into various content areas
add_filter('the_content', 'replace_word_content');
add_filter('the_title', 'replace_word_content');
add_filter('widget_text_content', 'replace_word_content');
