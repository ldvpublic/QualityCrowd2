QualityCrowd 2
==============

Video quality assessment with subjective testing is both time 
consuming and expensive. An interesting new approach to traditional 
testing is the so-called crowdsourcing, moving the testing effort into
the internet. We therefore propose in this contribution the 
QualityCrowd framework to effortlessly perform subjective quality 
assessment with crowdsourcing. QualityCrowd allows codec independent 
quality assessment with a simple web interface, usable with common 
web browsers.

QualityCrowd 2 differs in many points from the previous QualityCrowd software.
Instead of providing a web interface for designing and defining a test batch, 
QualityCrowd 2 introduces a text based definition of tests. Quality tests are now
defined through little QualiyCrowd-Scripts (short: QC-Scripts). The QC-Scripting language
is easy to learn and gives the the operator even more control over his test than before.

In addition QualityCrowd 2 does no longer require a databse and the complete 
rewrite of all source code allows much more flexibility and a much easier extensibility.

To keep it simple all the connections to services like Mechanical Turk, Crowdflower and
Amazon S3 have been removed. Instead a system of worker ids and tokens is used:

 - the worker browses a URL constructed like this:

	`http://qualitycrowdserver.example/<batchid>/<workerid>`

 - after finishing the task the worker is told a token which he can use to prove
 that he has completed this task

This method is quite common on platforms like microWorkers.com and can also 
equally be used on e.g. Mechanical Turk.


Requirements
------------

 - HTTP server, perferably Apache httpd
 - PHP, minimum 4.3.2, recommended >= 5.2

Qualitycrowd 2 has been tested with Apache httpd 2.2.14 and PHP 5.3.1

Setup
-----

1. Place all files in a directory inside your webservers document root.
2. Make sure the webserver user has write permissions in this directory.
3. Browse to `.../setup/` with your web browser.
4. If any errors are displayed fix them and refresh the `.../setup/`.
5. Remove the /setup directory.
6. You may want to change the `$securitySalt` variable to a random string of your choice in `/core/config.php` and set a secure admin password in the same file.
7. Done. You can acces the admin interface by browsing to the install directory and entering your password.

License
-------

If you use QualityCrowd 2 for your research project, please cite the 
following paper in your publication:

> C. Keimel, J. Habigt, C. Horch, and K. Diepold, "QualityCrowd - <br />
> A Framework for Crowd-based Quality Evaluation," in Picture Coding <br />
> Symposium 2012 (PCS 2012), May 2012, pp. 245-248

 - Preprint: https://mediatum.ub.tum.de/doc/1098076/1098076.pdf
 - BibTeX: https://mediatum.ub.tum.de/export/1098076/bibtex

For the full license see the LICENSE file.
