<?php
class BatchSql extends AppModel
{
public $useTable = 'members';

  function select_autoincrement_id(){
    $strSql   = "select LAST_INSERT_ID() as id	\n";
    return $this->query($strSql);
  }

}

?>