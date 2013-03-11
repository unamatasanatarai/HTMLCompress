HTMLCompress
============

Compressing of HTML inside PHP

USAGE
============
<pre>
ob_start();
(...)
echo HTMLCompress::stripWhitespace(ob_get_clean());
</pre>
