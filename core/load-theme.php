<?php

/* 
 * Load_Theme: Responsible for treat the request and call the correspondent theme files.
 * 
 * @package Avant
 */
namespace Avant\Core;

use Avant\Url_Mapping;

class Load_Theme {
    private $reservedFiles = array(
        'index', 'header', 'footer'
    );
    
    public function __construct($request)
    {
        $this->loadLanguages();
        
        $GLOBALS['v1'] = FALSE;

        if (isset($request['params'][0])) {

            
            if ( $request['params'][0] == 'v1') {
                $GLOBALS['v1'] = TRUE;
                $newrequest = array('0' => 'index');
                foreach ($request['params'] as $key => $value) {
                    if ($key > 0) {
                        if (!empty($value)) {
                            $newrequest[($key - 1)] = $value;
                        } else if ($key == 1) {
                            $newrequest[0] = 'index';
                        }
                    }
                }
                $request['params'] = $newrequest;
                $templateFile = 'v1-' . $request['params'][0];
            } else {
                $templateFile = $request['params'][0];
            }

            if ($this->isReserved($templateFile)) {
                $templateFile = $templateFile . '-2';
            }
        } else {
            $templateFile = 'index';
            $GLOBALS['avant']['page'] = 'index';
        }
                
        if (is_readable(THEME_PATH . $templateFile . '.php')) {
            include THEME_PATH . $templateFile . '.php';
        } else if (is_readable(THEME_PATH . '404.php') && (!DEBUG)){
            $GLOBALS['avant']['page'] = '404';
            include THEME_PATH . '404.php';
        } else {
            // Die with an error message
            die("I can't find the <code>" . $templateFile . ".php</code> file.");
        }
    }
    
    private function isReserved($fileName)
    {
        if (in_array($fileName, $this->reservedFiles)) {
            return true;
        } else {
            return false;
        }
    }
    
    private function loadLanguages()
    {
        _textdomain(THEME, ROOT . THEMES_DIR . DS . THEME . DS . 'languages');
    }
    
    public function __destruct()
    {
        _textdomain('avant');
    }
}