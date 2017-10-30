<?php

namespace Drupal\datetimepicker_widget\Plugin\Field\FieldWidget;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *   id = "datetimepicker_widget",
 *   module = "datetimepicker_widget",
 *   label = @Translation("DateTimePicker"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class DateTimePickerWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'format' => 'd.m.Y H:i',
        'inline' => 0,
        'allow_times' => '',
        'mask' => '',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $default_value = isset($items[$delta]->value) ? $items[$delta]->value : '';
    $default_value_corrected = date($this->getSetting('format'), strtotime($default_value));

    $settings = $this->getSettings();
    // Mask
    if (empty($settings['mask'])) {
      $settings['mask'] = FALSE;
    }
    elseif ($settings['mask'] === 'auto') {
      $settings['mask'] = TRUE;
    }
    // Allowed times.
    if (!empty($settings['allow_times'])) {
      $settings['allow_times'] = explode(',', $settings['allow_times']);
    }

    // Convert all settings array keys to camelCase.
    foreach ($settings as $key => $value) {
      $new_key = lcfirst(implode('', array_map('ucfirst', explode('_', $key))));
      // If new key is the same, we do nothing.
      if ($new_key !== $key) {
        $settings[$new_key] = $value;
        unset($settings[$key]);
      }
    }

    $element += [
      '#type' => 'textfield',
      '#default_value' => $default_value_corrected,
      '#size' => 16,
      '#maxlength' => 16,
      '#attributes' => [
        'class' => ['datetimepicker-widget'],
        'data-datetimepicker-settings' => Json::encode($settings),
      ],
    ];

    $element['#attached']['library'][] = 'datetimepicker_widget/datetimepicker.widget';
    return ['value' => $element];
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $delta => &$value) {
      $value_from_form = $value['value'];
      $date = new DrupalDateTime($value_from_form);
      $date->setTimezone(new \DateTimezone(DATETIME_STORAGE_TIMEZONE));
      $value['value'] = $date->format(DATETIME_DATETIME_STORAGE_FORMAT);
    }
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    if ($this->getFieldSetting('datetime_type') == 'datetime') {
      $element['format'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Format'),
        '#required' => TRUE,
        '#default_value' => $this->getSetting('format'),
      ];

      $element['inline'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Inline'),
        '#desciption' => $this->t('If selected, widget will be showed inline and replace input field.'),
        '#default_value' => $this->getSetting('inline'),
      ];

      $element['allow_times'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Allowed times'),
        '#description' => $this->t('Allowed times to select. F.e. 12:00, 13:00. Split multiple values by comma. If not set, all times will be allowed.'),
        '#default_value' => $this->getSetting('allow_times'),
      ];

      $element['mask'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Mask'),
        '#description' => $this->t('Enable mask support for field. Leave it empty to leave disabled, enter \'auto\' to enable automatically mask, or enter exact mask which you want.'),
        '#default_value' => $this->getSetting('mask'),
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = t('Format: @format', ['@format' => $this->getSetting('format')]);

    if (!empty($this->getSetting('mask'))) {
      $summary[] = t('Mask: @mask', ['@mask' => $this->getSetting('mask')]);
    }

    if ($this->getSetting('inline')) {
      $summary[] = t('Inline');
    }

    return $summary;
  }

}