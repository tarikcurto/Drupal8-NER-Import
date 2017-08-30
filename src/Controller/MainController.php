<?php
/**
 * DRUPAL 8 NER importer.
 * Copyright (C) 2017. Tarik Curto <centro.tarik@live.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 */

namespace Drupal\ner_import\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Default controller of module
 *
 * @package Drupal\ner
 */
class MainController extends ControllerBase {

    /**
     * NER importer home page.
     *
     * @return array
     */
    public function setup() {

        return [
            '#theme' => 'setup_page',
            '#links' => [
                (string)$this->t('Import structure') => Url::fromRoute('ner_import.import_structure'),
                (string)$this->t('Import content') => Url::fromRoute('ner_import.import_content')
            ]
        ];
    }

    /**
     * Processed data when client execute
     * any structure import process.
     *
     * @return array
     */
    public function structureProcessed(){

        return [
            '#theme' => 'structure_processed_page',
            '#compressed_module_url' => \Drupal::request()->query->get('compressed_module_url'),
            '#property_field_map' => \Drupal::request()->query->get('property_field_map')
        ];
    }

    /**
     * Processed data when client execute
     * any data import process.
     *
     * @return array
     */
    public function contentProcessed(){

        return [

        ];
    }
}