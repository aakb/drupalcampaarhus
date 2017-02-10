<?php
/**
 * @file
 * Contains Drupal\itkore_base\Form\ItkoreSettingsForm.
 */

namespace Drupal\itkore_views_settings\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;


/**
 * Class ContentEntityExampleSettingsForm.
 * @package Drupal\itkore_views_settings\Form
 * @ingroup itkore_base
 */
class ItkoreViewsSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itkore_views_settings';
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

    // Add news wrapper.
    $form['news_wrapper'] = array(
      '#title' => $this->t('News overview page settings'),
      '#type' => 'details',
      '#weight' => '1',
      '#open' => TRUE,
    );

    $form['news_wrapper']['news_title'] = array(
      '#title' => $this->t('News overview title'),
      '#type' => 'textfield',
      '#default_value' => $config->get('itkore_views.news_title'),
      '#weight' => '1',
      '#maxlength' => 256,
    );

    $fids = array();
    if (!empty($input)) {
      if (!empty($input['news_image'])) {
        $fids[0] = $form_state->getValue('news_image');
      }
    }
    else {
      $fids[0] = $config->get('itkore_views.news_image', '');
    }

    $form['news_wrapper']['news_image'] = array(
      '#title' => $this->t('News overview page image'),
      '#type' => 'managed_file',
      '#default_value' => ($fids[0]) ? $fids : '',
      '#upload_location' => 'public://',
      '#weight' => '4',
      '#open' => TRUE,
      '#description' => t('The image used at the top of the news overview page.'),
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
    $old_fid = $config->get('itkore_views.news_image', '');

    // Load the file set in the form.
    $value = $form_state->getValue('news_image');
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
      'itkore_views.news_title' => $form_state->getValue('news_title'),
      'itkore_views.news_image' => $file ? $file->id() : NULL
      )
    );
  }
}