Symfony Request Objects
===========================

**Note**: Please note that this is very unstable solution so be careful with it. And please give your feedback!
I appreciate it.

Most of Symfony developers uses `symfony/forms` to map request data to some objects.
This object then validates with `symfony/validation` and system start to work with data.

Symfony/forms is great component which simplifies life alot when you are dealing with forms.
But what if you don't have any forms? For example you are developing HTTP API and you already have
pretty much structured request data, which should be validated.

FosRest bundle provides `ParamFetcher`, but it doesn't allow to validate all request and will fail on
first failed constraint.

Spring and Laravel has diffrent implementation of the same ideas: map request data to some object
and validate it at front-controller level. So why not in Symfony?

## Usage

For example we have simple registration action:

```php
public function registerUserAction(Request $request)
{
    $data = $request->request->all();
    $errors = $this->get('validator')->validate($data, new Assert\Collection([
        'email' => new Assert\Email(['message' => 'Please fill in valid email']),
        'password' => new Assert\Length(['min' => 4, 'minMessage' => 'Password is to short']),
        'first_name' => new Assert\NotBlank(['message' => 'Please provide your first name']),
        'last_name' => new Assert\NotBlank(['message' => 'Please provide your last name'])
    ]));

    if (0 !== count($errors) {
        $this->failWithInvalidRequestData($errors);
    }

    // do real stuff
}
```

This is very annoying... Our controllers became messy, there is possibility of code duplication with
pretty similar requests over your API. So... let's move this validation stuff to separate object which
extended from custom request object:

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

And then we can just use in in our controllers:

```php
public function registerUserAction(RegisterUserRequest $request)
{
    $data = $request->all();

    // do real stuff! Data is valid!
}
```

And that's all! No configuration needed!

## Contribution

At least provide feedback! Write issues! And i will be happy to see any PRs. Thanks.
