Composer Compatibility Check
============================

This check determine if the [Contao Composer Client](https://github.com/contao-community-alliance/composer) can be used
in the tested environment. To run the check, download the
[composer-check.php](https://raw.githubusercontent.com/contao-community-alliance/composer-check/master/composer-check.php)
(**YES, nothing else is required!**),
open your browser and navigate to the file `http://example.com/path/to/composer-check.php`.

Installation
------------

### General

Open [composer-check.php](https://raw.githubusercontent.com/contao-community-alliance/composer-check/master/composer-check.php)
in your browser, press `Ctrl+S` or use the Menu `File -> Save` to save the file.
Put the `composer-check.php` in your Contao installation or upload it via FTP (**Warning: always use BINARY Transfer!!!**)
or something similar.

### Unix

```
wget https://raw.githubusercontent.com/contao-community-alliance/composer-check/master/composer-check.php \
     -O composer-check.php
```

Debugging
---------

If you have trouble running the check, try the not optimised/obfuscated
[composer-check-dbg.php](https://raw.githubusercontent.com/contao-community-alliance/composer-check/master/composer-check-dbg.php).

You also can clone this repository and navigate to its root with your browser `http://example.com/path/to/composer-check/`.
