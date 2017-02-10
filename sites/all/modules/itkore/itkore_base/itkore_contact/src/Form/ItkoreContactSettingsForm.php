<?php
/**
 * @file
 * Contains Drupal\itkore_base\Form\ItkoreSettingsForm.
 */

namespace Drupal\itkore_contact\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;


/**
 * Class ContentEntityExampleSettingsForm.
 * @package Drupal\itkore_contact\Form
 * @ingroup itkore_base
 */
class ItkoreContactSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itkore_contact_settings';
  }

  /**
   * Get key/value storage for base config.
   *
   * @return object
   */
  private function getBaseConfig() {
    return \Drupal::getContainer()->get('itkore_base.itkore_config');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->getBaseConfig();

    // Add contact wrapper.
    $form['contact_wrapper'] = array(
      '#title' => $this->t('Contact settings'),
      '#type' => 'details',
      '#weight' => '1',
      '#open' => TRUE,
    );

    $form['contact_wrapper']['contact_title'] = array(
      '#title' => $this->t('Contact title'),
      '#type' => 'textfield',
      '#default_value' => $config->get('itkore_contact.contact_title'),
      '#weight' => '1',
      '#maxlength' => 256,
    );

    $form['contact_wrapper']['contact_lead'] = array(
      '#title' => $this->t('Contact lead text'),
      '#type' => 'textfield',
      '#default_value' => $config->get('itkore_contact.contact_lead'),
      '#weight' => '2',
      '#maxlength' => 256,
    );

    $form['contact_wrapper']['contact_text'] = array(
      '#title' => $this->t('Contact text'),
      '#type' => 'text_format',
      '#format' => 'filtered_html',
      '#default_value' => $config->get('itkore_contact.contact_text'),
      '#weight' => '3',
    );

    $fids = array();
    if (!empty($input)) {
      if (!empty($input['contact_image'])) {
        $fids[0] = $form_state->getValue('contact_image');
      }
    }
    else {
      $fids[0] = $config->get('itkore_contact.contact_image', '');
    }

    $form['contact_wrapper']['contact_image'] = array(
      '#title' => $this->t('Contact image'),
      '#type' => 'managed_file',
      '#default_value' => ($fids[0]) ? $fids : '',
      '#upload_location' => 'public://',
      '#weight' => '4',
      '#open' => TRUE,
      '#description' => t('The image used at the top of the contact page.'),
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save changes'),
      '#weight' => '6',
    );


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message('Settings saved');

    // Fetch the file id previously saved.
    $config = $this->getBaseConfig();
    $old_fid = $config->get('itkore_contact.contact_image', '');

    // Load the file set in the form.
    $value = $form_state->getValue('contact_image');
    $form_fid = count($value) > 0 ? $value[0] : 0;
    $file = ($form_fid) ? File::load($form_fid) : FALSE;

    // If a file is set.
    if ($file) {
      $fid = $file->id();
      // Check if the file has changed.
      if ($fid != $old_fid) {

        // Remove old file.
        if ($old_fid) {
          removeFile($old_fid);
        }

        // Add file to file_usage table.
        \Drupal::service('file.usage')->add($file, 'itkore_base', 'user', '1');
      }
    }
    else {
      // If old file exists but no file set in form, remove old file.
      if ($old_fid) {
        removeFile($old_fid);
      }
    }

    $this->getBaseConfig()->setMultiple(array(
      'itkore_contact.contact_title' => $form_state->getValue('contact_title'),
      'itkore_contact.contact_lead' => $form_state->getValue('contact_lead'),
      'itkore_contact.contact_text' => $form_state->getValue('contact_text')['value'],
      'itkore_contact.contact_image' => $file ? $file->id() : NULL
      )
    );
  }
}