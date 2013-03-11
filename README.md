HTMLCompress
============

Compressing of HTML inside PHP

USAGE
============
ob_start();
(...)
echo HTMLCompress::stripWhitespace(ob_get_clean());
