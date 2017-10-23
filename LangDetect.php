<?php

/**
 * @package Language detector
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\langdetect;

use gplcart\core\Module;

/**
 * Main class for Language detector module
 */
class LangDetect extends Module
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Implements hook "route.list"
     * @param array $routes
     */
    public function hookRouteList(array &$routes)
    {
        $routes['admin/module/settings/langdetect'] = array(
            'access' => 'module_edit',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\langdetect\\controllers\\Settings', 'editSettings')
            )
        );
    }

    /**
     * Implements hook "language.set.before"
     * @param string $langcode
     * @return null
     */
    public function hookLanguageSetBefore($langcode)
    {
        $settings = $this->config->module('langdetect');

        if (empty($settings['redirect'])) {
            return null;
        }

        /* @var $session_helper \gplcart\core\helpers\Session */
        $session_helper = $this->getHelper('Session');
        $saved = $session_helper->get('langdetect');

        if (isset($saved)) {
            return null;
        }

        /* @var $request_helper \gplcart\core\helpers\Request */
        $request_helper = $this->getHelper('Request');
        $detected_langcode = $request_helper->language();

        if (!in_array($detected_langcode, $settings['redirect'])) {
            return null;
        }

        $language = $this->getLanguage()->get($detected_langcode);

        if (empty($language['status'])) {
            return null;
        }

        /* @var $url_helper \gplcart\core\helpers\Url */
        $url_helper = $this->getHelper('Url');
        $session_helper->set('langdetect', $detected_langcode);

        if ($detected_langcode !== $langcode) {
            $redirect = $url_helper->language($detected_langcode, $url_helper->path());
            $url_helper->redirect($redirect, array(), true, true);
        }

        return null;
    }

}
