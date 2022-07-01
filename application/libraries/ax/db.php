<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once(__DIR__ . "/adodb5/adodb.inc.php");
 
DEFINE('ENV_MODE', 'PROD');

define('DBAX_HOST_DEV', '202.159.100.85');
define('DBAX_PORT_DEV', '1433');
define('DBAX_USER_DEV', 'custportal');
define('DBAX_PASS_DEV', 'Portal15');
define('DBAX_SCHE_DEV', 'MicrosoftDynamicsAX');
 
// define('DBAX_HOST_DEV', '202.159.100.86'); //Updated 05-Feb-2020
// define('DBAX_PORT_DEV', '1433'); 
// define('DBAX_USER_DEV', 'MyPortal'); //Update 05-Feb-2020 
// define('DBAX_PASS_DEV', 'Indonet2020)(*'); //Update 05-Feb-2020
// define('DBAX_SCHE_DEV', 'MicrosoftDynamicsAX');

 
define('DBAX_HOST_PROD', '202.159.100.86'); //Updated 05-Feb-2020 // server prod
//define('DBAX_HOST_PROD', '202.159.100.89'); //Updated 05-Feb-2020 // server dev
define('DBAX_PORT_PROD', '1433');
define('DBAX_USER_PROD', 'MyPortal'); //Update 05-Feb-2020 //MyPortal
define('DBAX_PASS_PROD', 'Indonet2020*&^'); //Update 05-Feb-2020
define('DBAX_SCHE_PROD', 'MicrosoftDynamicsAX');

define('DBMP_HOST_DEV', 'localhost');
define('DBMP_PORT_DEV', '3306');
define('DBMP_USER_DEV', 'devportal');
define('DBMP_PASS_DEV', 'indo2015');
define('DBMP_SCHE_DEV', 'devportal');

define('DBMP_HOST_PROD', 'localhost');
define('DBMP_PORT_PROD', '3306');
define('DBMP_USER_PROD', 'myportal');
define('DBMP_PASS_PROD', 'k0d0kg0r3nk');
define('DBMP_SCHE_PROD', 'myportal');

define('DBPOP_HOST_DEV', '117.54.9.210');
define('DBPOP_PORT_DEV', '3306');
define('DBPOP_USER_DEV', 'axbridge');
define('DBPOP_PASS_DEV', 'xSj39g3Jw83_sjeaAXw2016');
define('DBPOP_SCHE_DEV', 'vmail');
define('DBAMA_SCHE_DEV', 'amavisd');
define('DBIRE_SCHE_DEV', 'iredadmin');
define('DBAPD_SCHE_DEV', 'iredapd');

define('DBPOP_HOST_PROD', '117.54.9.210');
define('DBPOP_PORT_PROD', '3306');
define('DBPOP_USER_PROD', 'axbridge');
define('DBPOP_PASS_PROD', 'xSj39g3Jw83_sjeaAXw2016');
define('DBPOP_SCHE_PROD', 'vmail');
define('DBAMA_SCHE_PROD', 'amavisd');
define('DBIRE_SCHE_PROD', 'iredadmin');
define('DBAPD_SCHE_PROD', 'iredapd');



if (ENV_MODE === 'DEV') {
    DEFINE('AX_HOST', DBAX_HOST_DEV);
    DEFINE('AX_PORT', DBAX_PORT_DEV);
    DEFINE('AX_USER', DBAX_USER_DEV);
    DEFINE('AX_PASS', DBAX_PASS_DEV);
    DEFINE('AX_SCHE', DBAX_SCHE_DEV);

    DEFINE('MP_HOST', DBMP_HOST_DEV);
    DEFINE('MP_PORT', DBMP_PORT_DEV);
    DEFINE('MP_USER', DBMP_USER_DEV);
    DEFINE('MP_PASS', DBMP_PASS_DEV);
    DEFINE('MP_SCHE', DBMP_SCHE_DEV);

    DEFINE('POP_HOST', DBPOP_HOST_DEV);
    DEFINE('POP_PORT', DBPOP_PORT_DEV);
    DEFINE('POP_USER', DBPOP_USER_DEV);
    DEFINE('POP_PASS', DBPOP_PASS_DEV);
    DEFINE('POP_SCHE', DBPOP_SCHE_DEV);
    DEFINE('AMA_SCHE', DBAMA_SCHE_DEV);
    DEFINE('IRE_SCHE', DBIRE_SCHE_DEV);
    DEFINE('APD_SCHE', DBAPD_SCHE_DEV);
} else {
    DEFINE('AX_HOST', DBAX_HOST_PROD);
    DEFINE('AX_PORT', DBAX_PORT_PROD);
    DEFINE('AX_USER', DBAX_USER_PROD);
    DEFINE('AX_PASS', DBAX_PASS_PROD);
    DEFINE('AX_SCHE', DBAX_SCHE_PROD);

    DEFINE('MP_HOST', DBMP_HOST_PROD);
    DEFINE('MP_PORT', DBMP_PORT_PROD);
    DEFINE('MP_USER', DBMP_USER_PROD);
    DEFINE('MP_PASS', DBMP_PASS_PROD);
    DEFINE('MP_SCHE', DBMP_SCHE_PROD);

    DEFINE('POP_HOST', DBPOP_HOST_PROD);
    DEFINE('POP_PORT', DBPOP_PORT_PROD);
    DEFINE('POP_USER', DBPOP_USER_PROD);
    DEFINE('POP_PASS', DBPOP_PASS_PROD);
    DEFINE('POP_SCHE', DBPOP_SCHE_PROD);
    DEFINE('AMA_SCHE', DBAMA_SCHE_PROD);
    DEFINE('IRE_SCHE', DBIRE_SCHE_PROD);
    DEFINE('APD_SCHE', DBAPD_SCHE_PROD);
}

function newAXDB() {
//    $conn = ADONewConnection('mssql');
    $conn = ADONewConnection('mssqlnative');
//    $conn->memCache = TRUE;
//    $conn->memCachePort = 11211;
//    $conn->memCacheHost = '127.0.0.1';
    $conn->Connect(AX_HOST . ':' . AX_PORT, AX_USER, AX_PASS, AX_SCHE);

//    if (!$conn->isConnected()) {
//	error_log ($conn->errorMsg());
//    }


    return $conn;
}

function newMPDB() {
    $conn = ADONewConnection('mysqli');
    $conn->Connect(MP_HOST, MP_USER, MP_PASS, MP_SCHE);
    return $conn;
}

function newPOPDB() {
    $conn = ADONewConnection('mysqli');
    $conn->Connect(POP_HOST, POP_USER, POP_PASS, POP_SCHE);
    return $conn;
}

function generateRandomString($length = 1) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
