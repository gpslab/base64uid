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

You can customize charset.

```php
$charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/';
$uid = Base64UID::generate(11, $charset);

$charset = '0123456789abcdef';
$uid = Base64UID::generate(11, $charset);
```

### Other algorithms for generate UID

#### Random char

Generate random characters of a finite UID from a charset.

```php
$generator = new RandomCharGenerator();
$uid = $generator->generate(); // iKtwBpOH2E
```

Limit the length of the UID and the charset.

```php
$charset = '0123456789abcdef';
$generator = new RandomCharGenerator(6, $charset);
$uid = $generator->generate(); // fa6c7d
```

#### Random bytes

Generate random bytes and encode it in Base64.

```php
$generator = new RandomBytesGenerator();
$uid = $generator->generate(); // YCfGKBxd9k4
```

```php
$generator = new RandomBytesGenerator(5);
$uid = $generator->generate(); // Mm7dpkM
```

#### Encoded random bits

Generate bitmap with random bits and encode it in Base64.
The bitmap length is 64 bits and it require 64-bit mode of processor architecture.

```php
$binary_generator = new RandomBinaryGenerator(32);
$encoder = new HexToBase64BitmapEncoder();
$generator = new EncodeBitmapGenerator($binary_generator, $encoder);
$uid = $generator->generate(); // 7MWx2BuWJUw
```

#### Encoded bitmap of time

Generate bitmap with current time in microseconds and encode it in Base64.
The bitmap length is 64 bits and it require 64-bit mode of processor architecture.

```php
$binary_generator = new TimeBinaryGenerator();
$encoder = new HexToBase64BitmapEncoder();
$generator = new EncodeBitmapGenerator($binary_generator, $encoder);
$uid = $generator->generate(); // koLfRhzAoI0
$uid = $generator->generate(); // zALfRhzAovg
$uid = $generator->generate(); // 18LfRhzAoQw
```

Generated bitmap has a structure:

```
{first bit}{random prefix}{current time}{random suffix}
```

* *first bit* - bitmap limiter for fixed size of bitmap;
* *prefix* - random bits used in prefix of bitmap. The length of the generated bits can be configured from `$prefix_length`;
* *time* - bits of current time in microseconds. 
* *suffix* - random bits used in suffix of bitmap. The length is calculated from `64 - 1 - $prefix_length - $time_length`.

Responsibly select the number of bits allocated to store the current time. The `$time_length` defines the limit of the
stored date:

| Bits limit | Maximum available bitmap | Unix Timestamp | Date |
|---|---|---|---|
| 40-bits | `1111111111111111111111111111111111111111`      | `1099511627775`  | 2004-11-03 19:53:48 (UTC) |
| 41-bits | `11111111111111111111111111111111111111111`     | `2199023255551`  | 2039-09-07 15:47:36 (UTC) |
| 42-bits | `111111111111111111111111111111111111111111`    | `4398046511103`  | 2109-05-15 07:35:11 (UTC) |
| 43-bits | `1111111111111111111111111111111111111111111`   | `8796093022207`  | 2248-09-26 15:10:22 (UTC) |
| 44-bits | `11111111111111111111111111111111111111111111`  | `17592186044415` | 2527-06-23 06:20:44 (UTC) |
| 45-bits | `111111111111111111111111111111111111111111111` | `35184372088831` | 3084-12-12 12:41:29 (UTC) |

To reduce the size of the saved time, you can use a `$time_offset` that allows you to move the starting point of time:

| Offset microseconds | Offset date | Maximum available date for 41-bits |
|---|---|---|
| 0             | 1970-01-01 00:00:00 (UTC) | 2039-09-07 15:47:36 (UTC) |
| 1577836800000 | 2020-01-01 00:00:00 (UTC) | 2089-09-06 15:47:36 (UTC) |

#### Encoded bitmap of floating time

It is similar to the previous generator `TimeBinaryGenerator`, but the position with bits of the current time is
floating. That is, the length of the prefix and suffix is randomly generated each time. Simultaneously generated
identifiers have less similarity, but the likelihood of collision increases.

```php
$binary_generator = new FloatingTimeGenerator();
$encoder = new HexToBase64BitmapEncoder();
$generator = new EncodeBitmapGenerator($binary_generator, $encoder);
$uid = $generator->generate(); // 5mqhb6MPH7g
$uid = $generator->generate(); // kFvow8joJys
$uid = $generator->generate(); // 8QRC30YeP3E
```

#### Snowflake-id

Snowflake-id use time in microseconds, data center index and machine index. This allows you to customize the
generator to your environment and reduce the likelihood of a collision, but the identifiers are very similar to each
other and the identifier reveals the scheme of your internal infrastructure. Snowflake-id used in Twitter, Instagram, etc. 

```php
$generator_id = 0; // value 0-1023
$binary_generator = new SnowflakeGenerator($generator_id);
$encoder = new HexToBase64BitmapEncoder();
$generator = new EncodeBitmapGenerator($binary_generator, $encoder);
$uid = $generator->generate(); // gBFKQeuAAAA
$uid = $generator->generate(); // gBFKQeuAAAE
$uid = $generator->generate(); // gBFKQevAAAA
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
