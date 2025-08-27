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

    'accepted' => 'يجب قبول حقل :attribute.',
    'accepted_if' => 'يجب قبول حقل :attribute عندما يكون :other هو :value.',
    'active_url' => 'حقل :attribute يجب أن يكون رابطاً صحيحاً.',
    'after' => 'حقل :attribute يجب أن يكون تاريخاً بعد :date.',
    'after_or_equal' => 'حقل :attribute يجب أن يكون تاريخاً بعد أو يساوي :date.',
    'alpha' => 'حقل :attribute يجب أن يحتوي على أحرف فقط.',
    'alpha_dash' => 'حقل :attribute يجب أن يحتوي على أحرف، أرقام، شرطات وشرطات سفلية فقط.',
    'alpha_num' => 'حقل :attribute يجب أن يحتوي على أحرف وأرقام فقط.',
    'array' => 'حقل :attribute يجب أن يكون مصفوفة.',
    'ascii' => 'حقل :attribute يجب أن يحتوي فقط على أحرف وأرقام ورموز أحادية البايت.',
    'before' => 'حقل :attribute يجب أن يكون تاريخاً قبل :date.',
    'before_or_equal' => 'حقل :attribute يجب أن يكون تاريخاً قبل أو يساوي :date.',
    'between' => [
        'array' => 'حقل :attribute يجب أن يحتوي على :min إلى :max عنصراً.',
        'file' => 'حقل :attribute يجب أن يكون بين :min و :max كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون بين :min و :max.',
        'string' => 'حقل :attribute يجب أن يكون بين :min و :max حرفاً.',
    ],
    'boolean' => 'حقل :attribute يجب أن يكون صحيح أو خاطئ.',
    'can' => 'حقل :attribute يحتوي على قيمة غير مصرح بها.',
    'confirmed' => 'تأكيد حقل :attribute غير متطابق.',
    'contains' => 'حقل :attribute يفتقد قيمة مطلوبة.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'حقل :attribute يجب أن يكون تاريخاً صحيحاً.',
    'date_equals' => 'حقل :attribute يجب أن يكون تاريخاً يساوي :date.',
    'date_format' => 'حقل :attribute يجب أن يطابق الصيغة :format.',
    'decimal' => 'حقل :attribute يجب أن يحتوي على :decimal من الأرقام العشرية.',
    'declined' => 'حقل :attribute يجب أن يكون مرفوضاً.',
    'declined_if' => 'حقل :attribute يجب أن يكون مرفوضاً عندما يكون :other هو :value.',
    'different' => 'حقل :attribute و :other يجب أن يكونا مختلفين.',
    'digits' => 'حقل :attribute يجب أن يكون :digits أرقام.',
    'digits_between' => 'حقل :attribute يجب أن يكون بين :min و :max رقماً.',
    'dimensions' => 'حقل :attribute يحتوي على أبعاد صورة غير صحيحة.',
    'distinct' => 'حقل :attribute يحتوي على قيمة مكررة.',
    'doesnt_end_with' => 'حقل :attribute يجب ألا ينتهي بأحد القيم التالية: :values.',
    'doesnt_start_with' => 'حقل :attribute يجب ألا يبدأ بأحد القيم التالية: :values.',
    'email' => 'حقل :attribute يجب أن يكون عنوان بريد إلكتروني صحيح.',
    'ends_with' => 'حقل :attribute يجب أن ينتهي بأحد القيم التالية: :values.',
    'enum' => 'القيمة المحددة لـ :attribute غير صحيحة.',
    'exists' => 'القيمة المحددة لـ :attribute غير صحيحة.',
    'extensions' => 'حقل :attribute يجب أن يحتوي على إحدى الامتدادات التالية: :values.',
    'file' => 'حقل :attribute يجب أن يكون ملفاً.',
    'filled' => 'حقل :attribute يجب أن يحتوي على قيمة.',
    'gt' => [
        'array' => 'حقل :attribute يجب أن يحتوي على أكثر من :value عنصراً.',
        'file' => 'حقل :attribute يجب أن يكون أكبر من :value كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون أكبر من :value.',
        'string' => 'حقل :attribute يجب أن يكون أكبر من :value حرفاً.',
    ],
    'gte' => [
        'array' => 'حقل :attribute يجب أن يحتوي على :value عنصراً أو أكثر.',
        'file' => 'حقل :attribute يجب أن يكون أكبر من أو يساوي :value كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون أكبر من أو يساوي :value.',
        'string' => 'حقل :attribute يجب أن يكون أكبر من أو يساوي :value حرفاً.',
    ],
    'hex_color' => 'حقل :attribute يجب أن يكون لوناً سادس عشر صحيحاً.',
    'image' => 'حقل :attribute يجب أن يكون صورة.',
    'in' => 'القيمة المحددة لـ :attribute غير صحيحة.',
    'in_array' => 'حقل :attribute يجب أن يكون موجوداً في :other.',
    'integer' => 'حقل :attribute يجب أن يكون رقماً صحيحاً.',
    'ip' => 'حقل :attribute يجب أن يكون عنوان IP صحيح.',
    'ipv4' => 'حقل :attribute يجب أن يكون عنوان IPv4 صحيح.',
    'ipv6' => 'حقل :attribute يجب أن يكون عنوان IPv6 صحيح.',
    'json' => 'حقل :attribute يجب أن يكون نص JSON صحيح.',
    'list' => 'حقل :attribute يجب أن يكون قائمة.',
    'lowercase' => 'حقل :attribute يجب أن يكون بأحرف صغيرة.',
    'lt' => [
        'array' => 'حقل :attribute يجب أن يحتوي على أقل من :value عنصراً.',
        'file' => 'حقل :attribute يجب أن يكون أقل من :value كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون أقل من :value.',
        'string' => 'حقل :attribute يجب أن يكون أقل من :value حرفاً.',
    ],
    'lte' => [
        'array' => 'حقل :attribute يجب ألا يحتوي على أكثر من :value عنصراً.',
        'file' => 'حقل :attribute يجب أن يكون أقل من أو يساوي :value كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون أقل من أو يساوي :value.',
        'string' => 'حقل :attribute يجب أن يكون أقل من أو يساوي :value حرفاً.',
    ],
    'mac_address' => 'حقل :attribute يجب أن يكون عنوان MAC صحيح.',
    'max' => [
        'array' => 'حقل :attribute يجب ألا يحتوي على أكثر من :max عنصراً.',
        'file' => 'حقل :attribute يجب ألا يكون أكبر من :max كيلوبايت.',
        'numeric' => 'حقل :attribute يجب ألا يكون أكبر من :max.',
        'string' => 'حقل :attribute يجب ألا يكون أكبر من :max حرفاً.',
    ],
    'max_digits' => 'حقل :attribute يجب ألا يحتوي على أكثر من :max رقماً.',
    'mimes' => 'حقل :attribute يجب أن يكون ملفاً من نوع: :values.',
    'mimetypes' => 'حقل :attribute يجب أن يكون ملفاً من نوع: :values.',
    'min' => [
        'array' => 'حقل :attribute يجب أن يحتوي على :min عنصراً على الأقل.',
        'file' => 'حقل :attribute يجب أن يكون :min كيلوبايت على الأقل.',
        'numeric' => 'حقل :attribute يجب أن يكون :min على الأقل.',
        'string' => 'حقل :attribute يجب أن يكون :min حرفاً على الأقل.',
    ],
    'min_digits' => 'حقل :attribute يجب أن يحتوي على :min أرقام على الأقل.',
    'missing' => 'حقل :attribute يجب أن يكون مفقوداً.',
    'missing_if' => 'حقل :attribute يجب أن يكون مفقوداً عندما يكون :other هو :value.',
    'missing_unless' => 'حقل :attribute يجب أن يكون مفقوداً إلا إذا كان :other هو :value.',
    'missing_with' => 'حقل :attribute يجب أن يكون مفقوداً عندما يكون :values موجوداً.',
    'missing_with_all' => 'حقل :attribute يجب أن يكون مفقوداً عندما تكون :values موجودة.',
    'multiple_of' => 'حقل :attribute يجب أن يكون مضاعفاً لـ :value.',
    'not_in' => 'القيمة المحددة لـ :attribute غير صحيحة.',
    'not_regex' => 'صيغة حقل :attribute غير صحيحة.',
    'numeric' => 'حقل :attribute يجب أن يكون رقماً.',
    'password' => [
        'letters' => 'حقل :attribute يجب أن يحتوي على حرف واحد على الأقل.',
        'mixed' => 'حقل :attribute يجب أن يحتوي على حرف كبير وحرف صغير على الأقل.',
        'numbers' => 'حقل :attribute يجب أن يحتوي على رقم واحد على الأقل.',
        'symbols' => 'حقل :attribute يجب أن يحتوي على رمز واحد على الأقل.',
        'uncompromised' => 'كلمة المرور المعطاة :attribute ظهرت في تسريب بيانات. يرجى اختيار كلمة مرور أخرى.',
    ],
    'present' => 'حقل :attribute يجب أن يكون موجوداً.',
    'present_if' => 'حقل :attribute يجب أن يكون موجوداً عندما يكون :other هو :value.',
    'present_unless' => 'حقل :attribute يجب أن يكون موجوداً إلا إذا كان :other هو :value.',
    'present_with' => 'حقل :attribute يجب أن يكون موجوداً عندما يكون :values موجوداً.',
    'present_with_all' => 'حقل :attribute يجب أن يكون موجوداً عندما تكون :values موجودة.',
    'prohibited' => 'حقل :attribute محظور.',
    'prohibited_if' => 'حقل :attribute محظور عندما يكون :other هو :value.',
    'prohibited_unless' => 'حقل :attribute محظور إلا إذا كان :other في :values.',
    'prohibits' => 'حقل :attribute يحظر وجود :other.',
    'regex' => 'صيغة حقل :attribute غير صحيحة.',
    'required' => 'حقل :attribute مطلوب.',
    'required_array_keys' => 'حقل :attribute يجب أن يحتوي على مدخلات لـ: :values.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون :other هو :value.',
    'required_if_accepted' => 'حقل :attribute مطلوب عندما يكون :other مقبولاً.',
    'required_if_declined' => 'حقل :attribute مطلوب عندما يكون :other مرفوضاً.',
    'required_unless' => 'حقل :attribute مطلوب إلا إذا كان :other في :values.',
    'required_with' => 'حقل :attribute مطلوب عندما يكون :values موجوداً.',
    'required_with_all' => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_without' => 'حقل :attribute مطلوب عندما لا يكون :values موجوداً.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا تكون أي من :values موجودة.',
    'same' => 'حقل :attribute يجب أن يطابق :other.',
    'size' => [
        'array' => 'حقل :attribute يجب أن يحتوي على :size عنصراً.',
        'file' => 'حقل :attribute يجب أن يكون :size كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون :size.',
        'string' => 'حقل :attribute يجب أن يكون :size حرفاً.',
    ],
    'starts_with' => 'حقل :attribute يجب أن يبدأ بأحد القيم التالية: :values.',
    'string' => 'حقل :attribute يجب أن يكون نصاً.',
    'timezone' => 'حقل :attribute يجب أن يكون منطقة زمنية صحيحة.',
    'unique' => 'قيمة حقل :attribute مُستخدمة من قبل.',
    'uploaded' => 'فشل في رفع حقل :attribute.',
    'uppercase' => 'حقل :attribute يجب أن يكون بأحرف كبيرة.',
    'url' => 'حقل :attribute يجب أن يكون رابطاً صحيحاً.',
    'ulid' => 'حقل :attribute يجب أن يكون ULID صحيح.',
    'uuid' => 'حقل :attribute يجب أن يكون UUID صحيح.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'identifier' => [
            'invalid_format' => 'المعرف يجب أن يكون عنوان بريد إلكتروني أو اسم مستخدم صحيح.',
        ],
        'username' => [
            'regex' => 'اسم المستخدم يجب أن يحتوي على أحرف وأرقام وشرطات وشرطات سفلية فقط.',
        ],
        'phone' => [
            'unique' => 'رقم الهاتف هذا مسجل بالفعل.',
            'exists' => 'رقم الهاتف هذا غير مسجل.',
            'invalid_format' => 'صيغة رقم الهاتف غير صحيحة.',
        ],
        'password' => [
            'current_password' => 'كلمة المرور الحالية غير صحيحة.',
            'confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ],
        'birthdate' => [
            'before' => 'يجب أن تكون 18 سنة على الأقل.',
            'date_format' => 'تاريخ الميلاد يجب أن يكون بصيغة يوم-شهر-سنة.',
        ],
        'verification_token' => [
            'required' => 'رمز التحقق مطلوب.',
            'invalid' => 'رمز التحقق غير صحيح.',
        ],
        'user_type' => [
            'in' => 'يرجى اختيار نوع مستخدم صحيح.',
        ],
        'otp_code' => [
            'required' => 'رمز التحقق مطلوب.',
            'digits' => 'رمز التحقق يجب أن يكون 6 أرقام بالضبط.',
            'invalid' => 'رمز التحقق غير صحيح أو منتهي الصلاحية.',
        ],
        'device_token' => [
            'required' => 'رمز الجهاز مطلوب للإشعارات.',
        ],
        'country_code' => [
            'required' => 'رمز البلد مطلوب.',
            'max' => 'رمز البلد طويل جداً.',
        ],
        'file' => [
            'max' => 'حجم الملف يجب ألا يتجاوز :max ميجابايت.',
            'mimes' => 'فقط ملفات :values مسموحة.',
        ],
        'image' => [
            'image' => 'الملف يجب أن يكون صورة.',
            'max' => 'حجم الصورة يجب ألا يتجاوز :max ميجابايت.',
            'dimensions' => 'أبعاد الصورة غير صحيحة.',
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
        'identifier' => 'البريد الإلكتروني أو اسم المستخدم',
        'username' => 'اسم المستخدم',
        'first_name' => 'الاسم الأول',
        'last_name' => 'اسم العائلة',
        'email' => 'عنوان البريد الإلكتروني',
        'phone' => 'رقم الهاتف',
        'country_code' => 'رمز البلد',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'current_password' => 'كلمة المرور الحالية',
        'new_password' => 'كلمة المرور الجديدة',
        'birthdate' => 'تاريخ الميلاد',
        'user_type' => 'نوع المستخدم',
        'verification_token' => 'رمز التحقق',
        'otp_code' => 'رمز التحقق',
        'device_token' => 'رمز الجهاز',
        'name' => 'الاسم',
        'subject' => 'الموضوع',
        'message' => 'الرسالة',
        'title' => 'العنوان',
        'description' => 'الوصف',
        'content' => 'المحتوى',
        'image' => 'الصورة',
        'file' => 'الملف',
        'slug' => 'الرابط الدائم',
        'status' => 'الحالة',
        'sort_order' => 'ترتيب الفرز',
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
        'is_active' => 'حالة النشاط',
        'is_enabled' => 'حالة التفعيل',
        'locale' => 'اللغة',
        'page' => 'الصفحة',
        'per_page' => 'عناصر لكل صفحة',
        'search' => 'مصطلح البحث',
        'sort_by' => 'فرز بواسطة',
    ],
];