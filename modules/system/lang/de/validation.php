<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted'             => ':attribute muss bestätigt werden.',
    'active_url'           => ':attribute ist keine gültige URL.',
    'after'                => ':attribute muss ein Datum nach :date sein.',
    'after_or_equal'       => ':attribute muss ein Datum nach oder gleich :date sein.',
    'alpha'                => ':attribute darf nur Buchstaben enthalten.',
    'alpha_dash'           => ':attribute darf nur Buchstaben, Ziffern und Bindestriche enthalten.',
    'alpha_num'            => ':attribute darf nur Buchstaben und Ziffern enthalten.',
    'array'                => ':attribute muss ein Array sein.',
    'before'               => ':attribute muss ein Datum vor :date sein.',
    'before_or_equal'      => ':attribute muss ein Datum vor oder gleich :date sein.',
    'between'              => [
        'numeric' => ':attribute muss zwischen :min und :max liegen.',
        'file'    => ':attribute muss zwischen :min und :max Kilobytes groß sein.',
        'string'  => ':attribute muss eine Zeichenanzahl zwischen :min und :max haben.',
        'array'   => ':attribute muss eine Elementanzahl zwischen :min und :max haben.',
    ],
    'boolean'              => ':attribute muss true oder false sein.',
    'confirmed'            => 'Bestätigung zu :attribute stimmt nicht überein',
    'date'                 => ':attribute ist kein gültiges Datum.',
    'date_equals'          => ':attribute muss gleich :date sein.',
    'date_format'          => ':attribute entspricht nicht dem gültiges Datumsformat :format.',
    'different'            => ':attribute und :other müssen sich unterscheiden.',
    'digits'               => ':attribute benötigt :digits Ziffern.',
    'digits_between'       => ':attribute muss eine Zeichenanzahl zwischen :min und :max haben.',
    'dimensions'           => ':attribute has invalid image dimensions.',
    'distinct'             => ':attribute enthält einen doppelten Wert.',
    'email'                => 'Das Format von :attribute ist ungültig.',
    'ends_with'            => ':attribute muss mit einem der folgenden Werte enden: :values.',
    'exists'               => 'Das ausgewählte Attribut :attribute ist ungültig.',
    'file'                 => ':attribute muss eine Datei sein.',
    'filled'               => ':attribute muss einen Wert haben.',
    'gt'                   => [
        'numeric' => ':attribute muss größer sein als :value.',
        'file'    => ':attribute muss größer sein als :value Kilobytes.',
        'string'  => ':attribute muss mehr als :value Zeichen haben.',
        'array'   => ':attribute muss mehr als :value Elemente haben.',
    ],
    'gte'                  => [
        'numeric' => ':attribute muss mindestens :value sein.',
        'file'    => ':attribute muss mindestens :value Kilobytes groß sein.',
        'string'  => ':attribute muss mindestens :value Zeichen haben.',
        'array'   => ':attribute muss mindestens :value Elemente haben.',
    ],
    'image'                => ':attribute muss ein Bild sein.',
    'in'                   => 'Das ausgewählte Attribut :attribute ist ungültig.',
    'in_array'             => ':attribute existiert nicht in :other.',
    'integer'              => ':attribute muss eine Ganzzahl (integer) sein.',
    'ip'                   => ':attribute muss eine gültige IP-Adresse sein.',
    'ipv4'                 => ':attribute muss eine gültige IPv4-Adresse sein.',
    'ipv6'                 => ':attribute muss eine gültige IPv6-Adresse sein.',
    'json'                 => ':attribute muss ein gültiger JSON-String sein.',
    'lt'                   => [
        'numeric' => ':attribute muss kleiner sein als :value.',
        'file'    => ':attribute muss kleiner sein als :value Kilobytes.',
        'string'  => ':attribute muss weniger als :value Zeichen haben.',
        'array'   => ':attribute muss weniger als :value Elemente haben.',
    ],
    'lte'                  => [
        'numeric' => ':attribute darf maximal :value sein.',
        'file'    => ':attribute darf maximal :value Kilobytes groß sein.',
        'string'  => ':attribute darf maximal :value Zeichen haben.',
        'array'   => ':attribute darf maximal :value Elemente haben.',
    ],
    'max'                  => [
        'numeric' => ':attribute darf nicht größer als :max sein.',
        'file'    => ':attribute darf nicht größer als :max Kilobytes sein.',
        'string'  => ':attribute darf nicht mehr als :max Zeichen haben.',
        'array'   => ':attribute darf nicht mehr als :max Elemente haben.',
    ],
    'mimes'                => ':attribute muss eine Datei des Typs: :values sein.',
    'mimetypes'            => ':attribute muss eine Datei des Typs: :values sein.',
    'min'              => [
        'numeric' => ':attribute muss mindestens :min sein.',
        'file'    => ':attribute darf nicht kleiner als :min Kilobytes sein.',
        'string'  => ':attribute darf nicht weniger als :min Zeichen haben.',
        'array'   => ':attribute darf nicht weniger als :min Elemente haben.',
    ],
    'not_in'               => 'Das ausgewählte Attribut :attribute ist ungültig.',
    'not_regex'            => 'Das Format von :attribute ist ungültig.',
    'numeric'              => ':attribute muss eine Zahl sein.',
    'present'              => ':attribute muss gegeben sein.',
    'regex'                => 'Das Format von :attribute ist ungültig.',
    'required'             => ':attribute wird benötigt.',
    'required_if'          => ':attribute wird benötigt, wenn :other den Wert :value hat.',
    'required_unless'      => ':attribute wird benötigt, wenn nicht :other den Wert :values hat.',
    'required_with'        => ':attribute wird benötigt, wenn :values existiert.',
    'required_with_all'    => ':attribute wird benötigt, wenn :values existiert.',
    'required_without'     => ':attribute wird benötigt, wenn :values nicht existiert.',
    'required_without_all' => ':attribute wird benötigt, wenn keine der Werte :values existieren.',
    'same'                 => ':attribute und :other müssen übereinstimmen.',
    'size'                 => [
        'numeric' => ':attribute muss :size groß sein.',
        'file'    => ':attribute muss :size Kilobytes groß sein.',
        'string'  => ':attribute muss :size Zeichen haben.',
        'array'   => ':attribute muss :size Elemente haben.',
    ],
    'starts_with'          => ':attribute muss mit einem der folgenden Werte beginnen: :values.',
    'string'               => ':attribute muss eine Zeichenkette (String) sein.',
    'timezone'             => ':attribute muss eine gültige Zeitzone sein.',
    'unique'               => ':attribute muss eindeutig sein.',
    'uploaded'             => ':attribute konnte nicht hochgeladen werden.',
    'url'                  => 'Format von :attribute ist ungültig.',
    'uuid'                 => ':attribute muss eine gültige UUID sein.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
