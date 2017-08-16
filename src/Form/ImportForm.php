<?php

namespace Drupal\ner_importer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ner\ObjectEntity;
use Drupal\ner_importer\JsonImport;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implements NER importer form.
 *
 * @package Drupal\ner_importer\Form
 */
class ImportForm extends FormBase
{

    /**
     * @var JsonImport
     */
    private $nerJsonImport;

    /**
     * {@inheritdoc}
     */
    public function __construct(JsonImport $nerImport)
    {
        $this->nerJsonImport = $nerImport;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('ner_importer.json_import')
        );
    }


    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'ner_importer.import';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['source_import'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Import source'),
            '#required' => true,
            '#attributes' => [
                'placeholder' => 'JSON of object list.'
            ]
        ];
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Import'),
            '#button_type' => 'primary',
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        // Convert source_import: JSON string => Object || Array
        $form_state->setValue('source_import', \json_decode($form_state->getValue('source_import')));
        if (!(\is_object($form_state->getValue('source_import')) || \is_array($form_state->getValue('source_import'))))
            $form_state->setErrorByName('source_import', $this->t('Your import source is not valid.'));
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $sourceImportType = gettype($form_state->getValue('source_import'));
        $sourceImportIsSingleObject = $sourceImportType == 'object' && property_exists($form_state->getValue('source_import'), 'id');

        if ($sourceImportIsSingleObject) {

            $objectEntity = $this->nerJsonImport->objectEntityByJson($form_state->getValue('source_import'));
            $this->nerJsonImport->contentTypeByObjectEntity($objectEntity);
        } else {

            // source_import can be a map object OR array.
            $objectEntityList = $this->nerJsonImport->objectEntityListByJson($form_state->getValue('source_import'));
        }

        exit();
    }
}