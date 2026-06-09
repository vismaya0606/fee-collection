<?php

return [
    'accepted' => 'The :attribute field must be accepted.',
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute field must be a valid email address.',
    'min' => [
        'numeric' => 'The :attribute field must be at least :min.',
        'string' => 'The :attribute field must be at least :min characters.',
    ],
    'max' => [
        'numeric' => 'The :attribute field must not be greater than :max.',
        'string' => 'The :attribute field must not be greater than :max characters.',
    ],
    'unique' => 'The :attribute has already been taken.',
    'exists' => 'The selected :attribute is invalid.',
    'numeric' => 'The :attribute field must be a number.',
    'integer' => 'The :attribute field must be an integer.',
    'date' => 'The :attribute field must be a valid date.',
    'in' => 'The selected :attribute is invalid.',
    'between' => [
        'numeric' => 'The :attribute field must be between :min and :max.',
    ],
    'after' => 'The :attribute field must be a date after :date.',
    'date_format' => 'The :attribute field must match the format :format.',
    'attributes' => [],
];
