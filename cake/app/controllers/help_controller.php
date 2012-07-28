<?php

class HelpController extends AppController{

  var $uses = array('Member','Photo','Comment');

  function page1(){
    $this->Session->write('test1','aaaaaaaaa');
  }


  function session_manage(){
    $session_data = $this->Session->read("member_info");
    $this->session_data = $session_data;
    if(strlen($session_data['id'])==0){
      $this->redirect('/login/session_timeout/');
    }
  }
}