<?php

namespace Drupal\ner_importer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Default controller of module
 *
 * @package Drupal\ner
 */
class MainController extends ControllerBase
{

    /**
     * NER importer home page.
     *
     * @return array
     */
    public function setup()
    {

        return [
            '#theme' => 'setup_page',
            '#links' => [
                (string)$this->t('NER import') => Url::fromRoute('ner_importer.import')
            ]
        ];
    }
}