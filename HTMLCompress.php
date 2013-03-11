<?php 
class HTMLCompress{

  public static function stripWhitespace($buffer)
	{
		$chunks = preg_split( '/(<pre.*?\/pre>)/ms', $buffer, -1, PREG_SPLIT_DELIM_CAPTURE );
		$buffer = '';
		$replace = array(
			'#[\n\r\t\s]+#'           => ' ',  // remove new lines & tabs
			'#>\s{2,}<#'              => '><', // remove inter-tag whitespace
			'#\/\*.*?\*\/#i'          => '',   // remove CSS & JS comments
			'#<!--(?![\[>]).*?-->#si' => '',   // strip comments, but leave IF IE (<!--[...]) and "<!-->""
			'#\s+<(html|head|meta|style|/style|title|script|/script|/body|/html|/ul|/ol|li)#' => '<$1', // before those elements, whitespace is dumb, so kick it out!!
			'#\s+(/?)>#' => '$1>', // just before the closing of " >"|" />"
			'#class="\s+#'=> 'class="', // at times, there is whitespace before class=" className"
			'#(script|style)>\s+#' => '$1>', // <script> var after_tag_has_whitespace = 'nonsens';
		);
		$search = array_keys($replace);
		foreach ( $chunks as $c )
		{
			if ( strpos( $c, '<pre' ) !== 0 )
			{
				$c = preg_replace($search, $replace, $c);
			}
			$buffer .= $c;
		}
		return $buffer;
	}
}
