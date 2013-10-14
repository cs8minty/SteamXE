<?php
    /**
     * @class  steamxeAdminController
     * @author 스비라
     * @brief SteamXE 모듈의 admin controller class
     */
    class steamxeAdminController extends steamxe {
        /**
         * @brief 초기화
         **/
        function init()
        {
        
        }
        
        /**
         * @brief 설정 저장
         **/
        public function procSteamxeAdminSaveConfig() {
            
            if(Context::get('skin')) $args->skin = Context::get('skin');
            if(Context::get('apikey')) $args->apikey = Context::get('apikey');
            if(Context::get('required_games')) $args->required_games = Context::get('required_games');
    
            $oModuleController = getController('module');
            $output = $oModuleController->updateModuleConfig('steamxe', $args);
    
            // default setting end
            $this->setMessage('success_updated');
    
            $returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispSteamxeAdminConfig');
            $this->setRedirectUrl($returnUrl);
            
        }
}
?>