# This file contains the table definitions necessary for using the OAuth provider class out of the box.

CREATE TABLE `oauth_consumer` (
`consumer_id` int(11) NOT NULL AUTO_INCREMENT,
`consumer_key` varchar(40) NOT NULL,
`consumer_secret` varchar(40) NOT NULL,
`consumer_create_date` int(11) NOT NULL,
PRIMARY KEY (`consumer_id`)
);

# Could be emptied by a cronjonb every five minutes for each row where nonce_data < now() - 5 minutes
CREATE TABLE `oauth_provider_nonce` (
`nonce` varchar(255) NOT NULL,
`nonce_consumer_key` varchar(40) NOT NULL,
`nonce_date` int(11) NOT NULL,
PRIMARY KEY (`nonce`)
);

# Could be emptied every hour for each row where request_token_date < now() - 60 minutes
CREATE TABLE `oauth_provider_request_token` (
`request_token_id` int(11) NOT NULL AUTO_INCREMENT,
`request_token_token` varchar(30) NOT NULL,
`request_token_secret` varchar(10) NOT NULL,
`request_token_verification_code` varchar(40),
`request_token_user_id` int(11),
`request_token_date` int(11) NOT NULL,
`request_token_consumer_key` varchar(40) NOT NULL,
`request_token_callback` text NOT NULL,
`request_token_scope` text NOT NULL,
PRIMARY KEY (`request_token_id`)
);
#TODO unique keys op tokens?
CREATE TABLE `oauth_provider_access_token` (
`access_token_id` int(11) NOT NULL AUTO_INCREMENT,
`access_token` varchar(30) NOT NULL,
`access_token_secret` varchar(10) NOT NULL,
`access_token_state` tinyint(1),
`access_token_user_id` int(11),
`access_token_date` int(11) NOT NULL,
`access_token_consumer_key` varchar(40) NOT NULL,
`access_token_scope` text NOT NULL,
PRIMARY KEY (`access_token_id`)
);
