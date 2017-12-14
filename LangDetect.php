<?php

/**
 * @package Language detector
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\langdetect;

use gplcart\core\Module,
    gplcart\core\Container;
use gplcart\core\helpers\Url as UrlHelper,
    gplcart\core\helpers\Server as ServerHelper,
    gplcart\core\helpers\Session as SessionHelper;

/**
 * Main class for Language detector module
 */
class LangDetect
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
     * @param Module $module
     * @param UrlHelper $url
     * @param ServerHelper $server
     * @param SessionHelper $session
     */
    public function __construct(Module $module, UrlHelper $url, ServerHelper $server,
            SessionHelper $session)
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
        $this->setDetectedLanguage($langcode);
    }

    /**
     * @param $langcode
     * @return null
     */
    protected function setDetectedLanguage($langcode)
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

        $language = $this->getLanguage()->get($detected_langcode);

        if (empty($language['status'])) {
            return null;
        }

        $this->session->set('langdetect', $detected_langcode);

        if ($detected_langcode !== $langcode) {
            $redirect = $this->url->language($detected_langcode, $this->url->path());
            $this->url->redirect($redirect, array(), true, true);
        }
    }

    /**
     * Language model class instance
     * @return \gplcart\core\models\Language
     */
    protected function getLanguage()
    {
        return Container::get('gplcart\\core\\models\\Language');
    }

}
