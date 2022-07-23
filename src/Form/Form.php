<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class Form extends AbstractType
{
    protected $inputAttr = [
        'class' => 'form-control form-control-solid',
    ];

    protected $selectAttr = [
        'class' => 'form-control form-control-solid selectpicker',
        'required' => 'true',
        'data-live-search' => "true",
    ];

    protected $dateAttr = [
        'class' => 'form-control form-control-solid date',
    ];

    protected $htmlAttr = [
        'minlength' => 6,
        'maxlength' => 4096,
        'class' => 'form-control form-control-solid form-control-lg',
    ];
}