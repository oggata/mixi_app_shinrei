<?php

class UploadController extends AppController{

  var $uses = array('Member','Photo','Comment');

  function top(){
    $this->session_manage();
    $member_id = $this->session_data['id'];
    $this->set('alert_txt','');
    //外部ストレージが接続されているか確認する
    if (file_exists(STORAGE_MOUNT_FILE)==false) {
      $alert_txt = "jAlert('エラーが起こりました。管理者にご一報ください。', 'お願い');";
      $this->set('alert_txt',$alert_txt);
    }
    //現在の枚数を数える
    $count = $this->Photo->findCount(array('member_id'=>$member_id,'valid_flag'=>1));
    if($count >= SAVE_PHOTO_COUNT){
      $message = '保存可能枚数に達しました。変換後の写真を保存したい場合は不要な写真を削除してからお使い下さい。';
      $alert_txt = "jAlert('".$message."', 'お願い');";
      $this->set('alert_txt',$alert_txt);
    }
    //セッションエラーを受け取る
    $error_txt = $this->Session->read('Error');
    if(strlen($error_txt)>0){
          $message = $error_txt;
          $alert_txt = "jAlert('".$message."', 'お願い');";
          $this->set('alert_txt',$alert_txt);
          //$this->redirect('/upload/top/');
    }else{
        $this->set('count',$count);
        $this->Session->write('Error','');
        $this->set('Error','');
        $this->Session->write('PageGenre',1);
        $this->Session->write('FixTmpPass','');
        $this->Session->write('TmpFileName','');
    }
    $this->Session->write('Error','');
  }

  function jqupload(){
    $error_flag = 0;
    $import_data = $_FILES['data']['type'];
    //ファイルタイプを検証
    $file_type = $this->getExtension($import_data['Upload']['photo']);
    if($file_type == false){
      $alert_txt = "jAlert('対応してない画像フォーマットです。', 'お願い');";
      $this->set('alert_txt',$alert_txt);
      $error_flag = 1;
    }
    $tmp_file_name = time();
    $up_tmp_pass = PHOTO_TMP_DIR.'/'.$tmp_file_name.'.'.$file_type;
    $this->Session->write('TmpFileName',$tmp_file_name);
    $upload_file = $_FILES['data']['tmp_name']['Upload']['photo'];
    if (move_uploaded_file($upload_file,$up_tmp_pass) == FALSE){
      $alert_txt = "jAlert('写真のアップロードに失敗しました。', 'お願い');";
      $this->set('alert_txt',$alert_txt);
      $error_flag = 1;
    }
    if($error_flag ==0){
      $image = new Imagick("$up_tmp_pass");
      $image -> setImageFormat("JPG");
      $image -> thumbnailImage(400,0);
      $image -> setCompressionQuality(80);
      $fix_tmp_pass = PHOTO_FIX_DIR.'/'.$tmp_file_name.'.jpg';
      $this->Session->write('FixTmpPass',$fix_tmp_pass);
      $image->writeImage("$fix_tmp_pass");
      //$this->Session->write('TmpFileName',$tmp_file_name);
      $fix_img_pass = '/img/fix/'.$tmp_file_name.'.jpg';
      $this->set('img_url',$fix_img_pass);
      $this->render($layout='jqupload',$file='Jqupload');
    }else{
      $this->render($layout='jqupload_error',$file='Jqupload');
    }
  }


  function confirm(){
    $this->session_manage();
    $member_id = $this->session_data['id'];
    $page_genre = $this->Session->read('PageGenre');

    $fix_tmp_pass = $this->Session->read('FixTmpPass');
    $this->Session->write('FixTmpPass',$fix_tmp_pass);

    $tmp_file_name = $this->Session->read('TmpFileName');
    $this->Session->write('TmpFileName',$tmp_file_name);

    $fix_img_pass = '/img/fix/'.$tmp_file_name.'.jpg';
    $this->set('img_url',$fix_img_pass);

    $this->Session->write('PageGenre',2);
  }

  function execute(){
    $this->session_manage();
    $member_id = $this->session_data['id'];
    $page_genre = $this->Session->read('PageGenre');
    if($page_genre==2){
      //パラメータ取得
      $level = $this->params['data']['level'];
      $photo_level = $this->params['data']['photo'];
      $target_flag = $this->params['data']['target'];
      $fix_tmp_pass = $this->Session->read('FixTmpPass');

      $this->Session->write('Level',$level);
      $this->Session->write('PhotoLevel',$photo_level);
      $this->Session->write('TargetFlag',$target_flag);
      $this->Session->write('FixTmpPass',$fix_tmp_pass);
      $this->Session->write('PageGenre',3);
    }elseif($page_genre==3){
      //パラメータ取得
      $level = $this->Session->read('Level');
      $photo_level = $this->Session->read('PhotoLevel');
      $target_flag = $this->Session->read('TargetFlag');
      $fix_tmp_pass = $this->Session->read('FixTmpPass');
      $this->Session->write('FixTmpPass',$fix_tmp_pass);
      $this->Session->write('PageGenre',3);
    }
    $lv_data = $this->return_lv_state($level);
    $photo_data = $this->return_photo_state($photo_level);
    if(strlen($target_flag)==0){
      $target_flag = 0;
    }
    //実行
    $image = new Imagick("$fix_tmp_pass");
    $image -> setImageFormat("JPG");

    //サイズ
    $width = $image->getImageWidth();
    $height = $image->getImageHeight();
    $canvas = new Imagick();
    $canvas->newImage($width, $height, new ImagickPixel('none'));
    $canvas->setImageFormat("JPG");

    for($i=1;$i<=$lv_data['face_count'];$i++){
      $rand_mapx = rand(0,$width-60);
      $rand_mapy = rand(0,$height-60);
      $color_rad = rand(0,10);
      $right_rad = rand(30,100);
      $rei_rad = rand(1,18);

      $goast_positions[$i]['mapx']=$rand_mapx;
      $goast_positions[$i]['mapy']=$rand_mapy;
      $draw_img_pass = IMG_DIR.'/yurei/'.$rei_rad.'.png';
      ${'draw_img_'.$i} = new Imagick("$draw_img_pass");
      //モーションブラー [1:半径　2:標準偏差(1～8位が妥当) 3:角度]
      ${'draw_img_'.$i}->motionBlurImage(0,3,10);
      //明度、飽和度、色相 [1:明度　2:飽和度差 3:色相]
      ${'draw_img_'.$i}->modulateImage($right_rad, 80, $color_rad*10);
      $canvas->compositeImage(${'draw_img_'.$i}, imagick::COMPOSITE_OVER, $rand_mapx, $rand_mapy);
    }
    //アップロード画像と合成
    $image->compositeImage($canvas,imagick::COMPOSITE_OVER, 0, 0);
    $tmp_file_name = $this->Session->read('TmpFileName');
    $fix_img_pass = PHOTO_FIX_DIR.'/'.$tmp_file_name.'.jpg';
    $out_img_pass = PHOTO_OUT_DIR.'/'.$tmp_file_name.'.jpg';
    $out_s_img_pass = PHOTO_OUT_DIR.'/'.$tmp_file_name.'_s.jpg';

    //結果画像の調整=============================>
    //ブラー
    //$image->blurImage(5,3);
    //コントラストを強調
    //$image->normalizeImage(100);
    //ノイズを調整
    if($photo_data['noize_lv']==4){
      $image->addNoiseImage(Imagick::NOISE_LAPLACIAN);
    }elseif($photo_data['noize_lv']==3){
      $image->addNoiseImage(Imagick::NOISE_POISSON);
    }elseif($photo_data['noize_lv']==2){
      $image->addNoiseImage(Imagick::NOISE_IMPULSE);
    }
    //明度、飽和度、色相 [1:明度　2:飽和度差 3:色相(0-100)]
    $image->modulateImage(80, 60, 90);
    if($target_flag==1){
      //ターゲットマーカーの追加
      $j = 1;
      foreach($goast_positions as $goast_position){
        $mapx = $goast_position['mapx'];
        $mapy = $goast_position['mapy'];
        ${'position_draw_'.$j}=new ImagickDraw();
        ${'position_draw_'.$j}->setFillColor('white');
        ${'position_draw_'.$j}->setStrokeColor("#FF0000");
        ${'position_draw_'.$j}->setStrokeWidth(2);
        ${'position_draw_'.$j}->setStrokeAlpha(1);
        ${'position_draw_'.$j}->setFillAlpha(0);
        ${'position_draw_'.$j}->ellipse($mapx+75,$mapy+75,30,30,0,360);
        $image->drawImage(${'position_draw_'.$j});
        $j+=1;
      }
    }
    $image->writeImage("$out_img_pass");

    //サムネイル用に小さく作り直す
    $image -> thumbnailImage(300,0);
    $image->writeImage("$out_s_img_pass");
    $width = $image->getImageWidth();
    $height = $image->getImageHeight();
    $this->set('img_w',$width);
    $this->set('img_h',$height);
    //画面表示用
    $img_url = '/img/out/'.$tmp_file_name.'_s.jpg';
    $this->set('tmp_file_name',$tmp_file_name);
    $this->set('img_url',$img_url);

    //現在の枚数を数える
    $count = $this->Photo->findCount(array('member_id'=>$member_id,'valid_flag'=>1));
    if($count >= SAVE_PHOTO_COUNT){
      $this->set('storage_free_flag',0);
    }else{
      $this->set('storage_free_flag',1);
    }

    //ダウンロード用のＵＲＬ設定
    $down_path = BASE_URL.'/php/download.php?file=/var/www/html/shinrei/cake/app/webroot/img/out/'.$tmp_file_name.'.jpg';
    $this->set('down_path',$down_path);
  }

  function return_lv_state($level){
    //個数
    $face_count = 1 + floor($level/10);
    $data['face_count'] = $face_count;
    //透過率
    $data['alpha'] = 0.1;
    //その他
    return $data;
  }

  function return_photo_state($level){
    //ノイズ種類
    //lv1.NOISE_UNIFORM lv2.NOISE_IMPULSE lv3.NOISE_POISSON lv4.NOISE_LAPLACIAN
    if($level>90){
      $data['noize_lv'] = 4;
    }elseif($level>80){
      $data['noize_lv'] = 3;
    }elseif($level>70){
      $data['noize_lv'] = 2;
    }else{
      $data['noize_lv'] = 1;
    }
    return $data;
  }

  function throw_mixi($img_name){
    $upimg_url = BASE_URL.'/img/out/'.$img_name.'.jpg';
    $this->set('upimg_url',$upimg_url);
  }

  function delete_photo($photo_id){
    $this->session_manage();
    $member_id = $this->session_data['id'];
  }

  private function getExtension($ext){
      if($ext == "image/jpeg"){return "jpg";}
      elseif($ext == "image/jpg"){return "jpg";}
      elseif($ext == "image/png"){return "png";}
      elseif($ext == "image/gif"){return "gif";}
      elseif($ext == "image/bmp"){return "bmp";}
      elseif($ext == "image/x-png"){return "png";}
      elseif($ext == "image/pjpeg"){return "jpg";}
      else{return false;}
  }

  function session_manage(){
    $session_data = $this->Session->read("member_info");
    $this->session_data = $session_data;
    if(strlen($session_data['id'])==0){
      $this->redirect('/login/session_timeout/');
    }
  }
}