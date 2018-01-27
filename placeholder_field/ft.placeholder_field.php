<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * Class Placeholder_field_ft
 */
class Placeholder_field_ft extends EE_Fieldtype
{
    public function __construct()
    {
        /** @var \EE_Session $session */
        $session = ee()->session;

        $isCp = defined('REQ') && REQ === 'CP';

        if (! $isCp) {
            return;
        }

        if (! $isCp || $session->cache('placeholder_field', 'cpAssetsSet')) {
            return;
        }

        /** @var \Cp $cp */
        $cp = ee()->cp;

        $cssStr = file_get_contents(
            PATH_THIRD . 'placeholder_field/resources/style.css'
        );

        $cp->add_to_head("<style type='text/css'>{$cssStr}</style>");

        $session->set_cache('placeholder_field', 'cpAssetsSet', true);
    }

    /**
     * Required info for EE fieldtype
     *
     * @var array $info
     */
    public $info = array(
        'name' => PLACEHOLDER_FIELD_NAME,
        'version' => PLACEHOLDER_FIELD_VER
    );

    /**
     * Specifies what content types the field supports
     * @param $name
     * @return bool
     */
    public function accepts_content_type($name)
    {
        return in_array(
            $name,
            [
                'blocks/1',
                'channel',
                'grid',
                'fluid_field',
            ],
            true
        );
    }

    /**
     * Creates the common display settings
     * @param string $content
     * @return array
     */
    private function commonDisplaySettings($content = '')
    {
        return [
            [
                'title' => 'Content (markdown formatting)',
                'fields' => [
                    [
                        'type' => 'html',
                        'content' => '<textarea name="placeholder_field_content" rows="12">' .
                            $content .
                            '</textarea>',
                    ]
                ],
            ],
        ];
    }

    /**
     * Displays field type settings
     * @param $data
     * @return array
     */
    public function display_settings($data)
    {
        $content = isset($data['field_settings']['content']) ?
            $data['field_settings']['content'] :
            '';

        return [
            'field_options_placeholder_field' => [
                'label' => 'field_options',
                'group' => 'placeholder_field',
                'settings' => $this->commonDisplaySettings($content),
            ],
        ];
    }

    /**
     * Displays grid settings
     * @param $data
     * @return array
     */
    public function grid_display_settings($data)
    {
        $content = isset($data['content']) ? $data['content'] : '';

        return [
            'field_options' => $this->commonDisplaySettings($content),
        ];
    }

    /**
     * Saves the field type's settings
     * @param $data
     * @return array|mixed
     */
    public function save_settings($data)
    {
        return [
            'field_wide' => true,
            'content' => $data['placeholder_field_content'],
        ];
    }

    /**
     * Common function to display content
     * @param $content
     * @return string
     */
    private function displayFieldCommon($content = '')
    {
        ee()->load->library('typography');
        ee()->typography->initialize();

        /** @var \EE_Typography $typography */
        $typography = ee()->typography;

        $str = ['<div class="placeholder-field-wrapper">',
            $typography->markdown($content),
            '</div>',
        ];

        return implode('', $str);
    }

    /**
     * Displays the field
     * @param $data
     * @return string
     */
    public function display_field($data)
    {
        $content = isset($this->settings['field_settings']['content']) ?
            $this->settings['field_settings']['content'] :
            '';

        return $this->displayFieldCommon($content);
    }

    /**
     * Displays grid field
     * @param $data
     * @return string
     */
    public function grid_display_field($data)
    {
        $content = isset($this->settings['content']) ?
            $this->settings['content'] :
            '';

        return $this->displayFieldCommon($content);
    }

    /**
     * Required apparently
     * @param $data
     * @return string
     */
    public function save($data)
    {
        return 'placeholder_field';
    }
}
