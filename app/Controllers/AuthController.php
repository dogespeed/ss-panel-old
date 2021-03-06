<?php

namespace App\Controllers;

use App\Models\InviteCode;
use App\Models\User;
use App\Services\Auth;
use App\Services\Auth\EmailVerify;
use App\Services\Config;
use App\Services\Logger;
use App\Services\Mail;
use App\Utils\Check;
use App\Utils\Hash;
use App\Utils\Http;
use App\Utils\Tools;
use App\Models\TgLogin;


/**
 *  AuthController
 */
class AuthController extends BaseController
{
    // Register Error Code
    const WrongCode = 501;
    const IllegalEmail = 502;
    const PasswordTooShort = 511;
    const PasswordNotEqual = 512;
    const EmailUsed = 521;

    // Login Error Code
    const UserNotExist = 601;
    const UserPasswordWrong = 602;
    const UserIsProhibited = 603;

    // Verify Email
    const VerifyEmailWrongEmail = 701;
    const VerifyEmailExist = 702;

    public function login($request, $response, $args)
    {
        //Create random login request
        $random = Tools::genRandomUpCaseChar(6);

        //clean up if exist
        if ( TgLogin::where('safecode', $random)->value('safecode') == null ){
            $error = null;
            $safecode = new TgLogin();
            $safecode->safecode = $random;
            $safecode->created_at = strtotime("now");
            if ( $safecode->save() ) {
                return $this->view()->assign('safecode', $safecode)->assign('error', $error)->display('auth/login.tpl');
            }
        }
        $code = null;
        $error = "安全码生成失败,请刷新页面重试!";
        return $this->view()->assign('safecode', $safecode)->assign('error', $error)->display('auth/login.tpl');
    }

    public function loginHandle($request, $response, $args)
    {
        // $data = $request->post('sdf');
        $email = $request->getParam('email');
        $email = strtolower($email);
        $passwd = $request->getParam('passwd');
        $rememberMe = $request->getParam('remember_me');

        // Handle Login
        $user = User::where('email', '=', $email)->first();

        if ($user == null) {
            $res['ret'] = 0;
            $res['error_code'] = self::UserNotExist;
            $res['msg'] = "邮箱或者密码错误";
            return $this->echoJson($response, $res);
        }

        if (!Hash::checkPassword($user->pass, $passwd)) {
            $res['ret'] = 0;
            $res['error_code'] = self::UserPasswordWrong;
            $res['msg'] = "邮箱或者密码错误";
            return $this->echoJson($response, $res);
        }

        if ( !$user->allow_login ){
            $res['ret'] = 0;
            $res['error_code'] = self::UserIsProhibited;
            $res['msg'] = "用户被禁止登录!请联系管理员获得帮助";
            return $this->echoJson($response, $res);
        }

        // @todo
        $time = 3600 * 24;
        if ($rememberMe) {
            $time = 3600 * 24 * 7;
        }
        Logger::info("login user $user->id ");
        Auth::login($user->id, $time);

        $res['ret'] = 1;
        $res['msg'] = "欢迎回来";
        return $this->echoJson($response, $res);
    }

    public function pre_tglogin($request, $response, $args){
        //Create random login request
        $random = Tools::genRandomUpCaseChar(6);

        //clean up if exist
        if ( TgLogin::where('safecode', $random)->value('safecode') == null ){
            $error = null;
            $safecode = new TgLogin();
            $safecode->safecode = $random;
            $safecode->created_at = strtotime("now");
            if ( $safecode->save() ) {
                return $this->view()->assign('safecode', $safecode)->assign('error', $error)->display('auth/tglogin.tpl');
            }
        }
            $code = null;
            $error = "安全码生成失败,请刷新页面重试!";
            return $this->view()->assign('safecode', $safecode)->assign('error', $error)->display('auth/tglogin.tpl');

    }

    public function tglogin_verify($request, $response, $args){

        $rememberMe = $request->getParam('remember_me');
        $safecode = $request->getParam('code');

        $safecode = TgLogin::where('safecode', $safecode)->first();

        //start dash!!
        if ( $safecode == null ){
            $res['ret'] = 0;
            $res['msg'] = "安全码发生异常!请重试!";
            return $this->echoJson($response, $res);
        }

        $created_at = $safecode->created_at ;
        $expire_at = strtotime("+30 seconds", $created_at);  //验证后的安全码过期时间为30S
        if ( $expire_at - strtotime("now") < 0 ){
            $res['ret'] = 0;
            $res['msg'] = "安全码已经过期,请刷新重试!";
            $safecode->delete();
            return $this->echoJson($response, $res);
        }

        if ( $safecode->is_verify == false ){
            $res['ret'] = 0;
            $res['msg'] = "您尚未验证,请前往Telegram完整登录操作";
            return $this->echoJson($response, $res);
        }

        if ( $safecode->user_id == null ){
            $res['ret'] = 0;
            $res['msg'] = "500 服务器发生了奇怪的问题,请上报管理员";
            return $this->echoJson($response, $res);
        }

        $time = 3600 * 24;
        if ($rememberMe) {
            $time = 3600 * 24 * 7;
        }
        Logger::info("login user $safecode->user_id ");
        Auth::login($safecode->user_id, $time);

        $safecode->delete();

        //clean up !
        //Actually, it should be done regular.
        $codes = TgLogin::all();
        foreach ($codes as $code) {
            if ( strtotime("+180 seconds" , $code->created_at) - strtotime("now") < 0 ){
                $code->delete();
            }
        }

        $res['ret'] = 1;
        $res['msg'] = "欢迎回来";
        return $this->echoJson($response, $res);

    }

    public function register($request, $response, $args)
    {
        $ary = $request->getQueryParams();
        $code = "";
        if (isset($ary['code'])) {
            $code = $ary['code'];
        }
        $requireEmailVerification = Config::get('emailVerifyEnabled');
        return $this->view()->assign('code', $code)->assign('requireEmailVerification', $requireEmailVerification)->display('auth/register.tpl');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function registerHandle($request, $response, $args)
    {
        $name = $request->getParam('name');
        $email = $request->getParam('email');
        $email = strtolower($email);
        $passwd = $request->getParam('passwd');
        $repasswd = $request->getParam('repasswd');
        $code = $request->getParam('code');
        $verifycode = $request->getParam('verifycode');

        // check code
        $c = InviteCode::where('code', $code)->first();
        if ($c == null) {
            $res['ret'] = 0;
            $res['error_code'] = self::WrongCode;
            $res['msg'] = "邀请码无效";
            return $this->echoJson($response, $res);
        }

        // check email format
        if (!Check::isEmailLegal($email)) {
            $res['ret'] = 0;
            $res['error_code'] = self::IllegalEmail;
            $res['msg'] = "邮箱无效";
            return $this->echoJson($response, $res);
        }
        // check pwd length
        if (strlen($passwd) < 8) {
            $res['ret'] = 0;
            $res['error_code'] = self::PasswordTooShort;
            $res['msg'] = "密码太短";
            return $this->echoJson($response, $res);
        }

        // check pwd re
        if ($passwd != $repasswd) {
            $res['ret'] = 0;
            $res['error_code'] = self::PasswordNotEqual;
            $res['msg'] = "两次密码输入不符";
            return $this->echoJson($response, $res);
        }

        // check email
        $user = User::where('email', $email)->first();
        if ($user != null) {
            $res['ret'] = 0;
            $res['error_code'] = self::EmailUsed;
            $res['msg'] = "邮箱已经被注册了";
            return $this->echoJson($response, $res);
        }

        // verify email
        if (Config::get('emailVerifyEnabled') && !EmailVerify::checkVerifyCode($email, $verifycode)) {
            $res['ret'] = 0;
            $res['msg'] = '邮箱验证代码不正确';
            return $this->echoJson($response, $res);
        }

        // check ip limit
        $ip = Http::getClientIP();
        $ipRegCount = Check::getIpRegCount($ip);
        if ($ipRegCount >= Config::get('ipDayLimit')) {
            $res['ret'] = 0;
            $res['msg'] = '当前IP注册次数超过限制';
            return $this->echoJson($response, $res);
        }

        // do reg user
        $user = new User();
        $user->user_name = $name;
        $user->email = $email;
        $user->pass = Hash::passwordHash($passwd);
        $user->passwd = Tools::genRandomChar(6);
        $user->port = Tools::getAvailablePort();
        $user->method = Config::get( "defaultMethod" );
        $user->t = 0;
        $user->u = 0;
        $user->d = 0;
        $groups = array( 1, 2, 3, 4, 5 );
        foreach ( $groups as $group ) {
            $prefix = Config::get( "g{$group}CodePrefix" );
            if ( !empty( $prefix ) && 0 === strpos( $code, Config::get( "g{$group}CodePrefix" ) ) ) {
                $user->user_type = $group;
                break;
            }
        }
        if ( !$user->user_type ) $user->user_type = Config::get( "defaultGroup" );
        $user->transfer_enable = Tools::toGB(Config::get("g{$user->user_type}DefaultTraffic"));
        $user->transfer_enable_next = Tools::toGB(Config::get("g{$user->user_type}DefaultNextTraffic"));
/*
        if ( 0 === strpos( $code, Config::get( "vipCodePrefix" ) ) ) {
            $user->transfer_enable = Tools::toGB(Config::get('defaultVipTraffic'));
        } else {
            $user->transfer_enable = Tools::toGB(Config::get('defaultTraffic'));
        }
*/
        $user->invite_num = Config::get('inviteNum');
        $user->reg_ip = Http::getClientIP();
        $user->ref_by = $c->user_id;

        if ($user->save()) {
            $res['ret'] = 1;
            $res['msg'] = "注册成功";
            $c->delete();
            return $this->echoJson($response, $res);
        }
        $res['ret'] = 0;
        $res['msg'] = "未知错误";
        return $this->echoJson($response, $res);
    }

    public function sendVerifyEmail($request, $response, $args)
    {
        $res = [];
        $email = $request->getParam('email');

        if (!Check::isEmailLegal($email)) {
            $res['ret'] = 0;
            $res['error_code'] = self::VerifyEmailWrongEmail;
            $res['msg'] = '邮箱无效';
            return $this->echoJson($response, $res);
        }

        // check email
        $user = User::where('email', $email)->first();
        if ($user != null) {
            $res['ret'] = 0;
            $res['error_code'] = self::VerifyEmailExist;
            $res['msg'] = "邮箱已经被注册了";
            return $this->echoJson($response, $res);
        }

        if (EmailVerify::sendVerification($email)) {
            $res['ret'] = 1;
            $res['msg'] = '验证代码已发送至您的邮箱，请在登录邮箱后将验证码填到相应位置.';
            return $this->echoJson($response, $res);
        }
        $res['ret'] = 0;
        $res['msg'] = '邮件发送失败，请联系管理员';
        return $this->echoJson($response, $res);
    }

    public function logout($request, $response, $args)
    {
        Auth::logout();
        return $this->redirect($response, '/auth/login');
    }

}
