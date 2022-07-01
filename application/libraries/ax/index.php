<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once(__DIR__ . '/db.php');

global $axdb;
define('TBL_CINFO', 'IDN_CUSTINFOPORTAL');
define('TBL_CINVOICE', 'IDN_CUSTINVOICEPORTAL');
define('TBL_CBACC', 'CUSTBANKACCOUNT');
define('TBL_CPROD', 'IDN_CUSTPRODUCTPORTAL');
define('TBL_CTRANS', 'CUSTTRANS');
define('TBL_CAGING', 'IDN_CUSTAGINGPORTAL');
define('TBL_CUNAME', 'IDN_USERNAMEAX');
define('TBL_CUBAL', 'IDN_CUSTBALANCE');
define('TBL_CUDOM', 'IDN_CUSTDOMAIN');
define('TBL_CUSTRANS', 'IDN_CustInvoiceJourPortal');
define('TBL_NPWP', 'MK_CUSTTABLEPOSTALADDRESS');
define('TBL_PROD', 'MK_PRODUCTHIERARCHY');
define('TBL_INSTALLADDRESS', 'IDN_CUSTADDRESSVIEW');

$axdb = newAXDB();
// print_r($axdb);
//$axdb->debug = TRUE;
$axdb->memCache = TRUE;
$axdb->memCacheHost = 'localhost';
$axdb->SetFetchMode(ADODB_FETCH_ASSOC);

function getProdMediaList(){
    global $axdb;
    $sql = "SELECT DISTINCT MEDIAID FROM " . TBL_PROD ." WHERE MEDIAID <> ''";
    $rs = $axdb->execute($sql);
    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
}

function getProdHierarchyList(){
    global $axdb;
    $sql = "SELECT DESCRIPTION,INVOICEDESCRIPTION,MEDIAID,ACTIVE FROM " . TBL_PROD ." WHERE MEDIAID <> ''";
    $rs = $axdb->execute($sql);
    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
}

function getProdAllCustList() {
    global $axdb;
    $sql = "SELECT CUSTTABLE, PRODUCTHIERARCHY, INVOICEDESCRIPTION, " . TBL_CPROD . ".STATUS AS STATUS FROM " . TBL_CPROD . " JOIN " . TBL_PROD . " ON " . TBL_CPROD . ".PRODUCTHIERARCHY = " . TBL_CPROD . " .PRODUCTHIERARCHY";
    $rs = $axdb->execute($sql);

    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
}

function getDomain($custID){
    global $axdb;
    $sql = "SELECT * FROM " . TBL_CUDOM ." WHERE CUSTACCOUNT = ?";
    $rs = $axdb->execute($sql, array($custID));
    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
}

function getNpwp($custID) {
    global $axdb;
    $sql = "SELECT TOP 1 ACCOUNTNUM, ADDRESS, CITY FROM " . TBL_NPWP . " WHERE ACCOUNTNUM = ? AND PURPOSE = 'Faktur Pajak'";
    $rs = $axdb->execute($sql, array($custID));
    if ($rs) {
        $cinfo = $rs->GetArray();
        return $cinfo;
    } else {
        return null;
    }
}

function getInvById($custID) {
    global $axdb;
    $date_now = date("Y-m-d");
    $date_min = date("Y-m-d", strtotime("-3 months")); 
    $sql = "SELECT * FROM " . TBL_CUSTRANS . " WHERE INVOICEACCOUNT = ? AND INVOICEDATE<= ? AND INVOICEDATE>= ?";
    $rs = $axdb->execute($sql, array($custID, $date_now, $date_min));
    if ($rs) {
        $cinfo = $rs->GetArray();
        return $cinfo;
    } else {
        return null;
    }
}

function getCustInfoOnly($custID) {
    global $axdb;
    $sql = "SELECT * FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    $rs = $axdb->execute($sql, array($custID));

    if ($rs) {
        $cinfo = $rs->GetArray(); 
        return $cinfo;
    } else {
        return false;
    }
}

function getCustInfo($custID) {
    global $axdb;
    $sql = "SELECT * FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    $rs = $axdb->execute($sql, array($custID));

    if ($rs) {
        $cinfo = $rs->GetArray();
        $cinfo['VIRTUAL_ACC'] = getCustBankAcc($custID);
        $cinfo['PROD_LIST'] = getCustProd($custID);
        $cinfo['TRANS_LIST'] = getCustTrans($custID);
        $cinfo['AGING'] = getCustAging($custID);
        $cinfo['USERID'] = getCustUsernameList($custID);
        $cinfo['BALANCE'] = getCustBalance($custID);
        $cinfo['INV_LIST'] = getInvById($custID);
        $cinfo['NPWP'] = getNpwp($custID);
        $cinfo['ADDR_LIST'] = getInstallAddress($custID);
        return $cinfo;
    } else {
        return null;
    }
}


function getCustInfoTransAX($custID) {
    global $axdb;
    $sql = "SELECT * FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    $rs = $axdb->execute($sql, array($custID));

    if ($rs) {
        $cinfo = $rs->GetArray(); 
        $cinfo['TRANS_LIST'] = getCustTrans($custID); 
        $cinfo['BALANCE'] = getCustBalance($custID);
        $cinfo['INV_LIST'] = getInvById($custID); 
        return $cinfo;
    } else {
        return null;
    }
}
function getCustInfoData($custID) {
    global $axdb;
    $sql = "SELECT * FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    $rs = $axdb->execute($sql, array($custID));
    if ($rs) {
        $cinfo = $rs->GetArray();
        return $cinfo;
    } else {
        return null;
    }
}

function getCustInfoToInv($custID) {
    global $axdb;
    $sql = "SELECT * FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    $rs = $axdb->execute($sql, array($custID));

    if ($rs) {
        $cinfo = $rs->GetArray();
        $cinfo['VIRTUAL_ACC'] = getCustBankAcc($custID);
//        $cinfo['PROD_LIST'] = getCustProd($custID);
        $cinfo['TRANS_LIST'] = getCustTrans($custID);
//        $cinfo['AGING'] = getCustAging($custID);
//        $cinfo['USERID'] = getCustUsernameList($custID);
//        $cinfo['BALANCE'] = getCustBalance($custID);
//        $cinfo['INV_LIST'] = getInvById($custID);
//        $cinfo['NPWP'] = getNpwp($custID);
        return $cinfo;
    } else {
        return null;
    }
}

function getCustInfoBal($custID) {
    global $axdb;
    $sql = "SELECT ACCOUNTNUM, NAME, KNOWNAS, DISTRICTNAME, SALESDISTRICTID, MK_CUSTSTATUS, EMAIL FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    $rs = $axdb->execute($sql, array($custID));

    if ($rs) {
        $cinfo = $rs->GetArray();
        // $cinfo['BALANCE'] = getCustBalance($custID);
        $cinfo['USERID'] = getCustUsernameList($custID);
        return $cinfo;
    } else {
        return null;
    }
}
function getCustDisticName($custID) {
    global $axdb;
    $sql = "SELECT ACCOUNTNUM, DISTRICTNAME, SALESDISTRICTID FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    $rs = $axdb->execute($sql, array($custID));

    if ($rs) {
        $cinfo = $rs->GetArray();
        return $cinfo;
    } else {
        return false;
    }
}


function getCustByUserId($userId) {
    global $axdb;
    $sql = "SELECT * FROM " . TBL_CUNAME . " WHERE USERNAME = ?";
    $rs = $axdb->execute($sql, array($userId));

    if ($rs) {
        $cinfo = $rs->GetArray();
        // $cinfo['BALANCE'] = getCustBalance($custID);
        // $cinfo['USERID'] = getCustUsernameList($custID);
        return $cinfo;
    } else {
        return null;
    }
}

function getCustInfoAging($custID) {
    global $axdb;
    $sql = "SELECT ACCOUNTNUM, NAME, KNOWNAS FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    $rs = $axdb->execute($sql, array($custID));

    if ($rs) {
        $cinfo = $rs->GetArray();
        $cinfo['AGING'] = getCustAging($custID);
        $cinfo['USERID'] = getCustUsernameList($custID);
        return $cinfo;
    } else {
        return null;
    }
}

function getCustUsernameList($custID) {
    global $axdb;
    $sql = "SELECT * FROM " . TBL_CUNAME . " WHERE CUSTACCOUNT = ?";
    $rs = $axdb->execute($sql, array($custID));

    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
}

function getCustBankAcc($custID) {
    global $axdb;
    $sql = "SELECT CUSTACCOUNT, ACCOUNTID AS BANKTYPE, MK_VIRTUALACCOUNT FROM " . TBL_CBACC . " WHERE CUSTACCOUNT = ?";
    $rs = $axdb->execute($sql, array($custID));

    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
}

function getCustBalance($custID) {
    global $axdb;
    $sql = "SELECT BALANCEMST FROM " . TBL_CUBAL . " WHERE ACCOUNTNUM = ?";
    $rs = $axdb->execute($sql, array($custID));

    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
}

function getCustAging($custID) {
    global $axdb;
    $sql = "SELECT * FROM " . TBL_CAGING . " WHERE ACCOUNTNUM = ?";
    $rs = $axdb->execute($sql, array($custID));

    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
}

function getCustAgingList() {
    global $axdb;
    $sql = "SELECT * FROM " . TBL_CAGING;
    $rs = $axdb->execute($sql);

    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
}

function getCustProd($custID) {
    global $axdb;
    $sql = "SELECT * FROM " . TBL_CPROD . " WHERE CUSTTABLE = ?";
    $rs = $axdb->execute($sql, array($custID));

    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
} 
function getCustTrans($custID) {
    $date_now = date("Y-m-d");
    $date_min = date("Y-m-d", strtotime("-2 months"));  
    global $axdb;
    $sql = "SELECT VOUCHER, TRANSTYPE, TXT, PAYMREFERENCE, TRANSDATE, INVOICE, AMOUNTCUR, CURRENCYCODE,ACCOUNTNUM,TRANSTYPE from " . TBL_CTRANS . " where TRANSTYPE in (0,8,15) AND ACCOUNTNUM = ? AND TRANSDATE <= ? AND TRANSDATE >= ? ORDER BY TRANSDATE DESC;";
    $rs = $axdb->execute($sql, array($custID, $date_now, $date_min));

    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
}

function getCustInvoiceMonthly($custID, $year = null, $month = null) {
    global $axdb;
    if (!isset($year))
        $year = date("Y");
    if (!isset($month))
        $month = date("n");
    
    $monthMin = date('m', strtotime(date($year .'-'. $month)." -1 month"));
    $yearMin = date('Y', strtotime(date($year .'-'. $month)." -1 month"));
    $from = "{$yearMin}-{$monthMin}-02";
    $to = "{$year}-{$month}-01";

    $sql = "SELECT * FROM " . TBL_CINVOICE . "
            WHERE accountnum = ?
            AND transdate BETWEEN ? and ?
            AND transtype = 8
            ORDER BY transdate DESC";
    $rs = $axdb->execute($sql, array($custID, $from, $to));
    
    $sql2 = "SELECT * FROM " . TBL_CTRANS . "
            WHERE accountnum = ?
            AND YEAR(transdate) = ?
            AND MONTH(transdate) = ?
            AND transtype = 15";
    $rs2 = $axdb->execute($sql2, array($custID, $yearMin, $monthMin));
    
    
//    $rs = $axdb->execute($sql, array($custID, $year, $month));

    if ($rs) {
        $ret = $rs->GetArray();        
        $ret2 = $rs2->GetArray();
        $resRet = array_merge($ret, $ret2);
        $lastmo = getCustLastMonth($custID, $year, $month);

        if ($lastmo) {
            array_push($resRet, $lastmo);
        }
        return $resRet;
    } else {
        return null;
    }
}

function getCustInvoiceMonthlyNoLastMonth($custID, $year = null, $month = null) {
    global $axdb;
    if (!isset($year))
        $year = date("Y");
    if (!isset($month))
        $month = date("n");
    $sql = "SELECT * FROM " . TBL_CINVOICE . "
            WHERE accountnum = ?
            AND YEAR(transdate) = ?
            AND MONTH(transdate) = ?";
    $rs = $axdb->execute($sql, array($custID, $year, $month));

    if ($rs) {
        $ret = $rs->GetArray();
//        $lastmo = getCustLastMonth($custID, $year, $month);
//
//        if ($lastmo) {
//            array_push($ret, $lastmo);
//        }

        return $ret;
    } else {
        return null;
    }
}

function getCustInvoiceMonthRange($custID, $fromyear = null, $frommonth = null, $toyear = null, $tomonth = null) {
    global $axdb;
    if (!isset($fromyear))
        $fromyear = date("Y");
    if (!isset($frommonth))
        $frommonth = date("n");
    if (!isset($toyear))
        $toyear = $fromyear;
    if (!isset($tomonth))
        $tomonth = $frommonth;

    $from = "{$fromyear}-{$frommonth}-01";
    $to = "{$toyear}-{$tomonth}-01";

    $sql = "SELECT * FROM " . TBL_CINVOICE . "
            WHERE accountnum = ?
            AND transdate BETWEEN ? and ?
            ORDER BY transdate DESC";
    $rs = $axdb->execute($sql, array($custID, $from, $to));

    if ($rs) {
        $ret = $rs->GetArray();
 
        return $ret;
    } else {
        return null;
    }
}

function getCustLastMonth($custID, $year = null, $month = null) {
    global $axdb;
    if (!isset($year))
        $year = date("Y");
    if (!isset($month))
        $month = date("n");
    $thedate = "$year-$month-01";
//    $sql = "SELECT ACCOUNTNUM, SUM(ORIGAMOUNT) AS ORIGAMOUNT, SUM(AMOUNTMST) AS AMOUNTMST FROM " . TBL_CINVOICE . "
//            WHERE accountnum = ?
//            AND transdate < ?
//            GROUP BY accountnum";
    $sql = "SELECT SUM(AMOUNTMST) AS AMOUNT FROM " . TBL_CTRANS . "
            WHERE ACCOUNTNUM = ?
            AND TRANSDATE <= DATEADD(MONTH, -1, ?)
            GROUP BY ACCOUNTNUM";

    $rs = $axdb->execute($sql, array($custID, $thedate));
    if ($rs) {
        $ls = $rs->GetArray();
        $ret['ACCOUNTNUM'] = $custID;
        $ret['TXT'] = 'SALDO BULAN LALU';
        $ret['TRANSDATE'] = $thedate;
        $ret['TRANSTYPE'] = 95;
        // $ret['ORIGAMOUNT'] = $ls[0]['AMOUNT'];  isset($var) ? $var : "default";
        // $ret['AMOUNTMST'] = $ls[0]['AMOUNT'];
        $ret['AMOUNTMST'] = isset($ls[0]['AMOUNT']) ? $ls[0]['AMOUNT'] : 0; 
        return $ret;
    } else {
        return null;
    }
}

function getCustLastMonthSettlement($custID, $year = null, $month = null) {
    global $axdb;
    if (!isset($year))
        $year = date("Y");
    if (!isset($month))
        $month = date("n");
    $from = strtotime("{$year}-{$month}-01");
    $from = strtotime("-1 month", $from);

    $year = (int) date("Y", $from);
    $month = (int) date("n", $from);

    $sql = "SELECT * FROM " . TBL_CINVOICE . "
            WHERE accountnum = ?
            AND YEAR(transdate) = ?
            AND MONTH(transdate) = ?
            AND transtype = 15
            ORDER BY transdate DESC";

    $rs = $axdb->execute($sql, array($custID, $year, $month));
    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
}

function getCustLastMonthRange($custID, $fromyear = null, $frommonth = null, $toyear = null, $tomonth = null) {
    global $axdb;
    if (!isset($fromyear))
        $fromyear = date("Y");
    if (!isset($frommonth))
        $frommonth = date("n");
    if (!isset($toyear))
        $toyear = $fromyear;
    if (!isset($tomonth))
        $tomonth = $frommonth;


    $to = "{$toyear}-{$tomonth}-31";

    $sql = "SELECT * FROM " . TBL_CINVOICE . "
            WHERE accountnum = ?
            AND transdate BETWEEN ? and ?
            ORDER BY transdate DESC";
    $rs = $axdb->execute($sql, array($custID, $fromyear, $to));

    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
}

function getCustTotalInvoice($custID, $year, $month) {
    if (!isset($year))
        $year = date("Y");
    if (!isset($month))
        $month = date("n");

    $invoices = getCustInvoiceMonthly($custID, $year, $month);
    $total = 0;

    foreach ($invoices as $invoice) {
        switch ($invoice['TRANSTYPE']) {
            case 8:
            case 15:
                // $total += $invoice['AMOUNTMST']; 
                $total += isset($invoice['AMOUNTMST']) ? $invoice['AMOUNTMST'] : 0; 
                break;
            case 95:
                // $total += $invoice['AMOUNTMST'];
                $total += isset($invoice['AMOUNTMST']) ? $invoice['AMOUNTMST'] : 0; 
                break;
            default:
                break;
        }
    }

    return $total;
}

function getCustTotalInvoiceNoAll($custID, $year, $month) {
    if (!isset($year))
        $year = date("Y");
    if (!isset($month))
        $month = date("n");

    $invoices = getCustInvoiceMonthlyNoLastMonth($custID, $year, $month);
    $total = 0;

    foreach ($invoices as $invoice) {
        switch ($invoice['TRANSTYPE']) {
            case 8:
            case 15:
                $total += $invoice['AMOUNTMST'];
                break;
            case 95:
                $total += $invoice['AMOUNTMST'];
                break;
            default:
                break;
        }
    }

    return $total;
}

function getCustTotalPayment($custID, $year, $month) {
    if (!isset($year))
        $year = date("Y");
    if (!isset($month))
        $month = date("n");

    $invoices = getCustInvoiceMonthly($custID, $year, $month);
    $total = 0;

    foreach ($invoices as $invoice) {
        if ($invoice['transtype'] === 15) {
            $total += $invoice['amountcur'];
        }
    }

    return $total;
}

function getSubnetList() {
    global $axdb;
    $sql = "SELECT * FROM smmBusRelSalesDistrictGroup";
    $rs = $axdb->CacheExecute(300, $sql);
    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
}

function isValidSubnet($subid) {
    $sublist = getSubnetList();
    foreach ($sublist as $sub) {
        if (strtolower($sub['SALESDISTRICTID']) == strtolower($subid))
            return true;
    }
    return false;
}

function getCustListUnderSubnet($subid) {
    if (!isValidSubnet($subid)) {
        return false;
    } else {
        global $axdb;
        $sql = "SELECT * FROM " . TBL_CINFO . " WHERE SALESDISTRICTID = ?";

//        $rs = $axdb->Cacheexecute(300, $sql, array($subid));
        $rs = $axdb->execute($sql, array($subid));

        if ($rs) {
            return $rs->GetArray();
        } else {
            return null;
        }
    }
}

function getCustAccListUnderSubnet($subid) {
    if (!isValidSubnet($subid)) {
        return false;
    } else {
        global $axdb;
        $sql = "SELECT ACCOUNTNUM, NAME, KNOWNAS, SALESDISTRICTID, DISTRICTNAME, MK_CUSTSTATUS, TYPECUST FROM " . TBL_CINFO . " WHERE SALESDISTRICTID = ?";

        $rs = $axdb->execute($sql, array($subid));

        if ($rs) {
            return $rs->GetArray();
        } else {
            return null;
        }
    }
} 
function getCustAccListUnderSubnetType($subid, $type) {
    if (!isValidSubnet($subid)) {
        return false;
    } else {
        global $axdb;
        $sql = "SELECT ACCOUNTNUM, NAME, KNOWNAS, SALESDISTRICTID, DISTRICTNAME, MK_CUSTSTATUS, TYPECUST, VATNUM, NPWPNAME, EMAIL, INSTALATIONNAME FROM " . TBL_CINFO . " WHERE SALESDISTRICTID = ? AND TYPECUST = ?";
        $rs = $axdb->execute($sql, array($subid, $type));

        if ($rs) {
            return $rs->GetArray();
        } else {
            return null;
        }
    }
}
function getCustAccActiveListUnderSubnet($subid) {
    if (!isValidSubnet($subid)) {
        return false;
    } else {
        global $axdb;
        $sql = "SELECT ACCOUNTNUM, NAME, KNOWNAS, SALESDISTRICTID, DISTRICTNAME, MK_CUSTSTATUS, TYPECUST, VATNUM, NPWPNAME, EMAIL, INSTALATIONNAME FROM " . TBL_CINFO . " WHERE SALESDISTRICTID = ? AND MK_CUSTSTATUS = 0";

        $rs = $axdb->execute($sql, array($subid));

        if ($rs) {
            return $rs->GetArray();
        } else {
            return null;
        }
    }
}

function getCustListUnderMultiSubnet($sublist) {
    $subs = [];
    $subnets = explode(',', $sublist);
    foreach ($subnets as $subnet) {
        if (isValidSubnet($subnet)) {
            $subs[] = $subnet;
        }
    }
    $res = [];

    foreach ($subs as $sub) {
        $c = getCustListUnderSubnet($sub);
        if ($c) {
            $res = array_merge($res, $c);
        }
    }

    if (sizeof($res) > 0) {
        return $res;
    } else {
        return null;
    }
}

function getCustListIdNameUnderSubnet($subid) {
    if (!isValidSubnet($subid)) {
        return false;
    } else {
        global $axdb;
        $sql = "SELECT ACCOUNTNUM, DISTRICTNAME FROM " . TBL_CINFO . " WHERE SALESDISTRICTID = ?";

//        $rs = $axdb->Cacheexecute(300, $sql, array($subid));
        $rs = $axdb->execute($sql, array($subid));

        if ($rs) {
            return $rs->GetArray();
        } else {
            return false;
        }
    }
}

function getCustListIdNameUnderSubnetLimit($subid, $limit = 25, $offset = -1) {    //offset start from 0
    if (!isValidSubnet($subid)) {
        return false;
    } else {
        global $axdb;
        $sql = "SELECT ACCOUNTNUM, DISTRICTNAME FROM " . TBL_CINFO . " WHERE SALESDISTRICTID = ? ORDER BY ACCOUNTNUM ASC";

//        $rs = $axdb->Cacheexecute(300, $sql, array($subid));
        $rs = $axdb->CacheSelectLimit(600, $sql, $limit, $offset, array($subid));

        if ($rs) {
            return $rs->GetArray();
        } else {
            return null;
        }
    }
}

function getCustListIdNameUnderMultiSubnetLimit($sublist, $limit = 50, $offset = -1) {    //offset start from 0
    $subs = [];
    $subnets = explode(',', $sublist);
    foreach ($subnets as $subnet) {
        if (isValidSubnet($subnet)) {
            $subs[] = $subnet;
        }
    }
    if (!sizeof($subs)) {
        return false;
    } else {
        $count = count($subs);
        $in_params = trim(str_repeat('?, ', $count), ', ');
        global $axdb;
        $sql = "SELECT ACCOUNTNUM, DISTRICTNAME, SALESDISTRICTID FROM " . TBL_CINFO . " WHERE SALESDISTRICTID IN ({$in_params}) ORDER BY ACCOUNTNUM ASC";

//        $rs = $axdb->Cacheexecute(300, $sql, array($subid));
        $rs = $axdb->CacheSelectLimit(600, $sql, $limit, $offset, $subs);

        if ($rs) {
            return $rs->GetArray();
        } else {
            return null;
        }
    }
}

function getCustIdUnderSubnet($subid) {
    if (!isValidSubnet($subid)) {
        return false;
    } else {
        global $axdb;
        $sql = "SELECT ACCOUNTNUM FROM " . TBL_CINFO . " WHERE SALESDISTRICTID = ?";

//        $rs = $axdb->Cacheexecute(300, $sql, array($subid));
        $rs = $axdb->execute($sql, array($subid));

        if ($rs) {
            return $rs->GetArray();
        } else {
            return null;
        }
    }
}

function getCustIdUnderMultiSubnet($sublist) {
    $subs = [];
    $subnets = explode(',', $sublist);
    foreach ($subnets as $subnet) {
        if (isValidSubnet($subnet)) {
            $subs[] = $subnet;
        }
    }
    $res = [];
    foreach ($subs as $sub) {
        $c = getCustIdUnderSubnet($sub);
        if ($c) {
            $res = array_merge($res, $c);
        }
    }
    if (sizeof($res) > 0) {
        return $res;
    } else {
        return null;
    }
}

function getCustIdUnderSubnetJakarta($subid, $page) {
    $num_rec_per_page = 100;
    if (!isValidSubnet($subid)) {
        return false;
    } else {
        $start_from = ($page - 1) * $num_rec_per_page;
        global $axdb;
        $sql = "SELECT ACCOUNTNUM FROM " . TBL_CINFO . " WHERE SALESDISTRICTID = ?"
                . " LIMIT ?, ?";

//        $rs = $axdb->Cacheexecute(300, $sql, array($subid));
        $rs = $axdb->execute($sql, array($subid, $start_from, $num_rec_per_page));

        if ($rs) {
            return $rs->GetArray();
        } else {
            return null;
        }
    }
}

function checkDuplicate($custID) {
    global $axdb;
    $sql = "SELECT * FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    $rs = $axdb->execute($sql, array($custID));
    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
}

function getInstallAddress($custID) {
   global $axdb;
    $sql = "SELECT * FROM " . TBL_INSTALLADDRESS . " WHERE ACCOUNTNUM = ?";
    $rs = $axdb->execute($sql, array($custID));

    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    } 
}

function getCustInfoAll($custID, $year, $month, $fromyear_array, $frommonth_array) { 
    global $axdb;
    $sql = "SELECT TOP 1 ACCOUNTNUM, NAME, KNOWNAS, SALESDISTRICTID, TYPETAGIHAN, MK_CUSTSTATUS, MK_REGISTRATIONDATE, TYPECUST, DISTRICTNAME, INSTALATIONNAME, NPWPNAME, INVOICEADDRESS, INSTALATIONADDRESS, FAKTURPAJAKADDRESS, PHONE, EMAIL, FAX FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    
    $rs = $axdb->execute($sql, array($custID));
    if ($rs) { 
        $cinfo = $rs->GetArray();
        $cinfo['VIRTUAL_ACC'] = getCustBankAcc($custID);
        $cinfo['PROD_LIST'] = getCustProd($custID);
        $cinfo['TRANS_LIST'] = getCustTrans($custID);
        //$cinfo['AGING'] = getCustAging($custID); // skip
        $cinfo['USERID'] = getCustUsernameList($custID);
        $cinfo['BALANCE'] = getCustBalance($custID);
        $cinfo['INV_LIST'] = getInvById($custID);
        $cinfo['DOMAIN'] = getDomain($custID); 
        //$cinfo['ADDR_LIST'] = getInstallAddress($custID); // skip       
	//$cinfo['INV_TOTAL'] = getCustTotalInvoice($custID, $year, $month); // skip
        for ($i=0; $i <= 2; $i++) { 
            $cinfo['INV_MONTH_TOTAL'][$fromyear_array[$i].''.$frommonth_array[$i]] = getCustTotalInvoice($custID, $fromyear_array[$i], $frommonth_array[$i]);  
        }
        for ($i=0; $i <= 2; $i++) { 
             $cinfo['INV_DETAIL_DATA'][$fromyear_array[$i].''.$frommonth_array[$i]] = getCustInvoiceMonthly($custID, $fromyear_array[$i], $frommonth_array[$i]); 
        }
        return $cinfo;
    } else {
        return false;
    }
}

function getCustInfoAll_new($custID, $year, $month, $fromyear_array, $frommonth_array, $posting_date_from, $posting_date_to) { 
    global $axdb;
    $sql = "SELECT TOP 1 ACCOUNTNUM, NAME, KNOWNAS, SALESDISTRICTID, TYPETAGIHAN, MK_CUSTSTATUS, MK_REGISTRATIONDATE, TYPECUST, DISTRICTNAME, INSTALATIONNAME, NPWPNAME, INVOICEADDRESS, INSTALATIONADDRESS, FAKTURPAJAKADDRESS, PHONE, EMAIL, FAX FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    
    $rs = $axdb->execute($sql, array($custID));
    if ($rs) { 
        $cinfo = $rs->GetArray();
        $cinfo['VIRTUAL_ACC'] = getCustBankAcc($custID);
        $cinfo['PROD_LIST'] = getCustProd($custID);
        $cinfo['TRANS_LIST'] = getCustTrans($custID);
        //$cinfo['AGING'] = getCustAging($custID); // skip
        $cinfo['USERID'] = getCustUsernameList($custID);
        $cinfo['BALANCE'] = getCustBalance($custID);
        $cinfo['INV_LIST'] = getInvById($custID);
        $cinfo['DOMAIN'] = getDomain($custID); 
        //$cinfo['ADDR_LIST'] = getInstallAddress($custID); // skip       
	//$cinfo['INV_TOTAL'] = getCustTotalInvoice($custID, $year, $month); // skip
        for ($i=0; $i <= 2; $i++) { 
            $cinfo['INV_MONTH_TOTAL'][$fromyear_array[$i].''.$frommonth_array[$i]] = getCustTotalInvoice_new($custID, $fromyear_array[$i], $frommonth_array[$i], $posting_date_from[$i], $posting_date_to[$i]);  
        }
        for ($i=0; $i <= 2; $i++) { 
             $cinfo['INV_DETAIL_DATA'][$fromyear_array[$i].''.$frommonth_array[$i]] = getCustInvoiceMonthly_new($custID, $fromyear_array[$i], $frommonth_array[$i],  $posting_date_from[$i], $posting_date_to[$i]); 
        }
        return $cinfo;
    } else {
        return false;
    }
}

function getCustTotalInvoice_new($custID, $year, $month, $posting_date_from, $posting_date_to) {
    if (!isset($year))
        $year = date("Y");
    if (!isset($month))
        $month = date("n");

    $invoices = getCustInvoiceMonthly_new($custID, $year, $month, $posting_date_from, $posting_date_to);
    $total = 0;

    foreach ($invoices as $invoice) {
        switch ($invoice['TRANSTYPE']) {
            case 8:
            case 15:
                // $total += $invoice['AMOUNTMST']; 
                $total += isset($invoice['AMOUNTMST']) ? $invoice['AMOUNTMST'] : 0; 
                break;
            case 95:
                // $total += $invoice['AMOUNTMST'];
                $total += isset($invoice['AMOUNTMST']) ? $invoice['AMOUNTMST'] : 0; 
                break;
            default:
                break;
        }
    }

    return $total;
}


function getCustInvoiceMonthly_new($custID, $year = null, $month = null, $posting_date_from, $posting_date_to) { 
    global $axdb;
    if (!isset($year))
        $year = date("Y");
    if (!isset($month))
        $month = date("n");
    
    $monthMin = date('m', strtotime(date($year .'-'. $month)." -1 month"));
    $yearMin = date('Y', strtotime(date($year .'-'. $month)." -1 month"));
    $from = "{$yearMin}-{$monthMin}-02";
    $to = "{$year}-{$month}-01";

    $sql = "SELECT * FROM " . TBL_CINVOICE . "
            WHERE accountnum = ?
            AND transdate BETWEEN ? and ?
            AND transtype = 8
            ORDER BY transdate DESC";
    $rs = $axdb->execute($sql, array($custID, $posting_date_from, $posting_date_to));
    
    // $sql2 = "SELECT * FROM " . TBL_CINVOICE . "
    //         WHERE accountnum = ?
    //         AND YEAR(transdate) = ?
    //         AND MONTH(transdate) = ?
    //         AND transtype = 15 
    //         ORDER BY transdate DESC";
    // $rs2 = $axdb->execute($sql2, array($custID, $year, $month));
    $sql2 = "SELECT * FROM " . TBL_CTRANS . "
            WHERE accountnum = ? 
            AND (transdate) BETWEEN ? and ? 
            AND transtype = 15"; 
    $rs2 = $axdb->execute($sql2, array($custID, $posting_date_from, $posting_date_to)); 

    if ($rs) {
        $ret = $rs->GetArray();        
        $ret2 = $rs2->GetArray();
        $resRet = array_merge($ret, $ret2); 
        $lastmo = getCustLastMonth_new($custID, $year, $month, $posting_date_from, $posting_date_to);

        if ($lastmo) {
            array_push($resRet, $lastmo);
        }
        return $resRet;
    } else {
        return null;
    }
}



function getCustLastMonth_new($custID, $year = null, $month = null, $posting_date_from, $posting_date_to) {
    global $axdb;
    if (!isset($year))
        $year = date("Y");
    if (!isset($month))
        $month = date("n"); 
    // $thedate = date('Y-m-d', strtotime('-1 day', strtotime($posting_date_from)));
    // $thedate = '05/09/2022';
    $thedate = $posting_date_from;
    // $thedate = '2022-05-09';
//    $sql = "SELECT ACCOUNTNUM, SUM(ORIGAMOUNT) AS ORIGAMOUNT, SUM(AMOUNTMST) AS AMOUNTMST FROM " . TBL_CINVOICE . "
//            WHERE accountnum = ?
//            AND transdate < ?
//            GROUP BY accountnum";
        // $sql = "SELECT SUM(AMOUNTMST) AS AMOUNT FROM " . TBL_CTRANS . "
        // WHERE accountnum = ? 
        // AND TRANSTYPE = 95
        // AND TRANSDATE <= DATEADD(MONTH, -1, ?)
        // GROUP BY ACCOUNTNUM"; 
        // $rs = $axdb->execute($sql, array($custID, $posting_date_from)); 


    $sql = "SELECT SUM(AMOUNTMST) AS AMOUNT FROM " . TBL_CTRANS . "
            WHERE ACCOUNTNUM = ?
            AND TRANSDATE <= ?
            GROUP BY ACCOUNTNUM";

    $rs = $axdb->execute($sql, array($custID, $thedate));
    if ($rs) {
        $ls = $rs->GetArray();
        $ret['ACCOUNTNUM'] = $custID;
        $ret['TXT'] = 'SALDO BULAN LALU';
        $ret['TRANSDATE'] = $thedate;
        $ret['TRANSTYPE'] = 95;
        // $ret['ORIGAMOUNT'] = $ls[0]['AMOUNT'];  isset($var) ? $var : "default";
        // $ret['AMOUNTMST'] = $ls[0]['AMOUNT'];
        $ret['AMOUNTMST'] = isset($ls[0]['AMOUNT']) ? $ls[0]['AMOUNT'] : 0; 
        return $ret;
    } else {
        return null;
    }
}
function getCustInfoToInvMonth($custID, $year, $month) { 
    global $axdb;
    $sql = "SELECT TOP 1 ACCOUNTNUM, NAME, KNOWNAS, SALESDISTRICTID, TYPETAGIHAN, MK_CUSTSTATUS, MK_REGISTRATIONDATE, TYPECUST, DISTRICTNAME, INSTALATIONNAME, NPWPNAME, INVOICEADDRESS, INSTALATIONADDRESS, FAKTURPAJAKADDRESS, PHONE, EMAIL, FAX FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    
    $rs = $axdb->execute($sql, array($custID));
    if ($rs) { 
        $cinfo = $rs->GetArray();
        $cinfo['VIRTUAL_ACC'] = getCustBankAcc($custID); 
        $cinfo['INV_LIST'] = getInvById($custID);    
	    $cinfo['INV_MONTH_TOTAL'] = getCustTotalInvoice($custID, $year, $month); // skip
	    $cinfo['INV_DETAIL_DATA'] = getCustInvoiceMonthly($custID, $year, $month); // skip
        return $cinfo;
    } else {
        return false;
    }
}
function getCustInfoToInvMonth_new($custID, $year, $month, $posting_date_from, $posting_date_to) { 
    global $axdb;
    $sql = "SELECT TOP 1 ACCOUNTNUM, NAME, KNOWNAS, SALESDISTRICTID, TYPETAGIHAN, MK_CUSTSTATUS, MK_REGISTRATIONDATE, TYPECUST, DISTRICTNAME, INSTALATIONNAME, NPWPNAME, INVOICEADDRESS, INSTALATIONADDRESS, FAKTURPAJAKADDRESS, PHONE, EMAIL, FAX FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    
    $rs = $axdb->execute($sql, array($custID));
    if ($rs) { 
        $cinfo = $rs->GetArray();
        $cinfo['VIRTUAL_ACC'] = getCustBankAcc($custID); 
        $cinfo['INV_LIST'] = getInvById($custID);    
	    $cinfo['INV_MONTH_TOTAL'] = getCustTotalInvoice_new($custID, $year, $month, $posting_date_from, $posting_date_to); // skip
	    $cinfo['INV_DETAIL_DATA'] = getCustInvoiceMonthly_new($custID, $year, $month, $posting_date_from, $posting_date_to); // skip
        return $cinfo;
    } else {
        return false;
    }
}
function getCustInfoTest($custID) {
    global $axdb;
    $sql = "SELECT * FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    $rs = $axdb->execute($sql, array($custID));
    if ($rs) {
        $cinfo = $rs->GetArray();
        $cinfo['VIRTUAL_ACC'] = getCustBankAcc($custID);
        $cinfo['PROD_LIST'] = getCustProd($custID);
        $cinfo['TRANS_LIST'] = getCustTrans($custID);
        $cinfo['AGING'] = getCustAging($custID);
        $cinfo['USERID'] = getCustUsernameList($custID);
        $cinfo['BALANCE'] = getCustBalance($custID);
        $cinfo['INV_LIST'] = getInvById($custID);
        $cinfo['DOMAIN'] = getDomain($custID);
        $cinfo['ADDR_LIST'] = getInstallAddress($custID);
        return $cinfo;
    } else {
        return null;
    }
}

function getCustInfoInvoice($custID) {
    global $axdb;
    $array_cinfo = array();
    $sql = "SELECT * FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    $rs = $axdb->execute($sql, array($custID));
    if ($rs) {
        $cinfo = end($rs->GetArray());
        $cinfo['VIRTUAL_ACC'] = getCustBankAcc($custID);
        $cinfo['PROD_LIST'] = getCustProd($custID);
        $cinfo['TRANS_LIST'] = getCustTrans($custID);
        $cinfo['AGING'] = getCustAging($custID);
        $cinfo['USERID'] = getCustUsernameList($custID);
        $cinfo['BALANCE'] = getCustBalance($custID);
        $cinfo['INV_LIST'] = getInvById($custID);
        $cinfo['DOMAIN'] = getDomain($custID);
        $cinfo['ADDR_LIST'] = getInstallAddress($custID);
        return $cinfo;
    } else {
        return null;
    }
}


function getCustInfoForCutomerPrint($custID) {
    global $axdb;
    $sql = "SELECT ACCOUNTNUM, NAME, KNOWNAS, CUSTCLASSIFICATIONID, TYPECUST, TYPETAGIHAN, ADDRESS, PHONE, EMAIL, CURRENCY, MK_CUSTSTATUS FROM " . TBL_CINFO . " WHERE ACCOUNTNUM = ?";
    $rs = $axdb->execute($sql, array($custID));
    if ($rs) {
        $cinfo = $rs->GetArray();
//        $cinfo['VIRTUAL_ACC'] = getCustBankAcc($custID);
//        $cinfo['PROD_LIST'] = getCustProd($custID);
//        $cinfo['TRANS_LIST'] = getCustTrans($custID);
//        $cinfo['AGING'] = getCustAging($custID);
//        $cinfo['USERID'] = getCustUsernameList($custID);
//        $cinfo['BALANCE'] = getCustBalance($custID);
//        $cinfo['INV_LIST'] = getInvById($custID);
//        $cinfo['DOMAIN'] = getDomain($custID);
        return $cinfo;
    } else {
        return null;
    }
}
function getCustTransByDate($custID, $minDate) {
    global $axdb;
    $sql = "SELECT VOUCHER, TRANSTYPE, TXT, PAYMREFERENCE, TRANSDATE, INVOICE, AMOUNTCUR, CURRENCYCODE,ACCOUNTNUM,TRANSTYPE from " . TBL_CTRANS . " where TRANSTYPE in (0,8,15) AND ACCOUNTNUM = ? AND TRANSDATE >= ?  ORDER BY TRANSDATE DESC;";
    $rs = $axdb->execute($sql, array($custID, $minDate));

    if ($rs) {
        return $rs->GetArray();
    } else {
        return null;
    }
}
function getInvByIdDate($custID, $minDate) {
    global $axdb;
    $sql = "SELECT * FROM " . TBL_CUSTRANS . " WHERE INVOICEACCOUNT = ? AND INVOICEDATE >= ? ORDER BY INVOICEDATE DESC;";
    $rs = $axdb->execute($sql, array($custID, $minDate));
    if ($rs) {
        $cinfo = $rs->GetArray();
        return $cinfo;
    } else {
        return null;
    }
}
