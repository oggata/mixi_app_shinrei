<?php
$filename = basename($_FILES['yourkey']['name']);
if (move_uploaded_file($_FILES['yourkey']['tmp_name'], '/path/to/save/' . $filename)) {
    $data = array('filename' => $filename);
} else {
    $data = array('error' => 'Failed to save');
}

header('Content-type: text/html');
echo json_encode($data);

?>