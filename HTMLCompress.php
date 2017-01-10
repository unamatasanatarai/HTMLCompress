<?php

class HTMLCompress
{

    public static function stripWhitespace($buffer)
    {
        $chunks = preg_split('/(<pre.*?\/pre>)|(<script.*?\/script>)/ms', $buffer, -1, PREG_SPLIT_DELIM_CAPTURE);
        $buffer = '';
        $replace = [
            '#[\n\r\t\s]+#'                                                                   => ' ',
            // remove new lines & tabs
            '#>\s{2,}<#'                                                                      => '><',
            // remove inter-tag whitespace
            '#\/\*.*?\*\/#i'                                                                  => '',
            // remove CSS & JS comments
            '#<!--(?![\[>]).*?-->#si'                                                         => '',
            // strip comments, but leave IF IE (<!--[...]) and "<!-->""
            '#\s+<(html|head|meta|style|/style|title|script|/script|/body|/html|/ul|/ol|li)#' => '<$1',
            // before those elements, whitespace is dumb, so kick it out!!
            '#\s+(/?)>#'                                                                      => '$1>',
            // just before the closing of " >"|" />"
            '#class="\s+#'                                                                    => 'class="',
            // at times, there is whitespace before class=" className"
            '#(script|style)>\s+#'                                                            => '$1>',
            // <script> var after_tag_has_whitespace = 'nonsens';
            ///
            /// http://stackoverflow.com/a/29363569/2119863
            ///
            //remove tabs before and after HTML tags
            '/\>[^\S ]+/s'                                                                    => '>',
            '/[^\S ]+\</s'                                                                    => '<',
            //shorten multiple whitespace sequences; keep new-line characters because they matter in JS!!!
            '/([\t ])+/s'                                                                     => ' ',
            //remove leading and trailing spaces
            '/^([\t ])+/m'                                                                    => '',
            '/([\t ])+$/m'                                                                    => '',
            // remove JS line comments (simple only); do NOT remove lines containing URL (e.g. 'src="http://server.com/"')!!!
            '~//[a-zA-Z0-9 ]+$~m'                                                             => '',
            //remove empty lines (sequence of line-end and white-space characters)
            '/[\r\n]+([\t ]?[\r\n]+)+/s'                                                      => "\n",
            //remove empty lines (between HTML tags); cannot remove just any line-end characters because in inline JS they can matter!
            '/\>[\r\n\t ]+\</s'                                                               => '><',
            //remove "empty" lines containing only JS's block end character; join with next line (e.g. "}\n}\n</script>" --> "}}</script>"
            '/}[\r\n\t ]+/s'                                                                  => '}',
            '/}[\r\n\t ]+,[\r\n\t ]+/s'                                                       => '},',
            //remove new-line after JS's function or condition start; join with next line
            '/\)[\r\n\t ]?{[\r\n\t ]+/s'                                                      => '){',
            '/,[\r\n\t ]?{[\r\n\t ]+/s'                                                       => ',{',
            //remove new-line after JS's line end (only most obvious and safe cases)
            '/\),[\r\n\t ]+/s'                                                                => '),',
            //remove quotes from HTML attributes that does not contain spaces; keep quotes around URLs!
            '~([\r\n\t ])?([a-zA-Z0-9]+)="([a-zA-Z0-9_/\\-]+)"([\r\n\t ])?~s'                 => '$1$2=$3$4',
            //$1 and $4 insert first white-space character found before/after attribute
        ];
        $search = array_keys($replace);
        foreach ($chunks as $c) {
            if (strpos($c, '<pre') !== 0 && strpos($c, '<script') !== 0) {
                $c = preg_replace($search, $replace, $c);
            } elseif (strpos($c, '<pre') !== 0) {
                $c = preg_replace('#[\n\r\t]+#', '', $c);
                $c = preg_replace('#[\s]{2,}#', '', $c);
            }
            $buffer .= $c;
        }

        $remove = [
            '</option>',
            '</li>',
            '</dt>',
            '</tr>',
            '</dd>',
            '</th>',
            '</td>',
        ];
        $buffer = str_ireplace($remove, '', $buffer);

        return $buffer;
    }

}
