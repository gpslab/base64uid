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
the length of the identifier).* This gives us 64<sup>10</sup> = 2<sup>60</sup> = 1 152 921 504 606 846 976 combinations.

To represent this number, imagine that in order to get all possible values of identifiers with a length of **10**
characters and generating an ID every microsecond, it takes **36 559** years.

[UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier) works on the same principle, but its main drawback
is that it's too long. It is not convenient to use it as a public identifier, for example in the URL. In order to get
the same number of combinations as the UUID, we need 2<sup>128</sup> = 64<sup>21</sup> lines 21 characters long, that
is, almost 2 times shorter than the UUID (37 characters). And if we take an identifier of the same length as the UUID,
then we get 64<sup>37</sup> = 2<sup>222</sup> against 2<sup>128</sup> for the UUID.

The most important advantage of this approach is that you ourselves control the number of combinations by changing the
length of the string and the character set. This will optimize the length of the identifier for your business
requirements.

## Collision

The probability of collision of identifiers can be calculated by the formula:

```
p(n) ≈ 1 - exp(N * (ln(N - 1) - ln(N - n)) + n * (ln(N - n) - ln(N) - 1) - (ln(N - 1) - ln(N) - 1))
```

Where
 * *N* - number of possible options;
 * *n* - number of generated keys.

Take an identifier with a length of 11 characters, like YouTube, which will give us *N* = 64<sup>11</sup> =
2<sup>66</sup> and we will get:

 * p(2<sup>25</sup>) ≈ 7.62 * 10<sup>-6</sup>
 * p(2<sup>30</sup>) ≈ 0.0077
 * p(2<sup>36</sup>) ≈ 0.9999

That is, by generating 2<sup>36</sup> = 68 719 476 736 identifiers you are almost guaranteed to get a collision.

*For calculations with large numbers, i recommend [this](https://web2.0calc.com/) online calculator.*

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

With length 6 chars (64<sup>6</sup> = 68 719 476 736 combinations).

```php
$uid = Base64UID::generate(6); // nWzfgA
```

The floating-length identifier will give more unique identifiers
(64<sup>8</sup> + 64<sup>9</sup> + 64<sup>10</sup> = 1 171 217 378 093 039 616 combinations).

```php
$uid = Base64UID::generate(random_int(8, 10));
```

You can customize charset.

```php
$charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/';
$uid = Base64UID::generate(11, $charset);

$charset = '0123456789abcdef';
$uid = Base64UID::generate(11, $charset);
```

## Domain-driven design (DDD)

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
