<?php

class TopController extends AppController{

  var $uses = array('Member','Photo','Comment');

  function top(){
    $this->session_manage();
    $member_id = $this->session_data['id'];
  }

  function session_manage(){
    $session_data = $this->Session->read("member_info");
    $this->session_data = $session_data;
    if(strlen($session_data['id'])==0){
      $this->redirect('/login/session_timeout/');
    }
  }
}