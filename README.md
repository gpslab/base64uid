[![Latest Stable Version](https://img.shields.io/packagist/v/gpslab/base64uid.svg?maxAge=3600&label=stable)](https://packagist.org/packages/gpslab/base64uid)
[![Total Downloads](https://img.shields.io/packagist/dt/gpslab/base64uid.svg?maxAge=3600)](https://packagist.org/packages/gpslab/base64uid)
[![Build Status](https://img.shields.io/travis/gpslab/base64uid.svg?maxAge=3600)](https://travis-ci.org/gpslab/base64uid)
[![Coverage Status](https://img.shields.io/coveralls/gpslab/base64uid.svg?maxAge=3600)](https://coveralls.io/github/gpslab/base64uid?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/gpslab/base64uid.svg?maxAge=3600)](https://scrutinizer-ci.com/g/gpslab/base64uid/?branch=master)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/0feb22b7-b64d-462d-b8ba-da49e548be70.svg?maxAge=3600&label=SLInsight)](https://insight.sensiolabs.com/projects/0feb22b7-b64d-462d-b8ba-da49e548be70)
[![StyleCI](https://styleci.io/repos/94885299/shield?branch=master)](https://styleci.io/repos/94885299)
[![License](https://img.shields.io/packagist/l/gpslab/base64uid.svg?maxAge=3600)](https://github.com/gpslab/base64uid)

# Base64 UID

Generate UID like YouTube.

## Introduction

The library generates a unique identifier consisting of 64 characters and a length of 10 characters *(you can change
the length of the identifier).* This gives us a lot of combinations.

```
64^10 = 2^60 = 1 152 921 504 606 846 976 (combinations)
```

To represent this number, imagine that in order to get all possible values of identifiers with a length of **10**
characters and generating an ID every microsecond, it takes **36 559** years.

[UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier) works on the same principle, but its main drawback
is that it's too long. It is not convenient to use it as a public identifier, for example in the URL.

Due to the fact that **Base64 UID** uses 64 chars instead of 36, the identifier turns out to be noticeably shorter.
Also you have the opportunity to manage the long identifier and the number of possible values. This will optimize the length of the identifier for your business requirements.

## Installation

Pretty simple with [Composer](http://packagist.org), run:

```sh
composer require gpslab/base64uid
```

## Usage

```php
use GpsLab\Component\Base64UID\Base64UID;

$uid = Base64UID::generate(); // iKtwBpOH2E
```

With length 6 chars

```php
// 64^6 = 68 719 476 736 (combinations)
$uid = Base64UID::generate(6); // nWzfgA
```

The floating-length identifier will give more unique identifiers.

```php
// 64^10 + 64^9 + 64^8 = 1 171 217 378 093 039 616 (combinations)
$uid = Base64UID::generate(random_int(8, 10));
```

## DDD

How to usage in your [domain](https://en.wikipedia.org/wiki/Domain-driven_design).

For example create a `ArticleId` ValueObject:

```php
class ArticleId
{
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function id()
    {
        return $this->id;
    }
}
```

Repository interface for Article:

```php
interface ArticleRepository
{
    public function nextId();

    // more methods ...
}
```

Concrete repository for Article:

```php
use GpsLab\Component\Base64UID\Base64UID;

class ConcreteArticleRepository implements ArticleRepository
{
    public function nextId()
    {
        return new ArticleId(Base64UID::generate());
    }

    // more methods ...
}
```

Now we can create a new entity with `ArticleId`:

```php
$article = new Article(
    $repository->nextId(),
    // more article parameters ...
);
```

## License

This bundle is under the [MIT license](http://opensource.org/licenses/MIT). See the complete license in the file: LICENSE
