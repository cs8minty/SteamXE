<?php
    /**
     * @class  steamxeAdminView
     * @author 스비라
     * @brief  SteamXE 모듈의 admin view class
     */
 
    class steamxeAdminView extends steamxe {
 
        /**
         * @brief 초기화
         **/
        function init() {
            
            // 관리자 템플릿 파일의 경로 설정 (tpl)
            $template_path = sprintf("%stpl/",$this->module_path);
            $this->setTemplatePath($template_path);
            
        }
 
        /**
         * @brief 설정 
         **/
        function dispSteamxeAdminConfig() {

            // 설정 정보를 받아옴
            $config = $this->getConfig();
            Context::set('config', $config);

            // 스킨 리스트
            $oModuleModel = &getModel('module');
            $skin_list = $oModuleModel->getSkins($this->module_path);
            Context::set('skin_list', $skin_list);
 
            // 템플릿 파일 설정
            $this->setTemplateFile('config');
        }
        
        function dispSteamxeAdminManual() {
            
            $this->setTemplateFile('manual');
            
        }
    }
?>