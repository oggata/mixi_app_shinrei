<?php

class ManifestoController extends AppController{

  var $uses = array('Member','Photo','Comment');

  function top(){
    $this->session_manage();
    $member_id = $this->session_data['id'];
  }

  function make_word(){
    $this->session_manage();
    $member_id = $this->session_data['id'];
    //$data = $this->Element->findByGenreId(1);
    $data = $this->Element->find(array('genre_id'=>1),null,'rand()');
    $data = $this->Elements->findByGenreId(2);
    $data = $this->Elements->findByGenreId(3);
    $data = $this->Elements->findByGenreId(4);
    $data = $this->Elements->findByGenreId(5);
  }

  function voice($voice){
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