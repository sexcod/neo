<?php

namespace Model;

use Model\App\Base;
use Neo\App;

class Usuario extends Base
{

 public $table = 'usuario';

    //Listar
    function index($start = 0, $len = 10, $search = '')
    {   
        $search = trim($search);
        $where = $search == '' ? '' : ' WHERE nome LIKE :se ';
        $count = $this->db->query("SELECT count(*) as total FROM usuario $where ", [':se'=>'%'.$search.'%']); 
        $data = $this->db->query("SELECT id, nome, login FROM usuario $where LIMIT ".(0+$start).", ".(0+$len), [':se'=>'%'.$search.'%']);
        
        return ['count'=>(isset($count[0]) ? $count[0]->get('total') : 0), 'data'=>$data];
    }

    //vizualizar
    function show($id)
    {
        return $this->doShow($id);
    }

    //Salvar
    function insert($values)
    {
        return $this->doSave($values);
    }

    //Atualizar
    function update($values)
    {
        return $this->doSave($values);
    }

    //Deletar
    function delete($id)
    {
        return $this->doDelete($id);
    }

    //Verifica login/senha de usuário
    function login($login, $passw)
    {
        $this->db->query('SELECT * FROM usuario WHERE login = :lg AND senha = :pw',[':lg'=>$login,':pw'=>$passw]);
        $res = $this->db->result();
        return isset($res[0]) ? $res[0] : false;    
    }


    //Grava um token no usuário
    function setToken($id, $token)
    {
        $this->db->query('UPDATE usuario SET token = :tk WHERE id = :id',[':tk'=>$token, ':id'=>$id]);
    }

    function getById($id)
    {
        $this->db->query('SELECT * FROM usuario WHERE id = :id', [':id'=>$id]); 
        if($this->db->result()) return $this->db->result()[0]->getAll();
        return false;
    }

    function getUserKey($id)
    {
        $this->db->query('SELECT token FROM usuario WHERE id = :id', [':id'=>$id]);
        if($this->db->result()) return $this->db->result()[0]->get('id');
        return false;
    }
}