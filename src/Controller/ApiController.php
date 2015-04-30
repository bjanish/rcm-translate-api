<?php

namespace Reliv\RcmTranslateApi\Controller;

use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * Class ApiController
 *
 * LongDescHere
 *
 * PHP version 5
 *
 * @category  Reliv
 * @package   moduleNameHere
 * @author    James Jervis <jjervis@relivinc.com>
 * @copyright 2015 Reliv International
 * @license   License.txt New BSD License
 * @version   Release: <package_version>
 * @link      https://github.com/reliv
 */
class ApiController extends AbstractRestfulController
{

    /**
     * getCurrentSite
     *
     * @return \Rcm\Entity\Site
     */
    protected function getCurrentSite()
    {
        return $this->serviceLocator->get('Rcm\Service\CurrentSite');
    }

    /**
     * getTranslator
     *
     * @return Translator
     */
    protected function getTranslator()
    {
        return $this->serviceLocator->get('MvcTranslator');
    }

    /**
     * Return list of resources
     *
     * @return mixed
     */
    public function getList()
    {
        $translator = $this->getTranslator();

        // @todo Might be a better way to prevent spamming
        // We ignore events so we don't get spammed.
        $translator->disableEventManager();

        $site = $this->getCurrentSite();
        $locale = $site->getLocale();

        $trimfilter = new StringTrim();
        $stripTagsFilter = new StripTags();

        $translationParams = $this->params()->fromQuery();

        $translationKeys = array_keys($translationParams);

        $translations = [];

        foreach ($translationKeys as $message) {

            $message = (string)$message;
            // Clean
            $message = $stripTagsFilter->filter($message);
            $message = $trimfilter->filter($message);

            $translations[$message] = $translator->translate($message, 'default', $locale);
        }

        return new JsonModel($translations);
    }

}