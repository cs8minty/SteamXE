<?php
    /**
     * @class  steamxeController
     * @author 스비라
     * @brief  SteamXE 모듈의 controller class
     */
     
    class steamxeController extends steamxe {
     
        var $config = null;
        
        /**
         * @brief 초기화
         **/
        function init() {            
        }
        
        function procSteamxeSignin() {
            
            $OpenID = new LightOpenID($_SERVER['HTTP_HOST']);
            $OpenID->identity = 'http://steamcommunity.com/openid/';
            
            // 인증 후 받아온 파라메터를 얻는다
            $args = Context::gets(
                'openid_mode',
                'openid_identity'
            );
            
            if(!$OpenID->mode && !Context::get('is_logged')) $this->setRedirectUrl($OpenID->authUrl()); //인증 주소로 리다이렉트
            elseif(Context::get('is_logged')) return new Object(-1, 'already logged');
            elseif($OpenID->mode == 'cancel') return new Object(-1, 'User has canceled Authenticiation');
            elseif($OpenID->mode && $OpenID->validate()) {
                
                // 스팀 아이디 추출
                $SteamID64 = str_replace('http://steamcommunity.com/openid/id/', '', $args->openid_identity);

                // 가입된 멤버가 있는지 검색
                $args->user_id = $SteamID64;
                $output = executeQuery('member.getMemberInfo', $args);
                if (!$output->toBool()) return $output;
                
                if(!$output->data) $this->doSignup($SteamID64); // 가입된 멤버가 없으면 가입처리
                else $this->doLogin($output->data->member_srl); // 로그인 처리
                
            }
            
        }
        
        function procSteamxeSignout($return_url = null) {
            
            $oMemberController = &getController('member');
            $oMemberController->procMemberLogout();
            
            // 리다이렉트
            if($return_url) {
                $this->setRedirectUrl($return_url);
            } else {
                $this->setRedirectUrl(getNotEncodedUrl(''));
            }
            
        }
        
        function procSteamxeProfileUpdate() {
            $oMemberModel = &getModel('member');
            $member_info = $oMemberModel->getLoggedInfo();
            
            $this->updateProfileImage($member_info->user_id, $member_info->member_srl);
        }
        
        function doLogin($member_srl, $return_url = null) {
        
            // 로그인 처리
            $oMemberModel = &getModel('member');
            $member_info = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
            if (!$member_info) return new Object(-1, 'something wrong');
            
            $oMemberController = &getController('member');
            $oMemberController->doLogin($member_info->user_id, '', false);
            
            // 리다이렉트
            if($return_url) {
                $this->setRedirectUrl($return_url);
            } else {
                $this->setRedirectUrl(getNotEncodedUrl(''));
            }
                
        }
        
        function doSignup($communityId) {

            // Steam Web API로 유저 프로필을 받아옴
            //$oSteamxeModel = &getModel('steamxe');
            //$profile = $oSteamxeModel->getPlayerSummaries($communityId);
            //$steamId = $oSteamxeModel->convert64to32($communityId);
            //76561198087058227
            
            $user = SteamId::create($communityId);

            // 가입 처리
            $text_array = array('\~','\!','\@','\#','\$','\%','\^','\&','\*','\(','\)','\_','\+','\=','\-','\`','\,','\.','\/','\<','\>','\?','\;','\'','\:','\"','\[','\]','\{','\}','\\','\|');
            
            $args->member_srl = getNextSequence();
            $args->list_order = -1 * $args->member_srl;
            $args->user_id = $communityId;
            //$args->user_name = $steamId;
            $args->user_name = SteamId::convertCommunityIdToSteamId($communityId);
            //$args->nick_name = $profile->response->players[0]->personaname;
            $args->nick_name = $user->getNickname();
            $args->password = $text_array[rand(0,31)].md5(time()).$text_array[rand(0,31)].md5(getmicrotime());
            $output = executeQuery('steamxe.insertMember', $args);
            if(!$output->toBool()) return new Object(-1, 'msg_faild_signup');

            // 프로필 이미지 생성
            $this->updateProfileImage($communityId, $args->member_srl);
            
            // 가입 후 로그인
            $this->doLogin($args->member_srl);
        
        }
        
        // 프로필 이미지 업데이트 (스팀 API로 받아옴)
        function updateProfileImage($steamid64, $member_srl) {

            $user = new SteamId($steamid64);
            $source_file = $this->get_contents($user->getFullAvatarUrl());
            
            // Get a target path to save
            $target_path = sprintf('files/member_extra_info/profile_image/%s', getNumberingPath($member_srl));
		    FileHandler::makeDir($target_path);
            
            $target_file = sprintf('%s%d.%s', $target_path, $member_srl, 'jpg');
            
            FileHandler::writeFile($target_file, $source_file);
            FileHandler::createImageFile($target_file, $target_file ,184, 184, 'jpg');
            
            //return true;
        }
        
        function get_contents($URL) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $URL);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }
     }
?>