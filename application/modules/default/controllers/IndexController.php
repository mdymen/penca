<?php

include APPLICATION_PATH.'/models/users.php';
include APPLICATION_PATH.'/models/pencas.php';
include APPLICATION_PATH."/helpers/data.php";
include APPLICATION_PATH."/helpers/html.php";
include APPLICATION_PATH."/helpers/translate.php";
include APPLICATION_PATH.'/helpers/box.php';
class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function testAction() {
        
        print_r($config->host);
        die(".");
    }
    
    public function indexAction() {
        try {
            $params = $this->_request->getParams();
            //$this->view->error = $params['error'];
            
            $ordem = "";
            if (!empty($params['ordem'])) {
                $ordem = $params['ordem'];
            }

            $storage = new Zend_Auth_Storage_Session();
            $data = (get_object_vars($storage->read()));

            $result = new Application_Model_Result();
            $em_acao_group = $result->palpites_em_acao_group($data['us_id'], $ordem);

            $config = new Zend_Config_Ini('config.ini');

            $h_date = new Helpers_Data();
            $this->view->palpites = $em_acao_group;

        }
        catch (Exception $e) {
//             $config = new Zend_Config_Ini("config.ini");
//            $this->redirect("/index/logout");
        }
    }
    
    public function puntuacaoAction() {
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read()));
        
//        $result = new Application_Model_Result();
        $results = new Application_Model_Users();
        
        $resultados = array();
        
        $id_user = $data['us_id'];
        
        $resultados['perdidos'] = $results->getLostMatches($id_user);
        $resultados['ganados'] = $results->getWonMatches($id_user);
        $resultados['jogados'] = $results->getPlayedMatches($id_user);
        $resultados['pontos'] = $results->getPoints($id_user);
        $resultados['position'] = $results->getPoisition($id_user);
//        $ganados = $result->ganados($data['us_id']);
//        $perdidos = $result->perdidos($data['us_id']);
//        $puntuacao = $result->puntuacao($data['us_id']);
//        
//        $resultados = array($ganados[0], $perdidos[0], $puntuacao[0]);
//        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($resultados);
        
    }
    
    public function registerAction() {
       $params = $this->_request->getParams();
       
       $user = new Application_Model_Users();
       $usuario = $user->load_user($params['username']);
       
       if (empty($usuario)) {
            $user->save($params);
            $this->login1($params['username'], $params['password']);
       } else {
           $this->redirect("?register&error=yes");
       }
       
       
    }
    
    function logoutAction() {
        $storage = new Zend_Auth_Storage_Session();
        $storage->clear();
        $this->_redirect();
    }           
    
    public function loginAction() {
        $params = $this->_request->getParams();
        
        $user = $params["username"];
        $password = $params["password"];
        
        $error = false;
        
        $users = new Application_Model_Users();
        $auth = Zend_Auth::getInstance();
        $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(),'user');
        $authAdapter->setIdentityColumn('us_username')
                    ->setCredentialColumn('us_password');
        $authAdapter->setIdentity($user)
                    ->setCredential($password);

        $result = $auth->authenticate($authAdapter);
        
        if ($result->isValid()) {         
            $storage = new Zend_Auth_Storage_Session();
            $storage->write($authAdapter->getResultRowObject());
            $this->_redirect('/index');    
        } else {
            $error = true;
            $this->_redirect('/index?error=yes');
        }

    }
    
    public function registerteamsAction() {
        
    }   
    
    public function registerteamsformAction() {
        $params = $this->_request->getParams();
        
        print_r($params["teams"]);
        die(".");
        
        $teams = explode(' ', $params["teams"]);
        print_r($teams);
        die(".");
    }
    
    public function teamsAction() {
        
        $teams = new Application_Model_Teams();
        $ts = $teams->load_teams_championship(1);
        
        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($ts);
    }
    
   
    public function loginfaceAction() {
        $params = $this->_request->getParams();
        
        $us = new Application_Model_Users();
        $result = $us->isUserRegistered($params['facebookid']);
        
        if (empty($result)) {
            $us->facebookUserSave($params['facebookid']);
        }
           
        if (!empty($params['facebookid'])) {        
            $this->login1($result['us_username'], $result['us_password']);
        }
//        $this->_request->setParams(array("username" => $params['facebookid'], "password" => $params['facebookid']));
        //$this->forward("login", "index", "default",array("username" => $params['facebookid'], "password" => $params['facebookid']));
//           $this->_helper->redirector("login", "index", "default", array("username" => $params['facebookid'], "password" => $params['facebookid']));
       // $this->redirect("/index/login",array("username" => $params['facebookid'], "password" => $params['facebookid']));

        $this->getResponse()
         ->setHeader('Content-Type', 'application/json');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $this->_helper->json($result);
        
        
    }
    
    public function login1($usuario, $pass) {
        //$params = $this->_request->getParams();
        
        $user = $usuario;
        $password = $pass;
        
//        print_r($params);
//        die(".");
        
        $users = new Application_Model_Users();
        $auth = Zend_Auth::getInstance();
        $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(),'user');
        $authAdapter->setIdentityColumn('us_username')
                    ->setCredentialColumn('us_password');
        $authAdapter->setIdentity($user)
                    ->setCredential($password);

        $result = $auth->authenticate($authAdapter);
        
        if ($result->isValid()) {         
            $storage = new Zend_Auth_Storage_Session();
            $storage->write($authAdapter->getResultRowObject());
            
        }
        
//        $this->_redirect('/index');
    }
    
    public function addfacebookuserAction() {
//        $this->render("addfacebookuser")
    }
    
}

