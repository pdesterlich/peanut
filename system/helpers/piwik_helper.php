<?php

	/**
	 * helper Piwik
	 *
	 * @package peanut system helpers
	 * @author Phelipe de Sterlich
	 **/
	class piwik
	{

		/**
		 * funzione generateTrackingCode
		 *
		 * @return string codice javascript per tracking sito su piwik
		 * @author Phelipe de Sterlich
		 **/
		public static function generateTrackingCode($sHost, $iTrackingCode)
		{
			$result = "";
			$result .= "<!-- Piwik -->\n";
			$result .= "<script type='text/javascript'>\n";
			$result .= "var pkBaseURL = (('https:' == document.location.protocol) ? 'https://{$sHost}/' : 'http://{$sHost}/');\n";
			$result .= "document.write(unescape('%3Cscript src='' + pkBaseURL + 'piwik.js' type='text/javascript'%3E%3C/script%3E'));\n";
			$result .= "</script><script type='text/javascript'>\n";
			$result .= "try {\n";
			$result .= "var piwikTracker = Piwik.getTracker(pkBaseURL + 'piwik.php', {$iTrackingCode});\n";
			$result .= "piwikTracker.trackPageView();\n";
			$result .= "piwikTracker.enableLinkTracking();\n";
			$result .= "} catch( err ) {}\n";
			$result .= "</script><noscript><p><img src='http://piwik.mooreasoft.net/piwik.php?idsite=2' style='border:0' alt='' /></p></noscript>\n";
			$result .= "<!-- End Piwik Tracking Code -->\n";
			
			return $result;
		}

	} // END class piwik

?>