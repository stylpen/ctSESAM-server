<?php
/*

    Copyright (c) 2015 Oliver Lau <ola@ct.de>, Heise Medien GmbH & Co. KG

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

require_once 'globals.php';

assert_basic_auth();

if (!$dbh) {
    $res['status'] = 'error';
    $res['error'] = 'Connecting to database failed';
    goto end;
}

if (!isset($_REQUEST['data'])) {
    $res['status'] = 'error';
    $res['error'] = '"data" missing or invalid';
    goto end;
}

$domain = $_REQUEST['data'];

try {
    $domain = json_decode($domain, true);
}
catch (Exception $e) {
    $res['status'] = 'error';
    $res['error'] = $e->getMessage();
    goto end;
}

$res['domain'] = $domain;

$sth = $dbh->prepare('SELECT * FROM `domains` WHERE `name` = :name');
$sth->bindParam(':name', $domain['domain']);
$result = $sth->execute();
$rows = $sth->fetch(PDO::FETCH_NUM);
if ($rows) {
	$sql = 'UPDATE `domains` SET' .
        ' `username` = :username,' .
        ' `useLowerCase` = :useLowerCase,' .
        ' `useUpperCase` = :useUpperCase,' .
        ' `useDigits` = :useDigits,' .
        ' `useExtra` = :useExtra,' .
        ' `useCustom` = :useCustom,' .
        ' `avoidAmbiguous` = :avoidAmbiguous,' .
        ' `customCharacters` = :customCharacters,' .
        ' `iterations` = :iterations,' .
        ' `length` = :length,' .
        ' `salt` = :salt,' .
        ' `cDate` = :cDate,' .
        ' `mDate` = :mDate' .
        ' WHERE `name` = :name';
	$sth = $dbh->prepare($sql);
    $sth->bindParam(':name', $domain['domain']);
    $sth->bindParam(':username', $domain['username']);
    $sth->bindParam(':useLowerCase', $domain['useLowerCase']);
    $sth->bindParam(':useUpperCase', $domain['useUpperCase']);
    $sth->bindParam(':useDigits', $domain['useDigits']);
    $sth->bindParam(':useExtra', $domain['useExtra']);
    $sth->bindParam(':useCustom', $domain['useCustom']);
    $sth->bindParam(':avoidAmbiguous', $domain['avoidAmbiguous']);
    $sth->bindParam(':customCharacters', $domain['customCharacters']);
    $sth->bindParam(':iterations', $domain['iterations']);
    $sth->bindParam(':length', $domain['length']);
    $sth->bindParam(':salt', $domain['salt']);
    $sth->bindParam(':cDate', $domain['cDate']);
    $sth->bindParam(':mDate', $domain['mDate']);
    try {
    	$result = $sth->execute();
    }
    catch (PDOException $e) {
    	$res['status'] = 'error';
    	$res['error'] = $e->getMessage();
    	$res['data'] = $data;
    }
    $res['$result'] = $result;
    $res['rowcount'] = $sth->rowCount();
    $res['sql'] = $sql;
}
else {
	$sql = 'INSERT INTO `domains` ' .
		' (name, username, useLowerCase, useUpperCase, useDigits, useExtra, useCustom, avoidAmbiguous, customCharacters, iterations, length, salt, cDate, mDate)' .
		' VALUES(' .
        ' :name,' .
        ' :username,' .
        ' :useLowerCase,' .
        ' :useUpperCase,' .
        ' :useDigits,' .
        ' :useExtra,' .
        ' :useCustom,' .
        ' :avoidAmbiguous,' .
        ' :customCharacters,' .
        ' :iterations,' .
        ' :length,' .
        ' :salt,' .
        ' :cDate,' .
        ' :mDate)';
	$sth = $dbh->prepare($sql);
    $sth->bindParam(':name', $domain['domain']);
    $sth->bindParam(':username', $domain['username']);
    $sth->bindParam(':useLowerCase', $domain['useLowerCase']);
    $sth->bindParam(':useUpperCase', $domain['useUpperCase']);
    $sth->bindParam(':useDigits', $domain['useDigits']);
    $sth->bindParam(':useExtra', $domain['useExtra']);
    $sth->bindParam(':useCustom', $domain['useCustom']);
    $sth->bindParam(':avoidAmbiguous', $domain['avoidAmbiguous']);
    $sth->bindParam(':customCharacters', $domain['customCharacters']);
    $sth->bindParam(':iterations', $domain['iterations']);
    $sth->bindParam(':length', $domain['length']);
    $sth->bindParam(':salt', $domain['salt']);
    $sth->bindParam(':cDate', $domain['cDate']);
    $sth->bindParam(':mDate', $domain['mDate']);
    try {
    	$result = $sth->execute();
    }
    catch (PDOException $e) {
    	$res['status'] = 'error';
    	$res['error'] = $e->getMessage();
    	$res['data'] = $domain;
    }
    $res['$result'] = $result;
    $res['rowcount'] = $sth->rowCount();
    $res['sql'] = $sql;
}


end:
if (isset($res['status']) && $res['status'] === 'error') {
    // $dbh->exec('ROLLBACK');
    $res['inserted'] = 0;
}

header('Content-Type: text/json');
echo json_encode($res);

?>