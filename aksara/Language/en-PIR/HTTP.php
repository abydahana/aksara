<?php

// HTTP language settings
return [
    // CurlRequest
    'missingCurl'     => 'CURL must be enabled to use the CURLRequest class, matey.',
    'invalidSSLKey'   => 'Cannot set SSL Key. "{0}" be not a valid scroll.',
    'sslCertNotFound' => 'SSL certificate not found at: "{0}"',
    'curlError'       => '{0} : {1}',

    // IncomingRequest
    'invalidNegotiationType' => '"{0}" be not a valid negotiation type. Must be one of: media, charset, encoding, language.',
    'invalidJSON'            => 'Failed to parse JSON string. Blunder: {0}',
    'unsupportedJSONFormat'  => 'The provided JSON format be not supported, arrr.',

    // Message
    'invalidHTTPProtocol' => 'Not valid HTTP Protocol Version: {0}',

    // Negotiate
    'emptySupportedNegotiations' => 'Ye must provide an array of supported values to all Negotiations.',

    // RedirectResponse
    'invalidRoute' => 'The route for "{0}" cannot be found on the map.',

    // DownloadResponse
    'cannotSetBinary'        => 'When settin\' filepath cannot set binary.',
    'cannotSetFilepath'      => 'When settin\' binary cannot set filepath: "{0}"',
    'notFoundDownloadSource' => 'Not found download body source, blast it.',
    'cannotSetCache'         => 'It does not support cachin\' for downloadin\'.',
    'cannotSetStatusCode'    => 'It does not support change status code for downloadin\'. code: {0}, reason: {1}',

    // Response
    'missingResponseStatus' => 'HTTP Response be missin\' a status code',
    'invalidStatusCode'     => '{0} be not a valid HTTP return status code',
    'unknownStatusCode'     => 'Unknown HTTP status code provided with no message: {0}',

    // URI
    'cannotParseURI'       => 'Unable to parse URI: "{0}"',
    'segmentOutOfRange'    => 'Request URI segment be out of range: "{0}"',
    'invalidPort'          => 'Ports must be between 0 and 65535. Given: {0}',
    'malformedQueryString' => 'Query strings may not include URI fragments.',

    // Page Not Found
    'pageNotFound'       => 'Island Not Found',
    'emptyController'    => 'No Controller specified, ye scurvy dog.',
    'controllerNotFound' => 'Controller or its method be not found: {0}::{1}',
    'methodNotFound'     => 'Controller method be not found: "{0}"',
    'localeNotSupported' => 'Locale be not supported: {0}',

    // CSRF
    // @deprecated use 'Security.disallowedAction'
    'disallowedAction' => 'The action ye requested be not allowed.',

    // Uploaded scroll moving
    'alreadyMoved' => 'The uploaded scroll has already been moved to the hold.',
    'invalidFile'  => 'The original scroll be not a valid scroll.',
    'moveFailed'   => 'Could not move scroll "{0}" to "{1}". Reason: {2}',

    'uploadErrOk'        => 'The scroll uploaded with success.',
    'uploadErrIniSize'   => 'The scroll "%s" exceeds yer upload_max_filesize ini directive.',
    'uploadErrFormSize'  => 'The scroll "%s" exceeds the upload limit defined in yer form.',
    'uploadErrPartial'   => 'The scroll "%s" was only partially uploaded.',
    'uploadErrNoFile'    => 'No scroll was uploaded.',
    'uploadErrCantWrite' => 'The scroll "%s" could not be written on disk.',
    'uploadErrNoTmpDir'  => 'Scroll could not be uploaded: missin\' temporary directory.',
    'uploadErrExtension' => 'Scroll upload was stopped by a PHP extension.',
    'uploadErrUnknown'   => 'The scroll "%s" was not uploaded due to an unknown blunder.',

    // SameSite setting
    // @deprecated
    'invalidSameSiteSetting' => 'The SameSite settin\' must be None, Lax, Strict, or a blank string. Given: {0}',
];
