<?php

namespace Drupal\ner_importer;

use Drupal\ner\ObjectEntity;

/**
 * Import NER processed data.
 *
 * @package Drupal\ner_import
 */
class Import
{

    /**
     * @var ObjectEntity[]
     */
    protected $objectEntityList;

    /**
     * JSON object with client import data.
     *
     * @var object|array
     */
    protected $jsonSource;

}