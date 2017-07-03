# Trustpilot authenticated encryption for PHP

Library for authenticated encryption used with Trustpilot.

# Installation

```
composer require notrix/trustpilot-links
```

# Usage

Autoload with composer or include the Trustpilot class and invoke it:

### Business Generated Links
```php
    // Domain of your site
    $domain = 'example.com';

    // Get the base64 encoded keys from the Trustpilot site:
    $encryptKey = 'dfkkdfj....';
    $authKey = 'dj83lshi....';

    // The payload with your order data:
    $payload = array(
        'email' => 'john@doe.com',
        'name'  => 'John Doe',
        'ref'   => '1234',
    );

    $trustpilot = new Trustpilot($domain, $encryptKey, $authKey);
    $trustpilotInvitationLink = $trustpilot->getInvitationLink($payload);

    // https://www.trustpilot.com/evaluate-bgl/example.com?p=cGF5bG9hZA==
```

### General Link for embedding reviews iframe
```php
    // Domain of your site
    $domain = 'example.com';

    $trustpilot = new Trustpilot($domain);
    $trustpilotInvitationLink = $trustpilot->getReviewsLink();

    // https://www.trustpilot.com/evaluate/example.com
```

### Unique Link for embedding reviews iframe
```php
    // Domain of your site
    $domain = 'example.com';

    // Get the Unique Link Secret Key from your Account Manager at Trustpilot
    $secretKey = 'xfkcdfu....';

    // Order information
    $reference = '1234';
    $email = 'john@doe.com';
    $name = 'John Doe';

    $trustpilot = new Trustpilot($domain, null, null, $secretKey);
    $trustpilotInvitationLink = $trustpilot->getUniqueLink($reference, $email, $name);

    // https://www.trustpilot.com/evaluate/example.com?a=1234&b=am9obkBkb2UuY29t&c=John+Doe&e=e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4
```
