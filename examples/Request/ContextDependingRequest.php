<?php

namespace Fesor\RequestObject\Examples\Request;

use Fesor\RequestObject\RequestObject;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ContextDependingRequest
 * @package Fesor\RequestObject\Examples\Request
 *
 * Please note that this example is more a hack
 * than real solution due limitations of `Collection`
 * validator... Please consider to use CallbackValidator
 * for cases like this. Or you can make some helper-function.
 */
class ContextDependingRequest extends RequestObject
{
    public function rules()
    {
        return [
            // Add required fileds
            $this->collection([
                'buz' => new Assert\Type('string'),
                'context' => new Assert\Optional(
                    new Assert\Choice(['first', 'second'])
                ),
                // to be sure that no extra fields allowed by default
                'foo' => new Assert\Optional(),
                'bar' => new Assert\Optional()
            ]),
            // add fields required within "first" validation groups
            $this->collection([
                'foo' => new Assert\Type('string'),
            ], ['groups' => ['first'], 'allowExtraFields' => true]),
            // add fields required within "second" validation groups
            $this->collection([
                'bar' => new Assert\Type('string'),
            ], ['groups' => ['second'], 'allowExtraFields' => true]),
        ];
    }

    public function validationGroup(array $payload)
    {
        return isset($payload['context']) ?
            ['Default', $payload['context']] : null;
    }

    private function collection($fields, array $options = null)
    {
        if (!$options) {
            $options = [];
        }
        $options['fields'] = array_map(function ($constraints) use ($options) {
            if ($constraints instanceof Assert\Existence || !array_key_exists('groups', $options)) {
                return $constraints;
            }

            return new Assert\Required([
                'constraints' => $constraints,
                'groups' => $options['groups']
            ]);
        }, $fields);

        return new Assert\Collection($options);
    }
}
