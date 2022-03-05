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

    'accepted'             => ':attribute jābūt apstiprinātam.',
    'active_url'           => ':attribute nav derīga URL.',
    'after'                => ':attribute jābūt datumam pēc :date.',
    'after_or_equal'       => ':attribute jābūt datumam pēc :date vai vienādam ar to.',
    'alpha'                => ':attribute var saturēt tikai burtus.',
    'alpha_dash'           => ':attribute var saturēt tikai burtus, skaitļus un domuzīmes.',
    'alpha_num'            => ':attribute var saturēt tikai burtus un skaitļus.',
    'array'                => ':attribute jābūt masīvam.',
    'before'               => ':attribute jābūt datumam pirms :date.',
    'before_or_equal'      => ':attribute jābūt datumam pirms :date vai vienādam ar to.',
    'between'              => [
        'numeric' => ':attribute jābūt starp :min un :max.',
        'file'    => ':attribute jābūt starp :min un :max kilobaitiem.',
        'string'  => ':attribute jāsatur :min - :max simbolus.',
        'array'   => ':attribute jāsatur :min - :max vienumus.',
    ],
    'boolean'              => ':attribute laukam jābūt patiesam (true) vai nepatiesam (false).',
    'confirmed'            => ':attribute apstiprinājums nesakrīt.',
    'date'                 => ':attribute nav derīgs datums.',
    'date_equals'          => ':attribute jāsakrīt ar datumu :date.',
    'date_format'          => ':attribute nesakrīt ar formātu :format.',
    'different'            => ':attribute un :other jābūt atšķirīgiem.',
    'digits'               => ':attribute jāsatur :digits ciparus.',
    'digits_between'       => ':attribute jāsatur :min - :max ciparus.',
    'dimensions'           => ':attribute satur nederīgus attēla izmērus.',
    'distinct'             => ':attribute satur dublikāta vērtību.',
    'email'                => ':attribute jābūt derīgai e-pasta adresei.',
    'ends_with'            => ':attribute jābeidzas ar vienu no sekojošām vērtībām: :values.',
    'exists'               => 'Izvēlētais :attribute nav derīgs.',
    'file'                 => ':attribute jābūt failam.',
    'filled'               => ':attribute jābūt norādītai vērtībai.',
    'gt'                   => [
        'numeric' => ':attribute jābūt lielākam nekā :value.',
        'file'    => ':attribute jābūt lielākam nekā :value kilobaitiem.',
        'string'  => ':attribute jāsatur vairāk nekā :value simbolus.',
        'array'   => ':attribute jāsatur vairāk nekā :value vienumus.',
    ],
    'gte'                  => [
        'numeric' => ':attribute jābūt lielākam vai vienādam ar :value.',
        'file'    => ':attribute jābūt lielākam vai vienādam ar :value kilobaitiem.',
        'string'  => ':attribute jāsatur :value vai vairāk simbolu.',
        'array'   => ':attribute jāsatur :value vai vairāk vienumu.',
    ],
    'image'                => ':attribute jābūt attēlam.',
    'in'                   => 'Izvēlētais :attribute ir nederīgs.',
    'in_array'             => ':attribute lauks neietilpst :other.',
    'integer'              => ':attribute jābūt veselam skaitlim.',
    'ip'                   => ':attribute jābūt derīgai IP adresei.',
    'ipv4'                 => ':attribute jābūt derīgai IPv4 adresei.',
    'ipv6'                 => ':attribute jābūt derīgai IPv6 adresei.',
    'json'                 => ':attribute jābūt derīgai JSON virknei.',
    'lt'                   => [
        'numeric' => ':attribute jābūt mazākam nekā :value.',
        'file'    => ':attribute jābūt mazākam nekā :value kilobaitiem.',
        'string'  => ':attribute jāsatur mazāk nekā :value simbolus.',
        'array'   => ':attribute jāsatur mazāk nekā :value vienumus.',
    ],
    'lte'                  => [
        'numeric' => ':attribute jābūt mazākam vai vienādam ar :value.',
        'file'    => ':attribute jābūt mazākam vai vienādam ar :value kilobaitiem.',
        'string'  => ':attribute jāsatur :value vai mazāk simbolus.',
        'array'   => ':attribute jāsatur :value vai mazāk vienumus.',
    ],
    'max'                  => [
        'numeric' => ':attribute nedrīkst pārsniegt :max.',
        'file'    => ':attribute nedrīkst pārsniegt :max kilobaitus.',
        'string'  => ':attribute nedrīkst saturēt vairāk kā :max simbolus.',
        'array'   => ':attribute nedrīkst saturēt vairāk kā :max vienumus.',
    ],
    'mimes'                => ':attribute jābūt šāda veida failam: :values.',
    'extensions'           => ':attribute jābūt šāda veida failam: :values.',
    'min'                  => [
        'numeric' => ':attribute jābūt vismaz :min.',
        'file'    => ':attribute jābūt vismaz :min kilobaitiem.',
        'string'  => ':attribute jāsatur vismaz :min simbolus.',
        'array'   => ':attribute jāsatur vismaz :min vienumus.',
    ],
    'not_in'               => 'Izvēlētais :attribute nav derīgs.',
    'not_regex'            => ':attribute formāts nav derīgs.',
    'numeric'              => ':attribute jābūt skaitlim.',
    'present'              => ':attribute laukam jābūt norādītam.',
    'regex'                => ':attribute formāts nav derīgs.',
    'required'             => ':attribute lauks ir obligāts.',
    'required_if'          => ':attribute lauks ir obligāts, ja :other ir :value.',
    'required_unless'      => ':attribute lauks ir obligāts, ja vien :other ietilpst :values.',
    'required_with'        => ':attribute lauks ir obligāts, ja :values ir norādītas.',
    'required_with_all'    => ':attribute lauks ir obligāts, ja :values ir norādītas.',
    'required_without'     => ':attribute lauks ir obligāts, ja :values nav norādītas.',
    'required_without_all' => ':attribute lauks ir obligāts, ja neviena no :values nav norādīta.',
    'same'                 => ':attribute un :other jāsakrīt.',
    'size'                 => [
        'numeric' => ':attribute jābūt :size.',
        'file'    => ':attribute jābūt :size kilobaitiem.',
        'string'  => ':attribute jāsatur :size simbolus.',
        'array'   => ':attribute jāsatur :size vienumus.',
    ],
    'starts_with'          => ':attribute jāsākas ar vienu no sekojošām vērtībām: :values.',
    'string'               => ':attribute jābūt teksta virknei.',
    'timezone'             => ':attribute jābūt derīgai laika zonai.',
    'unique'               => ':attribute ir jau aizņemts.',
    'uploaded'             => ':attribute neizdevās augšupielādēt.',
    'url'                  => ':attribute formāts nav derīgs.',
    'uuid'                 => ':attribute jābūt derīgam UUID.',

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
