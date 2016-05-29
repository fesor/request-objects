<?php

namespace Fesor\RequestObject;

use Symfony\Component\Validator\Constraint;

interface ValidationRequiredRequest
{
    /**
     * Returns validator constraints for given request
     *
     * @return Null|Constraint|Constraint[]|array
     */
    public function rules();

    /**
     * @return string|string[] list of validation groups
     */
    public function validationGroup();
}
