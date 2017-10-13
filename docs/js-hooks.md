# JS Hooks

JS hook system allow you to modify settings in JS for every called widget, write own callbacks, change values, add new or completely rewrite them based on your logic.

We can't move all settings to administrative interface, for example, all calbacks methods for jQuery plugin can't be writted in widget settings, but you can face the problemn when you need this behavior, and this hook system can help you.

For using this approach, you need to create your own JS file in module or theme, define this JS file as library in coressponding .libraries.yml file and attach when and where you want.

All you need to in JS file, is create special behavior which gets two arguments:

- `options`: The object with widget options, here will be all generated options by current module JS according on administrative settings and other behaviors.
- `element`: The element for which this options are. 

This method must return new or modified options object.


## Example 1

```js
/**
 * First example.
 *
 * Just modify options what you needs and let other still the same.
 */
Drupal.behaviors.myBehavior.dateTimePickerWidget = function(options, element) {
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
```

## Example 2

```js
/**
 * Second example.
 *
 * This example fully controls options on it's own.
 */
Drupal.behaviors.myBehavior.dateTimePickerWidget = function(options, element) {
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
```
