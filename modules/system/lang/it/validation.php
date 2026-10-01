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

    "accepted"         => ":attribute deve essere accettato.",
    "active_url"       => ":attribute non è un URL valido.",
    "after"            => ":attribute deve essere una data maggiore di :date.",
    "after_or_equal"   => ":attribute deve essere una data maggiore o uguale a :date.",
    "alpha"            => ":attribute può contenere solo lettere.",
    "alpha_dash"       => ":attribute può contenere solo lettere, numeri e trattini.",
    "alpha_num"        => ":attribute può contenere solo lettere e numeri.",
    "array"            => ":attribute deve essere un array.",
    "before"           => ":attribute deve essere una data minore di :date.",
    "before_or_equal"  => ":attribute deve essere una data minore o uguale a :date.",
    "between"          => [
        "numeric" => ":attribute deve essere compreso tra :min e :max.",
        "file"    => ":attribute deve essere compreso tra :min e :max kilobytes.",
        "string"  => ":attribute deve essere compreso tra :min e :max caratteri.",
        "array"   => ":attribute deve avere tra :min e :max elementi.",
    ],
    "boolean"          => "Il campo :attribute deve essere vero o falso.",
    "confirmed"        => "La conferma :attribute non corrisponde.",
    "date"             => ":attribute non è una data valida.",
    "date_equals"      => ":attribute deve essere una data uguale a :date.",
    "date_format"      => ":attribute non corrisponde al formato :format.",
    "different"        => ":attribute e :other devono essere diversi.",
    "digits"           => ":attribute deve essere di :digits cifre.",
    "digits_between"   => ":attribute deve essere tra :min e :max cifre.",
    'dimensions'       => ':attribute ha delle dimensioni non valide.',
    'distinct'         => ':attribute ha un valore duplicato.',
    "email"            => "Il formato di :attribute non è valido.",
    "ends_with"        => ":attribute deve terminare con uno dei seguenti valori: :values.",
    "exists"           => "Il valore di :attribute non è valido.",
    "file"             => ":attribute deve essere un file.",
    "filled"           => "Il campo :attribute deve contenere un valore.",
    "gt"               => [
        "numeric" => ":attribute deve essere maggiore di :value.",
        "file"    => ":attribute deve essere maggiore di :value kilobytes.",
        "string"  => ":attribute deve essere maggiore di :value caratteri.",
        "array"   => ":attribute deve avere più di :value elementi.",
    ],
    "gte"              => [
        "numeric" => ":attribute deve essere maggiore o uguale a :value.",
        "file"    => ":attribute deve essere maggiore o uguale a :value kilobytes.",
        "string"  => ":attribute deve essere maggiore o uguale a :value caratteri.",
        "array"   => ":attribute deve avere :value elementi o più.",
    ],
    "image"            => ":attribute deve essere un'immagine.",
    "in"               => "Il valore di :attribute non è valido.",
    "in_array"         => "Il campo :attribute non esiste in :other.",
    "integer"          => ":attribute deve essere un numero intero.",
    "ip"               => ":attribute deve essere un indirizzo IP valido.",
    "ipv4"             => ":attribute deve essere un indirizzo IPv4 valido.",
    "ipv6"             => ":attribute deve essere un indirizzo IPv6 valido.",
    "json"             => ":attribute deve essere una stringa JSON valida.",
    "lt"               => [
        "numeric" => ":attribute deve essere minore di :value.",
        "file"    => ":attribute deve essere minore di :value kilobytes.",
        "string"  => ":attribute deve essere minore di :value caratteri.",
        "array"   => ":attribute deve avere meno di :value elementi.",
    ],
    "lte"              => [
        "numeric" => ":attribute deve essere minore o uguale a :value.",
        "file"    => ":attribute deve essere minore o uguale a :value kilobytes.",
        "string"  => ":attribute deve essere minore o uguale a :value caratteri.",
        "array"   => ":attribute non deve avere più di :value elementi.",
    ],
    "max"              => [
        "numeric" => ":attribute non può essere maggiore di :max.",
        "file"    => ":attribute non può essere maggiore di :max kilobytes.",
        "string"  => ":attribute non può essere maggiore di :max caratteri.",
        "array"   => ":attribute non può avere più di :max elementi.",
    ],
    "mimes"            => ":attribute deve essere un file di tipo: :values.",
    "mimetypes"        => ":attribute deve essere un file di tipo: :values.",
    "extensions"       => ":attribute deve avere un estensione: :values.",
    "min"              => [
        "numeric" => ":attribute deve essere almeno :min.",
        "file"    => ":attribute deve essere almeno :min kilobytes.",
        "string"  => ":attribute deve essere almeno :min caratteri.",
        "array"   => ":attribute deve avere almeno :min elementi.",
    ],
    "not_in"           => "Il valore di :attribute non è valido.",
    "not_regex"        => "Il formato di :attribute non è valido.",
    "numeric"          => ":attribute deve essere un numero.",
    "present"          => "Il campo :attribute deve essere presente.",
    "regex"            => "Il formato di :attribute non è valido.",
    "required"         => "Il campo :attribute è obbligatorio.",
    "required_if"      => "Il campo :attribute è obbligatorio quando :other è :value.",
    "required_unless"  => "Il campo :attribute è obbligatorio a meno che :other sia in :values.",
    "required_with"    => "Il campo :attribute è obbligatorio quando :values è presente.",
    "required_with_all" => "Il campo :attribute è obbligatorio quando :values è presente.",
    "required_without" => "Il campo :attribute è obbligatorio quando :values non è presente.",
    "required_without_all" => "Il campo :attribute è obbligatorio quando nessuno dei :values è presente.",
    "same"             => ":attribute e :other devono corrispondere.",
    "size"             => [
        "numeric" => ":attribute deve essere :size.",
        "file"    => ":attribute deve essere :size kilobytes.",
        "string"  => ":attribute deve essere :size caratteri.",
        "array"   => ":attribute deve contenere :size elementi.",
    ],
    "starts_with"      => ":attribute deve iniziare con uno dei seguenti valori: :values.",
    "string"           => ":attribute deve essere una stringa.",
    "timezone"         => ":attribute deve essere un fuso orario valido.",
    "unique"           => ":attribute è già presente.",
    "uploaded"         => "Il caricamento di :attribute non è riuscito.",
    "url"              => "Il formato di :attribute non è valido.",
    "uuid"             => ":attribute deve essere un UUID valido.",

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
