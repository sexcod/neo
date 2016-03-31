<?php

/**
 * NEOS PHP FRAMEWORK
 * @copyright   Bill Rocha - http://plus.google.com/+BillRocha
 * @license     MIT
 * @author      Bill Rocha - prbr@ymail.com
 * @version     0.0.1
 * @package     Model
 * @access      public
 * @since       0.3.0
 *
 */
 
namespace Model\App;

use Neo\Db;
use Config\Database;
use Neo\App;

class Base {

    public $db = null;
    public $error = false;

    function __construct(){
        $this->db = new Db(Database::get());
    }

    function getError(){
        return $this->error;
    }

    function setError($error = true){
        return $this->error = $error;
    }


    //Lista todos
    final public function doIndex($start = 0, $len = 30, $search = null)
    {
        //SEarch
        if($search !== null && is_array($search)){
            $tmp = ' WHERE ';
            $and = '';
            foreach($search as $k=>$v){
                $tmp .= $and.$k.' LIKE "%'.$v.'%" ';
                $and = ' AND ';
            }
            $search = $tmp;
        } else $search = '';

        //Execute
        $this->db->query('SELECT * FROM '.$this->table.$search.' LIMIT '.(0+$start).', '.(0+$len));
        return $this->db->result();
    }

    //Lista o selecionado    
    final public function doShow($id)
    {
        $this->db->query('SELECT * FROM '.$this->table.' WHERE id = :id',[':id'=>$id]);
        $result = $this->db->result();
        return $result ? $result : false;
    }

    //Insert/Update
    final public function doSave($values)
    { 
        if(isset($values['id'])){
            $action = 'UPDATE ';
            $where = ' WHERE id = :id';
        } else {
            $action = 'INSERT INTO ';
            $where = '';
        }

        $cols = '';
        $vals = [];
        foreach($values as $k=>$v){     
            if($k !== 'id') $cols .= $k.' = :'.$k.',';            
            $vals[':'.$k] = $v;          
        }
        
        $cols = substr($cols, 0, -1); //tirando a ultima vírgula

        return $this->db->query($action.$this->table.' SET '.$cols.$where, $vals);
    }

    //Deletar
    final public function doDelete($id)
    {
        return $this->db->query('DELETE FROM '.$this->table.' WHERE id = :id',[':id'=>$id]);
    }


//Área comum para todo o sistema

//Estado
    public function estado()
    {
        $this->db->query('SELECT * FROM estado WHERE id_pais = 1');
        if($this->db->result()){
            foreach ($this->db->result() as $k => $v) {
                $o[$v->get('id')] = ['nome'=>$v->get('nome'), 'uf'=>$v->get('uf')];
            }
            return $o;
        }
        return [];
    }

    public function cidade($estado)
    {
        $this->db->query('SELECT * FROM cidade WHERE id_estado = :est',[':est'=>(0+$estado)]);
        if($this->db->result()){
            foreach ($this->db->result() as $k => $v) {
                $o[$v->get('id')] = ['nome'=>$v->get('nome')];
            }
            return $o;
        }
        return [];
    }

    //Cliente empresa
    public function cliente($cliente=null)
    {
        if($cliente ==null)
        {
            $and = '';
        }
        else{
            $and = ' AND id = '.$cliente;
        } 

        $this->db->query('SELECT * FROM empresa WHERE tipo = \'Cliente\' '.$and);
        if($this->db->result()){
            foreach ($this->db->result() as $k => $v) {
                $o[$v->get('id')] = ['nome_fantasia'=>$v->get('nome_fantasia')];
            }
            return $o;
        }
        return [];
    }
    
     //Reboque empresa
    public function reboque($reboque=null)
    {
        if($reboque ==null)
        {
            $and = '';
        }
        else{
            $and = ' AND id = '.$reboque; 
        } 

        $this->db->query('SELECT * FROM empresa WHERE tipo = \'Reboque\' '.$and);
        if($this->db->result()){
            foreach ($this->db->result() as $k => $v) {
                $o[$v->get('id')] = ['nome_fantasia'=>$v->get('nome_fantasia')];
            }
            return $o;
        }
        return [];
    }

    //Veículo Reboque
    public function veiculoReboque($veiculo=null)
    {
        if($veiculo ==null)
        {
            $and = '';
        }
        else{

            $and = ' AND id = '.$veiculo;
        } 

        $this->db->query('SELECT * FROM veiculo WHERE tipo = \'reboque\' '.$and);
        if($this->db->result()){
            foreach ($this->db->result() as $k => $v) {
                $o[$v->get('id')] = ['placa'=>$v->get('placa')];
            }
            return $o;
        }
        return [];
    }

    //Empresa Matriz, Cliente, Reboque
    public function empresas($empresas=null)
    {
        if($empresas ==null)
        {
            $and = '';
        }
        else{
            $and = ' WHERE id = '.$empresas;
        } 

        $this->db->query('SELECT * FROM empresa '.$and);
        if($this->db->result()){
            foreach ($this->db->result() as $k => $v) {
                $o[$v->get('id')] = ['nome_fantasia'=>$v->get('nome_fantasia')];
            }
            return $o;
        }
        return [];
    }




}