CREATE TABLE `oauth_consumer` (
`consumer_id` int(11) NOT NULL AUTO_INCREMENT,
`consumer_key` varchar(40) NOT NULL,
`consumer_secret` varchar(40) NOT NULL,
`consumer_create_date` int(11) NOT NULL,
PRIMARY KEY (`consumer_id`)
);

# Could be emptied by a cronjonb every five minutes for each row where nonce_data < now() - 5 minutes
CREATE TABLE `oauth_nonce` (
`nonce` varchar(255) NOT NULL,
`nonce_consumer_key` varchar(40) NOT NULL,
`nonce_date` int(11) NOT NULL,
PRIMARY KEY (`nonce`)
);

# Could be emptied every hour for each row where request_token_date < now() - 60 minutes
CREATE TABLE `oauth_request_token` (
`request_token_id` int(11) NOT NULL AUTO_INCREMENT,
`request_token` varchar(40) NOT NULL,
`request_token_secret` varchar(40) NOT NULL,
`request_token_verification_code` varchar(40),
`request_token_user_id` int(11),
`request_token_date` int(11) NOT NULL,
`consumer_key` varchar(40) NOT NULL,
`callback` text NOT NULL,
`scope` text NOT NULL,
PRIMARY KEY (`request_token_id`)
);

CREATE TABLE `oauth_access_token` (
`access_token_id` int(11) NOT NULL AUTO_INCREMENT,
`access_token` varchar(40) NOT NULL,
`access_token_secret` varchar(40) NOT NULL,
`access_token_state` tinyint(1),
`access_token_user_id` int(11),
`access_token_date` int(11) NOT NULL,
`consumer_key` varchar(40) NOT NULL,
`scope` text NOT NULL,
PRIMARY KEY (`access_token_id`)
