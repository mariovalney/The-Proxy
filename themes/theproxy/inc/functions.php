<?php

global $aes;

/** Crypt **/
$aes = new Avant\Themes\TheProxy\Inc\AES( AES_KEY );

/** Functions **/

function checkEmpty($value) {
    if (empty($value) && $value != '0') {
        $value = '-';
    }
    return $value;
}

function checkIP($string = '') {
    if ($string != '') {
        $string = explode('.', $string);
        
        if (count($string) < 4 ) { return false; }
        
        foreach ($string as $value) {
            if (!is_numeric($value)) { return false; }
            
            if ($value < 0) { return false; }
            if ($value > 255) { return false; }            
        }
    } else {
        return false;
    }
    
    return true;
}

function checkProtocol($string = '') {
    switch ($string) {
        case 'TCP':
            return true;
            break;
        case 'UDP':
            return true;
            break;
        case 'ICMP':
            return true;
            break;
        default:
            return false;
            break;
    }    
}

function checkDoor($string = '') {
    if ($string != '') {
        if (!is_numeric($string)) { return false; }

        if ($string < 0) { return false; }
        if ($string > 65536) { return false; }
    } else {
        return false;
    }
    
    return true;
}

function checkData($string = '') {
    if ($string != '') {
        if (strlen($string) != 50) { return false; }
    } else {
        return false;
    }
    
    return true;
}

function completeData($string = '') {
    if (strlen($string) <= 50) {
        $string = str_pad($string, 50);
    } else {
        $string = substr($string, 0, 50);
    }
    
    return $string;
}

function encode($string) {
    global $aes;
    $array = array();
    
    if (strlen($string) > 32) {
        array_push($array, base64_encode($aes->encrypt(substr($string, 0, 32))));
        array_push($array, base64_encode($aes->encrypt(substr($string, 32, 18))));
    } else {
        array_push($array, base64_encode($aes->encrypt($string)));
    }

    return $array;
}

function decode($array) {
    global $aes;
    $decodedArray = array();
    
    foreach ($array as $string) {
        $string = $aes->decrypt(base64_decode($string));
        array_push($decodedArray, $string);
    }
    
    return implode($decodedArray);
}