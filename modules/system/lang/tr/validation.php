<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'          => ':attribute kabul edilmelidir.',
    'active_url'        => ':attribute geçerli bir URL olmalıdır.',
    'after' => ':attribute şundan eski bir tarih olmalıdır :date.',
    'after_or_equal'    => ':attribute şundan eski veya aynı bir tarih olmalıdır :date.',
    'alpha'             => ':attribute sadece harflerden oluşmalıdır.',
    'alpha_dash'        => ':attribute sadece harfler, rakamlar ve tirelerden oluşmalıdır.',
    'alpha_num'         => ':attribute sadece harfler ve rakamlar içermelidir.',
    'array'             => ':attribute dizi olmalıdır.',
    'before'            => ':attribute şundan önceki bir tarih olmalıdır :date.',
    'before_or_equal'   => ':attribute şundan önceki veya aynı bir tarih olmalıdır :date.',
    'between'           => [
        'numeric' => ':attribute :min - :max arasında olmalıdır.',
        'file'    => ':attribute :min - :max arasındaki kilobayt değeri olmalıdır.',
        'string'  => ':attribute :min - :max arasında karakterden oluşmalıdır.',
        'array'   => ':attribute :min - :max arasında nesneye sahip olmalıdır.',
    ],
    'boolean'           => ':attribute alanı true veya false olmalıdır.',
    'confirmed'         => ':attribute tekrarı eşleşmiyor.',
    'date'              => ':attribute geçerli bir tarih olmalıdır.',
    'date_equals'       => ':attribute alanı :date ile eşit bir tarih olmalıdır.',
    'date_format'       => ':attribute :format biçimi ile eşleşmiyor.',
    'different'         => ':attribute ile :other birbirinden farklı olmalıdır.',
    'digits'            => ':attribute :digits rakam olmalıdır.',
    'digits_between'    => ':attribute :min ile :max arasında rakam olmalıdır.',
    'dimensions'        => ':attribute geçersiz resim boyutlarına sahip.',
    'distinct'          => ':attribute alanı yinelenen bir değere sahip.',
    'email'             => ':attribute biçimi geçersiz.',
    'ends_with'         => ':attribute şunlardan biri ile bitmeli: :values.',
    'exists'            => 'Seçili :attribute geçersiz.',
    'file'              => ':attribute bir dosya olmalı.',
    'filled'            => ':attribute alanın bir değeri içermelidir.',
    'gt'                => [
        'numeric' => ':attribute, :value değerinden büyük olmalıdır.',
        'file'    => ':attribute, :value kilobayttan büyük olmalıdır.',
        'string'  => ':attribute, :value karakterden büyük olmalıdır.',
        'array'   => ':attribute, :value öğeden daha fazla olmalıdır.',
    ],
    'gte'               => [
        'numeric'   => ':attribute, :value değerinden büyük veya ona eşit olmalıdır.',
        'file'      => ':attribute, :value kilobayttan büyük veya ona eşit olmalıdır.',
        'string'    => ':attribute, :value karakterden büyük veya ona eşit olmalıdır.',
        'array'     => ':attribute, :value öğeden veya daha fazlasına sahip olmalıdır.'
    ],
    'image'             => ':attribute alanı resim dosyası olmalıdır.',
    'in'                => ':attribute değeri geçersiz.',
    'in_array'          => ':attribute alanı, :other da bulunmuyor.',
    'integer'           => ':attribute rakam olmalıdır.',
    'ip'                => ':attribute geçerli bir IP adresi olmalıdır.',
    'ipv4'              => ':attribute geçerli bir IPv4 adresi olmalıdır.',
    'ipv6'              => ':attribute geçerli bir IPv6 adresi olmalıdır.',
    'json'              => ':attribute geçerli bir JSON string olmalıdır.',
    'lt'                => [
        'numeric'   => ':attribute, :value değerinden küçük olmalıdır.',
        'file'      => ':attribute, :value kilobayttan küçük olmalıdır.',
        'string'    => ':attribute, :value karakterden küçük olmalıdır.',
        'array'     => ':attribute, :value öğeden az olmalıdır.',
    ],
    'lte'               => [
        'numeric'   => ':attribute, :value değerinden küçük veya ona eşit olmalıdır.',
        'file'      => ':attribute, :value kilobayttan küçük veya ona eşit olmalıdır.',
        'string'    => ':attribute, :value karakterden küçük veya ona eşit olmalıdır.',
        'array'     => ':attribute, :value öğeden fazla olmamalıdır.',
    ],
    'max'               => [
        'numeric' => ':attribute değeri :max değerinden küçük olmalıdır.',
        'file'    => ':attribute değeri :max kilobayt değerinden küçük olmalıdır.',
        'string'  => ':attribute değeri :max karakter değerinden küçük olmalıdır.',
        'array'   => ':attribute değeri :max adedinden az nesneye sahip olmalıdır.',
    ],
    'mimes'             => ':attribute dosya biçimi :values olmalıdır.',
    'mimetypes'         => ':attribute dosya biçimi :values olmalıdır.',
    'min'               => [
        'numeric' => ':attribute değeri :min değerinden büyük olmalıdır.',
        'file'    => ':attribute değeri :min kilobayt değerinden büyük olmalıdır.',
        'string'  => ':attribute değeri :min karakter değerinden büyük olmalıdır.',
        'array'   => ':attribute en az :min nesneye sahip olmalıdır.',
    ],
    'not_in'               => 'Seçili :attribute geçersiz.',
    'not_regex'            => ':attribute formatı geçersiz.',
    'numeric'              => ':attribute rakam olmalıdır.',
    'present'              => ':attribute alanı mevcut olmalı.',
    'regex'                => ':attribute biçimi geçersiz.',
    'required'             => ':attribute alanı zorunludur.',
    'required_if'          => ':attribute alanı, :other :value değerine sahip olduğunda zorunludur.',
    'required_unless'      => ':other değeri :values içinde olmadığı müddetçe :attribute alanı zorunludur.',
    'required_with'        => ':attribute alanı :values varken zorunludur.',
    'required_with_all'    => ':values değeri olduğu durumda :attribute alanı zorunludur.',
    'required_without'     => ':attribute alanı :values yokken zorunludur.',
    'required_without_all' => ':values değerleri olmadığı müddetçe :attribute alanı zorunludur.',
    'same'              => ':attribute ile :other eşleşmelidir.',
    'size'              => [
        'numeric' => ':attribute :size olmalıdır.',
        'file'    => ':attribute :size kilobyte olmalıdır.',
        'string'  => ':attribute :size karakter olmalıdır.',
        'array'   => ':attribute :size nesneye sahip olmalıdır.',
    ],
    'starts_with'   => ':attribute şunlardan biriyle başlamalıdır: :values.',
    'string'        => 'The :attribute must be a string.',
    'timezone'      => 'The :attribute must be a valid zone.',
    'unique'        => ':attribute daha önceden kayıt edilmiş.',
    'uploaded'      => 'The :attribute failed to upload.',
    'url'           => ':attribute biçimi geçersiz.',
    'uuid'          => ':attribute geçerli bir UUID olmalıdır.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention 'attribute.rule' to name the lines. This makes it quick to
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
    | of 'email'. This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
