<?php
/**
 * NEOS PHP FRAMEWORK
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

namespace Controller\App;

use Model;
use Neos;
use Neos\Data;

abstract class Base 
{
    public $model = null;
    public $key = null;

    public $scripts = ['all'];
    public $styles = ['all'];

    public $navbar = null;


    /** Abstratic Controller constructor
     *  -- Bypass it in your controller
     */
    function __construct() 
    {
        session_start();
        $this->checkTokenSession();

        $this->model = new Model\App\Base;
        $this->navbar = 'admin/navbar';
    }

    /** Default MAIN method
     * -- Bypass it in your controller
     */
    function main() 
    {
        $d = new Neos\Html('nopage');
        $d->sendCache();
        $d->val('title', 'Zumbi :: 404')
                ->insertStyles(['reset', 'nopage'])
                ->body('nopage')
                ->render()
                ->send();
    }

    // ----------- USER FUNCTIONS --------------
    
    /**
     * checa se a sessão tem um token válido
     * @param  string $url Vai para a URL indicada em caso de falha
     * @return bool        Retorna TRUE
     */
    final function checkTokenSession($url = '')
    {
        
        return true;

        if(!isset($_SESSION['token'])) App::go($url);

        $db = (new Model\User)->getById($_SESSION['login']);

        Data\Aes::size(256);
        $dec = Data\Aes::dec($_SESSION['token'], $db['token']);

        if($dec){
            if($db['login'].$db['senha'] == $dec) return true;
        }
        App::go('logout');
    }

    /** Decodifica entrada via Post
     *
     *
     */
    final function decodePostData() 
    {
        if (!isset($_POST['data']))
            return false;
        $rec = json_decode($_POST['data']);

        //Se não for JSON...
        if (!is_object($rec))
            return false;

        if (isset($rec->enc)) {
            //$zumbi = new Model\Zumbi;
            $this->key = $this->model->getUserKey($rec->id);
            if ($this->key === false)
                return false;

            //Decriptando
            Data\Aes::size(256);
            return ['data' => $rec, 'dec' => json_decode(Data\Aes::dec($rec->enc, $this->key))];
        }
        return ['data' => $rec];
    }

    /** Envia dados criptografados para o browser
     *
     *
     */
    final function sendEncriptedData($dt) 
    {
        //Json encoder
        $enc = json_encode($dt);

        //Encriptando
        Data\Aes::size(256);
        $enc = Data\Aes::enc($enc, $this->key);

        //Enviando
        exit($enc);
    }

    /** Retorna o diretório para linguagem aceita pelo browser
     * Default = 'lang/en/'
     *
     */
    final function langPath() 
    {
        $lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

        switch (substr($lang[0], 0, 2)) {
            case 'pt': $l = 'pt';
                break;
            case 'es': $l = 'es';
                break;
            case 'fr': $l = 'fr';
                break;
            default: $l = 'en';
                break;
        }
        return 'lang/' . $l . '/';
    }


    /** 
     * Cria, configura e retorna o HTML para o usuário
     */
    public function response(
        $body, 
        $var = null, 
        $name = null, 
        $jsvar = null, 
        $scripts = null, 
        $styles = null
        )
    {
        $d = new Neos\Html(($name === null ? 'body' : $name));
        
        if($this->navbar !== null) $d->body($this->navbar);

        $d->body($body);

        $d->val('title', 'iReboque');

        if($var !== null){
            foreach ($var as $k=>$v) {
                $d->val($k, $v);
            }
        }

        if($jsvar !== null){
            foreach ($jsvar as $k=>$v) {
                $d->jsvar($k, $v);
            }
        }

        if($scripts !== null) $this->scripts = array_merge($this->scripts, $scripts);
        if($styles !== null)  $this->styles  = array_merge($this->styles, $styles);

        $d->insertScripts($this->scripts);
        $d->insertStyles($this->styles);
        
        return $d->render()->send();
    }


    /**
     * Pivo de ação
     */
    public final function action() 
    {
        switch($_POST['action']){
            case 'delete':
                return $this->delete($_POST['id']);
                break;

            case 'form':
                return $this->form($_POST['id']);
                break;

            case 'save':                
                //decode Json object
                $data = json_decode(json_decode($_POST['data']));

                //Object to Array
                $values = get_object_vars($data);
                
                //Select DB action: update/insert and get hook
                if(isset($values['id']) && (0+$values['id'] < 0 || $values['id'] !== '')){
                    return $this->update($values);
                } else {
                    unset($values['id']);
                    return $this->insert($values);
                }
                break;
        }  
    }

}
