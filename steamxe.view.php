<?php
    /**
     * @class  steamxeView
     * @author 스비라
     * @brief SteamXE 모듈의 view class
     */
 
    class steamxeView extends steamxe {
 
        /**
         * @brief 초기화
         **/
        function init() {
        
            // SteamXE 설정을 얻는다
		    $config = $this->getConfig();
 
            $skin = $config->skin;
            if(!$skin) $template_path = sprintf('%sskins/%s', $this->module_path, 'default');
            else $template_path = sprintf('%sskins/%s', $this->module_path, $skin);
            $this->setTemplatePath($template_path);
 
        }
         
        /**
         * @brief SteamXE 프로필
         **/
        function dispSteamxeProfile() {
            
            $communityId = Context::get('id');
            if(!$communityId && Context::get('is_logged')) {
                $logged_info = Context::get('logged_info');
                $communityId = $logged_info->user_id;
            }

            $oMemberModel = &getModel('member');
            $member_info = $oMemberModel->getMemberInfoByUserID($communityId);
            $extra = new SteamId($communityId);

            $args->communityId = $member_info->user_id;
            $args->steamId = $member_info->user_name;
            $args->nickname = $member_info->nick_name;
            $args->signature = $member_info->signature;
            $args->groups = $member_info->group_list;
            $args->avatar = $member_info->profile_image->file;
            $args->profileUrl = 'http://steamcommunity.com/profile/' . $args->communityId;
            $args->tradeBanState = $extra->getTradeBanState();
            $args->isBanned = $extra->isBanned();
            $args->inInGame = $extra->isInGame();
            $args->inOnline = $extra->isOnline();
            $args->isPublic = $extra->isPublic();
            
            //Context::set('args', $args);
            Context::set('args', $args);
            
            $this->setTemplateFile('profile');
        }
    }
?>