<?php


namespace Drupal\to_pdf;


class Detector {

    public static function _detect_clear_version($version) {
        $version = preg_replace('/[^0-9,.,a-z,A-Z-]/','',$version);
        return substr($version, 0, strpos($version, '.'));
    }

    public static function detect_user_browser() {
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $classes = array();

        // Add ie extra class with the version number
        $ie_pattern = '/(?:\b(ms)?ie\s+|\btrident\/7\.0;.*\s+rv:)(\d+)/';
        $ie_matches = array();
        $ie_m = preg_match($ie_pattern, $agent, $ie_matches);

        if ($ie_m === 1) {
            $classes[] = 'ie';

            if (isset($ie_matches[2])) {
                $classes[] = 'ie' . $ie_matches[2];
            }
        }

        if (stristr($agent, 'opera') !== FALSE) {
            $classes[] = 'opera';
            $aresult = explode('/', stristr($agent, 'version'));
            if(isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                $classes[] = 'opera' . self::_detect_clear_version($aversion[0]);
            }
        }

        // Check for chrome desktop first, then chrome mobile, lastly check for
        // safari, as these are mutually exclusive.
        if (stristr($agent, 'chrome') !== FALSE) {
            $classes[] = 'chrome';
            $aresult = explode('/', stristr($agent, 'chrome'));
            $aversion = explode(' ', $aresult[1]);
            $classes[] = 'chrome' . self::_detect_clear_version($aversion[0]);
        }
        elseif (stristr($agent, 'crios') !== FALSE) {
            $classes[] = 'chrome';

            $aresult = explode('/', stristr($agent, 'crios'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                $classes[] = 'chrome' . self::_detect_clear_version($aversion[0]);
            }
        }
        elseif (stristr($agent, 'safari') !== FALSE) {
            $classes[] = 'safari';

            $aresult = explode('/', stristr($agent, 'version'));
            if(isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                $classes[] = 'safari' . self::_detect_clear_version($aversion[0]);
            }
        }

        if (stristr($agent, 'netscape') !== FALSE) {
            $classes[] = 'netscape';
            if (preg_match('/navigator\/([^ ]*)/', $agent, $matches)) {
                $classes[] = 'netscape' . self::_detect_clear_version($matches[1]);
            }
            elseif (preg_match('/netscape6?\/([^ ]*)/', $agent, $matches)) {
                $classes[] = 'netscape' . self::_detect_clear_version($matches[1]);
            }
        }

        if (stristr($agent, 'firefox') !== FALSE) {
            $classes[] = 'ff';

            if(preg_match("/firefox[\/ \(]([^ ;\)]+)/", $agent, $matches)) {
                $classes[] = 'ff' . self::_detect_clear_version($matches[1]);
            }
        }

        if (stristr($agent, 'konqueror') !== FALSE) {
            $classes[] = 'konqueror';
            $aresult = explode(' ', stristr($agent, 'konqueror'));
            $aversion = explode('/', $aresult[0]);
            $classes[] = 'konqueror' . self::_detect_clear_version($aversion[1]);
        }

        if (stristr($agent, 'dillo') !== FALSE) {
            $classes[] = 'dillo';
        }

        if (stristr($agent, 'chimera') !== FALSE) {
            $classes[] = 'chimera';
        }

        if (stristr($agent, 'beonex') !== FALSE) {
            $classes[] = 'beonex';
        }

        if (stristr($agent, 'aweb') !== FALSE) {
            $classes[] = 'aweb';
        }

        if (stristr($agent, 'amaya') !== FALSE) {
            $classes[] = 'amaya';
        }

        if (stristr($agent, 'icab') !== FALSE) {
            $classes[] = 'icab';
        }

        if (stristr($agent, 'lynx') !== FALSE) {
            $classes[] = 'lynx';
        }

        if (stristr($agent, 'galeon') !== FALSE) {
            $classes[] = 'galeon';
        }

        if (stristr($agent, 'opera mini') !== FALSE) {
            $classes[] = 'operamini';

            $resultant = stristr($agent, 'opera mini');
            if(preg_match('/\//', $resultant)) {
                $aresult = explode('/', $resultant);
                $aversion = explode(' ', $aresult[1]);
                $classes[] = 'operamini' . self::_detect_clear_version($aversion[0]);
            }
            else {
                $aversion = explode(' ', stristr($resultant, 'opera mini'));
                $classes[] = 'operamini' . self::_detect_clear_version($aversion[1]);
            }
        }

        return $classes;
    }
    public static function detect_server_is_windows() {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Get the user's operating system
     *
     * @param   string  $userAgent  The user's user agent
     *
     * @return  string  Returns the user's operating system as human readable string,
     *  if it cannot be determined 'n/a' is returned.
     */
public static function detect_user_OS() {
        $userAgent=$_SERVER['HTTP_USER_AGENT'];
        // Create list of operating systems with operating system name as array key
        $oses = array (
            'iPhone'            => '(iPhone)',
            'Windows 3.11'      => 'Win16',
            'Windows 95'        => '(Windows 95)|(Win95)|(Windows_95)',
            'Windows 98'        => '(Windows 98)|(Win98)',
            'Windows 2000'      => '(Windows NT 5.0)|(Windows 2000)',
            'Windows XP'        => '(Windows NT 5.1)|(Windows XP)',
            'Windows 2003'      => '(Windows NT 5.2)',
            'Windows Vista'     => '(Windows NT 6.0)|(Windows Vista)',
            'Windows 7'         => '(Windows NT 6.1)|(Windows 7)',
            'Windows NT 4.0'    => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
            'Windows ME'        => 'Windows ME',
            'Open BSD'          => 'OpenBSD',
            'Sun OS'            => 'SunOS',
            'Linux'             => '(Linux)|(X11)',
            'Safari'            => '(Safari)',
            'Mac OS'            => '(Mac_PowerPC)|(Macintosh)',
            'QNX'               => 'QNX',
            'BeOS'              => 'BeOS',
            'OS/2'              => 'OS/2',
            'Search Bot'        => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)'
        );

        // Loop through $oses array
        foreach($oses as $os => $preg_pattern) {
            // Use regular expressions to check operating system type
            if ( preg_match('@' . $preg_pattern . '@', $userAgent) ) {
                // Operating system was matched so return $oses key
                return $os;
            }
        }

        // Cannot find operating system so return Unknown

        return 'n/a';
    }
}