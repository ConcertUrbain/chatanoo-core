<?php

  /**
   * Permet d'interagir avec les questions et la base de donnŽes
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Service
   */

  /**
   * Interface de service ayant des datas
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once(dirname(__FILE__) . '/Interface/Data.php');

  /**
   * Interface de service ayant des mŽtadonnŽes
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once(dirname(__FILE__) . '/Interface/Meta.php');

  /**
   * Interface de service ayant des auteurs
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once(dirname(__FILE__) . '/Interface/User.php');

  /**
   * Interface de service ayant des fonctions de modŽration
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once(dirname(__FILE__) . '/Interface/Validate.php');

  /**
   * Classes d'abstraction des services
   *
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   */
  require_once(dirname(__FILE__) . '/Abstract.php');

  /* user defined includes */

  /* user defined constants */

  /**
   * Permet d'interagir avec les questions et la base de donnŽes
   *
   * @access public
   * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
   * @package Service
   */
  class Service_Queries extends Service_Abstract implements Service_Interface_Meta,
                                        Service_Interface_User,
                                        Service_Interface_Validate,
                                        Service_Interface_Data
  {
      // --- ASSOCIATIONS ---


      // --- ATTRIBUTES ---

      /**
       * Passerelles vers la table des questions
       *
       * @access private
       * @var Table_Queries
       */
      private $_queriesTable = null;

      /**
       * Passerelles vers la table des sessions
       *
       * @access private
       * @var Table_Sessions
       */
      private $_sessionsTable = null;

      /**
       * Passerelles vers la table des items
       *
       * @access private
       * @var Table_Items
       */
      private $_itemsTable = null;

      /**
       * Service des items
       *
       * @access protected
       * @var Service_Items
       */
      protected $_itemsService = null;

      /**
       * Passerelles vers la table de liaison entre les questions et les items
       *
       * @access protected
       * @var Table_QueriesAssocItems
       */
      protected $_queriesAssocItemsTable = null;

      /**
       * Passerelles vers la table de liaison entre les sessions de les questions
       *
       * @access protected
       * @var Table_SessionsAssocQueries
       */
      protected $_sessionsAssocQueriesTable = null;

      /**
       * Passerelles vers la table de liaison des datas
       *
       * @access protected
       * @var Table_Datas_Assoc
       */
      protected $_datasAssocTable = null;

      /**
       * Passerelles vers la table de liaison des metas
       *
       * @access protected
       * @var Table_MetasAssoc
       */
      protected $_metasAssocTable = null;

      /**
       * Passerelles vers la table de liaison des metas
       *
       * @access protected
       * @var Table_Media_Assoc
       */
      protected $_mediasAssocTable = null;

      /**
       * Service des datas
       *
       * @access protected
       * @var Service_Datas
       */
      protected $_datasService = null;

      /**
       * Service des mŽdias
       *
       * @access protected
       * @var Service_Data
       */
      protected $_mediasService = null;

      /**
       * Service de recherche
       *
       * @access protected
       * @var Service_Search
       */
      protected $_searchService = null;

      // --- OPERATIONS ---

      /**
       * Constructeur de la classe
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @return mixed
       */
      public function __construct()
      {
          $this->_queriesTable = new Table_Queries();
          $this->_sessionsTable = new Table_Sessions();
          $this->_itemsTable = new Table_Items();
          $this->_datasAssocTable = new Table_Datas_Assoc();
          $this->_mediasAssocTable = new Table_Medias_Assoc();
          $this->_queriesAssocItemsTable = new Table_QueriesAssocItems();
          $this->_sessionsAssocQueriesTable = new Table_SessionsAssocQueries();
          $this->_datasService = new Service_Datas();
          $this->_itemsService = new Service_Items();
          $this->_mediasService = new Service_Medias();
          $this->_searchService = new Service_Search();
          $this->_metasAssocTable = new Table_MetasAssoc();
      }

      /**
       * Retourne toutes les questions contenues dans la base de donnŽes en
       * de options
       * Options:
       *  - where -> array(array('cond', 'value'))
       *  - order  -> string
       *  - limit  -> array(count, offset)
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  array options Options pour le retour de la fonction
       * @return array
       */
      public function getQueries($options = array())
      {
        $queries = array();
        $select = Zend_Registry::get('db')->select();
      $select->from('sessions_assoc_queries', null)
          ->join('queries', 'sessions_assoc_queries.queries_id = queries.id')
          ->where("sessions_assoc_queries.sessions_id = ?", Zend_Registry::get('sessionID'));
        if(count($options))
        {
          foreach($options['where'] as $key=>$where)
            $select->where('queries.' . $where[0], $where[1]);
          if(isset($options['order']))
            $select->order($options['order']);
          if(isset($options['limit']))
            $select->limit($options['limit'][0], $options['limit'][1]);
        }
          $queriesRowset = Zend_Registry::get('db')->fetchAll($select);
      if(count($queriesRowset))
            $queries = Vo_Factory::getInstance()->rowsToVoArray(Vo_Factory::$QUERY_TYPE, $queriesRowset);
          return $queries;
      }

      /**
       * Retourne une question de la base de donnŽes en fonction de son
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int queryId Identifiant d'une question
       * @return Vo_Query
       */
      public function getQueryById($queryId)
      {
        $query = null;
        
        $select = Zend_Registry::get('db')->select();
      $select->from('sessions_assoc_queries', null)
          ->join('queries', 'sessions_assoc_queries.queries_id = queries.id')
          ->where("queries.id = ?", $queryId)
          ->where("sessions_assoc_queries.sessions_id = ?", Zend_Registry::get('sessionID'));
        
          $queryRow = Zend_Registry::get('db')->fetchRow($select);
        if(!is_null($queryRow) && $queryRow)
            $query = Vo_Factory::getInstance()->factory(Vo_Factory::$QUERY_TYPE, $queryRow);
          return $query;
      }

      /**
       * Retourne toutes les questions contenues dans une session
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int sessionId Identifiant d'une session
       * @return array
       */
      public function getQueriesBySessionId($sessionId)
      {
        if($sessionId != Zend_Registry::get('sessionID'))
          return array();
        
        $queries = array();
        $sessionsRow = $this->_sessionsTable->find($sessionId)->current();
        if($sessionsRow)
        {
        $queriesRowset = $sessionsRow->findManyToManyRowset('Table_Queries', 'Table_SessionsAssocQueries');
        if($queriesRowset->count())
              $queries = Vo_Factory::getInstance()->rowsetToVoArray(Vo_Factory::$QUERY_TYPE, $queriesRowset);
        }
          return $queries;
      }

      /**
       * Retourne toutes les questions contenant un item
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int sessionId Identifiant d'une session
       * @return array
       */
      public function getQueriesByItemId($itemId)
      {
        $queries = array();
        
        $select = $this->_itemsTable->select();
        $select->where("id = ?", $itemId);
        $select->where("sessions_id = ?", Zend_Registry::get('sessionID'));
        
        $itemRow = $this->_itemsTable->fetchRow($select);
        if($itemRow)
        {
        $queriesRowset = $itemRow->findManyToManyRowset('Table_Queries', 'Table_QueriesAssocItems');
        if($queriesRowset->count())
              $queries = Vo_Factory::getInstance()->rowsetToVoArray(Vo_Factory::$QUERY_TYPE, $queriesRowset);
        }
          return $queries;
      }

      /**
       * Retourne toutes les questions pour une metadonnée
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int metaId Identifiant d'une métadonnée
       * @return array
       */
      public function getQueriesByMetaId($metaId)
      {
        $queries = array();
        
          $select = Zend_Registry::get('db')->select();
        $table = 'queries';
      $select->from('metas_assoc', null)
          ->join($table, 'metas_assoc.assoc_id = '.$table.'.id')
          ->join('sessions_assoc_queries', 'sessions_assoc_queries.queries_id = '.$table.'.id')
          ->where('metas_assoc.metas_id = ?', $metaId)
          ->where("metas_assoc.assocType = ?", 'Item')
          ->where("sessions_assoc_queries.sessions_id = ?", Zend_Registry::get('sessionID'));
      $queriesRows = Zend_Registry::get('db')->fetchAll($select);
      if(count($queriesRows))
            $queries = Vo_Factory::getInstance()->rowsToVoArray(Vo_Factory::$QUERY_TYPE, $queriesRows);
          return $queries;
      }

      /**
       * Ajoute une question ˆ la base de donnŽes
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  Vo_Query query Une question
       * @return int Identifiant de la question
       */
      public function addQuery( Vo_Query $query)
      {
      $queryRow = $this->_queriesTable->createRow($query->toRowArray());
      $queryRow->addDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
      $queryRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
      $queryRow->users_id = Zend_Registry::get('userID');
      
      $id = $queryRow->save();
      
      $assoc = $this->_sessionsAssocQueriesTable->createRow();
      $assoc->queries_id = $id;
      $assoc->sessions_id = Zend_Registry::get('sessionID');
      $assoc->save();
      
      return $id;
      }

      /**
       * Modifie une question dans la base de donnŽes
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  Vo_Query query Une question
       * @return void
       */
      public function setQuery( Vo_Query $query)
      {
          $queryRow = $this->_queriesTable->find($query->id)->current();
          $sessionsRowset = $queryRow->findManyToManyRowset('Table_Sessions', 'Table_SessionsAssocQueries');
          if($sessionsRowset->count())
          {
            $flag = false;
            foreach($sessionsRowset->toArray() as $key=>$value)
            {
              if($value['id'] == Zend_Registry::get('sessionID') && !$flag)
                $flag = true;
            }
            if(!$flag)
              return;
          }   
          else
            return;
          
        $queryRowArray = $query->toRowArray();
      foreach($queryRowArray as $key=>$value)
      {
        if($queryRowArray[$key] != $queryRow->$key)
          $queryRow->$key = $value;
      }
      $queryRow->setDate = Zend_Date::now()->toString('YYYY.MM.dd HH:mm:ss');
      return $queryRow->save();
      }

      /**
       * Supprime une question de la base de donnŽes
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int queryId Identifiant d'une question
       * @return void
       */
      public function deleteQuery($queryId)
      {
          $queryRow = $this->_queriesTable->find($queryId)->current();
          if(is_null($queryRow) || !$queryRow)
            return true;
          
          $sessionsRowset = $queryRow->findManyToManyRowset('Table_Sessions', 'Table_SessionsAssocQueries');
          if($sessionsRowset->count())
          {
            $flag = false;
            foreach($sessionsRowset->toArray() as $key=>$value)
            {
              if($value['id'] == Zend_Registry::get('sessionID') && !$flag)
                $flag = true;
            }
            if(!$flag)
              return true;
          }   
          else
            return true;
          
      if($queryRow->delete())
      {
        $this->_queriesAssocItemsTable->delete('queries_id = ' . $queryId);
        $this->_sessionsAssocQueriesTable->delete('queries_id = ' . $queryId);
        $where = array(
          "assoc_id = '" . $queryId . "'",
          "assocType = 'Query'"
        );
        $this->_datasAssocTable->delete($where);
        $this->_metasAssocTable->delete($where);
        $this->_mediasAssocTable->delete($where);
      }
      return true;
      }

      /**
       * Ajoute un item ˆ un question
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  Vo_Item item Un item
       * @param  int queryId Identifiant d'une question
       * @return int Identifiant du nouvel item
       */
      public function addItemIntoQuery( Vo_Item $item, $queryId)
      {
        if(!$item->id)
          $item->id = $this->_itemsService->addItem($item);

        $linkRow = $this->_queriesAssocItemsTable->createRow();
        $linkRow->queries_id = $queryId;
        $linkRow->items_id = $item->id;
        $linkRow->save();
        return $item->id;
      }

      /**
       * Retire un item d'une question
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int itemId Identifiant d'un item
       * @param  int queryId Identifiant d'une question
       * @return void
       */
      public function removeItemFromQuery($itemId, $queryId)
      {
        $where = array(
          "queries_id = '" . $queryId . "'",
          "items_id = '" . $itemId . "'",
        );
          $this->_queriesAssocItemsTable->delete($where);
      return true;
      }

      /**
       * Ajoute un mŽdia ˆ un item
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  Vo_Media_Abstract media Un mŽdia
       * @param  int queryId Identifiant d'une question
       * @return void
       */
      public function addMediaIntoQuery( Vo_Media_Abstract $media, $queryId)
      {
        if(!$media->id)
          $media->id = $this->_mediasService->addMedia($media);

        $linkRow = $this->_mediasAssocTable->createRow();
        $linkRow->medias_id = $media->id;
        $linkRow->mediaType = $media->getType();
        $linkRow->assoc_id = $queryId;
        $linkRow->assocType = 'Query';
        $linkRow->save();
        return $media->id;
      }

      /**
       * Retire un mŽdia d'un item
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int mediaId Identifiant d'un mŽdia
       * @param  string mediaType Type du mŽdia
       * @param  int queryId Identifiant d'une question
       * @return void
       */
      public function removeMediaFromQuery($mediaId, $mediaType, $queryId)
      {
        $where = array(
          "medias_id = '" . $mediaId . "'",
          "mediaType = '" . $mediaType. "'",
          "assoc_id = '" . $queryId. "'",
          "assocType = 'Query'"
        );
          $this->_mediasAssocTable->delete($where);
      return true;
      }

      /**
       * Ajoute une mŽtadonnŽes dans le Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  Vo_Meta meta Un mŽtadonnŽe
       * @param  int voId Identifiant du Value Object
       * @return int Identifiant de la nouvelle mŽtadonnŽe
       */
      public function addMetaIntoVo( Vo_Meta $meta, $voId)
      {
          if(!$meta->id)
            $meta->id = $this->_searchService->addMeta($meta);

          $linkRow = $this->_metasAssocTable->createRow();
          $linkRow->metas_id = $meta->id;
          $linkRow->assoc_id = $voId;
          $linkRow->assocType = 'Query';
          $linkRow->save();
          return $meta->id;
      }

      /**
       * Retire une mŽtadonnŽe du Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int metaId Identifiant d'une mŽtadonnŽe
       * @param  int voId Identifiant du Value Object
       * @return void
       */
      public function removeMetaFromVo($metaId, $voId)
      {
        $where = array(
          "metas_id = '" . $metaId . "'",
          "assoc_id = '" . $voId . "'",
          "assocType = 'Query'"
        );
          $this->_metasAssocTable->delete($where);
      return true;
      }

      /**
       * Ajoute un utilisateur dans le Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int voId Identifiant du Value Object
       * @return void
       */
      public function getUserFromVo($voId)
      {
      $queryRow = $this->_queriesTable->find($voId)->current();
      $userRow = $queryRow->findParentTable_Users();
      $user = new Vo_User($userRow);
      return $user;
      }

      /**
       * Retire un utilisateur du Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int userId Identifiant d'un utilisateur
       * @param  int voId Identifiant du Value Object
       * @return void
       */
      public function setUserOfVo($userId, $voId)
      {
      $queryRow = $this->_queriesTable->find($voId)->current();
      $queryRow->users_id = $userId;
      return $queryRow->save();
      }

      /**
       * Retourne tous les Values Objects du type du service ayant pour
       * celui prŽcisŽ
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int userId Identifiant d'un utilisateur
       * @return array
       */
      public function getVosByUserId($userId)
      {
        $select = $this->_queriesTable->select();
        $select->where('users_id = ?', $userId);

        $queries = array();
      $queriesRowset = $this->_queriesTable->fetchAll($select);
          $queries = Vo_Factory::getInstance()->rowsetToVoArray(Vo_Factory::$QUERY_TYPE, $queriesRowset);
          return $queries;
      }

      /**
       * Valide ou invalide un Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int voId Identifiant du Value Object
       * @param  bool trueOrFalse True pour valide et false pour invalide
       * @param  bool all Si true, alors tous les enfants du Value Object seront validŽs ou invalidŽs
       * @return void
       */
      public function validateVo($voId, $trueOrFalse, $all = false)
      {
      $queryRow = $this->_queriesTable->find($voId)->current();
      $queryRow->isValid = $trueOrFalse ? 1 : 0;
      return $queryRow->save();
      }

      /**
       * Ajoute une data dans le Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  Vo_Data_Abstract data Une data
       * @param  int voId Identifiant du Value Object
       * @return int Identifiant de la nouvelle data
       */
      public function addDataIntoVo( Vo_Data_Abstract $data, $voId)
      {
      if(!$data->id)
        $data->id = $this->_datasService->addData($data);
      $linkRow = $this->_datasAssocTable->createRow();
      $linkRow->datas_id = $data->id;
      $linkRow->dataType = $data->getType();
      $linkRow->assoc_id = $voId;
      $linkRow->assocType = 'Query';
      $linkRow->save();
      return $data->id;
      }

      /**
       * Retire une data du Value Object
       *
       * @access public
       * @author Mathieu DesvŽ, <mathieu.desve@unflux.fr>
       * @param  int dataId Identifaint d'une data
       * @param  string dataType Type de la data
       * @param  int voId Identifiant du Value Object
       * @return void
       */
      public function removeDataFromVo($dataId, $dataType, $voId)
      {
      $this->_datasAssocTable->delete("datas_id = '" . $dataId . "' AND dataType = " . $this->_datasAssocTable->getAdapter()->quote($dataType) . " AND assoc_id = '" . $voId . "' AND assocType = 'Query'");
      return true;
      }

  } /* end of class Service_Queries */