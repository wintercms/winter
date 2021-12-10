<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Linii de limbaj de validare
    |--------------------------------------------------------------------------
    |
    | Următoarele linii de limbă conțin mesajele de eroare implicite utilizate de
    | clasa Validator. Unele dintre aceste reguli au astfel de versiuni multiple
    | precum regulile de mărime. Simțiți-vă liber să modificați fiecare dintre aceste mesaje.
    |
    */

    'accepted'             => 'Atributul :attribute trebuie să fie acceptat.',
    'active_url'           => 'Atributul :attribute nu este un URL valid.',
    'after'                => 'Atributul :attribute trebuie să fie o dată după data :date.',
    'after_or_equal'       => 'Atributul :attribute trebuie să fie o dată după sau egală cu :date.',
    'alpha'                => 'Atributul :attribute poate să conțină doar litere.',
    'alpha_dash'           => 'Atributul :attribute poate să conțină doar litere, numere, și liniuțe.',
    'alpha_num'            => 'Atributul :attribute poate să conțină doar litere și numere.',
    'array'                => 'Atributul :attribute trebuie să fie de tip matrice.',
    'before'               => 'Atributul :attribute trebuie să fie o dată înainte de data :date.',
    'before_or_equal'      => 'Atributul :attribute trebuie să fie o dată anterioară sau egală cu :date.',
    'between'              => [
        'numeric' => 'Atributul :attribute trebuie să fie între :min și :max.',
        'file'    => 'Atributul :attribute trebuie să fie între :min și :max kilobaiți (kB).',
        'string'  => 'Atributul :attribute trebuie să fie între :min și :max caractere.',
        'array'   => 'Atributul :attribute trebuie să aibă între :min și :max elemente.',
    ],
    'boolean'              => 'Câmpul :attribute trebuie să fie adevărat sau fals.',
    'confirmed'            => 'Confirmarea :attribute nu se potrivește.',
    'date'                 => 'Atributul :attribute nu este o dată validă.',
    'date_equals'          => 'Atributul :attribute trebuie să fie o dată egală cu :date.',
    'date_format'          => 'Atributul :attribute nu se potrivește cu formatul :format.',
    'different'            => 'Atributul :attribute și :other trebuie să fie diferite.',
    'digits'               => 'Atributul :attribute trebuie să fie :digits cifre.',
    'digits_between'       => 'Atributul :attribute trebuie să fie între :min și :max cifre.',
    'dimensions'           => 'Atributul :attribute nu sunt dimensiuni valide ale imaginii.',
    'distinct'             => 'Câmpul :attribute are o valoare duplicat.',
    'email'                => 'Atributul :attribute trebuie să fie o adresă de e-mail validă.',
    'ends_with'            => 'Atributul :attribute trebuie să se încheie cu unul dintre următoarele: :values.',
    'exists'               => 'Atributul :attribute selectat nu este valid.',
    'file'                 => 'Atributul :attribute trebuie să fie un fișier.',
    'filled'               => 'Câmpul :attribute trebuie să aibă o valoare.',
    'gt'                   => [
        'numeric'  => 'Atributul :attribute trebuie să fie mai mare decât :value.',
        'file'     => 'Atributul :attribute trebuie să fie mai mare decât :value kilobaiți (kB).',
        'string'   => 'Atributul :attribute trebuie să fie mai mare decât :value caractere.',
        'array'    => 'Atributul :attribute trebuie să aibă mai mult de :value elemente.',
    ],
    'gte'                   => [
        'numeric' => 'Atributul :attribute trebuie să fie mai mare sau egal cu :value.',
        'file'    => 'Atributul :attribute trebuie să fie mai mare sau egal cu :value kilobaiți (kB).',
        'string'  => 'Atributul :attribute trebuie să fie mai mare sau egal cu :value caractere.',
        'array'   => 'Atributul :attribute trebuie să aibă :value elemente sau mai multe.',
    ],
    'image'                => 'Atributul :attribute trebuie să fie o imagine.',
    'in'                   => 'Atributul :attribute selectat nu este valid.',
    'in_array'             => 'Câmpul :attribute nu există în :other.',
    'integer'              => 'Atributul :attribute trebuie să fie un număr întreg.',
    'ip'                   => 'Atributul :attribute trebuie să fie o adresă IP validă.',
    'ipv4'                 => 'Atributul :attribute trebuie să fie o adresă IPv4 validă.',
    'ipv6'                 => 'Atributul :attribute trebuie să fie o adresă IPv6 validă.',
    'json'                 => 'Atributul :attribute trebuie să fie un șir JSON valid.',
    'lt'                   => [
        'numeric' => 'Atributul :attribute trebuie să fie mai mic decât :value.',
        'file'    => 'Atributul :attribute trebuie să fie mai mic de :value kilobaiți (kB).',
        'string'  => 'Atributul :attribute trebuie să fie mai mic de :value caractere.',
        'array'   => 'Atributul :attribute trebuie să aibă mai puțin de :value elemente.',
    ],
    'lte'                   => [
        'numeric' => 'Atributul :attribute trebuie să fie mai mic sau egal cu :value.',
        'file'    => 'Atributul :attribute trebuie să fie mai mic sau egal :value kilobaiți (kB).',
        'string'  => 'Atributul :attribute trebuie să fie mai mic sau egal :value caractere.',
        'array'   => 'Atributul :attribute nu trebuie să aibă mai mult de :value elemente.',
    ],
    'max'                   => [
        'numeric' => 'Atributul :attribute nu poate fi mai mare de :max.',
        'file'    => 'Atributul :attribute nu poate fi mai mare de :max kilobaiți (kB).',
        'string'  => 'Atributul :attribute nu poate fi mai mare de :max caractere.',
        'array'   => 'Atributul :attribute nu poate avea mai mult de :max elemente.',
    ],
    'mimes'                 => 'Atributul :attribute trebuie să fie un fișier de tipul: :values.',
    'mimetypes'             => 'Atributul :attribute trebuie să fie un fișier de tipul: :values.',
    'min'                   => [
        'numeric' => 'Atributul :attribute trebuie să aibă cel puțin :min caractere',
        'file'    => 'Atributul :attribute trebuie să aibă cel puțin :min kilobaiți (kB).',
        'string'  => 'Atributul :attribute trebuie să aibă cel puțin :min caractere.',
        'array'   => 'Atributul :attribute trebuie să aibă cel puțin :min elemente.',
    ],
    'not_in'               => 'Atributul :attribute selectat nu este valid.',
    'not_regex'            => 'Formatul :attribute nu este valid.',
    'numeric'              => 'Atributul :attribute trebuie să fie un număr.',
    'present'              => 'Câmpul :attribute trebuie să fie prezent.',
    'regex'                => 'Formatul :attribute nu este valid.',
    'required'             => 'Câmpul :attribute este necesar.',
    'required_if'          => 'Câmpul :attribute este necesar când atributul :other are valoarea :value.',
    'required_unless'      => 'Câmpul :attribute este obligatoriu, cu excepția cazului în care :other este în :values.',
    'required_with'        => 'Campul :attribute este necesar când valorea :values este prezentă.',
    'required_with_all'    => 'Câmpul :attribute este necesar atunci când :values sunt prezente.',
    'required_without'     => 'Câmpul :attribute este necesar când valoarea :values nu este prezentă.',
    'required_without_all' => 'Câmpul :attribute este obligatoriu atunci când niciuna dintre :values nu este prezentă.',
    'same'                 => 'Atributul :attribute și :other trebuie să corespundă.',
    'size'                 => [
        'numeric' => 'Atributul :attribute trebuie să aibă dimensiunea :size.',
        'file'    => 'Atributul :attribute trebuie să aibă dimensiunea :size kilobaiți (kB).',
        'string'  => 'Atributul :attribute trebuie să aibă :size caractere.',
        'array'   => 'Atributul :attribute trebuie să conțină :size elemente.',
    ],
    'starts_with'          => 'Atributul :attribute trebuie să înceapă cu unul dintre următoarele: :values.',
    'string'               => 'Atributul :attribute trebuie să fie un șir de caractere.',
    'timezone'             => 'Atributul :attribute trebuie să fie o zonă validă.',
    'unique'               => 'Atributul :attribute a fost deja luat.',
    'uploaded'             => 'Atributul :attribute a eșuat la încărcare.',
    'url'                  => 'Formatul :attribute nu este valid.',
    'uuid'                 => 'Atributul :attribute trebuie să fie un UUID valid.',

    /*
    |--------------------------------------------------------------------------
    | Linii de Limbaj de Validare personalizate
    |--------------------------------------------------------------------------
    |
    | Aici puteți specifica mesaje de validare personalizate pentru atribute utilizând
    | convenția "atribut.regulă" pentru a denumi liniile. Acest lucru face rapidă
    | menționarea unei linii de limbaj personalizate specifice pentru o regulă de atribut dată.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'mesaj-personalizat',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Atribute de Validare Personalizate
    |--------------------------------------------------------------------------
    |
    | Următoarele linii de limbă sunt folosite pentru a schimba substitutele atributelor
    | cu ceva mai ușor de citit, cum ar fi "adresa_de_email" în loc de "email".
    | Acest lucru ne ajută pur și simplu să facem mesajele puțin mai curate.
    |
    */

    'attributes' => [],

];
