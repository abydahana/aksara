<?php

// Email language settings
return [
    'mustBeArray'           => 'The message validation method must be passed an array, matey.',
    'invalidAddress'        => 'Not valid message address: "{0}"',
    'attachmentMissing'     => 'Unable to locate the followin\' message attachment: "{0}"',
    'attachmentUnreadable'  => 'Unable to open this attachment, blast it: "{0}"',
    'noFrom'                => 'Cannot send message with no "From" header, arrr.',
    'noRecipients'          => 'Ye must include recipients: To, Cc, or Bcc, ye scallywag.',
    'sendFailurePHPMail'    => 'Unable to send message using PHP mail(). Yer ship might not be rigged to send messages using this method.',
    'sendFailureSendmail'   => 'Unable to send message using Sendmail. Yer ship might not be rigged to send messages using this method.',
    'sendFailureSmtp'       => 'Unable to send message using SMTP. Yer ship might not be rigged to send messages using this method.',
    'sent'                  => 'Yer message has been successfully sent usin\' the followin\' protocol: {0}',
    'noSocket'              => 'Unable to open a socket to Sendmail. Please check yer compass.',
    'noHostname'            => 'Ye did not specify a SMTP hostname, matey.',
    'SMTPError'             => 'The followin\' SMTP blunder was encountered: {0}',
    'noSMTPAuth'            => 'Blunder: Ye must assign an SMTP username and password.',
    'invalidSMTPAuthMethod' => 'Blunder: SMTP authorization method "{0}" be not supported in codeigniter, set either "login" or "plain" authorization method',
    'failureSMTPAuthMethod' => 'Unable to initiate AUTH command. Yer ship might not be rigged to use AUTH {0} authentication method.',
    'SMTPAuthCredentials'   => 'Failed to authenticate yer credentials. Blunder: {0}',
    'SMTPAuthUsername'      => 'Failed to authenticate username. Blunder: {0}',
    'SMTPAuthPassword'      => 'Failed to authenticate password. Blunder: {0}',
    'SMTPDataFailure'       => 'Unable to send yer treasure: {0}',
    'exitStatus'            => 'Exit status code: {0}',
    // @deprecated
    'failedSMTPLogin' => 'Failed to send AUTH LOGIN command. Blunder: {0}',
];
