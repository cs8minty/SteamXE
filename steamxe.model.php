<?php
    /**
     * @class  steamxeModel
     * @author 스비라
     * @brief  SteamXE 모듈의 model class
     */
 
    class steamxeModel extends steamxe {
 
        /**
         * @brief 초기화
         **/
        function init() {
        }
        
        function getSteamProfile($member_srl) {
            
            $oModuleModel = &getModel('member');
            $member_info = $oModuleModel->getMemberInfoByMemberSrl($member_srl);
            
            // 스팀 프로필을 받아옴
            $args->member_srl = $member_srl;
            $output = executeQuery('steamxe.getSteamProfileByMemberSrl', $args);
            if(!$output->toBool()) return new Object(-1, 'msg_faild');
            
            unset($args);
            $args = $output->data;
            $args->nick_name = $member_info->nick_name;
            $args->profile_image = $member_info->profile_image->file;
            
            return $args;
            
        }
        
        function getSteamProfileBySteamId64($steamid64) {
            
            // 스팀 프로필을 받아옴
            $args->user_id = $steamid64;
            $output = executeQuery('steamxe.getMemberBySteamId64', $args);
            if(!$output->toBool()) return new Object(-1, 'msg_faild');
            
            $oMemberModel = &getModel('member');
            $profile_image = $oMemberModel->getProfileImage($output->data->member_srl);
            
            unset($args);
            $args->steamid = $output->data->user_name;
            $args->steamid64 = $output->data->user_id;
            $args->nick_name = $output->data->nick_name;
            $args->profile_image = $profile_image;
            
            return $args;
        
        }
        
        function getPlayerSummaries($steamid64) {
            $config = $this->getConfig();
            $result = json_decode($this->get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $config->apikey . '&steamids=' . $steamid64 . '&format=json'));
            
            return $result;
        }
        
        function getOwnedGame($steamid64) {
            $config = $this->getConfig();
            $result = json_decode($this->get_contents('http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=' . $config->apikey . '&steamid=' . $steamid64 . '&format=json'));

            return $result;
        }
        
        function getRecentlyPlayedGames($steamid64) {
            $config = $this->getConfig();
            $result = json_decode($this->get_contents('http://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/?key=' . $config->apikey . '&steamid=' . $steamid64 . '&format=json'));
            
            return $result;
        }
        
        function getUserStatsForGame($steamid64, $appid) {
            $config = $this->getConfig();
            $result = json_decode($this->get_contents('http://api.steampowered.com/ISteamUserStats/GetUserStatsForGame/v0002/?appid=' . $appid . '&key=' . $config->apikey . '&steamid=' . $steamid64));
            return $result;
        }
        
        function getPlayerAchievements($steamid64, $appid) {
            $config = $this->getConfig();
            $result = json_decode($this->get_contents(' http://api.steampowered.com/ISteamUserStats/GetPlayerAchievements/v0001/?appid=' . $appid . '&key=' . $config->apikey . '&steamid=' . $steamid64));
            return $result;
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