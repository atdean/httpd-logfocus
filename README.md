# httpd-logslice

This is a work-in-progress PHP library and command-line utility designed to parse and filter Apache log files quickly and easily. For now, it is focused on processing standard combined-format access logs, but I plan to expand this to allow error log parsing as well as custom formats.

This is still very much in the early stages of development and is being worked on as a personal project. You're welcome to experiment with the code or submit pull requests. More detailed information and usage instructions will be made available as the project moves closer to a first release state.

Filtering options and alternate modes have not yet been implemented. Stay tuned.

## Setup

After ensuring that [Composer](https://www.getcomposer.org) in installed, clone the repository and run `composer install` to install dependencies.

This package utilizes version 4 of the Symfony's framework's Console module, and therefore requires PHP 7.1.3 or greater.

## Usage

`bin/httpd-logslice logfile:filter path/to/file`

This will output to the console a dump of data parsed from each entry. This is current debug behavior, as I experiment with different methods of processing the data and structuring the application. The included dummy access log file can be used as an example.
