<?php
require __DIR__ . '/vendor/autoload.php';

session_start();

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

// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {

    $options = [
    	'scope' => ['openid email profile offline_access accounting.transactions accounting.settings accounting.contacts']
    ];

    // Fetch the authorization URL from the provider; this returns the
    // urlAuthorize option and generates and applies any necessary parameters (e.g. state).
    $authorizationUrl = $provider->getAuthorizationUrl($options);

    // Get the state generated for you and store it to the session.
    $_SESSION['oauth2state'] = $provider->getState();

    // Redirect the user to the authorization URL.
    header('Location: ' . $authorizationUrl);
    exit();

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');

// Redirect back from Xero with code in query string param
} else {

    try {
        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // We have an access token, which we may use in authenticated requests 
        // Retrieve the array of connected orgs and their tenant ids.      
        $options['headers']['Accept'] = 'application/json';
        $connectionsResponse = $provider->getAuthenticatedRequest(
            'GET',
            'https://api.xero.com/Connections',
            $accessToken->getToken(),
            $options
        );

        $xeroTenantIdArray = $provider->getParsedResponse($connectionsResponse);
        
        echo "<h1>Congrats</h1>";
        echo "access token: " . $accessToken->getToken() . "<hr>";
        echo "refresh token: " . $accessToken->getRefreshToken() . "<hr>";
        echo "xero tenant id: " . $xeroTenantIdArray[0]['tenantId'] . "<hr>";

        // The provider provides a way to get an authenticated API request for
        // the service, using the access token; 
        // the xero-tentant-id header is required
        // the accept header can be either 'application/json' or 'application/xml'
        $options['headers']['xero-tenant-id'] = $xeroTenantIdArray[0]['tenantId'];
        $options['headers']['Accept'] = 'application/xml';        
        
        $request = $provider->getAuthenticatedRequest(
            'GET',
            'https://api.xero.com/api.xro/2.0/Organisation',
            $accessToken,
            $options
        );
        
        echo 'Organisation details:<br><textarea width: "300px"  height: 150px; row="50" cols="40">';
        var_export($provider->getParsedResponse($request));
        echo '</textarea>';


        $data = "<Contacts><Contact><Name>ABC Limited</Name></Contact></Contacts>";
        $options['body'] = $data ;

        $contactRequest = $provider->getAuthenticatedRequest(
            'PUT',
            'https://api.xero.com/api.xro/2.0/Contacts',
            $accessToken,
            $options
        );

        echo '<br><hr><br>New Contact:<br><textarea width: "300px"  height: 150px; row="50" cols="40">';
        var_export($provider->getParsedResponse($contactRequest));
        echo '</textarea>';

    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
        // Failed to get the access token or user details.
        exit($e->getMessage());
    }
}

?>

<html>
<head>
	<title>php oauth2 example</title>
    <style>
        textarea { border:1px solid #999999;  width:75%; height: 75%;  margin:5px 0; padding:3px;  }
    </style>
</head>
<body>
<h3>Success!</h3>
</body>
</html>