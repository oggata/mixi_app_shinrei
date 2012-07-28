<?php

class DetailController extends AppController{

  var $uses = array('Member','Photo','Comment');

  function photo($photo_id){
    $this->session_manage();
    $member_id = $this->session_data['id'];
    $datas = $this->Photo->findAllById($photo_id);
    $this->set('datas',$datas);
  }

  function friend_photo($photo_id){
    $this->session_manage();
    $member_id = $this->session_data['id'];
    $datas = $this->Photo->findAllById($photo_id);

    $friend_member_id= $datas['Photo']['member_id'];
    $this->set('friend_member_id',$friend_member_id);
    $this->set('datas',$datas);
  }

  function tweet($photo_id){
    $this->session_manage();
    $member_id = $this->session_data['id'];
    $photo_url = 'http://mixi.jp/run_appli.pl?id=32888';
    $this->set('photo_url',$photo_url);
  }

  function throw_mixi($img_name){
    $upimg_url = BASE_URL.'/img/out/'.$img_name.'.jpg';
    $this->set('upimg_url',$upimg_url);
  }

  function session_manage(){
    $session_data = $this->Session->read("member_info");
    $this->session_data = $session_data;
    if(strlen($session_data['id'])==0){
      $this->redirect('/login/session_timeout/');
    }
  }
}