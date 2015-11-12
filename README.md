# moneybird-php-client

PHP Client for Moneybird V2

**Work in progress!**

## Installation
Will be on Packagist/Composer soon.

## Usage
You need to have to following credentials and information ready. You can get this from your Moneybird account.
- Client ID
- Client Secret
- Callback URL

You need to be able to store some data locally:
- The three credentials mentioned above
- Authorizationcode
- Accesstoken

### Authorization code
If you have no authorization code yet, you will need this first. The client supports fetching the authorization code as follows.

```php
<?php

$connection = new \Picqer\Financials\Moneybird\Connection();
$connection->setRedirectUrl(REDIRECTURL);
$connection->setClientId(CLIENTID);
$connection->setClientSecret(CLIENTSECRET);
$connection->redirectForAuthorization();
```

This will perform a redirect to Moneybird at which you can login and authorize the app for a specific Moneybird administration.
After login, Moneybird will redirect you to the callback URL with request param "code" which you should save as the authorization code.

### Normal actions
After you have the authorization code as described above, you can perform normal requests. The client will take care of the accesstoken
automatically.

```php
<?php

$connection = new \Picqer\Financials\Moneybird\Connection();
$connection->setRedirectUrl(REDIRECTURL);
$connection->setClientId(CLIENTID);
$connection->setClientSecret(CLIENTSECRET);

// Get authorization code as described in readme (always set this when available)
$connection->setAuthorizationCode(AUTHORIZATIONCODE);

// Set this in case you got the access token, otherwise client will fetch it (always set this when available)
$connection->setAccessToken(ACCESSTOKEN);

try {
    $connection->connect();
} catch (\Exception $e) {
    throw new Exception('Could not connect to Moneybird: ' . $e->getMessage());
}

// After connection save the last access token for reuse 
$connection->getAccessToken(); // will return the access token you need to save

// Set up a new Moneybird instance and inject the connection
$moneybird = new \Picqer\Financials\Moneybird\Moneybird($connection);

// Example: Fetch list of salesinvoices 
$salesInvoices = $moneybird->salesInvoice->get();
var_dump($salesInvoices); // Array with SalesInvoice objects

// Example: Fetch a sales invoice
$salesInvoice = $moneybird->salesInvoice->find(3498576378625);
var_dump($salesInvoice); // SalesInvoice object

// Example: Create a new contact
$contact = $moneybird->contact();

$contact->company_name = 'Picqer';
$contact->firstname = 'Stephan';
$contact->lastname = 'Groen';
$contact->save();

// Example: Update existing contact, change email address
$contact = $moneybird->contact()->find(89672345789233);
$contact->email = 'example@example.org';
$contact->save();

// Example: Use the Moneybird synchronisation API
$contactVersions = $moneybird->contact()->listVersions();
var_dump($contactVersions); // Array with ids and versions to compare to your own

// Example: Use the Moneybird synchronisation API to get new versions of specific ids
$contacts = $moneybird->contact()->getVersions([
  2389475623478568,
  2384563478959922
]);
var_dump($contacts); // Array with two Contact objects
```

## Code example
See for example: [example/example.php](example/example.php)

## TODO
- Filtering
- Receiving webhooks support (would be nice)
- Some linked/nested entities (notes, attachments etcetera)
- Dedicated Exception for RateLimit reached and return of Retry-After value
