# **Proposed Changes #2 - Winter v1.1.3**

## **Hard coded brand colors getting their associated variable name**

Replaced those colors with their corresponding variables

* #103141 -> @brand-primary
* #2da7c7 -> @brand-secondary
* #6cc551 -> @brand-accent
* #52a838 -> @brand-success
* #de8754 -> @brand-warning
* #e01346 -> @brand-danger

### **Here are all the variables getting @brand-primary as a value**

* @tooltip-bg                           (from modules\backend\formwidgets\richeditor\assets\less\richeditor.less)
* @color-datepicker-today-text          (from modules\system\assets\ui\less\datepicker.variables.less)
* @color-list-nav-arrow                 (from modules\system\assets\ui\less\list.variables.less)
* @tooltip-bg                           (from modules\system\assets\ui\less\tooltip.variables.less)

### **Here are all the variables getting @brand-secondary as a value**

* @color-breadcrumb-active-background   (from modules\system\assets\ui\less\breadcrumb.less)
* @color-balloon-control-hover-bg       (from modules\system\assets\ui\less\checkbox.balloon.less)
* @color-checkbox-checked               (from modules\system\assets\ui\less\checkbox.less)
* @color-filter-items-bg-hover          (from modules\system\assets\ui\less\filter.less)
* @link-color                           (from modules\system\assets\ui\less\global.variables.less)
* @color-list-active-border             (from modules\system\assets\ui\less\list.variables.less)
* @color-list-progress-bg               (from modules\system\assets\ui\less\list.variables.less)
* @color-select-active-bg               (from modules\system\assets\ui\less\select.variables.less)

Those files are  also modified to take into account this change (as the brand-secondary color was being used there as well)

* \modules\backend\formwidgets\fileupload\assets\less\fileupload.base.less

### **Here are all the variables getting @brand-accent as a value**

* @color-component-hover-bg             (from modules\cms\assets\less\winter.components.less)

### **Here are all the variables getting @brand-Success as a value**

* @color-checkbox-switch-on             (from modules\system\assets\ui\less\checkbox.less)

### **Here are all the variables getting @brand-info as a value**

* None

### **Here are all the variables getting @brand-warning as a value**

* @color-flash-warning-bg               (from \modules\system\assets\less\framework.extras.less)
* @color-flash-warning-bg               (from \modules\system\assets\ui\less\flashmessage.less)

### **Here are all the variables getting @brand-danger as a value**

* @color-popover-danger-bg              (from modules\system\assets\ui\less\popover.less)
* @color-checkbox-switch-off            (from modules\system\assets\ui\less\checkbox.less)
* @color-error-component-bg             (from modules\cms\assets\less\winter.components.less)
* @color-btn-dangers                    (modules\system\assets\ui\less\button.variables.less)

Those files are also modified to take into account this change (as they use the @brand-danger color to style some of their colors, backgrounds and background-color).

* \modules\system\assets\less\updates\updates.less
* \modules\backend\formwidgets\fileupload\assets\less\fileupload.base.less
* \modules\backend\behaviors\importexportcontroller\assets\less\import.less


# **Proposed Changes #3 - Winter v1.1.3**

## **Hard coded brand colors getting their associated variable name - New variables**

To match the new color branding, and help make sure the backend is customizable, new variables are added (although it doesn't work as intended). They will directly takes as values certain shades of the new branding, the way the previous theme was.
These variables takes in directly the value from @brand-primary, @brand-secondary and @brand-accent and apply a lighten and darken of 5%.

By default, this is equal to have: 
* @brand-primary-lighter  -> #184962
* @brand-primary-darker -> #081821
* @brand-secondary-lighter -> #48B9D5
* @brand-secondary-darker -> #227F96
* @brand-accent-lighter -> #8BD175
* @brand-accent-darker -> #52A838

The list of modifed files is as follow

### **Here are all the variables getting @brand-primary-lighter as a value**

None

### **Here are all the variables getting @brand-primary-darker as a value**

* @color-datepicker-hover-text          (from \modules\system\assets\ui\less\datepicker.variables.less)
* @color-fancy-master-tabs-bg           (from \modules\backend\assets\less\core\variables.less)


### **Here are all the variables getting @brand-secondary-lighter as a value**

None

Edit : @brand-info takes the same color as @brand-secondary-lighter, but is not changed in order to make sure it's editable easily.

### **Here are all the variables getting @brand-secondary-darker as a value**

* @color-fancy-master-tabs-bg           (from \modules\backend\assets\less\core\variables.less)
* @color-fancy-master-panel-bg          (from \modules\backend\assets\less\core\variables.less)
* @color-fancy-form-text-selection      (from \modules\backend\assets\less\core\variables.less)

This file is also modified to take into account this change (as they use the default @brand-secondary-darker color to style a background and a mixin).

* \modules\backend\assets\less\layout\sidepanel.less

### **Here are all the variables getting @brand-accent-lighter as a value**

None

### **Here are all the variables getting @brand-accent-darker as a value**

Edit : @brand-success takes the same color as @brand-accent-darker, but is not changed in order to make sure it's editable easily.

## **Changes to buttons

* Globally darkened all the button (and their associated states) by 5%  so that they directly match the color branding instead of adding yet an other shade. 
This change aim to make btn-primary have a background-color matching the new color branding, but to achieve parity, it was made true to all other buttons.

List of modified files is as follow 
* modules\system\assets\ui\less\button.base.less
* modules\system\assets\ui\less\button.mixins.less
* modules\system\assets\ui\less\button.variables.less