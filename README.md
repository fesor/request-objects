Symfony Request Objects
===========================

[![Build Status](https://travis-ci.org/fesor/request-objects.svg?branch=master)](https://travis-ci.org/fesor/request-objects)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fesor/request-objects/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fesor/request-objects/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/fesor/request-objects/v/stable)](https://packagist.org/packages/fesor/request-objects)
[![Total Downloads](https://poser.pugx.org/fesor/request-objects/downloads)](https://packagist.org/packages/fesor/request-objects)
[![License](https://poser.pugx.org/fesor/request-objects/license)](https://packagist.org/packages/fesor/request-objects)

**Note**: This library should not be considered as production ready until 1.0 release.
Please provide your feedback to make it happen!

## Why?

Symfony Forms component is a very powerful tool for handling forms. But nowadays things have changed.
Complex forms are handled mostly on the client side. As for simple forms `symfony/forms` has very large overhead.

And in some cases you just don't have forms. For example, if you are developing an HTTP API, you probably just
need to interact with request payload. So why not just wrap request payload to some user defined object and
validate just it? This also encourages separation of concerns and will help you in case of API versioning.

## Usage

First of all, we need to install this package via composer:

```
composer require fesor/request-objects
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

Bundle doesn't require any additional configurations, but you could also specify error response
provider service in bundle config. We will back to this in "Handle validation errors" section.

### Define your request objects

All user defined requests should extended from `Fesor\RequestObject\RequestObject`. Let's create simple
request object for user registration action:

```php
use Fesor\RequestObject\Request;

class RegisterUserRequest extends RequestObject
{
    public function rules()
    {
        return new Assert\Collection([
            'email' => new Assert\Email(['message' => 'Please fill in valid email']),
            'password' => new Assert\Length(['min' => 4, 'minMessage' => 'Password is to short']),
            'first_name' => new Assert\NotNull(['message' => 'Please provide your first name']),
            'last_name' => new Assert\NotNull(['message' => 'Please provide your last name'])
        ]);
    }
}
```

After that we can just use it in our action:

```php
public function registerUserAction(RegisterUserRequest $request)
{
    // Do Stuff! Data is already validated!
}
```

This bundle will bind validated request object to argument `$request`. Request object has very simple interface
 for data interaction. It very similar to Symfony's request object but considered immutable by default (but you
 can add some setters if you wish so)

```php
// returns value from payload by specific key or default value if provided
$request->get('key', 'default value');

// returns whole payload
$request->all();
```

### Where payload comes from?

This library has default implementation of `PayloadResolver` interface, which acts this way:

1) If request can has body (i.e. it is POST, PUT, PATCH or whatever request with body)
it uses union of `$request->request->all()` and `$request->files->all()` arrays as payload.

2) If request can't has body (i.e. GET, HEAD verbs) then it uses `$request->query->all()`.

If you wish to apply custom logic for payload extraction, you could implement `PayloadResolver` interface within
your request object:

```php
class CustomizedPayloadRequest extends RequestObject implements PayloadResolver
{
    public function resolvePayload(Request $request)
    {
        $query = $request->query->all();
        // turn string to array of relations
        if (isset($query['includes'])) {
            $query['includes'] = explode(',', $query['includes']);
        }

        return $query;
    }
}
```

This will allow you to do some crazy stuff with your requests and DRY a lot of stuff.


### Validating payload

As you can see from previous example, method `rules` should return validation rules for [symfony validator](http://symfony.com/doc/current/book/validation.html).
Your request payload will be validated against it and you will get valid data in your action.

If you have some validation rules which depends of payload data, then you can handle it via validation groups.

**Please note**: due limitations in `Collection` constraint validator it is not so handy to use groups.
 So instead it is recommended to use `Callback` validator in tricky cases with dependencies on payload data.
 See [example](examples/Request/ContextDependingRequest.php) for details about problem.

You may provide validation group by implementing `validationGroup` method:

```php
public function validationGroup(array $payload)
{
    return isset($payload['context']) ?
        ['Default', $payload['context']] : null;
}
```

### Handle validation errors

If validated data is invalid, library will throw exception which wil contain validation errors and request object.

But if you don't want to handle it via `kernel.exception` listener, you have several options.

First is to use your controller action to handle errors:

```php

public function registerUserAction(RegisterUserRequest $request, ConstraintViolationList $errors)
{
    if (0 !== count($errors)) {
        // handle errors
    }
}

```

But this not so handy and will break DRY if you just need to return common error response. Thats why
library provide you `ErrorResponseProvider` interface. You can impllement it in you request object and move this
code to `getErrorResponse` method:

```php
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
```

## More examples

If you still not sure is it useful for you, please see `examples` directory for more use cases.
Didn't find your case? Then share your use case in issues!

## Contribution

Fill free to give feedback and feature requests or post issues. PR's are welcomed!
