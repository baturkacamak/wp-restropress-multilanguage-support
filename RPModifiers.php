<?php

/**
 * RPModifiers class provides support for language translation in the Restropress plugin
 *
 * The class registers several filters and an action that modify how certain options are stored and retrieved
 * in the WordPress options table in a way that allows for language translation of these options.
 *
 * @package Restropress Multilanguage Support
 * @author  Batur Kacamak
 */
class RPModifiers
{
    /**
     * Array of option keys to filter
     *
     * @var array
     */
    private static $optionKeys = [
        'rpress_settings',
        'rpress_options',
        'rpress_settings_general',
        'rpress_settings_gateways',
        'rpress_settings_emails',
        'rpress_settings_styles',
        'rpress_settings_taxes',
        'rpress_settings_misc',
    ];

    /**
     * RPModifiers constructor.
     */
    public function __construct()
    {
        $this->register();
    }

    /**
     * Retrieve an option value
     *
     * @param mixed $value Original option value
     * @param string $option Option name
     *
     * @return mixed Option value
     */
    public function getOption($value, $option)
    {
        $option_language_key = $this->getOptionKey($option);

        if ($value_lang = get_option($option_language_key)) {
            $value = $value_lang;
        }

        return $value;
    }

    /**
     * Update an option value
     *
     * @param mixed $value New option value
     * @param mixed $old_value Old option value
     * @param string $option Option name
     *
     * @return mixed New option value
     */
    public function updateOption($value, $old_value, $option)
    {
        $option_key = $this->getOptionKey($option);

        update_option($option_key, $value);

        return $value;
    }

    /**
     * Set the `$GLOBALS['rpress_options']` global variable to the value of a translated version of the
     * `rpress_settings` option, if it exists
     */
    public function setRestropressOptions()
    {
        if (isset($GLOBALS['rpress_options'])) {
            $option_key = $this->getOptionKey('rpress_settings');
            if ($language_options = get_option($option_key)) {
                $GLOBALS['rpress_options'] = $language_options;
            }
        }
    }

    /**
     * Register filters and action to modify how certain options are stored and retrieved in the options table
     */
    protected function register()
    {
        // multi language support
        // for restropress
        foreach (self::$optionKeys as $index => $option_key) {
            add_filter("pre_update_option_{$option_key}", [$this, 'updateOption'], 20, 3);
            add_filter("option_{$option_key}", [$this, 'getOption'], 10, 2);
        }

        add_action('init', [$this, 'setRestropressOptions'], 50);
    }


    /**
     * Check if the WPML plugin is active
     *
     * @return bool True if the WPML plugin is active, false otherwise
     */
    private function isWPMLActive()
    {
        return is_plugin_active('sitepress-multilingual-cms/sitepress.php');
    }

    /**
     * Get the current language code
     *
     * @return string Language code
     */
    private function getLanguageCode()
    {
        // set languages
        // default is turkish
        $lang = 'tr';
        if ($this->isWPMLActive()) {
            $lang = ICL_LANGUAGE_CODE;
        }

        return $lang;
    }

    /**
     * Get the option key for a translated option
     *
     * @param string $option Option name
     *
     * @return string Translated option key
     */
    private function getOptionKey($option)
    {
        $language_code = $this->getLanguageCode();

        return "{$option}-{$language_code}";
    }
}
