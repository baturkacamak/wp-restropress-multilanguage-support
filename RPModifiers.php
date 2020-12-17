<?php

class RPModifiers
{

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

    private function isWPMLActive()
    {
        return defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE;
    }

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

    private function getOptionKey($option)
    {
        $language_code = $this->getLanguageCode();

        return "{$option}-{$language_code}";
    }

    public function getOption($value, $option)
    {
        $option_language_key = $this->getOptionKey($option);

        if ($value_lang = get_option($option_language_key)) {
            $value = $value_lang;
        }

        return $value;
    }

    public function updateOption($value, $old_value, $option)
    {
        $option_key = $this->getOptionKey($option);

        update_option($option_key, $value);

        return $value;
    }

    public function setRestropressOptions()
    {
        if (isset($GLOBALS['rpress_options'])) {
            $option_key = $this->getOptionKey('rpress_settings');
            if ($language_options = get_option($option_key)) {
                $GLOBALS['rpress_options'] = $language_options;
            }
        }
    }

    protected function register()
    {
        // multi language support
        // for restropress
        {
            foreach (self::$optionKeys as $index => $option_key) {
                add_filter("pre_update_option_{$option_key}", [$this, 'updateOption'], 20, 3);
                add_filter("option_{$option_key}", [$this, 'getOption'], 10, 2);
            }
        }

        add_action('init', [$this, 'setRestropressOptions'], 50);
    }
}