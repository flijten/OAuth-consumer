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

This is a playground project, I might have made design choices you wouldn't have. There is stuff I don't
like as well:

1. Configuration::getDataStore() is called way to much. I isolated it to OAuthProviderWrapper
but inside it I simply need it at some places (callback functions) and I think it is prettier to have the whole
class behave in the same manner the have it differ from function to function.

2. The current method of having a save function in ModelBase isn't pretty as well as it forces models to
name the getter for its unique identifier getId().


Future plans
============

1. Find a better way of making this code customizable for various datasources. The overwriting of all these methods isn't
really pretty.
2. Find a good error reporting system. I started this out using exceptions mainly because I don't use them at work.
I need to find a good way of getting the final message to the user in a nice fashion. I don't really know if I want to maintain
the exceptions inside the models for instance.
3. Clean up code. There is code left from idea's which I don't find necessary anymore as well as code from ideas
I yet have to implement. Slowly remove TODO's.
4. Do something with the points at design defence :D
