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

namespace Drupal\ner_import\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\ner_import\ContentImport;
use Drupal\ner_import\JsonImport;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implements NER content import form.
 *
 * @package Drupal\ner_import\Form
 */
class ContentImportForm extends FormBase {

    /**
     * @var ContentImport
     */
    private $contentImport;

    /**
     * @var JsonImport
     */
    private $nerJsonImport;

    /**
     * {@inheritdoc}
     */
    public function __construct(ContentImport $contentImport, JsonImport $nerImport) {
        $this->contentImport = $contentImport;
        $this->nerJsonImport = $nerImport;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('ner_import.content_import'),
            $container->get('ner_import.json_import')
        );
    }


    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'ner_import.import_content';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['source_import'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Import source'),
            '#required' => true,
            '#attributes' => [
            ]
        ];
        $form['property_field_associative'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Property with field name, associative relation'),
            '#required' => true,
            '#attributes' => [
            ]
        ];
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Import content'),
            '#button_type' => 'primary',
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        // Convert source_import: JSON string => Object || Array
        $form_state->setValue('source_import', \json_decode($form_state->getValue('source_import')));
        if (!(\is_object($form_state->getValue('source_import')) || \is_array($form_state->getValue('source_import'))))
            $form_state->setErrorByName('source_import', $this->t('Your import source is not valid.'));
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {

        $sourceImportType = gettype($form_state->getValue('source_import'));
        $sourceImportIsSingleObject = $sourceImportType == 'object' && property_exists($form_state->getValue('source_import'), 'id');

        if ($sourceImportIsSingleObject) {
            $objectEntity = $this->nerJsonImport->objectEntityByJson($form_state->getValue('source_import'));
        } else {
            $objectEntityList = $this->nerJsonImport->objectEntityListByJson($form_state->getValue('source_import'));
        }

        $redirectUrl = new Url('ner_import.processed_content');
        $redirectUrl->setRouteParameters([
        ]);

        $form_state->setRedirectUrl($redirectUrl);
    }
}