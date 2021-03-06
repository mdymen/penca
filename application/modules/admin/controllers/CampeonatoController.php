<?php
include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH.'/models/teams.php';
include APPLICATION_PATH.'/models/matchs.php';
//include APPLICATION_PATH.'/models/bd_adapter.php';
//include APPLICATION_PATH.'/helpers/data.php';
include APPLICATION_PATH.'/helpers/translate.php';
class Admin_CampeonatoController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function cerrarAction() {
        $params = $this->_request->getParams();
        
        $c = new Application_Model_Championships();
        $champs = $c->load();
        $this->view->champs = $champs;
        if (!empty($params)) {
            
            $this->view->champ = $params['champ'];
            
        }
    }

    /**
     * Recibe todas las variables del campeonato
     * y manda grabar en la base de datos
     */
    public function indexAction()
    {
        $body = $this->getRequest()->getRawBody();
        $params = Zend_Json::decode($body);
     
        $request = $params["form"];
        $request['ch_atualround'] = 1;
        $champ = new Application_Model_Championships();
        $champ->save($request);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);        
       
       $this->_helper->json($request); 
    }
    
    public function registerAction() {}

    public function getIdUser() { 
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));        
        return $data['us_id'];
    }    
    
    public function salvarjogoAction() {
        $params = $this->_request->getParams();
        
        $ronda = $params['ronda'];
        $date = $params['date'];
        $hora = $params['hora'];
        $team1 = $params['team1'];
        $team2 = $params['team2'];
        $champ = $params['champ'];  
        
        $helper = new Helpers_Data();
        $date = $helper->for_save($date);
        
        $info = array(
            'round' => $ronda, 
            'team1' => $team1,
            'team2' => $team2,
            'date' => $date.' '.$hora,
            'championship' => $champ);
        
        $m = new Application_Model_Matchs();
        $m->save($info);
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json(200);
    }
    
    public function palpitesAction() { 
        $params = $this->_request->getParams();
        
       $id_champ = $params['championship'];
       
       $u = new Application_Model_Championships();
       $champ = $u->load();
       $this->view->champs = $champ;
       $this->view->champ = $id_champ;
        
        if ($this->getRequest()->isPost()) {
            $r = new Application_Model_Result();
            $results = $r->getresultsbychamp($id_champ);
            $this->view->results = $results;
        }
    }
    
    public function backupAction() {
        $params = $this->_request->getParams();
        
        $c = new Application_Model_Championships();
        
        $this->view->champs = $c->load();
        
        if (isset($params['champ'])) {
            
            $champ = $params['champ'];            
            
            $this->view->champ = $c->getChamp($champ);            
            $this->view->rounds = $c->getrondas($champ);                        
            $this->view->teams = $c->getTeams($champ);           
            $this->view->matchs = $c->getMatchs($champ);
        }
    }
    
}

