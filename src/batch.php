<?php
require_once ('inc/util.class.php');

if($_POST['subdms'] != '') {
    $input = trim($_POST['dms']);
    $dmsOutput = $input;
    $method = 'dms2dec';
}
else if($_POST['subdec'] != '') {
    $input = trim($_POST['dec']);
    $decOutput = $input;
    $method = 'dec2dms';
}
else {
    $input = '';
}

$inputParts = explode("\n", $input);

foreach ($inputParts AS $line) {
	$line = trim($line);

    switch ($method) {
        case 'dms2dec':
            if (strlen($line) != 27 || $line[0] != 'N') {
                $decOutput .= $line . "\n";
                continue;
            }
            $decOutput .= Util::dms2dec($line, 3) . "\n";
            break;
        case 'dec2dms':           
            if (strlen($line) != 20 || $line[0] != '2') {
                $dmsOutput .= $line . "\n";
                continue;
            }
            $dmsOutput .= Util::dec2dms($line) . "\n";
            break;    
    }

	//echo $line . "\n";
    


}

?>
<html>
<head>
</head>
<body>
    <h1>Batch Degree/Decimal Converter</h1>
    <p>Note: Lines that contain invalid data will be returned without editing</p>
    <form name="convert" action="" method="post">
    <table>
    <thead>
        <tr>
        <th>Data in Degrees/Mins/Seconds (DMS)</th>
        <th>&nbsp;</th>
        <th>Data in Decimal (DEC)</th>
        </tr>
    </thead>
    <tr>
        <td><textarea name="dms" rows=20 cols=60><?= $dmsOutput; ?></textarea></td>
        <td>
            <input type="submit" name="subdec" value="&laquo;"><br /><br />
            <input type="submit" name="subdms" value="&raquo;">
        </td>
        <td><textarea name="dec" rows=20 cols=60><?= $decOutput; ?></textarea></td>
    </tr>
    </table>
    </form>
</body>
</html>
