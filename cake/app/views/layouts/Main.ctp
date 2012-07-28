<div id="bodybg">
<div id="body">
<?php if($session->check('Message.flash')){
  $session->flash();
}
echo $content_for_layout;
?>
</div></div>