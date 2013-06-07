Welcome to the MinnowFramework
==============================
Development for this project has been halted due to lack of traction and sufficiently better javascript frameworks using NodeJS (such as Express, Meteor, Ember, Angular, etc). If you'd like to continue this project on your own, please fork it without regret.

Short list of pros:
-------------------

+ MVC and extensible class structures.
+ Easy form validation.
+ RequireJS, Modernizr, jQuery, Bootstrap, CanJS and Google Analytics.
+ Code completion support in Eclipse/Zend.
+ Automatic data formatting by file extension in url for JSON, XML, and HTML.
+ Log in and registration support for Facebook, MySpace, AOL, Google, Twitter, Windows Live, FourSquare and more included through HybridAuth.
+ Bundled support for MySQL, SQLite, Memcached, S3, Postmark, and Instagram currently included.
+ Simple image manipulation through Imagine.
+ Simple video transcoding through FFMPEG.
+ Database session support.
+ Postmark support by default so your emails never hit spam.
+ Web GUI for scaffolding pages from sitemaps, objects and classes from database structures, and dynamic form generation. 
+ Security focused code using secure encrypted cookies, hashed password storage using PBKDF2, and prepared statements in PDO calls.
+ Configurable environmental settings are stored in ini files for easy deployment and customization.
+ UTF8 for multilingual support.
+ Clean syntax and legible naming convention. 
+ Scalable foundation code with bundled support for Memcached.

Shorter list of cons:
---------------------

- Requires PHP 5.4
- Will not utilize namespace support in PHP until [namespace support](https://bugs.php.net/bug.php?id=47472) is fixed.
- This framework is newly open sourced and may contain typos or lack some features in more mature frameworks.
- This project needs more documentation, and more people to write it.

> For more information on Minnow, please view our Demo site here: http://minnow.badpxl.com/

To install Minnow:
------------------

* [Download a copy of the latest Minnow release](https://github.com/jeffreytgilbert/MinnowFramework/archive/master.zip)
* Unzip Minnow into the folder where you host your web sites.
* Unzip the [Settings.zip](https://github.com/jeffreytgilbert/MinnowFramework/blob/master/Settings.zip?raw=true) file included in your download to a folder you'd like to store your application settings in. This folder should not be accessable to the web, but PHP does need permissions to access/read it. 
* Edit your settings files to reflect your current working environment.
* Open the db folder and import the schema.sql file into your MySQL DB.
* Edit your Apache's httpd.conf file to point your document root to the www folder (this folder can be named anything to reflect subdomains/cnames.
* Restart Apache

Alternative install instructions for Windows/FusionLeaf developers:
-------------------------------------------------------------------

Directions:
-----------

* Run FusionLeaf Studio.exe
* Click Start
* Browse to http://localhost

> Location of ZIP: https://code.google.com/p/fusionleaf/downloads/list

That should be it if you have all the requirements installed and set up correctly. Message me on github if you have any trouble and I'll help walk you through it.

<a href='http://www.pledgie.com/campaigns/19118'><img alt='Click here to lend your support to: MinnowFramework development and make a donation at www.pledgie.com !' src='http://www.pledgie.com/campaigns/19118.png?skin_name=chrome' border='0' /></a> ![MinnowFramwork](http://minnow.badpxl.com/img/Minnow-Framework-logo-icon.png) 
