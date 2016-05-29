Symfony Request Objects
===========================

[![Build Status](https://travis-ci.org/fesor/request-objects.svg?branch=master)](https://travis-ci.org/fesor/request-objects)

**Note**: Please note that this is very unstable solution so be careful with it. And please give your feedback!
I appreciate it.

## Why?

Most of Symfony developers uses `symfony/forms` to map request data to some objects.
This object then validates with `symfony/validation` and system start to work with data.

Symfony/forms is great component which simplifies life alot when you are dealing with forms.
But what if you don't have any forms? For example you are developing HTTP API and you already have
pretty much structured request data, which should be validated.

FosRest bundle provides `ParamFetcher`, but it doesn't allow to validate all request and will fail on
first failed constraint.

Spring and Laravel has diffrent implementation of the same ideas: map request data to some object
and validate it at front-controller level. So why not in Symfony?

## Installation

Use composer:

```
composer require fesor/request-objects:dev-master@dev
```

And register bundle:

```
    public function registerBundles()
    {
        $bundles = [
            // ...
            new \Fesor\RequestObject\Bundle\RequestObjectBundle(),
        ];
    }
```

And that's it!

## Usage

Just create your request object extended from `Fesor\RequestObject\Request`, defined validation rules:

```php
use Fesor\RequestObject\Request;

class RegisterUserRequest extends Request
{
    public function rules()
    {
        return new Assert\Collection([
            'email' => new Assert\Email(['message' => 'Please fill in valid email']),
            'password' => new Assert\Length(['min' => 4, 'minMessage' => 'Password is to short']),
            'first_name' => new Assert\NotBlank(['message' => 'Please provide your first name']),
            'last_name' => new Assert\NotBlank(['message' => 'Please provide your last name'])
        ]);
    }
}
```

And then we can use it in our controllers:

```php
public function registerUserAction(RegisterUserRequest $request)
{
    $data = $request->all();

    // do real stuff! Data is valid!
}
```

This library automatically resolves request by reflection, fill it with data and validate over defined rules.
If request data is invalid then exception will be thrown.

If you want to generate custom error response instead of relying on global error controller, just implement
`ErrorResponseProvider` interface in your request:

```
class ExtendedRegisterUserRequest extends RegisterUserRequest implements ErrorResponseProvider
{
    public function getErrorResponse(ConstraintViolationListInterface $errors)
    {
        return new JsonResponse([
            'message' => 'Please check your data',
            'errors' => array_map(function (ConstraintViolation $violation) {

                return [
                    'path' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage()
                ];
            }, iterator_to_array($errors))
        ], 400);
    }

}
```

In case of invalid request data method `getErrorResponse` will be called. Here you can create your
 custom error responses. Also you can move this to some base class to remove code duplication.

## Contribution

At least provide feedback! Write issues! And i will be happy to see any PRs. Thanks.
