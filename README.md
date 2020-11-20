# php-oauth2-example

This is a example code to perform OAuth 2.0 authentication and interact with Xero's API without a SDK.

You'll be able to connect to a Xero Organisation and make real API calls - we recommend you connect to the Demo company.

## Getting Started
To run locally, you'll need a local web server with PHP support.  
* MAMP is a good option [Download MAMP](https://www.mamp.info/en/downloads/) 

### Download this code
* Clone this repo into your local server webroot. i.e. `htdocs`
* Launch a terminal app and change to the newly cloned folder `php-oauth2-example`
* Download dependencies with Composer using the following command:

```
composer install
```

## Create a Xero App
To obtain your API keys, follow these steps and create a Xero app

* Create a [free Xero user account](https://www.xero.com/us/signup/api/) (if you don't have one)
* Login to [Xero developer center](https://developer.xero.com/myapps)
* Click "New App" link
* Enter your App name, company url, privacy policy url.
* Enter the redirect URI (your callback url - i.e. `http://localhost:8888/php-oauth2-example/index.php`)
* Agree to terms and condition and click "Create App".
* Click "Generate a secret" button.
* Copy your client id and client secret and save for use later.
* Click the "Save" button. You secret is now hidden.

## Configure API keys
You'll need to update your code where ever there is a clientId, clientSecret or redirectUri

- index.php

Sample PHP code from authorization.php
```php

$clientId = '__YOUR_CLIENT_ID__';
$clientSecret = '__YOUR_CLIENT_SECRET__';
$redirectUri = 'http://localhost:8888/php-oauth2-example/index.php';

$provider = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => $clientId,   
    'clientSecret'            => $clientSecret,
    'redirectUri'             => $redirectUri,
    'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
    'urlAccessToken'          => 'https://identity.xero.com/connect/token',
    'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Invoices'
]);


```
## Take it for a spin
Launch your browser and navigate to http://localhost:8888/php-oauth2-example/ (or whatever the correct path is). 

- The authorization flow will start automatically when you visit the above URL. (your index.php)
- Grant access to your user account and select the Demo company to connect to.
- The callback page is the same index.php and will execute the correct block of code to obtain an access token and call the organisation endpoint.

## License

This software is published under the [MIT License](http://en.wikipedia.org/wiki/MIT_License).

	Copyright (c) 2020 Xero Limited

	Permission is hereby granted, free of charge, to any person
	obtaining a copy of this software and associated documentation
	files (the "Software"), to deal in the Software without
	restriction, including without limitation the rights to use,
	copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the
	Software is furnished to do so, subject to the following
	conditions:

	The above copyright notice and this permission notice shall be
	included in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
	OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
	HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
	WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
	OTHER DEALINGS IN THE SOFTWARE.
