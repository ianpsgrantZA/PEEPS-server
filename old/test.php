
<pre>
<?php 

$content = file_get_contents("php://input");
$decoded = json_decode($content, true);
$username = $decoded['username'];
echo json_encode($decoded);

?>
</pre>