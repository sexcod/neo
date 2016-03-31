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

namespace Controller\Guest;

use Model;
use Neos;
use Neos\App;
use Neos\Data;

abstract class Base 
{
    public $model = null;
    public $key = null;
    public $params = [];

    public $scripts = ['source/jquery-1.12.2.min',
                       'source/bootstrap.min',
                       'source/clean-blog'];
    public $styles =  ['source/bootstrap',
                       'source/bootstrap-theme',
                       'source/clean-blog'];

    public $navbar = 'parts/navbar';

    /** Abstratic Controller constructor
     *  -- Bypass it in your controller
     */
    function __construct($params) 
    {
        //save params
        $this->params = $params;
    }

    /** Default MAIN method
     * -- Bypass it in your controller
     */
    function main() 
    {
        $d = new Neos\Html('nopage');
        //$d->sendCache();
        $d->val('title', 'Page not found :: 404')
                ->body('default/nopage')
                ->render()
                ->send();
    }

    // ----------- USER FUNCTIONS --------------

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

        $d->val('title', 'NEOS PHP FRAMEWORK');

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


}
