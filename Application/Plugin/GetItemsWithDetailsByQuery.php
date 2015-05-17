<?php

  class Plugin_GetItemsWithDetailsByQuery extends Plugin_Abstract
  {
      /**
       * Tableau contenant les passerelles vers les diffŽrentes tables de data
       *
       * @access protected
       * @var array
       */
      protected $_datasTables = array();

      /**
       * Tableau contenant les passerelles vers les diffŽrentes tables de media
       *
       * @access protected
       * @var array
       */
      protected $_mediasTables = array();

      /**
       * @var Zend_Db_Adapter_Abstract
       */
      protected $_db;

    public function __construct()
    {
      $this->_db = Zend_Registry::get('db');
      foreach(Zend_Registry::get('config')->datas->data as $data)
      {
        $className = $data->tableClass;
        $this->_datasTables[$data->type] = new $className;
      }
      foreach(Zend_Registry::get('config')->medias->media as $media)
      {
        $className = $media->tableClass;
        $this->_mediasTables[$media->type] = new $className;
      }
    }

    public function execute()
    {
      $params = $this->getParams();
      $result = array();

      // RŽcupŽration des items
      $is = new Service_Items();
      $items = $is->getItemsByQueryId($params[0]);

      if($items && count($items) > 0)
      {
        // RŽcupŽration de leurs auteurs
        $users = $this->_getUsersByItems($items);

        // RŽcupŽration de leurs mŽtadonnŽes
        $metas = $this->_getMetasByItems($items);

        // RŽcupŽration de leurs datas
        $datas = $this->_getDatasByItems($items);

        // RŽcupŽration de leurs medias
        $medias = $this->_getMediasByItems($items);

        // RŽcupŽration de leurs commentaires
        $comments = $this->_getCommentsByItems($items);

        // RŽcupŽration des votes des commentaires
        $commentsVotes = count($comments) > 0 ? $this->_getVotesByComments($comments) : array();

        // CrŽation de la rŽponse
        foreach($items as $key=>$item)
        {
          $itemArray = array();

          $itemArray['VO'] = $item;
          $itemArray['user'] = ($users && count($users) > 0) ? $this->_getUserOfItem($item, $users) : null;
          $itemArray['datas'] = ($datas && count($datas) > 0) ? $this->_getDatasOfItem($item, $datas) : null;
          $itemArray['comments'] = ($comments && count($comments) > 0) ? $this->_getCommentsOfItem($item, $comments) : null;
          $itemArray['medias'] = ($medias && count($medias) > 0) ? $this->_getMediasOfItem($item, $medias) : null;
          $itemArray['metas'] = ($metas && count($metas) > 0) ? $this->_getMetasOfItem($item, $metas) : null;
          $itemArray['rate'] = $this->_getRatesOfItem(
            $item,
            ($datas && count($datas) > 0 && array_key_exists('Vote', $datas)) ? $datas['Vote'] : null,
            ($comments && count($comments) > 0) ? $comments : null,
            ($commentsVotes && count($commentsVotes) > 0) ? $commentsVotes : null
          );

                  array_push($result, $itemArray);
        }
      }

      return $result;
    }


    ////////////////////////////////////////////////////

    private function _getUsersByItems($items)
    {
      $users = array();

        $select = $this->_db->select();
      $select->from('users');
        $selectOrWhere = array();
      foreach($items as $key=>$item)
        array_push($selectOrWhere, $this->_db->quoteInto("id = ?", $item->user));
      $select->where(implode(' '.Zend_Db_Select::SQL_OR.' ', $selectOrWhere));
      $usersRows = $this->_db->fetchAll($select);
      if(count($usersRows))
        $users = $usersRows;

      return $users;
    }

    private function _getMetasByItems($items)
    {
      $metas = array();
        $select = $this->_db->select();
      $select->from('metas_assoc')
          ->join('metas', 'metas_assoc.metas_id = metas.id');
        $selectOrWhere = array();
      foreach($items as $key=>$item)
        array_push($selectOrWhere, $this->_db->quoteInto("metas_assoc.assoc_id = ?", $item->id));
      $select->where(implode(' '.Zend_Db_Select::SQL_OR.' ', $selectOrWhere));
      $select->where("metas_assoc.assocType = ?", Vo_Factory::$ITEM_TYPE);
      $metasRows = $this->_db->fetchAll($select);
      if(count($metasRows))
        $metas = $metasRows;
      return $metas;
    }

    private function _getDatasByItems($items)
    {
      $datas = array();
        $selectOrWhere = array();
      foreach($items as $key=>$item)
        array_push($selectOrWhere, $this->_db->quoteInto('datas_assoc.assoc_id = ?', $item->id));
      foreach($this->_datasTables as $dataTable)
      {
          $select = $this->_db->select();
          $table = $dataTable->getTableName();
        $select->from('datas_assoc')
            ->join($table, 'datas_assoc.datas_id = '.$table.'.id')
            ->where('datas_assoc.dataType = ?', $dataTable->getDataType())
            ->where(implode(' '.Zend_Db_Select::SQL_OR.' ', $selectOrWhere))
            ->where("datas_assoc.assocType = ?", Vo_Factory::$ITEM_TYPE);
        $datasRows = $this->_db->fetchAll($select);
        if(count($datasRows))
          $datas[$dataTable->getDataType()] = $datasRows;
      }
      return $datas;
    }

    private function _getMediasByItems($items)
    {
      $medias = array();
        $selectOrWhere = array();
      foreach($items as $key=>$item)
        array_push($selectOrWhere, $this->_db->quoteInto('medias_assoc.assoc_id = ?', $item->id));
      foreach($this->_mediasTables as $mediaTable)
      {
          $select = $this->_db->select();
          $table = $mediaTable->getTableName();
        $select->from('medias_assoc')
            ->join($table, 'medias_assoc.medias_id = '.$table.'.id')
            ->where('medias_assoc.mediaType = ?', $mediaTable->getMediaType())
            ->where(implode(' '.Zend_Db_Select::SQL_OR.' ', $selectOrWhere))
            ->where("medias_assoc.assocType = ?", Vo_Factory::$ITEM_TYPE);
        $mediasRows = $this->_db->fetchAll($select);
        if(count($mediasRows))
          $medias[$mediaTable->getMediaType()] = $mediasRows;
      }
      return $medias;
    }

    private function _getCommentsByItems($items)
    {
      $comments = array();
        $selectOrWhere = array();
      foreach($items as $key=>$item)
        array_push($selectOrWhere, $this->_db->quoteInto('comments.items_id = ?', $item->id));
        $select = $this->_db->select();
      $select->from('comments')
          ->where(implode(' '.Zend_Db_Select::SQL_OR.' ', $selectOrWhere));
      $commentsRows = $this->_db->fetchAll($select);
      if(count($commentsRows))
        $comments = Vo_Factory::getInstance()->rowsToVoArray(Vo_Factory::$COMMENT_TYPE, $commentsRows);
      return $comments;
    }

    private function _getVotesByComments($comments)
    {
      $commentsVotes = array();
        $select = $this->_db->select();
        $table = $this->_datasTables['Vote']->getTableName();
        $selectOrWhere = array();
      foreach($comments as $key=>$comment)
        array_push($selectOrWhere, $this->_db->quoteInto('datas_assoc.assoc_id = ?', $comment->id));
      $select->from('datas_assoc')
          ->join($table, 'datas_assoc.datas_id = '.$table.'.id')
          ->where('datas_assoc.dataType = ?', $this->_datasTables['Vote']->getDataType())
          ->where(implode(' '.Zend_Db_Select::SQL_OR.' ', $selectOrWhere))
          ->where("datas_assoc.assocType = ?", Vo_Factory::$COMMENT_TYPE);
      $commentsVotesRows = $this->_db->fetchAll($select);
      if(count($commentsVotesRows))
        $commentsVotes = $commentsVotesRows;

      return $commentsVotes;
    }


    ////////////////////////////////////////////////////

    private function _getUserOfItem($item, $users)
    {
      foreach($users as $key=>$user)
      {
        if($user['id'] == $item->user)
        {
          return Vo_Factory::getInstance()->factory(Vo_Factory::$USER_TYPE, $user);
        }
      }
      return null;
    }

    private function _getCommentsOfItem($item, $comments)
    {
      $return = array();
      foreach($comments as $key=>$comment)
      {
        if($comment->item == $item->id)
        {
          array_push($return, $comment);
        }
      }
      return $return;
    }

    private function _getDatasOfItem($item, $datas)
    {
      $return = array();
      foreach($datas as $key=>$ds)
      {
        $return[$key] = array();
        foreach($ds as $data)
        {
          if($data['assoc_id'] == $item->id)
          {
            unset($data['datas_id']);
            unset($data['dataType']);
            unset($data['assoc_id']);
            unset($data['assocType']);
            array_push($return[$key], Vo_Data_Factory::getInstance()->factory($this->_datasTables[$key]->getDataVoClass(), $data));
          }
        }
      }
      return $return;
    }

    private function _getMediasOfItem($item, $medias)
    {
      $return = array();
      foreach($medias as $key=>$ms)
      {
        $return[$key] = array();
        foreach($ms as $media)
        {
          if($media['assoc_id'] == $item->id)
          {
            unset($media['medias_id']);
            unset($media['mediaType']);
            unset($media['assoc_id']);
            unset($media['assocType']);
            array_push($return[$key], Vo_Media_Factory::getInstance()->factory($this->_mediasTables[$key]->getMediaVoClass(), $media));
          }
        }
      }
      return $return;
    }

    private function _getMetasOfItem($item, $metas)
    {
      $return = array();
      foreach($metas as $key=>$meta)
      {
        if($meta['assoc_id'] == $item->id)
        {
          unset($meta['metas_id']);
          unset($meta['assoc_id']);
          unset($meta['assocType']);
          array_push($return, Vo_Factory::getInstance()->factory(Vo_Factory::$META_TYPE, $meta));
        }
      }
      return $return;
    }

    private function _getRatesOfItem($item, $itemsVotes, $comments, $commentsVotes)
    {
      $return = 0;
      if($itemsVotes && count($itemsVotes) > 0)
      {
        foreach($itemsVotes as $data)
        {
          if($data['assoc_id'] == $item->id)
            $return += $data['rate'];
        }
      }
      if($comments && count($comments) > 0 && $commentsVotes && count($commentsVotes) > 0)
      {
        foreach($comments as $comment)
        {
          foreach($commentsVotes as $vote)
          {
            if($comment->id == $vote['assoc_id'] && $comment->item == $item->id)
              $return += $vote['rate'];
          }
        }
      }
      return $return;
    }

  }