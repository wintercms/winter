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

    "accepted"         => "El campo :attribute debe ser aceptado.",
    "active_url"       => "El campo :attribute no es una URL válida",
    "after"            => "El campo :attribute debe ser una fecha posterior a :date.",
    "after_or_equal"   => "El campo :attribute debe ser una fecha posterior o igual a :date.",
    "alpha"            => "El campo :attribute sólo debe contener letras.",
    "alpha_dash"       => "El campo :attribute solo puede contener letras, numeros y barras.",
    "alpha_num"        => "El campo :attribute solo puede contener letras y números.",
    "array"            => "El campo :attribute debe ser un array.",
    "before"           => "El campo :attribute debe ser una fecha anterior a :date.",
    "before_or_equal"  => "El campo :attribute debe ser una fecha anterior o igual a :date.",
    "between"          => [
        "numeric" => "El campo :attribute debe tener estar entre :min y :max.",
        "file"    => "El campo :attribute debe ocupar entre :min y :max kilobytes.",
        "string"  => "El campo :attribute debe tener entre :min y :max caracteres.",
        "array"   => "El campo :attribute debe tener entre :min y :max elementos."
    ],
    "boolean"          => "El campo :attribute debe ser verdadero o falso.",
    "confirmed"        => "La confirmación del campo :attribute es incorrecta.",
    "date"             => "El campo :attribute no es una fecha válida.",
    "date_equals"      => "El campo :attribute debe ser una fecha igual a :date.",
    "date_format"      => "El campo :attribute no tiene el formato :format.",
    "different"        => "Los campos :attribute y :other debe ser distintos.",
    "digits"           => "El campo :attribute debe tener :digits dígitos.",
    "digits_between"   => "El campo :attribute debe tener entre :min y :max dígitos.",
    "dimensions"       => "El campo :attribute tiene unas dimensiones de imagen no válidas.",
    "distinct"         => "El campo :attribute tiene un valor duplicado.",
    "email"            => "El campo :attribute tiene un formato incorrecto.",
    "ends_with"        => "El campo :attribute debe terminar con uno de los siguientes valores: :values.",
    "exists"           => "El campo :attribute es incorrecto.",
    "file"             => "El campo :attribute debe ser un archivo.",
    "filled"           => "El campo :attribute debe tener un valor.",
    "gt"               => [
        "numeric" => "El campo :attribute debe ser mayor que :value.",
        "file"    => "El campo :attribute debe ocupar más de :value kilobytes.",
        "string"  => "El campo :attribute debe tener más de :value caracteres.",
        "array"   => "El campo :attribute debe tener más de :value elementos."
    ],
    "gte"              => [
        "numeric" => "El campo :attribute debe ser mayor o igual que :value.",
        "file"    => "El campo :attribute debe ocupar :value kilobytes o más.",
        "string"  => "El campo :attribute debe tener :value caracteres o más.",
        "array"   => "El campo :attribute debe tener :value elementos o más."
    ],
    "image"            => "El campo :attribute debe ser una imagen.",
    "in"               => "El campo :attribute es incorrecto.",
    "in_array"         => "El campo :attribute no existe en :other.",
    "integer"          => "El campo :attribute debe ser un entero.",
    "ip"               => "El campo :attribute debe ser una IP válida.",
    "ipv4"             => "El campo :attribute debe ser una dirección IPv4 válida.",
    "ipv6"             => "El campo :attribute debe ser una dirección IPv6 válida.",
    "json"             => "El campo :attribute debe ser una cadena JSON válida.",
    "lt"               => [
        "numeric" => "El campo :attribute debe ser menor que :value.",
        "file"    => "El campo :attribute debe ocupar menos de :value kilobytes.",
        "string"  => "El campo :attribute debe tener menos de :value caracteres.",
        "array"   => "El campo :attribute debe tener menos de :value elementos."
    ],
    "lte"              => [
        "numeric" => "El campo :attribute debe ser menor o igual que :value.",
        "file"    => "El campo :attribute no debe ocupar más de :value kilobytes.",
        "string"  => "El campo :attribute no debe tener más de :value caracteres.",
        "array"   => "El campo :attribute no debe tener más de :value elementos."
    ],
    "max"              => [
        "numeric" => "El campo :attribute no debe ser mayor que :max.",
        "file"    => "El campo :attribute no debe ocupar más de :max kilobytes.",
        "string"  => "El campo :attribute no debe tener más de :max caracteres.",
        "array"   => "El campo :attribute no debe tener más de :max elementos."
    ],
    "mimes"            => "El campo :attribute debe ser un archivo del tipo: :values.",
    "mimetypes"        => "El campo :attribute debe ser un archivo del tipo: :values.",
    "extensions"       => "El campo :attribute debe tener una extensión de: :values.",
    "min"              => [
        "numeric" => "El campo :attribute debe ser :min o más.",
        "file"    => "El campo :attribute debe ocupar :min kilobytes o más.",
        "string"  => "El campo :attribute debe tener :min caracteres o más.",
        "array"   => "El campo :attribute debe tener :min elementos o más."
    ],
    "not_in"           => "El campo :attribute es incorrecto.",
    "not_regex"        => "El campo :attribute tiene un formato incorrecto.",
    "numeric"          => "El campo :attribute debe ser un número.",
    "present"          => "El campo :attribute debe estar presente.",
    "regex"            => "El campo :attribute tiene un formato incorrecto.",
    "required"         => "El campo :attribute es obligatorio.",
    "required_if"      => "El campo :attribute es obligatorio si el campo :other es :value.",
    "required_unless"  => "El campo :attribute es obligatorio a menos que :other esté en :values.",
    "required_with"    => "El campo :attribute es obligatorio cuando el campo :values está presente.",
    "required_with_all" => "El campo :attribute es obligatorio cuando :values está presente.",
    "required_without" => "El campo :attribute es obligatorio cuando el campo :values no está presente.",
    "required_without_all" => "El campo :attribute es obligatorio cuando ninguno de :values está presente.",
    "same"             => "Los campos :attribute y :other deben ser iguales.",
    "size"             => [
        "numeric" => "El campo :attribute debe ser :size.",
        "file"    => "El campo :attribute debe ocupar :size kilobytes.",
        "string"  => "El campo :attribute debe tener :size caracteres.",
        "array"   => "El campo :attribute debe contener :size elementos."
    ],
    "starts_with"      => "El campo :attribute debe empezar por uno de los siguientes valores: :values.",
    "string"           => "El campo :attribute debe ser una cadena de texto.",
    "timezone"         => "El campo :attribute debe ser una zona horaria válida.",
    "unique"           => "El campo :attribute ya ha sido tomado.",
    "uploaded"         => "El campo :attribute no se ha podido subir.",
    "url"              => "El campo :attribute tiene un formato incorrecto.",
    "uuid"             => "El campo :attribute debe ser un UUID válido.",

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

    'attributes' => []

];
