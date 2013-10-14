<?php
	/**
     * @class  steamxe
     * @author 스비라
     * @brief  SteamXE 모듈의 클래스
     */
    
    require_once(_XE_PATH_.'modules/steamxe/openid.php');
    require_once(_XE_PATH_.'modules/steamxe/lib/steam-condenser.php');

	class steamxe extends ModuleObject {

		/**
         * @brief 설치시 추가 작업이 필요할시 구현
         **/
		function moduleInstall() {

			return new Object();

		}

		/**
         * @brief 설치가 이상이 없는지 체크하는 method
         **/
		function checkUpdate() {

			return false;

		}

		/**
         * @brief 업데이트 실행
         **/
		function moduleUpdate() {

			return new Object(0, 'success_updated');

		}

		function moduleUninstall() {
 
            return new Object();

        }

		/**
         * @brief 캐시 파일 재생성
         **/
		function recompileCache() {

		}
		
		function getConfig() {
            
            if ($GLOBALS['steamxe_config']) return $GLOBALS['socialxe_config'];
            
            // Get member configuration stored in the DB
		    $oModuleModel = &getModel('module');
		    $config = $oModuleModel->getModuleConfig('steamxe');
        
            if(!$config->apikey) $config->apikey = '';
            
            $GLOBALS['steamxe_config'] = $config;
            
            return $config;
        
        }
	}
?>