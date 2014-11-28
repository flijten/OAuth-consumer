Couchbase Version
=================

This project is refactored to use Couchbase instead of MySQL.


Configuration
===============

1. Make sure you have a Couchbase server running;
2. Use the files from /src/couchbase views to create the views in your Couchbase bucket;
3. Create a scheduler that deletes the nonce documents 5 minutes after they are created (Couchbase has a special function for this, but I did not figured out how to do it yet);
4. Change host, username, password and bucket to your Couchbase details in /src/lib/Configuration.php;
5. Change the endpoints in /src/example/consumer/config.php.


Getting Started
===============
1. Generate a Consumer Key and Consumer Secret just once for your "application" (/src/example/provider/create_consumer.php). Replace the consumerKey and consumerSecret in /src/example/consumer/config.php with the variables that were returned;
2. Call /src/example/consumer/get_request_token.php. If all goes ok, you should be redirected to authorize.php.
3. Log in with username = "Jason" and password = "pas" (these are statically defined in the code for this example);
4. An outh_token and oauth_token_secret should be returned. Replace the token and tokenSecret variables in /src/example/consumer/config.php with these.
5. Make the example API call (/src/example/consumer/api_call.php). This will call the API endpoint on the provider side which should return the user_id corresponding to the Access Token been used.


OAuth Provider
==============

This repository offers an abstraction around PECL's OAuth class which makes it even more easy to setup an OAuth Provider.
If you want to start using this immediately, simply follow the minimal setup instructions. If you want to
adapt the code to suit your own datastore or framework check the customizing section.


Notes
=====

This OAuth provider is setup as an OAuth v1.0a provider. OAuth 2.0 is currently not taken into account.


Minimal setup instructions
==========================

1. Import the table definitions from the file lib/tables.sql in your database.
2. Setup the endpoint scripts in your own application
3. Setup the authorize page (simple example provided in the main folder, see authorize.php)
4. Implement oauth at the API call (simple example provided in the main folder, see api.php)


Customizing
===========

NB: This section is here purely to have the example work with the database abstraction of your choice. If you want to take this
repositories code and use it in a live implementation you're by all means welcome, but you need to use you're common sense to
change the code to fit inside your application.

1. Setup/create your own schemes. Take into account that you need to cover all fields of all models
2. overwrite the CRUD methods of all models. Also overwrite (because these 3 factory methods and the checking method also interact directly with the DataStore):
 * OAuthAccessTokenModel::loadFromToken
 * OAuthRequestTokenModel::loadFromToken
 * OAuthAccessTokenModel::loadFromConsumerKey
 * OAuthAccessTokenModel::nonceExists

3. Setup the endpoint scripts in your own application
4. Setup the authorize page (simple example provided in the main folder, see authorize.php)
5. Implement oauth at the API call (simple example provided in the main folder, see api.php)


Design defence
==============

This is a playground project, I might have made design choices you wouldn't have. I am also trying out stuff
I might dislike a week from now. There is stuff I don't like already as well:

1. Configuration::getDataStore() is called way to much. I isolated it to OAuthProviderWrapper
but inside it I simply need it at some places (callback functions) and I think it is prettier to have the whole
class behave in the same manner then have it differ from function to function.

2. The current method of having a save function in ModelBase isn't pretty as well as it forces models to
name the getter for its unique identifier getId().


Future plans
============

1. Correct responses according to: http://tools.ietf.org/html/rfc5849#section-3.2
2. Find a better way of making this code customizable for various datasources. The overwriting of all these methods isn't really pretty.
3. Add blacklisting and/or throttling for consumers
4. OOB callback support
5. Have the OAuthProviderWrapper also work under OAuth 2.0. specs
6. Find a good error reporting system. I started this out using exceptions mainly because I don't use them at work. I need to find a good way of getting the final message to the user in a nice fashion. I don't really know if I want to maintain the exceptions inside the models for instance.
7. General code cleanup
8. Do something with the points at design defence :D
9. Namespacing
