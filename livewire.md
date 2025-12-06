## Livewire / Winter CMS Integration Notes:

- Livewire components can be one of three types:
    - Class based components
        - class lives in $domain/livewire/$component.php
        - view lives in $domain/views/livewire/component.blade.php
        - JS lives in ?
    - MFC based components
        - class lives in $domain/livewire/$component/$component.php
        - view lives in $domain/livewire/$component/$component.blade.php
        - JS lives in ?
    - SFC based components
        - class, view, & JS live in $domain/livewire/$component.blade.php

- LivewireManager -> Livewire::addNamespace() or addLocation() to register detection for the SFC & MFC types, addComponent for the class based type
objective is to have the following namespace:
- <livewire:system::counter />
- <livewire:winter.demo::counter/>
- <livewire:theme-demo::counter/>

- Commands:
    - `livewire:attribute` - Creates a class extending Livewire\Attribute, relies on $app->getNamespace();
    - `livewire:convert` Convert a livewire component between single-file and multi-file formats
    - `livewire:form` - Creates a class extending Livewire\Form, relies on $app->getNamespace();
    - `livewire:layout` - Creates a new app layout file /views/layouts/app.blade.php
        - Ask about use
    - `livewire:publish` - Publishes Livewire's config & view files
    - `livewire:stubs` - Publishes livewire's stub files to stubs directory, investigate further
    - `livewire:upgrade` - Remove
    - `make:livewire` - Creates a Livewire component (class, mfc, or sfc depending on options / default config set)
        - Refactor into `create:livewire $domain $component [--sfc|--mfc]` for Winter CMS context