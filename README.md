# Trustpilot authenticated encryption for PHP

Library for authenticated encryption used with Trustpilot.

# Installation

```
composer require notrix/trustpilot-links
```

# Usage

Include the Trustpilot class, and invoke it:

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
