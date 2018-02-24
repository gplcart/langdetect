<?php

/**
 * @package Language detector
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\langdetect;

use gplcart\core\Container;
use gplcart\core\helpers\Server;
use gplcart\core\helpers\Session;
use gplcart\core\helpers\Url;
use gplcart\core\Module;

/**
 * Main class for Language detector module
 */
class Main
{

    /**
     * URL helper class instance
     * @var \gplcart\core\helpers\Url $url
     */
    protected $url;

    /**
     * Module class instance
     * @var \gplcart\core\Module $module
     */
    protected $module;

    /**
     * Session helper class instance
     * @var \gplcart\core\helpers\Session $session
     */
    protected $session;

    /**
     * Server helper class instance
     * @var \gplcart\core\helpers\Server $server
     */
    protected $server;

    /**
     * Main constructor.
     * @param Module $module
     * @param Url $url
     * @param Server $server
     * @param Session $session
     */
    public function __construct(Module $module, Url $url, Server $server, Session $session)
    {
        $this->url = $url;
        $this->module = $module;
        $this->server = $server;
        $this->session = $session;
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
     * Implements hook "translation.set.before"
     * @param string $langcode
     */
    public function hookTranslationSetBefore($langcode)
    {
        $this->setLanguage($langcode);
    }

    /**
     * Sets detected language
     * @param $langcode
     * @return null
     */
    protected function setLanguage($langcode)
    {
        $settings = $this->module->getSettings('langdetect');

        if (empty($settings['redirect'])) {
            return null;
        }

        $saved = $this->session->get('langdetect');

        if (isset($saved)) {
            return null;
        }

        $detected_langcode = $this->server->httpLanguage();
        if (!in_array($detected_langcode, $settings['redirect'])) {
            return null;
        }

        $language = $this->getLanguageModel()->get($detected_langcode);

        if (empty($language['status'])) {
            return null;
        }

        $this->session->set('langdetect', $detected_langcode);

        if ($detected_langcode !== $langcode) {
            $redirect = $this->url->language($detected_langcode, $this->url->path());
            $this->url->redirect($redirect, array(), true, true);
        }

        return null;
    }

    /**
     * Language model class instance
     * @return \gplcart\core\models\Language
     */
    protected function getLanguageModel()
    {
        /** @var \gplcart\core\models\Language $instance */
        $instance = Container::get('gplcart\\core\\models\\Language');
        return $instance;
    }

}
