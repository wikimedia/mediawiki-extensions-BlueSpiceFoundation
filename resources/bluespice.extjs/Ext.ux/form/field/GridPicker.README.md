GridPicker
==========

Compatible with: *`ExtJS 4.2.1`*


This [`GridPicker`][0] component is a reimplementation of Rixo's [`GridPicker`][1] that replaces the default boundlist view in [`ComboBox`][2] with [`GridPanel`][3].

Too Remove all workarounds/fixes for layout issues that comes with gridpicker. By:
- Creating grid with different config.
- Implementing different alignPicker method


And Some other changes like:

+ Fire select event on selection by keyNav
+ scroll to highlighted item on query

TODO:

will be working on multiSelect support.

GridPickerSearch
================


This [`GridPickerSearch`][4] component extends this [`GridPicker`][0] to make it into searching combo.
It uses clear trigger and only expands picker on query.


Demo
---

- [GridPicker Examples][5]
 
- [GridPickerSearch Example][6]

  [0]: https://github.com/yogeshpandey009/GridPicker-Search-4.2.1/blob/master/ux/form/field/GridPicker.js
  [1]: https://github.com/rixo/GridPicker/
  [2]: http://docs.sencha.com/extjs/4.2.1/#!/api/Ext.form.field.ComboBox
  [3]: http://docs.sencha.com/extjs/4.2.1/#!/api/Ext.grid.Panel
  [4]: https://github.com/yogeshpandey009/GridPicker-Search-4.2.1/blob/master/ux/form/field/GridPickerSearch.js
  [5]: https://fiddle.sencha.com/#fiddle/4j9
  [6]: https://fiddle.sencha.com/#fiddle/4jb
