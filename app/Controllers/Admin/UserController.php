<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\User;
use App\Utils\Hash;
use App\Utils\Tools;

class UserController extends AdminController
{
    public function index($request, $response, $args)
    {
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $users = User::paginate(15, ['*'], 'page', $pageNum);
        $users->setPath('/admin/user');
        return $this->view()->assign('users', $users)->display('admin/user/index.tpl');
    }

    public function edit($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);
        if ($user == null) {

        }
        return $this->view()->assign('user', $user)->display('admin/user/edit.tpl');
    }

    public function update($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);

        $user->email = $request->getParam('email');
        if ($request->getParam('pass') != '') {
            $user->pass = Hash::passwordHash($request->getParam('pass'));
        }
        if ($request->getParam('passwd') != '') {
            $user->passwd = $request->getParam('passwd');
        }
        $user->port = $request->getParam('port');
        $user->transfer_enable = Tools::toGB($request->getParam('transfer_enable'));
        $user->invite_num = $request->getParam('invite_num');
        $user->method = $request->getParam('method');
        $user->enable = $request->getParam('enable');
        $user->is_admin = $request->getParam('is_admin');
        $user->allow_login = $request->getParam('allow_login');
        $user->user_type = $request->getParam('user_type');
        $user->ref_by = $request->getParam('ref_by');
        $user->donate_amount = $request->getParam('donate_amount');
        $user->is_protected = $request->getParam('is_protected');
        $user->note = $request->getParam('note');
        $user->telegram_id = $request->getParam('telegram_id');
        $acmod = false;
        if($user->ac_enable){
            if($user->ac_user_name != $request->getParam('ac_user_name')){
                $user->ac_user_name = $request->getParam('ac_user_name');
                $acmod = true;
            }
            if($user->ac_passwd != $request->getParam('ac_passwd')){
                $user->ac_passwd = $request->getParam('ac_passwd');
                $acmod = true;
            }
        }
        switch($request->getParam('ac_enable')){
            case "1":
                if($user->ac_enable) continue;
                $acname = Tools::genRandomChar(16);
                while(User::where("ac_user_name","=",$acname)->count())
                    $acname = Tools::genRandomChar(16);
                $acpasswd = Tools::genRandomChar(16);
                $user->ac_enable = 1;
                $user->ac_user_name = $acname;
                $user->ac_passwd = $acpasswd;
                $acmod = true;
                break;
            case "0":
                if($user->ac_enable){
                    $user->ac_enable = 0;
                    $user->ac_user_name = '';
                    $user->ac_passwd = '';
                    $acmod = true;
                }
                break;
        }
        if (!$user->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = "修改失败";
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = "修改成功";
        if($acmod) $rs['msg'].="，AnyConnect 已改变";
        return $response->getBody()->write(json_encode($rs));
    }

    public function delete($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);
        if (!$user->delete()) {
            $rs['ret'] = 0;
            $rs['msg'] = "删除失败";
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = "删除成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function deleteGet($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);
        $user->delete();
        $newResponse = $response->withStatus(302)->withHeader('Location', '/admin/user');
        return $newResponse;
    }
}
