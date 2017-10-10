# DateTimePicker Drupal field widget

This module provide new widget type `DateTimePicker` for datetime field type.
The widget use [DateTimePicker jQuery plugin](https://github.com/xdan/datetimepicker) and allow you to configure it on admin page.

Currently it's has limits:

- Only for Drupal 8
- Works only with date and time fields. So if you need yo use it only for time or for date, I can't guaranty this will work. I'm not tested.
- A lot of options is not available in admin page, I added just most important. This can be extended easily, just fork and then PR the changes, or ask me in issues, if I have time, I will extend it. For now module wrote just for special case and I don't know, need the other people it or not, because this is not disadvantage to me extend it by needs.

## Installation

1. Download and put it into your /modules/custom/ folder.
2. Download jQuery plugin from [link](https://github.com/xdan/datetimepicker/releases).
3. Extract jQuery archive to /libraries/datetimepicker so js file must be accessed at /libraries/datetimepicker/build/jquery.datetimepicker.full.min.js. And don't forget about stylesheet.
4. Use it!

## JS hook system

The admin configurations is limited for many reasons, maybe it will extended in the future, but anyway, it can't let you get control over all plugin functionality. So thery obvious, we can't let write JS callbacks in admin settings, this is madness. For this and other cases is created "hook" system based on behaviours.

This approach let you fully control options for each individual datetimepicker instance on the page. Only thing you need to do, it's create your own JS file, define it as Drupal library using .libraries.yml file and attach on pages where you need this.

To hook you need to write special behavior:

```js
Drupal.behaviors.myBehaviour.dateTimePickerWidget = function(options, element) {
  return options;
};
```
This behavior must return modified or completely new options object.

For examples see [datetimepicker-alter.example.js](assets/js/datetimepicker-alter.example.js) file.

## Some screenshots

![](https://i.imgur.com/gzBVeJL.png)

![](https://i.imgur.com/53rROMK.png)
