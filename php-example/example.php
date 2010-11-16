<?php
include 'client.php';

// add your username & password below for authenticated api requests
$username = '';
$password = '';
$baseUrl = 'http://hb.sand-08.adnxs.net';

$json = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$dw = new DW($baseUrl);
	$auth = $dw->get('/auth?username=' . $username . '&password=' . $password);
	if (isset($auth['response']['token'])) {
		$dw->setToken($auth['response']['token']);
	}

	if (isset($_REQUEST['json'])) {
		$json = json_decode(stripslashes($_REQUEST['json']));
	}
	$json = $dw->{$_REQUEST['method']}($_REQUEST['endpoint'], $json);
}

// lifted from http://recursive-design.com/blog/2008/03/11/format-json-with-php/
function indent($json) {
	$result    = ''; $pos       = 0; $strLen    = strlen($json); $indentStr = '  '; $newLine   = "\n"; for($i = 0; $i <= $strLen; $i++) { $char = substr($json, $i, 1); if($char == '}' || $char == ']') { $result .= $newLine; $pos --; for ($j=0; $j<$pos; $j++) { $result .= $indentStr; } } $result .= $char; if ($char == ',' || $char == '{' || $char == '[') { $result .= $newLine; if ($char == '{' || $char == '[') { $pos ++; } for ($j = 0; $j < $pos; $j++) { $result .= $indentStr; } } } return $result;
}
?>

<form action="" method="POST">
	URL: <input type="text" name="endpoint" size="100" value="<?= isset($_REQUEST['endpoint']) ? $_REQUEST['endpoint'] : '/advertiser' ?>" />
	&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
	Method: 
		<select name="method">
			<option <?= isset($_REQUEST['method']) && $_REQUEST['method'] == 'get' ? 'selected="selected"' : '' ?> value="get">GET</option>
			<option <?= isset($_REQUEST['method']) && $_REQUEST['method'] == 'post' ? 'selected="selected"' : '' ?> value="post">POST</option>
			<option <?= isset($_REQUEST['method']) && $_REQUEST['method'] == 'put' ? 'selected="selected"' : '' ?> value="put">PUT</option>
			<option <?= isset($_REQUEST['method']) && $_REQUEST['method'] == 'delete' ? 'selected="selected"' : '' ?> value="delete">DELETE</option>
		</select>
	&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
	<input type="submit" value="Go" /> <br /> <br />
	<textarea name="json" style="width:100%; height:90%">
<?= $json ? indent(json_encode($json)) : ''; ?>
	</textarea>
</form>
