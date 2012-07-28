<?php

require_once(BASE_DIR.'/cake/app/controllers/components/opensocial_get_friend.php');
require_once(BASE_DIR.'/cake/app/controllers/components/JSON.php');

class GalleryController extends AppController{

  var $uses = array('Member','Photo','Comment');

  function top(){
    $this->session_manage();
    $member_id = $this->session_data['id'];
    $datas = $this->Photo->findAll();
    $this->set('datas',$datas);
  }

  function photo_list($friend_member_id){
    $this->session_manage();
    $datas = $this->Photo->findAllByMemberIdAndValidFlag($friend_member_id,1);
    $this->set('datas',$datas);
  }

  function photo_detail($photo_id){
    $this->session_manage();
    $datas = $this->Photo->findById($photo_id);
    $this->set('datas',$datas);
  }

  function mymixi(){
    $this->session_manage();
    //セッションから会員番号を取得
    $member_id = $this->session_data['id'];
    $this->set('member_id',$member_id);
    $mdata = $this->Member->findById($member_id);
    $mixi_account_id = $mdata['Member']['mixi_account_id'];
    if(strlen($mixi_account_id)==0){
      $mixi_account_id = 0;
    }
    $api = new TestRestfulAPI($mixi_account_id);
    $data = $api->get();
    $json = new Services_JSON;
    $decode_data = $json->decode($data,true);
    //var_dump($decode_data);
    $ranking_count=0;
    foreach($decode_data->entry as $decodes){
      $mixi_id = $decodes->id;
      $mixi_id = str_replace("mixi.jp:","",$mixi_id);
      $fdata = $this->Member->findByMixiAccountId($mixi_id);
      $mmdata[$ranking_count]['thumnail_url']=$fdata['Member']['thumnail_url'];
      $mmdata[$ranking_count]['id']=$fdata['Member']['id'];
      $mmdata[$ranking_count]['name']=$fdata['Member']['name'];
      $ranking_count=$ranking_count+1;
    }
    $this->set('rankings',$mmdata);
  }

  function session_manage(){
    $session_data = $this->Session->read("member_info");
    $this->session_data = $session_data;
    if(strlen($session_data['id'])==0){
      $this->redirect('/login/session_timeout/');
    }
  }
}