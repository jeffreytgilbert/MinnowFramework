**www**: Customizable webroot. Point your apache instance here. Note, the webroot should not contain your sites source code.

* **.htaccess**: Required for routing requests in Apache. Apache must have mod_rewrite available.
* **index.php**: All controller requests are routed through here. You can change the location of the Source folder and Settings folder in this file.
* **static.php**: Handles static file requests for images, javascript, and css. These are meant to host themed page elements, not dynamic content.
* **humans.txt**: A human readable credits document provided by HTML5Boilerplate.
* **robots.txt**: A search engine readable document for including or excluding pages from the search engines view.
* **favicon.ico**: A sample favorites icon. It should be noted that these are being phased out by some browsers for security/authenticity reasons.
* **crossdomain.xml**: A cross domain file for handling Adobe Flash requests to your APIs. By default, everything is accessable.
* **apple-touch-icon-etc.png**: Sample icons for Apple iOS devices which show up when making bookmarks on the home screen.

**Source**: Contains the framework and your sites source as well as addons you can install to add functionality to your sites code.

* **Applications**: You may store one or more applications in your application folder, however multiple applications are currently a test feature and should not be used for production code. This ability was added for futureproofing.
* * **YourApplication**: The name of your application. This can be left as is, but it's recommended you rename your application to the parent domain name.
* * * **Actions**: This contains the sugar methods for converting data into standard DataObject and DataCollections.
* * * * **Required**: These files will be automatically loaded on each page request.
* * * * **Optional**: These files can be loaded from your page controller as needed.
* * * * **Actions.php**: Edit this file to add sugar methods from connections, or write your own convenience methods and store them here.
* * * **Models**: Contains data objects which mimic strongly typed objects used in OOP languages. All Objects inherit from a single Model class.
* * * * **Required**: These files will be automatically loaded on each page request.
* * * * **Optional**: These files can be loaded from your page controller as needed.
* * * * **DataObject.php**: Edit this to add your own methods to all your custom data objects.
* * * * **DataCollection.php**: Edit this to add your own methods to all your custom data collections.
* * * **Controllers**: Controllers handle authentication, business logic, and output formats.
* * * * **Pages**: Contains the controllers which handle all page requests.
* * * * * **IndexPage.php**: A sample page which can be called from supported formats.
* * * * * **Err404Page.php**: An example page for 404s which is used when a controller cannot be loaded or found.
* * * * * **Scaffold-etc.php**: These pages will generate code or files based on a sitemap, the database, and forms you create in example formats.
* * * * **PageController.php**: Customize page requests and methods in all your controllers with authentication systems, convenience methods, menu logic, etc.
* * * **Views**: All your view code is stored here. These files are generally PHP or HTML files, but third party support can be added to handle templates.
* * * * **Pages**: All page request views here.
* * * * **Emails**: All outgoing email templates here.
* * * * **Themes**: The themes for your pages should be added here. No need for header / footer documents.
* * * * **Fragments**: Primarily for menus and includes which aren't page requests, but can be considered "Widgets" or Menus.
* **AddOns**: The extensibility layer of the framework. Third party software can be easily added as connections or helpers with relative ease, and use of settings documents is encouraged for easy deployment to multiple environments.
* * **Connections**: Used for managing multiple connections to different data services, for instance MySQL, Memcached, SQLite, or 3rd parties like AmazonS3, Instagram, etc.
* * **Connections.php**: A simple, standard looking "copy paste" away from adding a new connection type. In future iterations of the framework, this can be handled by a GUI control panel.
* * **Example**: Your connection (for instance, MySQL) would have a folder which matches the connection type referenced in the Connections.php file.
* * **Requirements**: The library or required files which need to be loaded should be stored here.
* * **ExampleConnection.php**: The Connection class handles lazy loading of the requirements for each connection, its settings, instantiation, and destruction.
* * **ExampleActions.php**: Convenience methods (syntactic sugar) which serve to convert data into DataObject and DataCollections classes for the framework. These are loaded from the Actions.php class.
* * **example.ini**: If no settings file is found for this connection type, this example ini will be displayed to the developer/user along with the path it should be at.
* * **Helpers**: Singleton style operation for handling single tasks. Helpers should be used when multiple instances aren't required (ex: Sessions, HTML Helper methods, etc).
* * **Helpers.php**: A simple, standard looking "copy paste" away from adding new helper types. Installing new helpers can be handled by a GUI in future iterations of this framework.
* * **Example**: The helper class you create should have the same folder name and appear the same in the Helpers.php file.
* * **Requirements**: All library or required files for the helper should be included here.
* * **ExampleHelper.php**: Handles the initialization, settings, and destruction of the helper.
* * **example.ini**: If no settings file is found for this helper type, this example ini will be displayed to the developer/user along with the path it should be at.
* * **Formats**: Easily add support for other data formats to export from your controllers. 
* * **SampleFormat.php**: Includes a trait class and an interface for defining which methods are needed for each data format, and how they'll be called. These can be overwritten by each controller.
* * **Widgets**: tbd...
* * tbd...
* **Framework**: Contains all files in the framework. These files should not be changed by developers. Changing these potentially breaks compatibility with future framework updates.

**Temp**: A temp folder included as a convenience for devs to store files. Currently has no ties to the framework so it can be ignored or removed.

**js**: Contains all js and is linked to by the static.php file.

* **Pages**: Contains javascript files mapped to the View folder and controller folder structures.
* * **Index.js**: Sample js for the index page.
* * **Scaffold.js**: Used on the scaffolding pages to build forms and controller logic dynamically.
* **Libraries**: A folder created for jquery, modernizr, bootstrap, and other js libraries. 
* **default.js**: Included with every page request

**css**: Contains all css and is linked to by the static.php file

* **Pages**: Contains css files mapped to the View folder an	d controller folder structures.
* * **Index.css**: Sample style for the index page.
* * **Scaffold.css**: Used by the scaffolding pages
* **Libraries**: A folder created for jquery, modernizr, bootstrap, and other js libraries. 
* **default.css**: Included with every page request

**db**: A folder for storing the database schemas and files. SQLite databases can also be stored here.

**img**: Static files can be stored here. 