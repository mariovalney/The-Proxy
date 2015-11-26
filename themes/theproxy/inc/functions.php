<?php

global $aes;

/** Crypt **/
$aes = new Avant\Themes\TheProxy\Inc\AES( AES_KEY );

// Para os pacotes:

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

// Para as Regras

function checkName_forRule($string = '') {
    if ($string != '') {
        global $avdb;

        if (strlen($string) != 20) { return false; }

        $string = completeName_forRule($string);

        $allNames = $avdb->selectData('tp_regras', array('nome'));
        if (!empty($allNames)) {
            foreach ($allNames as $line) {
                if ($string == $line['nome']) {
                    return false;
                }
            }
        }
    } else {
        return false;
    }

    return true;
}

function completeName_forRule($string = '') {
    if (strlen($string) <= 20) {
        $string = str_pad($string, 20);
    } else {
        $string = substr($string, 0, 20);
    }
    
    return $string;
}

function checkPriority_forRule($string = '') {
    if ($string != '') {
        if (!is_numeric($string)) { return false; }
        if ($string <= 0) { return false; }
        if ($string > 99) { return false; }
    } else {
        return false;
    }
    
    return true;
}


function checkIP_forRule($string = '') {
    if ($string != '') {
        if ($string == '*') { return true; }

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

function checkDirection_forRule($string = '') {
    switch ($string) {
        case 'E':
            return true;
            break;
        case 'S':
            return true;
            break;
        default:
            return false;
            break;
    } 
}

function checkProtocol_forRule($string = '') {
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
        case '*':
            return true;
            break;
        default:
            return false;
            break;
    }    
}

function checkDoor_forRule($string = '') {
    if ($string != '') {
        if ($string == '*') { return true; }

        if (!is_numeric($string)) { return false; }
        if ($string < 0) { return false; }
        if ($string > 65536) { return false; }
    } else {
        return false;
    }
    
    return true;
}

function checkDoorFinal_forRule($string = '') {
    if ($string == '') {
        return true;
    } else {
        if (!is_numeric($string)) { return false; }
        if ($string < 0) { return false; }
        if ($string > 65536) { return false; }
        
        return true;
    }    
}

function checkAction_forRule($string = '') {
    switch ($string) {
        case 'P':
            return true;
            break;
        case 'B':
            return true;
            break;
        default:
            return false;
            break;
    } 
}

function checkData_forRule($string = '') {
    if ($string != '') {
        if (strlen($string) != 30) { return false; }
    } else {
        return false;
    }
    
    return true;
}

function completeData_forRule($string = '') {
    if (strlen($string) <= 30) {
        $string = str_pad($string, 30);
    } else {
        $string = substr($string, 0, 30);
    }
    
    return $string;
}

function encode_forRule($string) {
    global $aes;
    $encoded = base64_encode($aes->encrypt($string));
    return $encoded;
}

function decode_forRule($string) {
    global $aes;
    $decoded = $aes->decrypt(base64_decode($string));    
    return $decoded;
}

// Geral:

function checkEmpty($value) {
    if (empty($value) && $value != '0') {
        $value = '-';
    }
    return $value;
}

function checkIPinRange($ip, $initial, $final) {
    $ip = str_replace('.', '', $ip);
    $initial = str_replace('.', '', $initial);
    $final = str_replace('.', '', $final);

    if ($initial == '*' && $final == '*') {
        return true;
    }

    if ($initial == '*' && $final >= $ip) {
        return true;
    }

    if ($initial <= $ip && $final == '*') {
        return true;
    }

    if ($initial <= $ip && $final >= $ip) {
        return true;
    }

    return false;
}

function checkDoorinRange($door, $initial, $final) {
    if ($initial == '*') {
        return true;
    }

    if ($initial <= $door && ($final == '*' || $final == '')) {
        return true;
    }

    if ($initial <= $door && $final >= $door) {
        return true;
    }

    return false;
}