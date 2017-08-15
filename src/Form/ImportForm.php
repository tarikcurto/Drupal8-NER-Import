<?php

namespace Drupal\ner_importer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements NER importer form.
 *
 * @package Drupal\ner_importer\Form
 */
class ImportForm extends FormBase
{

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
        $form['json_import_object'] = [
            '#type' => 'textarea',
            '#title' => $this->t('JSON import object'),
        ];
        /*$form['rgx_capture'] = [
            '#type' => 'textarea',
            '#title' => $this->t('JSON import object'),
        ];*/
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

        if (!\is_object(\json_decode($form_state->getValue('json_import_object'))))
            $form_state->setErrorByName('json_import_object', $this->t('Your JSON import object is not valid.'));
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

    }
}