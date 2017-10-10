/**
 * @file
 * This file is example of how to alter jQuery plugin options.
 *
 * It's not attached anywhere, you can just copy-paste it for your needs.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * This behaviour is not necessary, just as boilerplate.
   */
  Drupal.behaviors.myBehaviour = {
    attach: function (context, settings) {

    }
  };

  /**
   * First example.
   *
   * Just modify options what you needs and let other still the same.
   */
  Drupal.behaviors.myBehaviour.dateTimePickerWidget = function(options, element) {
    // Change default format.
    options.format = 'd.m.Y H:i';
    // This condition allows us to apply this settings only when the datetime
    // field element is in specified Drupal contact form.
    if ($('.contact-message-apply-to-service-form').has(element).length) {
      options.inline = true;
    }
    // Don't forget to return new options!
    return options;
  };

  /**
   * Second example.
   *
   * This example fully controls options on it's own.
   */
  Drupal.behaviors.myBehaviour.dateTimePickerWidget = function(options, element) {
    return {
      theme: 'dark',
      minDate: 0,
      disabledWeekDays: [0, 6],
      timepicker: false,
      onChangeDateTime: function(dp, $input) {
        console.log($input.val());
      }
    };
  };

})(jQuery, Drupal);

