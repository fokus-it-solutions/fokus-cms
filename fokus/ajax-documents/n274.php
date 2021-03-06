<?php
if(!defined('DEPENDENCE'))
    exit('class is dependent');

if(!$user->r('dok') || $index != 'n274')
    exit($user->noRights());

$block = $fksdb->save($_REQUEST['block']);
$id = $fksdb->save($_REQUEST['id'], 1);
$fa = $_REQUEST['sort'];
$ibid = $fksdb->save($_REQUEST['ibid']);
$blockindex = $fksdb->save($_REQUEST['blockindex']);
parse_str($fa, $f);

$dokument = $fksdb->fetch("SELECT id, klasse, produkt, dversion_edit, von FROM ".SQLPRE."documents WHERE id = '".$id."' LIMIT 1");
$dve = $fksdb->fetch("SELECT id, klasse_inhalt FROM ".SQLPRE."document_versions WHERE id = '".$dokument->dversion_edit."' LIMIT 1");

if($user->r('dok', 'edit') || ($user->r('dok', 'new') && $dokument->von == $user->getID())) {}
else exit($user->noRights());

$f = $f['p'];
$html = array();
foreach($f as $f1 => $f2)
{
    $muster = '/[^a-zA-ZäüöÄÜÖß0-9-!?,_;:+=]/';
    $tmp_name = (trim(preg_replace($muster, ' ', $f2['name'])));
    $tmp_desc = (trim(preg_replace($muster, ' ', $f2['desc'])));

    $html[] = array("id" => $f2['id'], "name" => $tmp_name, "desc" => $tmp_desc, "hidev" => $f2['hidev'], "dir" => $f2['isdir']);
}

function __recalcserializedlengths($sObject)
{
    $__ret =preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $sObject );
    return $__ret;
}

$galerie_html = __recalcserializedlengths(serialize($html));

if(!$dokument->klasse && !$dokument->produkt)
{
    $upt = $fksdb->query("UPDATE ".SQLPRE."blocks SET html = '".$galerie_html."' WHERE id = '".$block."' AND dokument = '".$id."' LIMIT 1");
}
else
{
    $ki = $base->fixedUnserialize($dve->klasse_inhalt);

    if(!$ibid)
        $ki[$block]['html'] = $galerie_html;
    else
        $ki[$ibid]['html'][$blockindex]['html'] = $galerie_html;

    $kis = serialize($ki);

    $update = $fksdb->query("UPDATE ".SQLPRE."document_versions SET klasse_inhalt = '".$kis."' WHERE id = '".$dve->id."' LIMIT 1");
}

$d = $fksdb->fetch("SELECT dversion_edit FROM ".SQLPRE."documents WHERE id = '".$id."' LIMIT 1");
$update = $fksdb->query("UPDATE ".SQLPRE."document_versions SET edit = '1', ende = '0', von = '".$user->getID()."', timestamp_edit = '".$base->getTime()."' WHERE id = '".$d->dversion_edit."' LIMIT 1");
$base->create_dk_snippet($id);
?>