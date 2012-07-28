<?php

class ManageController extends AppController{

  var $uses = array('Member','Photo','Comment','BatchSql');
  var $components = array('Pager');

  function top(){
    $this->session_manage();
    $member_id = $this->session_data['id'];
    //$this->session_manage();
  }
/*
  function photo_list(){
    $this->session_manage();
    $member_id = $this->session_data['id'];
    $datas = $this->Photo->findAll(array('member_id'=>$member_id,'valid_flag'=>1), null, 'id desc',0,10);
    $this->set('datas',$datas);
  }
*/
  function my_list($page_no){
    $this->session_manage();
    $member_id = $this->session_data['id'];
    if(strlen($page_no)==0){
      $page_no =1;
    }
    $count_num = $this->Photo->findCount(array('member_id'=>$member_id,'valid_flag'=>1));
    $divide_no = 10;
    //ページ表示部分
    $vlist = $this->Pager->pagelink($divide_no,$count_num,'/cake/manage/my_list/'.$page_no);
    $this->set('vlist',$vlist);
    $page_end_no = $divide_no * $page_no;
    $page_start_no = $page_end_no - ($divide_no - 1) -1;
    $datas = $this->Photo->findAll(array('member_id'=>$member_id,'valid_flag'=>1), null, 'id desc',$page_start_no,$divide_no);

    $this->set('datas',$datas);
  }

  function save_storage($photo_tmp_name){
    $this->session_manage();
    $member_id = $this->session_data['id'];
    $my_storage_dir = MY_PHOTO_DIR.'/'.$member_id;
    // ディレクトリが存在するかチェックし、なければ作成
    if(!is_dir($my_storage_dir)){
      umask(0);
      $rc = mkdir($my_storage_dir, 0777);
    }

    $file = PHOTO_OUT_DIR.'/'.$photo_tmp_name.'.jpg';
    $newfile = $my_storage_dir.'/'.$photo_tmp_name.'.jpg';
    //ファイルコピー
    if (!copy($file, $newfile)) {
        //echo "failed to copy $file...\n";
        $this->Session->write('Error','写真の保存に失敗しました。再度試してください。');
        $this->redirect('/upload/top/');
    }

    //データ格納
    $data = array(
      'Photo' => array(
        'member_id' => $member_id,
        'name' => $photo_tmp_name,
        'valid_flag' => 1,
        'average_point' => 0,
        'insert_date' => date("Y-m-d H:i:s")
      )
    );
    $this->Photo->save($data);
    $id_data = $this->BatchSql->select_autoincrement_id();
    $this->set('up_photo_url',BASE_URL.'/img/my/'.$member_id.'/'.$photo_tmp_name.'.jpg');
    $this->set('photo_id',$id_data[0][0]['id']);
    $this->set('photo_name',$photo_tmp_name);
  }

  function remove_storage_confirm($tmp_file_name){
    $this->session_manage();
    $member_id = $this->session_data['id'];
    $this->Session->write('tmp_file_name',$tmp_file_name);
    $this->set('tmp_file_name',$tmp_file_name);
    $this->set('remove_photo_url',BASE_URL.'/img/my/'.$member_id.'/'.$tmp_file_name.'.jpg');
  }

  function remove_storage_execute(){
    $this->session_manage();
    $member_id = $this->session_data['id'];

    $tmp_file_name = $this->Session->read('tmp_file_name');
    //ファイルを削除
    $moto_file = MY_PHOTO_DIR.'/'.$member_id.'/'.$tmp_file_name.'.jpg';
    $remove_file = GARBAGE_DIR.'/'.$tmp_file_name.'.jpg';
    rename($moto_file,$remove_file);

    $photo_data = $this->Photo->findByName($tmp_file_name);
    $photo_id = $photo_data['Photo']['id'];
    //データ削除
    $data = array(
      'Photo' => array(
        'id' => $photo_id,
        'valid_flag' => 0
      )
    );
    $this->Photo->save($data);
    $this->redirect('/manage/my_list/');
    //$this->set('remove_photo_url',BASE_URL.'/img/garbage/'.$tmp_file_name.'.jpg');
  }

  function session_manage(){
    $session_data = $this->Session->read("member_info");
    $this->session_data = $session_data;
    if(strlen($session_data['id'])==0){
      $this->redirect('/login/session_timeout/');
    }
  }
}