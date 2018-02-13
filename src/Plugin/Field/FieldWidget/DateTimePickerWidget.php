<?php

namespace Drupal\datetimepicker_widget\Plugin\Field\FieldWidget;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Template\Attribute;

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
        'format_date' => NULL,
        'format_time' => NULL,
        'min_date' => NULL,
        'max_date' => NULL,
        'min_time' => NULL,
        'max_time' => NULL,
        'blur_on_focus' => FALSE,
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $default_value = $items[$delta]->isEmpty() ? $items[$delta]->date->getTimestamp() : \Drupal::service('datetime.time')
      ->getCurrentTime();
    $default_value_corrected = \Drupal::service('date.formatter')
      ->format($default_value, 'custom', $this->getSetting('format'));

    $settings = $this->getSettings();
    // Unset values which is not used directly by library.
    unset($settings['blur_on_focus']);
    // Why is we doing this manual conditions below? This is all because some
    // settings must be transformed or checked properly before they will be send
    // to JS. F.e. we need transform string to array for allowTimes or check
    // is value is set for min_date, where is 0 is also valid value and we can't
    // pass it trough empty().
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
    // Format date.
    if (strlen($settings['format_date']) == 0) {
      unset($settings['format_date']);
    }
    // Format time.
    if (strlen($settings['format_time']) == 0) {
      unset($settings['format_time']);
    }
    // Min date.
    if (strlen($settings['min_date']) == 0) {
      unset($settings['min_date']);
    }
    // Max date.
    if (strlen($settings['max_date']) == 0) {
      unset($settings['max_date']);
    }
    // Min time.
    if (strlen($settings['min_time']) == 0) {
      unset($settings['min_time']);
    }
    // Max time.
    if (strlen($settings['max_time']) == 0) {
      unset($settings['max_time']);
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

    $element_attributes = new Attribute();
    $element_attributes->addClass('datetimepicker-widget');
    $element_attributes->setAttribute('data-datetimepicker-settings', Json::encode($settings));
    if ($this->getSetting('blur_on_focus')) {
     $element_attributes->setAttribute('onfocus', 'blur();');
    }
    $element += [
      '#type' => 'textfield',
      '#default_value' => $default_value_corrected,
      '#size' => 16,
      '#maxlength' => 16,
      '#attributes' => $element_attributes->toArray(),
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
      $element['help'] = [
        '#markup' => $this->t('This settings are represents actual settings for library. For more information and examples refer to <a href="@href" target="_blank">documentation</a>.', [
          '@href' => 'https://xdsoft.net/jqplugins/datetimepicker/',
        ]),
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ];

      $element['blur_on_focus'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Blur on focus'),
        '#description' => $this->t("Prevent input to be focused. Useful if you don't won't allow to input value manually and prevent showing keyboard on mobile devices."),
        '#default_value' => $this->getSetting('blur_on_focus'),
      ];

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

      $element['format_date'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Format date'),
        '#description' => $this->t('Format date for minDate and maxDate. Default: Y/m/d.'),
        '#default_value' => $this->getSetting('format_date'),
      ];

      $element['format_time'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Format time'),
        '#description' => $this->t('Similarly, formatDate. But for minTime and maxTime. Default: H:i.'),
        '#default_value' => $this->getSetting('format_time'),
      ];

      $element['min_date'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Minimal date'),
        '#default_value' => $this->getSetting('min_date'),
      ];

      $element['max_date'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Maximum date'),
        '#default_value' => $this->getSetting('max_date'),
      ];

      $element['min_time'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Minimum time'),
        '#default_value' => $this->getSetting('min_time'),
      ];

      $element['max_time'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Maximum time'),
        '#default_value' => $this->getSetting('max_time'),
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
