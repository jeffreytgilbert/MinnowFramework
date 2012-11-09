MinnowFramework
===============

A tiny PHP framework for catching big results

===============

Why build another framework?

I've written a lot of code over the last couple decades, and while I've seen a number of egomaniacs claiming that their framework or library is the end-all-be-all of code repositories, I'm of the mind that there is no magical master tool to handle every solution perfectly. That said, I, like many other developers out there, want to rewrite less code over time, improbably love PHP, and intend to continue writing sites and services with it to kickstart some of my crazier business ideas. Hopefully, this code allows you to do something or gives you example code you can utilize in your projects, and you don't end up with this classic workflow: researching, developing, realizing you did something horribly unmaintainable or stupid, rearchitecting, rebuilding, seeing some new shiny way of doing things, rebuilding, rebuilding, and rebuilding.

Minnow Framework Goals:

* Bundle functionality into the framework for handling common tasks such as multilingual translations, data export apis, and common connectors likes MySQL, SQLite, and S3. 
* Include an example site with each copy of the framework which handles logins, messages, emailing notices, and roles and permissions.
* Within the example site code, hooks for common social platforms (like Facebook Connect) and services should be authored in to help developers kickstart their projects. 
* Eliminate the use of eval in code, so the framework can potentially be compiled into HipHop byte code further optimizing it, while also eliminating potential security issues.
* Eliminate use of global variables in methods and functions, so code is more maintainable and more portable, and preventing possible conflicts with other libraries.
* Enhance code completion in Eclipse IDEs to allow faster authoring of well written code. This should cut down on using arrays as parameter lists, allowing for clear to read and understand code and better code completion options in the future.
* Create easily portable data objects which can be exported in various formats from web controllers without rewriting code to provide APIs faster and more standardized in implementation.
* Enhance security through the use of prepared queries for escaping data and default output filter types for different data formats (e.g. $o->getHTML('field_name'), $o->getBoolean('field_name') ).
* Reduce the need for developers to alter the framework by allow developers to extend native functionality of classes like Model & ModelCollections as part of the customization layer.
* Provide GUI tools for generating site files, forms, SQL requests, and controller logic in a way that makes them easily understandable and customizable without having to know what kind of "magic" is happening behind the scenes.
* Limit dependencies on Pear and PHP add ons to allow for easy deployment of the framework without tons of server administration and configuration time.
* Encourage using best practices keeping environmental and configuration settings files out of the source repository, which makes it easier to update site code on many different platforms and makes it easier to pass the repository code around without worrying about passwords being attached to a version of the source.
* Eliminate "automagical" thought and verbiage in as many places as possible and replace it with easy to follow code conventions to reduce the need for online documentation while coding.
* Use MVC and accessor conventions so code can be generated in a standard way, maintained easily over time, and reused on different projects.
* Keep the framework simple so it can be easily understood by anyone wanting to enhance it through open source development, but providing conventions which encourage good practices and avoid sloppy code and scope creepage.

If it seems ambitious, it has been. If it seems a needlessly stupid, I've done stupider. If it seems right up your alley, please jump in, fork it, and lets write some sexy code.

--JG