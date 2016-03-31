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

use Neos\Db;
use Config\Database;
use Neos\App;

class Base 
{
    public $db = null;

    function __construct()
    {
        $this->db = new Db(Database::get());
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

        $this->db->query($action.$this->table.' SET '.$cols.$where, $vals);
        return $this->db->result();
    }

    //Deletar
    final public function doDelete($id)
    {
        $this->db->query('DELETE FROM '.$this->table.' WHERE id = :id',[':id'=>$id]);
        return $this->db->result();
    }


//Área comum para todo o sistema

//Estado
    public function estado($default = null)
    {
        $default = strtoupper($default);
        $this->db->query('SELECT * FROM estado WHERE id_pais = 1');

        $def = '';
        if($this->db->result()){
            foreach ($this->db->result() as $k => $v) {
                if($v->get('uf') == $default) $def = $v->get('id');                
                $o[] = ['nome'=>$v->get('nome'), 
                        'value'=>$v->get('id')];
            }
            $o['data'] = $o;
            $o['default'] = $def;
            return $o;
        }
        return [];
    }

    public function cidade($estado, $default = null)
    {
        $this->db->query('SELECT * FROM cidade WHERE id_estado = :est',[':est'=>(0+$estado)]);

        $def = '';
        if($this->db->result()){
            foreach ($this->db->result() as $k => $v) {
                if($v->get('id') == $default || $v->get('nome') == $default) $def = $v->get('id');
                $o[] = ['nome'=>$v->get('nome'),
                        'value'=>$v->get('id')];
            }
            $o['data'] = $o;
            $o['default'] = $def;
            return $o;
        }
        return [];
    }

}