Symfony Request Objects
===========================

[![Build Status](https://travis-ci.org/fesor/request-objects.svg?branch=master)](https://travis-ci.org/fesor/request-objects)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fesor/request-objects/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fesor/request-objects/?branch=master)

**Note**: This library should not be considered as production ready until 1.0 release.
Please provide your feedback to make it happen!

## Why?

Symfony Forms component is a very powerful tool for handling forms. But nowadays things have changed.
Complex forms are handled mostly on client side. As for simple forms symfony/forms has very large overhead.

And in some cases you just don't have forms. For example, if you are developing HTTP API, you probably just
need to interact with request payload. So why not just wrap request payload to some user defined object
and validate just it? This also encourages separation of concerns and will help you in case of API versioning.

## Usage

First of all we need to install this package via composer:

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

All user defined requests should extended from `Fesor\RequestObject\Request`. Let's create simple
request object for user registration action:

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

After that we can just use it in our action:

```php
public function registerUserAction(RegisterUserRequest $request)
{
    // Do Stuff! Data is already validated!
}
```

This bundle will bind validated request object to argument `$request`. Request object has very simple interface
 for data interaction. It very similar to symfony's request object but considered immutable by default (but you
 can add some setters if you wish so)

```
// returns value from payload by specific key or default value if provided
$request->get('key', 'default value');

// returns whole payload
$request->all();
```

### Validating payload

As you can see from previous example, method `rules` should return validation rules for symfony validator.
Your request payload will be validated against it and you will get valid data in your action.

### Handle validation errors

TODO

## Contribution

At least provide feedback! Write issues! And i will be happy to see any PRs. Thanks.
