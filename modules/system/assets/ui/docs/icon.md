# Icon library (Font Awesome 6)

Winter includes the [Font Awesome 6 Free](https://fontawesome.com/) icon set by default, allowing people to use over 1,500 free design icons and nearly 500 branding icons within the Backend or the CMS. We have also included compatibility with Font Awesome 4 classes for older plugins and themes.

For more information on the Font Awesome library, or to search through the available icons, visit the [Font Awesome website](https://fontawesome.com/search?m=free).

## Browsing the icons

You may browse the available icons on the Font Awesome website at the following URL:
https://fontawesome.com/search?m=free

Please note that we only include the free icons and brands.

## Using the icons

You may place icons just about anywhere by placing an inline tag (such as a `<i>` or `<span>` tag) and setting the class to the icon you wish to use:

```html
<i class="icon-camera-retro"></i> icon-camera-retro

<span class="icon-flag-checkered"></span> wn-icon-flag-checkered
```

<div class="frame" style="font-size: 22px;">
    <i class="icon-camera-retro"></i> icon-camera-retro
    <br />
    <span class="icon-flag-checkered"></span> wn-icon-flag-checkered
</div>

Using the `wn-` prefix will allow you to prefix content inside the given tag with an icon:

```html
<i class="wn-icon-star">You're a star!</i>

<strong class="wn-icon-snowflake">Winter is coming.</strong>
```

<div class="frame" style="font-size: 22px;">
    <i class="wn-icon-star">You're a star!</i>
    <br>
    <strong class="wn-icon-snowflake">Winter is coming.</strong>
</div>

You may also opt to use the standard Font Awesome classes as well:

```html
<i class="fas fa-star">You're a star!</i>

<strong class="fas fa-snowflake">Winter is coming.</strong>
```

<div class="frame" style="font-size: 22px;">
    <i class="fas fa-star">You're a star!</i>
    <br>
    <strong class="fas fa-snowflake">Winter is coming.</strong>
</div>

### Icon styles

As with Font Awesome 6, Winter also includes three styles of icon: solid, regular and brand. By default, the icons in Winter use the solid style, which has the full set of free icons available.

To use a regular style icon, which is less pronounced but also has much less available icons, you may include the `icon-regular` class alongside your icon class. For brands, you may include the `icon-brand` class.

```html
<i class="icon-star"></i> Solid

<i class="icon-regular icon-star"></i> Regular
```

<div class="frame" style="font-size: 22px;">
    <i class="icon-star"></i> Solid
    <br>
    <i class="icon-regular icon-star"></i> Regular
</div>

We also provide support for the Font Awesome style classes as well: `fas` for solid, `far` for regular and `fab` for brand.

### Icon sizes

Winter supports multiple sizing classes to control the size of the icons.

You may size by 1-10 times the regular size of icons.

```html
<i class="icon-camera-retro icon-10x"></i> icon-10x
<i class="icon-camera-retro icon-9x"></i> icon-9x
<i class="icon-camera-retro icon-8x"></i> icon-8x
<i class="icon-camera-retro icon-7x"></i> icon-7x
<i class="icon-camera-retro icon-6x"></i> icon-6x
<i class="icon-camera-retro icon-5x"></i> icon-5x
<i class="icon-camera-retro icon-4x"></i> icon-4x
<i class="icon-camera-retro icon-3x"></i> icon-3x
<i class="icon-camera-retro icon-2x"></i> icon-2x
<i class="icon-camera-retro icon-1x"></i> icon-1x
```

We also provide more classes that match common sizing prefixes in CSS frameworks such as `sm`, `lg`, etc.

```html
<i class="icon-camera-retro icon-2xs"></i> icon-2xs
<i class="icon-camera-retro icon-xs"></i> icon-xs
<i class="icon-camera-retro icon-sm"></i> icon-sm
<i class="icon-camera-retro icon-lg"></i> icon-lg
<i class="icon-camera-retro icon-xl"></i> icon-xl
<i class="icon-camera-retro icon-2xl"></i> icon-2xl
```

### Icon list items

You can apply icons to lists, allowing you to use the icon as the list item prefix as opposed to a standard circle.

You must include the `icon-ul` class to a `<ul>` tag, and then the `icon-li` class to all `<li>` items within to take advantage of this feature.

```html
<ul class="icon-ul">
    <li class="icon-li icon-battery-0">Empty</li>
    <li class="icon-li icon-battery">Low</li>
    <li class="icon-li icon-battery-half">Charging</li>
    <li class="icon-li icon-battery-full">Full</li>
</ul>
```

### Icon buttons

Feel free to use them alongside your buttons.

```html
<a class="btn btn-default wn-icon-refresh" href="#">
    Refresh
</a>
<a class="btn btn-success wn-icon-shopping" href="#">
    Checkout
</a>
<a class="btn btn-primary wn-icon-comment" href="#">
    Comment
</a>
<a class="btn btn-danger wn-icon-trash" href="#">
    Delete
</a>
<a class="btn btn-default wn-icon-cog" href="#">
    Settings
</a>
<a class="btn btn-info wn-icon-info" href="#">
    More Info
</a>
```
