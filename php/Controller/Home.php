<?php
/**
 * Limp - less is more in PHP
 * @copyright   Bill Rocha - http://google.com/+BillRocha
 * @license     MIT
 * @author      Bill Rocha - prbr@ymail.com
 * @version     0.0.1
 * @package     Controller
 * @access      public
 * @since       0.3.0
 *
 * The MIT License
 *
 * Copyright 2015 http://google.com/+BillRocha.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Controller;

use Controller\Guest\Base;
use Model;
use Neo\App;
use Neo\Data;

/**
 * Description of home
 *
 * @author Bill
 */
class Home extends Base 
{

    function index() 
    {
        $key = str_replace(array("\r",
                                 "\n",
                                 "-----BEGIN PUBLIC KEY-----",
                                 "-----END PUBLIC KEY-----"),
                              '',
                              file_get_contents(_CONFIG.'keys/public.key'));
        $this->response('index',null,'index',['KEY'=>$key],['login'],['index']);
    }


    function login()
    {
        if(isset($_POST['key'])
        	&& trim($_POST['key']) !== ''){ 

            //Decodificando RSA
            $private = file_get_contents(_CONFIG.'keys/private.key');
            $key = base64_decode($_POST['key']);
            if(!openssl_private_decrypt($key, 
                                        $key, 
                                        openssl_pkey_get_private($private)
                                        )) App::go();
            $key = json_decode($key);

            //Verificando se a senha e login existem
        	$model = new Model\Usuario;
	       	$login = $model->login($key->login, $key->password);
        	if($login !== false){ 
                
        		//Gravando o novo Token no BD
        		$model->setToken($login->get('id'), $key->asskey);

                //Encriptando o token
                Data\Aes::size(256);
                $token = Data\Aes::enc($login->get('login').$login->get('senha'), $key->asskey);

				//Gerando a sessão
				session_start();
        		$_SESSION['login'] = $login->get('id');
        		$_SESSION['token'] = $token; 

        		//Encaminha para a página do TIPO DE USUARIO
        		//TO DO: definir qual a página de usuário
        		App::go('dashboard');
        	}
        }
        //Em casos contrários, retorna a página inicial.
        App::go();
    }


    function logout()
    {
    	//Destruindo a session
    	session_start();
        $_SESSION['login'] = false;
        $_SESSION['token'] = '';
        session_destroy();

        //Vai para a página inicial
        App::go();
    }
}