<?php
defined("__FRAMEWORKNAME__") or die("No permission to access!");
use App\service\rpcserverimpl\Common as Common;
/**
 * 客服--用户列表
 * @pageroute
 */
function lst()
{
    $sessionObj = new \Lib\Session();
    $framework = getFrameworkInstance();
    $userModel = new \Model\AuthUser();
    $results = [];
    $data['queryType'] = I('post.type') ? I('post.type') : (\Lib\UserData::get('admin_user.serach.queryType') ?\Lib\UserData::get('admin_user.serach.queryType'): $sessionObj->get('userData.admin_user.serach.queryType')) ;
    $data['queryValue'] = I('post.phone') ? I('post.phone') :(\Lib\UserData::get('admin_user.serach.queryValue') ?\Lib\UserData::get('admin_user.serach.queryValue'): $sessionObj->get('userData.admin_user.serach.queryValue'));
    if($data['queryValue'])
    {
        $sessionObj->set('userData.admin_user.serach.queryType',$data['queryType']);
        $sessionObj->set('userData.admin_user.serach.queryValue',$data['queryValue']);
        if( $data['queryType']=='user_name')
        {
            $where['realname']=  $data['queryValue'];
        }else if( $data['queryType']=='phone_num')
        {
            $where = 'username ='. "'{$data['queryValue']}'";
            //$where['username'] =  "'{$data['queryValue']}'";
        }else if($data['queryType']=='id_number'){
            $where = 'id_number ='. "'{$data['queryValue']}'";
        }else if($data['queryType']=='user_id'){
            $where = 'id ='. $data['queryValue'];
        }
        $results = $userModel->where($where)->get()->resultArr();
        if(count($results)==1)
        {
            $userId = $results[0]['id'];
            $sessionObj->set('userData.admin_user.serach.userId',$userId);
        }
    }else if($sessionObj->get('userData.admin_user.serach.userId'))
    {
        $results = $userModel->where(['id'=>$sessionObj->get('userData.admin_user.serach.userId')])->get()->resultArr();
    }
    if($results)
    {
        $results = maskData($results);
    }else{
        $sessionObj->set('userData.admin_user.serach.userId','');
    }
    $framework->smarty->assign('sessionObj',$sessionObj);
    $framework->smarty->assign('lists',$results);
    $framework->smarty->display('user/index.html');
}
/**
 * 用户详情
 * @pageroute
 */
function detail()
{
    $framework = getFrameworkInstance();
    $id = I('get.id/d');
    //检查数据是否合法
    if(!$id || $id <= 0)
        redirect('?c=user&a=index', 2, '数据不合法');
    $userModel = new \Model\AuthUser();
    $result = $userModel->getUserDetailById($id);
    //查找邀请关系中用户的手机号
    if($result['basicUser']['from_user_id'] != 0 || $result['basicUser']['invite_user_id']){
        if($result['basicUser']['from_user_id'] !=0){
            $invite_id = $result['basicUser']['from_user_id'];
        }
        if($result['basicUser']['invite_user_id'] !=0){
            $invite_id = $result['basicUser']['invite_user_id'];
        }
        $invite = $userModel->fields('phone')->where(['id'=>$invite_id])->get()->rowArr();
        $result['basicUser']['invite_phone'] = $invite ? $invite['phone'] : '';
    }
    $authAccountModel = new \Model\AuthAccountRatethrottle();
    $authAccountInfo = $authAccountModel->where(['user_id'=>$id])->get()->rowArr();
    $result['ratethrottle']['trade_pwd_failed_count'] = 0;
    $result['ratethrottle']['login_failed_count'] = 0;
    $result['ratethrottle']['identify_failed_count'] = 0;
    if($authAccountInfo)
    {
        $result['ratethrottle']['trade_pwd_failed_count'] = $authAccountInfo['trade_pwd_failed_count'];
        $result['ratethrottle']['login_failed_count'] = $authAccountInfo['login_failed_count'];
        $result['ratethrottle']['identify_failed_count'] = $authAccountInfo['identify_failed_count'];
    }
    //是否加入自动投标
    $jsonrpcObj = new \Lib\JsonRpcClient(config('RPC_API.projects'));
    $res = $jsonrpcObj->customerList(['userId'=>$id]);
    $auto_planList = [];
    if(!array_key_exists('error',$res)){
        $auto_planList = $res['result']['data'];
    }
    $result['basicUser'] = maskData($result['basicUser']);
    $framework->smarty->assign('auto_planList',$auto_planList);
    $framework->smarty->assign('rateInfo',$result['ratethrottle']);
    $framework->smarty->assign('basicInfo',$result['basicUser']);
    $framework->smarty->assign('identifyInfo',$result['identityUser']);
    $framework->smarty->assign('addressInfo',$result['addressUser']);
    $framework->smarty->display('user/detail.html');

}


/**
 * 用户账户冻结/解冻功能
 * @pageroute
 */
function del()
{
    $id = I('get.id/d',0);
    $goto = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '?c=user&a=lst';
    if($id)
    {
        $userModel = new \Model\AuthUser();
        $users = $userModel->where(['id'=>$id])->get()->row();
        if($users)
        {
            if($users->is_active ==1){
                $data=['is_active'=>0];
            }elseif($users->is_active ==0){
                $data=['is_active'=>1];
            }
            if($userModel->where(['id'=>$id])->upd($data)){
                redirect($goto,'2',"成功");
            }else{
                redirect($goto,'2',"失败");
            }
        }else
        {
            redirect($goto,'2',"该用户不存在");
        }
    }
    else
    {
        redirect($goto, 2, '数据不合法');
    }

}

/**
 * 后台解卡操作
 * @pageroute
 */
function solutionCard()
{
    set_time_limit(90);
    $id = I('post.id');
    if(!$id)
        ajaxReturn(['error'=>'100','msg'=>'参数错误']);
    $bandCardModel = new \Model\AuthBankCard();
    $bankCardInfo = $bandCardModel->where(['id'=>$id])->get()->row();
    if(!$bankCardInfo)
        ajaxReturn(['error'=>'100','msg'=>'绑卡信息不存在']);
    if($bankCardInfo->status ==0)
        ajaxReturn(['error'=>'100','msg'=>'该卡还没有绑定,不能做解卡操作']);
        //解卡请求增加数据
        $solutionData = [
            'bid'=>$bankCardInfo->id,
            'requestid'=>generate_orderid(),
            'name'=>$bankCardInfo->realname,
            'status'=>0
        ];
        //解卡请求
        try
        {
            $requestData = [
                'requestid'=>$solutionData['requestid'],
                'cardno'=>$bankCardInfo->cardno,
                'identityid'=>$bankCardInfo->user_id,
                'channel_code'=>$bankCardInfo->channel,
            ];
            $config = array('timeout' => 60);
            $jsonRpc = new \Lib\JsonRpcClient(config('RPC_API.pay'),$config);
            $result = $jsonRpc->bankCardUnbind($requestData);
            if($result && is_array($result))
            {
                if(!(array_key_exists('error',$result)))
                {
                    $data['status'] = 2;//已解绑
                    $solutionUpdate['status'] =1;
                    $solutionUpdate['resp_msg'] =$result['result']['resp_msg'];
                    if($bandCardModel->update($data,['id'=>$id]))
                        ajaxReturn(['error'=>'200','msg'=>'解绑成功']);
                }else
                {
                    $solutionUpdate['status'] =0;
                    $solutionUpdate['resp_msg'] ='错误代码:'.$result['error']['code'].'--错误信息:'.$result['error']['message'];
                    ajaxReturn(['error'=>'100','msg'=>'解绑失败-原因:'.$result['error']['message']]);
                }
            }
        }catch (\Exception $e)
        {
            ajaxReturn(['error'=>'100','msg'=>$e->getMessage()]);
        }

}
/**
 * @pageroute
 * 设置查询UserId
 */
function setSearchUserId()
{
    $id = I('post.id');
    if(!$id)
        ajaxReturn(array('error'=>100,'msg'=>'参数错误'));
    $sessionObj = new \Lib\Session();
    $sessionObj->set('userData.admin_user.serach.userId',$id);
    ajaxReturn(array('error'=>200,'msg'=>'设置成功'));
}

/**
 * 交易密码解除冻结
 * @pageroute
 */
function trade_frozen()
{
    $data = I('post.');
    if(!$data)
        ajaxReturn(array('error'=>'100','msg'=>'信息不全'));
    $userId = $data['uid'];
    $status = $data['status'];//0:解冻 1：冻结
    $authAccountModel = new \Model\AuthAccountRatethrottle();
    $authAccountUser = $authAccountModel->where(['user_id'=>$userId])->get()->rowArr();
    if($status==0)//0解冻 1冻结不用判断 用上面的$forzenCount
        $forzenCount =0;
    else
        $forzenCount = 3;
    if($authAccountUser){
        $status = $authAccountModel->update(['trade_pwd_failed_count'=>$forzenCount,'trade_pwd_last_failed_time'=>time()],['user_id'=>$userId]);
    }else{
        $status = $authAccountModel->add(['user_id'=>$userId,'trade_pwd_failed_count'=>$forzenCount,'trade_pwd_last_failed_time'=>time()]);
    }
    if($status)
        ajaxReturn(array('error'=>200,'msg'=>'修改成功'));
    ajaxReturn(['error'=>100,'msg'=>'修改失败']);
}

/**
 * 登录冻结/解冻操作
 * @pageroute
 */
function signin_frozen()
{
    $data = I('post.');
    if(!$data)
        ajaxReturn(array('error'=>'100','msg'=>'信息不全'));
    $userId = $data['uid'];
    $status = $data['status'];//0:解冻 1：冻结
    $authAccountModel = new \Model\AuthAccountRatethrottle();
    $authAccountUser = $authAccountModel->where(['user_id'=>$userId])->get()->rowArr();
    if($status==0)//0解冻 1冻结不用判断 用上面的$forzenCount
        $forzenCount ='0';
    else
        $forzenCount = '6';
    if($authAccountUser){
        $status = $authAccountModel->update(['login_failed_count'=>$forzenCount,'login_last_failed_time'=>time()],['user_id'=>$userId]);
    }else{
        $status = $authAccountModel->add(['user_id'=>$userId,'login_failed_count'=>$forzenCount,'login_last_failed_time'=>time()]);
    }
    if($status)
        ajaxReturn(array('error'=>200,'msg'=>'修改成功'));
    ajaxReturn(['error'=>100,'msg'=>'修改失败']);
}
/**
 * 实名认证冻结/解冻操作
 * @pageroute
 */
function frozen_identify()
{
    $data = I('post.');
    if(!$data)
        ajaxReturn(array('error'=>'100','msg'=>'信息不全'));
    $userId = $data['uid'];
    $status = $data['status'];//0:解冻 1：冻结
    $authAccountModel = new \Model\AuthAccountRatethrottle();
    $authAccountUser = $authAccountModel->where(['user_id'=>$userId])->get()->rowArr();
    if($status==0)//0解冻 1冻结不用判断 用上面的$forzenCount
        $forzenCount ='0';
    else
        $forzenCount = '6';
    if($authAccountUser){
        $status = $authAccountModel->update(['identify_failed_count'=>$forzenCount,'identify_last_failed_time'=>time()],['user_id'=>$userId]);
    }else{
        $status = $authAccountModel->add(['user_id'=>$userId,'identify_failed_count'=>$forzenCount,'identify_last_failed_time'=>time()]);
    }
    if($status)
        ajaxReturn(array('error'=>200,'msg'=>'修改成功'));
    ajaxReturn(['error'=>100,'msg'=>'修改失败']);
}

/**
 * 清除后台中短信限制次数
 * @pageroute
 */
function reliveSms()
{
    $mobile = I('post.mobile');
    if(!$mobile)
        ajaxReturn(['error'=>'100','msg'=>'参数错误']);
    $authCaptchaModel = new \Model\AuthCaptchaRatethrottle();
    $authCapthInfo = $authCaptchaModel->where(['mobile'=>$mobile])->get()->resultArr();
    $status = [];
    if($authCapthInfo)
    {
        foreach($authCapthInfo as $v)
        {
            $status[$v['id']] = $authCaptchaModel->update(['counter'=>0,'not_valid'=>0],['mobile'=>$mobile]);
        }
    }
    ajaxReturn(['error'=>'200','msg'=>'清除成功']);
}

/**
 * 初始化交易密码
 * @pageroute
 */
function trade_pwd()
{
    $uid = I('post.uid');
    if(!$uid)
        ajaxReturn(['error'=>'100','msg'=>'参数错误']);
    $authModel = new \Model\AuthUser();
    $authInfo = $authModel->where(['id'=>$uid])->get()->rowArr();
    if($authInfo)
    {
        $trade_pwd = makePassword(substr($authInfo['username'],-6));
        if($authModel->update(['trade_pwd'=>$trade_pwd],['id'=>$uid])) {
            ajaxReturn(['error'=>'200','msg'=>'重置成功']);
        }else{
            ajaxReturn(['error'=>'200','msg'=>'重置失败']);
        }
    }
    ajaxReturn(['error'=>'100','msg'=>'该用户信息不存在']);
}

/**
 * @pageroute
 * 用户实名信息列表
 */
function identify()
{
    $framework = getFrameworkInstance();
    $results = array();//返回结果
    $sessionObj = new \Lib\Session();
    if(I('post.type') && I('post.phone'))
    {
        if(
            I('post.type') != $sessionObj->get('userData.admin_user.serach.queryType')
            ||
            I('post.phone') != $sessionObj->get('userData.admin_user.serach.queryValue')
        )
        {
            $sessionObj->set('userData.admin_user.serach.queryType',I('post.type'));
            $sessionObj->set('userData.admin_user.serach.queryValue',I('post.phone'));
            redirect('?c=user&a=lst');
        }
    }
    $userId = $sessionObj->get('userData.admin_user.serach.userId');
    if($userId)
    {
        $marginModel = new \Model\AuthIdentify();
        $results = $marginModel->where(['user_id'=>$userId])->get()->resultArr();
        $results = maskData($results);
    }
    $framework->smarty->assign('sessionObj',$sessionObj);
    $framework->smarty->assign('lists',$results);
    $framework->smarty->display('user/identify.html');
}

/**
 * @pageroute
 * 用户银行卡信息
 */
function banklist()
{
    $framework = getFrameworkInstance();
    $results = array();//返回结果
    $sessionObj = new \Lib\Session();
    if(I('post.type') && I('post.phone'))
    {
        if(
            I('post.type') != $sessionObj->get('userData.admin_user.serach.queryType')
            ||
            I('post.phone') != $sessionObj->get('userData.admin_user.serach.queryValue')
        )
        {
            $sessionObj->set('userData.admin_user.serach.queryType',I('post.type'));
            $sessionObj->set('userData.admin_user.serach.queryValue',I('post.phone'));
            redirect('?c=user&a=lst');
        }
    }
    $userId = $sessionObj->get('userData.admin_user.serach.userId');
    if($userId)
    {
        $marginModel = new \Model\AuthBankCard();
        $results = $marginModel->where(['user_id'=>$userId])->get()->resultArr();
        $results = maskData($results);
    }
    $framework->smarty->assign('sessionObj',$sessionObj);
    $framework->smarty->assign('lists',$results);
    $framework->smarty->display('user/banklist.html');
}
/**
 * 用户更改手机号
 * @pageroute
 */
function edit_phone()
{
    $authModel = new \Model\AuthUser();//用户表
    $marginModel = new \Model\MarginMargin();//资产表
    $marginRecordModel = new \Model\MarginRecord();//流水表
    $marginRechargeModel = new \Model\MarginRecharge();//充值表
    $authIdentifyModel = new \Model\AuthIdentify();
    $framework = getFrameworkInstance();
    $sessionObj = new \Lib\Session();
    $session = $sessionObj->get('userData.admin_user');//获取后台登录用户名
    if(I('post.type')==1){//查看修改的手机号是否注册，是否有资产等数据
        $result = [];
        $id = I('post.id');
        $new_phone = I('post.new_phone');
        $oldAuth = $authModel->fields('username,id')->where(['id'=>$id])->get()->rowArr();
        $result['authInfo'] = $result['marginInfo'] = $result['recordInfo'] =  $result['rechargeInfo'] =  $result['tradeInfo'] = $result['useramortizationInfo']= '';
        $result['authInfo'] = $authModel->fields('username,id')->where(['username'=>$new_phone,'phone'=>$new_phone])->get()->rowArr();
        if($result['authInfo']){
            $result['marginInfo'] = $marginModel->where(['user_id'=>$result['authInfo']['id']])->get()->rowArr();
            $result['rechargeInfo'] =$marginRechargeModel->where(['user_id'=>$result['authInfo']['id'],'status'=>200])->get()->resultArr();
             $result['recordInfo'] = $marginRecordModel->where(['user_id'=>$result['authInfo']['id']])->get()->resultArr();
        }
        $framework->smarty->assign('lists',$result);
        $framework->smarty->assign('oldAuth',$oldAuth);
        $framework->smarty->assign('newPhone',$new_phone);
        $framework->smarty->display('user/edit_phone.html');
    }elseif(I('post.type')==2){//修改手机号
        $old_uid = I('post.old_userId');
        $new_uid = I('post.new_userId');
        $old_phone = I('post.old_phone');
        $new_phone = I('post.new_mobile');
        $status = 0;
        if($new_uid) {//要修改的手机号存在
            if($session['role_id'] !=1)//权限不为超级管理员时,判断修改的手机号的账户是否有投资，有则不许修改
            {
                $rechargeInfo =$marginRechargeModel->where(['user_id'=>$new_uid,'status'=>200])->get()->resultArr();//充值
               if($rechargeInfo)
                {
                    ajaxReturn(['error'=>100,'msg'=>"修改的手机号有实际资产,无法修改，请联系后台"]);
                }
            }
            try
            {
                //判断修改的手机号用户是否实名
                $authIdentifyInfo = $authIdentifyModel->fields('is_valid')->where(['user_id'=>$new_uid])->get()->rowArr();
                $identifyStatus = $authIdentifyInfo && $authIdentifyInfo['is_valid']==1 ? 1 : 0;
                if($identifyStatus){
                    ajaxReturn(['error'=>100,'msg'=>"用户已实名认证,无法修改。请联系后台"]);
                }
                $authModel->transStart();
                //用户手机号更改
                $new_data = ['username'=>$old_phone.'old_die','phone'=>$old_phone,'display_name'=>mask_string($old_phone, 3, 6),'is_active'=>0];
                $o_status = $authModel->where(['id'=>$new_uid,'username'=>$new_phone])->upd($new_data);//注销的用户
                $n_status = $authModel->where(['id'=>$old_uid,'username'=>$old_phone])->upd(['username'=>$new_phone,'phone'=>$new_phone,'display_name'=> mask_string($new_phone, 3, 6)]);//修改手机号的用户
                $authModel->transCommit();
                if($n_status && $o_status)
                    $status = 1;
            }catch (Exception $e)
            {
                $authModel->transRollBack();
                ajaxReturn(['error'=>100,'msg'=>"用户手机号:{$new_phone}修改error"]);
            }
        }else
        {
            $status = $authModel->update(['username'=>$new_phone,'phone'=>$new_phone,'display_name'=> mask_string($new_phone, 3, 6)],['id'=>$old_uid]);
        }
        if($status){
            $oldInfo = $authModel->fields('realname')->where(['id'=>$old_uid])->get()->rowArr();
            if($oldInfo && $oldInfo['realname']) {
                $name = $oldInfo['realname'];
            }else{
                $name = $old_phone;
            }
            $mqObj = new \Lib\McQueue();
            Common::sendMessage($new_phone, 'change_phone_success', ['name'=>$name,'old_phone'=>$old_phone,'date'=>date("Y-m-d H:i:s"),'new_phone'=>$new_phone]);
            Common::sendMessage($old_phone, 'change_phone_success', ['name'=>$name,'old_phone'=>$old_phone,'date'=>date("Y-m-d H:i:s"),'new_phone'=>$new_phone]);
            sleep(5);
            $mqObj->put('editPhone',['user_id'=>$old_uid,'phone'=>$new_phone,'realname'=>$name,'display_name'=>mask_string($new_phone, 3, 6)]);
            ajaxReturn(['error'=>200,'msg'=>"用户手机号修改成功"]);
        }else{
            ajaxReturn(['error'=>100,'msg'=>"用户手机号修改失败"]);
        }
    }
}
/**
 * 用户数据
 * @pageroute
 */
function proUserInfo()
{
    $data = I('get.');
    $sessionObj = new \Lib\Session();
    $sessionData = $sessionObj->get(USER_DATA_SKEY);
    $sessionObj->set(USER_DATA_SKEY,
        array_merge($sessionData,[
        'user_id' => $data['id'],
        'user_name' => $data['username'],
        'display_name' => $data['dis_name']])
    );
    $authModel = new \Model\AuthUser();
    $authInfo = $authModel->getUserBasicInfo($data['id']);
    $res = json_encode($authInfo);
    $script =<<<SCRIPT
<script>
localStorage.setItem('wlbuser','{$res}');
var input = localStorage.getItem('wlbuser');
console.log(input);
</script>
SCRIPT;
    echo $script;
}
/**
 * 银行卡加入黑名单/解除黑名单
 * @pageroute
 */
function black_card()
{
    $authBankCardModel = new \Model\AuthBankCard();//绑卡表
    $authBalckCardModel = new \Model\AuthBlackCard();//黑名单mod
    $id = I('post.id');
    $card = I('post.card');
    $type = I('post.type');
    if($type ==1)
    {
        if(!$id && !$card) {
            ajaxReturn(['error'=>'100','msg'=>'参数错误']);
        }
        $authBankInfo = $authBankCardModel->where("id ='{$id}' AND cardno='{$card}'")->get()->rowArr();
        if($authBankInfo){
            $balck = $authBalckCardModel->delete(['user_id'=>$authBankInfo['user_id'],'card_no'=>$card]);
            if($balck)
                ajaxReturn(['error'=>'200','msg'=>'取消黑名单成功']);
            else
                ajaxReturn(['error'=>'100','msg'=>'取消黑名单失败']);
        }else
        {
            ajaxReturn(['error'=>'100','msg'=>'没有该卡的信息']);
        }
    }elseif($type==2)
    {
        $content = htmlspecialchars(I('post.content'));
        if(!($id) || !($card)){
            redirect('admin.php?c=user&a=banklist','2','参数错误');
        }
        if($content==''){
            redirect('admin.php?c=user&a=banklist','2','请填写拉黑原因');
        }
        $authBankInfo = $authBankCardModel->where("id ='{$id}' AND cardno='{$card}'")->get()->rowArr();
        if(!$authBankInfo)
            redirect('admin.php?c=user&a=banklist',2,'该数据不存在');
        if($authBankInfo['status']==1){
            $data = ['user_id'=>$authBankInfo['user_id'],'card_no'=>$card,'message'=>$content,'ip'=>getIp(),'create_time'=>date("Y-m-d H:i:s")];
            $balckStatus = $authBalckCardModel->add($data);
            if($balckStatus)
                redirect('admin.php?c=user&a=banklist',1,'加入黑名单成功');
            else
                redirect('admin.php?c=user&a=banklist',1,'加入黑名单失败');
        }else{
            redirect('admin.php?c=user&a=banklist',1,'该银行卡没有绑定,无法加入黑名单');
        }
    }
}
/**
 * 重置银行卡
 * @pageroute
 */
function resetting_card()
{
    $id = I('post.id');
    $card = I('post.card');
    if(!$id || !$card){
        ajaxReturn(['error'=>'100','msg'=>'参数错误']);
    }
    $authBankCardModel = new \Model\AuthBankCard();//绑卡表
    $authBankInfo = $authBankCardModel->where("id ='{$id}' AND cardno='{$card}'")->get()->rowArr();
    if(!$authBankInfo)
        ajaxReturn(['error'=>'100','msg'=>'该数据不存在']);
    if($authBankInfo['status']==1)
    {
        $bankStatus = $authBankCardModel->update(['status'=>0],['id'=>$id,'cardno'=>$card]);
        if($bankStatus)
            ajaxReturn(['error'=>'200','msg'=>'操作成功']);
        else
            ajaxReturn(['error'=>'200','msg'=>'操作失败']);
    }else
    {
        ajaxReturn(['error'=>'100','msg'=>'该数据以操作过']);
    }
}
/**
 * @pageroute
 * 修改银行卡号
 */
function edit_card()
{
    $authBankModel = new \Model\AuthBankCard();
    $postData['cardno'] = I('post.content');
    $postData['bankname'] = I('post.bank_name');
    $postData['bankcode'] = I('post.bank_code');
    $id = I('post.id');
    $oldCard = I('post.old_card');
    //更新数据判断
    $updateBankData = [];
    foreach($postData as $k=>$v){
        if($v){
            $updateBankData[$k]=$v;
        }
    }
    if(!$updateBankData){
        redirect('?c=user&a=banklist','3','修改的信息为空');
    }
    $authBankInfo = $authBankModel->where("id='{$id}' AND cardno='{$oldCard}'")->get()->rowArr();
    if(!$authBankInfo){
        redirect('?c=user&a=banklist','3','该条绑卡信息不存在');
    }
    //调用支付平台接口,更新数据
    $jsonrpcObj = new \Lib\JsonRpcClient(BANKCARDWHITE);
    $data = [
        'identityid'=>$authBankInfo['user_id'],
        'cardno'=>$postData['cardno'] ? $postData['cardno'] : $authBankInfo['cardno'],
        'bankname'=>$postData['bankname'],
        'bankcode'=>$postData['bankcode'],
        'channel_code'=>$authBankInfo['channel'],
        'old_bankcode'=>$authBankInfo['bankcode'],
        'old_bankname'=>$authBankInfo['bankname'],
    ];
    $result = $jsonrpcObj->create($data);
    //更新本地数据
    $status = $authBankModel->update($updateBankData,['id'=>$id]);
    if($status && !(array_key_exists('error',$result))) {
        redirect('?c=user&a=banklist','3','修改银行卡号成功');
    }else{
        logs(['data'=>$data,'result'=>$result],'adminEditCardError');
        redirect('?c=user&a=banklist','3','修改银行卡号失败');
    }
}
/**
 * @pageroute
 * 修改用户display_name
 */
function edit_displayName()
{
    $uid = I('post.uid');
    if(!$uid){
        ajaxReturn(['error'=>'100','msg'=>'参数错误']);
    }
    $authUserModel = new \Model\AuthUser();
    $authUserInfo = $authUserModel->where(['id'=>$uid])->get()->rowArr();
    if(!$authUserInfo){
        ajaxReturn(['error'=>'100','msg'=>'该用户信息不存在']);
    }
    $status = $authUserModel->where(['id'=>$uid])->upd(['display_name'=>mask_string($authUserInfo['username'], 3, 6)]);
    if($status)
    {
        ajaxReturn(['error'=>'200','msg'=>'操作成功']);
    }else
    {
        ajaxReturn(['error'=>'200','msg'=>'操作失败']);
    }
}
/**
 * 取消用户实名认证
 * @pageroute
 */
function cancelAuthentication()
{
    $id = I('post.id');
    $sessionObj = new \Lib\Session();
    $session = $sessionObj->get('userData.admin_user');
    $adminUserName = $session['username'];
    if(!$id){
        ajaxReturn(['error'=>100,'msg'=>'参数缺失']);
    }
    $authIdentifyModel = new \Model\AuthIdentify();
    $authIdentifyInfo = $authIdentifyModel->where(['id'=>$id])->get()->row();
    if(!$authIdentifyInfo){
        ajaxReturn(['error'=>100,'msg'=>'该用户信息不存在']);
    }
    $status = $authIdentifyModel->update(['is_valid'=>0],['id'=>$id]);
    logs(['admin_user'=>$adminUserName,'userInfo'=>$authIdentifyInfo,'status'=>$status],'cancelAuthentication');
    if($status)
        ajaxReturn(['error'=>200,'msg'=>'更新成功']);
    else
        ajaxReturn(['error'=>100,'msg'=>'更新失败']);
}








