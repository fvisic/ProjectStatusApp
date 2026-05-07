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

    'accepted' => 'Polje :attribute mora biti prihvaćeno.',
    'accepted_if' => 'Polje :attribute mora biti prihvaćeno kada je :other :value.',
    'active_url' => 'Polje :attribute mora biti valjani URL.',
    'after' => 'Polje :attribute mora biti datum nakon :date.',
    'after_or_equal' => 'Polje :attribute mora biti datum nakon ili jednak :date.',
    'alpha' => 'Polje :attribute smije sadržavati samo slova.',
    'alpha_dash' => 'Polje :attribute smije sadržavati samo slova, brojeve, crtice i podvlake.',
    'alpha_num' => 'Polje :attribute smije sadržavati samo slova i brojeve.',
    'any_of' => 'Polje :attribute nije valjano.',
    'array' => 'Polje :attribute mora biti niz.',
    'ascii' => 'Polje :attribute smije sadržavati samo jednobajtne alfanumeričke znakove i simbole.',
    'before' => 'Polje :attribute mora biti datum prije :date.',
    'before_or_equal' => 'Polje :attribute mora biti datum prije ili jednak :date.',
    'between' => [
        'array' => 'Polje :attribute mora imati između :min i :max stavki.',
        'file' => 'Polje :attribute mora biti između :min i :max kilobajta.',
        'numeric' => 'Polje :attribute mora biti između :min i :max.',
        'string' => 'Polje :attribute mora imati između :min i :max znakova.',
    ],
    'boolean' => 'Polje :attribute mora biti istinito ili neistinito.',
    'can' => 'Polje :attribute sadrži neovlaštenu vrijednost.',
    'confirmed' => 'Potvrda polja :attribute se ne podudara.',
    'contains' => 'Polju :attribute nedostaje obavezna vrijednost.',
    'current_password' => 'Lozinka je netočna.',
    'date' => 'Polje :attribute mora biti valjani datum.',
    'date_equals' => 'Polje :attribute mora biti datum jednak :date.',
    'date_format' => 'Polje :attribute mora odgovarati formatu :format.',
    'decimal' => 'Polje :attribute mora imati :decimal decimalnih mjesta.',
    'declined' => 'Polje :attribute mora biti odbijeno.',
    'declined_if' => 'Polje :attribute mora biti odbijeno kada je :other :value.',
    'different' => 'Polje :attribute i :other moraju biti različiti.',
    'digits' => 'Polje :attribute mora imati :digits znamenki.',
    'digits_between' => 'Polje :attribute mora imati između :min i :max znamenki.',
    'dimensions' => 'Polje :attribute ima nevaljane dimenzije slike.',
    'distinct' => 'Polje :attribute ima dupliciranu vrijednost.',
    'doesnt_contain' => 'Polje :attribute ne smije sadržavati ništa od sljedećeg: :values.',
    'doesnt_end_with' => 'Polje :attribute ne smije završavati s jednim od sljedećeg: :values.',
    'doesnt_start_with' => 'Polje :attribute ne smije započinjati s jednim od sljedećeg: :values.',
    'email' => 'Polje :attribute mora biti valjana adresa e-pošte.',
    'encoding' => 'Polje :attribute mora biti kodirano u :encoding.',
    'ends_with' => 'Polje :attribute mora završavati s jednim od sljedećeg: :values.',
    'enum' => 'Odabrani :attribute nije valjan.',
    'exists' => 'Odabrani :attribute nije valjan.',
    'extensions' => 'Polje :attribute mora imati jednu od sljedećih ekstenzija: :values.',
    'file' => 'Polje :attribute mora biti datoteka.',
    'filled' => 'Polje :attribute mora imati vrijednost.',
    'gt' => [
        'array' => 'Polje :attribute mora imati više od :value stavki.',
        'file' => 'Polje :attribute mora biti veće od :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti veće od :value.',
        'string' => 'Polje :attribute mora imati više od :value znakova.',
    ],
    'gte' => [
        'array' => 'Polje :attribute mora imati :value ili više stavki.',
        'file' => 'Polje :attribute mora biti veće od ili jednako :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti veće od ili jednako :value.',
        'string' => 'Polje :attribute mora imati :value ili više znakova.',
    ],
    'hex_color' => 'Polje :attribute mora biti valjana heksadecimalna boja.',
    'image' => 'Polje :attribute mora biti slika.',
    'in' => 'Odabrani :attribute nije valjan.',
    'in_array' => 'Polje :attribute mora postojati u :other.',
    'in_array_keys' => 'Polje :attribute mora sadržavati barem jedan od sljedećih ključeva: :values.',
    'integer' => 'Polje :attribute mora biti cijeli broj.',
    'ip' => 'Polje :attribute mora biti valjana IP adresa.',
    'ipv4' => 'Polje :attribute mora biti valjana IPv4 adresa.',
    'ipv6' => 'Polje :attribute mora biti valjana IPv6 adresa.',
    'json' => 'Polje :attribute mora biti valjani JSON niz.',
    'list' => 'Polje :attribute mora biti lista.',
    'lowercase' => 'Polje :attribute mora biti malim slovima.',
    'lt' => [
        'array' => 'Polje :attribute mora imati manje od :value stavki.',
        'file' => 'Polje :attribute mora biti manje od :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti manje od :value.',
        'string' => 'Polje :attribute mora imati manje od :value znakova.',
    ],
    'lte' => [
        'array' => 'Polje :attribute ne smije imati više od :value stavki.',
        'file' => 'Polje :attribute mora biti manje od ili jednako :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti manje od ili jednako :value.',
        'string' => 'Polje :attribute mora imati :value ili manje znakova.',
    ],
    'mac_address' => 'Polje :attribute mora biti valjana MAC adresa.',
    'max' => [
        'array' => 'Polje :attribute ne smije imati više od :max stavki.',
        'file' => 'Polje :attribute ne smije biti veće od :max kilobajta.',
        'numeric' => 'Polje :attribute ne smije biti veće od :max.',
        'string' => 'Polje :attribute ne smije imati više od :max znakova.',
    ],
    'max_digits' => 'Polje :attribute ne smije imati više od :max znamenki.',
    'mimes' => 'Polje :attribute mora biti datoteka tipa: :values.',
    'mimetypes' => 'Polje :attribute mora biti datoteka tipa: :values.',
    'min' => [
        'array' => 'Polje :attribute mora imati najmanje :min stavki.',
        'file' => 'Polje :attribute mora biti najmanje :min kilobajta.',
        'numeric' => 'Polje :attribute mora biti najmanje :min.',
        'string' => 'Polje :attribute mora imati najmanje :min znakova.',
    ],
    'min_digits' => 'Polje :attribute mora imati najmanje :min znamenki.',
    'missing' => 'Polje :attribute mora nedostajati.',
    'missing_if' => 'Polje :attribute mora nedostajati kada je :other :value.',
    'missing_unless' => 'Polje :attribute mora nedostajati osim ako :other nije :value.',
    'missing_with' => 'Polje :attribute mora nedostajati kada je :values prisutno.',
    'missing_with_all' => 'Polje :attribute mora nedostajati kada su :values prisutni.',
    'multiple_of' => 'Polje :attribute mora biti višekratnik od :value.',
    'not_in' => 'Odabrani :attribute nije valjan.',
    'not_regex' => 'Format polja :attribute nije valjan.',
    'numeric' => 'Polje :attribute mora biti broj.',
    'password' => [
        'letters' => 'Polje :attribute mora sadržavati barem jedno slovo.',
        'mixed' => 'Polje :attribute mora sadržavati barem jedno veliko i jedno malo slovo.',
        'numbers' => 'Polje :attribute mora sadržavati barem jedan broj.',
        'symbols' => 'Polje :attribute mora sadržavati barem jedan simbol.',
        'uncompromised' => 'Navedeni :attribute se pojavio u curenju podataka. Molimo odaberite drugi :attribute.',
    ],
    'present' => 'Polje :attribute mora biti prisutno.',
    'present_if' => 'Polje :attribute mora biti prisutno kada je :other :value.',
    'present_unless' => 'Polje :attribute mora biti prisutno osim ako :other nije :value.',
    'present_with' => 'Polje :attribute mora biti prisutno kada je :values prisutno.',
    'present_with_all' => 'Polje :attribute mora biti prisutno kada su :values prisutni.',
    'prohibited' => 'Polje :attribute je zabranjeno.',
    'prohibited_if' => 'Polje :attribute je zabranjeno kada je :other :value.',
    'prohibited_if_accepted' => 'Polje :attribute je zabranjeno kada je :other prihvaćeno.',
    'prohibited_if_declined' => 'Polje :attribute je zabranjeno kada je :other odbijeno.',
    'prohibited_unless' => 'Polje :attribute je zabranjeno osim ako :other nije u :values.',
    'prohibits' => 'Polje :attribute zabranjuje prisutnost polja :other.',
    'regex' => 'Format polja :attribute nije valjan.',
    'required' => 'Polje :attribute je obavezno.',
    'required_array_keys' => 'Polje :attribute mora sadržavati unose za: :values.',
    'required_if' => 'Polje :attribute je obavezno kada je :other :value.',
    'required_if_accepted' => 'Polje :attribute je obavezno kada je :other prihvaćeno.',
    'required_if_declined' => 'Polje :attribute je obavezno kada je :other odbijeno.',
    'required_unless' => 'Polje :attribute je obavezno osim ako :other nije u :values.',
    'required_with' => 'Polje :attribute je obavezno kada je :values prisutno.',
    'required_with_all' => 'Polje :attribute je obavezno kada su :values prisutni.',
    'required_without' => 'Polje :attribute je obavezno kada :values nije prisutno.',
    'required_without_all' => 'Polje :attribute je obavezno kada nijedno od :values nije prisutno.',
    'same' => 'Polje :attribute mora se podudarati s :other.',
    'size' => [
        'array' => 'Polje :attribute mora sadržavati :size stavki.',
        'file' => 'Polje :attribute mora biti :size kilobajta.',
        'numeric' => 'Polje :attribute mora biti :size.',
        'string' => 'Polje :attribute mora imati :size znakova.',
    ],
    'starts_with' => 'Polje :attribute mora započinjati s jednim od sljedećeg: :values.',
    'string' => 'Polje :attribute mora biti niz znakova.',
    'timezone' => 'Polje :attribute mora biti valjana vremenska zona.',
    'unique' => 'Polje :attribute je već zauzeto.',
    'uploaded' => 'Prijenos polja :attribute nije uspio.',
    'uppercase' => 'Polje :attribute mora biti velikim slovima.',
    'url' => 'Polje :attribute mora biti valjani URL.',
    'ulid' => 'Polje :attribute mora biti valjani ULID.',
    'uuid' => 'Polje :attribute mora biti valjani UUID.',

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
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'email' => 'e-pošta',
        'password' => 'lozinka',
        'password_confirmation' => 'potvrda lozinke',
        'name' => 'ime',
        'current_password' => 'trenutna lozinka',
        'new_password' => 'nova lozinka',
        'form.email' => 'e-pošta',
        'form.password' => 'lozinka',
    ],

];
