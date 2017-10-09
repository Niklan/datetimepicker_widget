/**
 * @file
 * DateTimePicker widget behaviors.
 */

(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.datetimepicker_widget = {
    attach: function (context, settings) {
      let $datetimepicker = $(context).find('.datetimepicker-widget').once('datetimepicker-widget');

      if ($datetimepicker.length) {
        $datetimepicker.each(function (i, element) {
          let $element = $(element);
          let elementSettings = $element.data('datetimepicker-settings');
          elementSettings.dayOfWeekStart = 1;
          // Add current language.
          elementSettings.lang = settings.path.currentLanguage;
          // Months localizations.
          let monthsOptions = {};
          monthsOptions.context = 'Full month name in the singular';
          let months = [
            Drupal.t('January', {}, monthsOptions),
            Drupal.t('February', {}, monthsOptions),
            Drupal.t('March', {}, monthsOptions),
            Drupal.t('April', {}, monthsOptions),
            Drupal.t('May', {}, monthsOptions),
            Drupal.t('June', {}, monthsOptions),
            Drupal.t('July', {}, monthsOptions),
            Drupal.t('August', {}, monthsOptions),
            Drupal.t('September', {}, monthsOptions),
            Drupal.t('October', {}, monthsOptions),
            Drupal.t('November', {}, monthsOptions),
            Drupal.t('December', {}, monthsOptions),
          ];

          // Short day of the weeks localization.
          let dayOfWeek = [
            Drupal.t('Mon'),
            Drupal.t('Tue'),
            Drupal.t('Wed'),
            Drupal.t('Thu'),
            Drupal.t('Fri'),
            Drupal.t('Sat'),
            Drupal.t('Sun'),
          ];

          // Add i18n settings.
          $.datetimepicker.setLocale(elementSettings.lang);
          elementSettings.i18n = {
            [elementSettings.lang]: {
              months: months,
              dayOfWeek: dayOfWeek,
            }
          };

          $(element).datetimepicker(elementSettings);
        });
      }
    }
  };

})(jQuery, Drupal);
