========
Overview
========

Changed Log
===========

**v3.2.8**
 - Fixed composer 2 deprecation notice
 - Update Web Service Production URL

**v3.2.9**
 - Refactored codebase
 - Allowed changing payment url, web service url and send billing info url programmatically
 - Can now get all available processors.

Requirements
============
 #. PHP >= 7.1
 #. SoapClient

.. _installation:


Installation
============
.. code-block:: bash

    composer require crazymeeks/dragonpay v3.2.9

Alternatively, you can specify DragonPay as a dependency in your project's
existing composer.json file:

.. code-block:: js

    {
      "require": {
         "crazymeeks/dragonpay": "^v3.2.9"
      }
   }