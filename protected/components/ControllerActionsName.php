<?php
//get all actions of controller and check them - PDQuang
class ControllerActionsName  extends CApplicationComponent{
    
    public static $ajax_actions = array('actionAjaxActivate', 'actionAjaxDeactivate', 'actionAjaxShow', 'actionAjaxNotShow', 'actionAjaxApprove');    

    //return string all actions in this controller, if not return null
    public static function getActions($controller, $module=null)
    {
        $controller_name = ucfirst($controller).'Controller';
        if ($module!=null){
            $path='protected'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.'controllers';
        }else{
            $path='protected'.DIRECTORY_SEPARATOR.'controllers';
        }        
        if(!file_exists($path.DIRECTORY_SEPARATOR.$controller_name.'.php') )
                return null;

        include_once($path.DIRECTORY_SEPARATOR.$controller_name.'.php');

        $reflection = new ReflectionClass($controller_name); 
        $methods = $reflection->getMethods();
 
        $actions = '';
        foreach($methods as $method)
        {           
            if (strpos($method->name,'action')===0 and ctype_upper($method->name[6]))
                    //uncomment when not return ajax action
                     //and (!in_array($method->name, ControllerActionsName::$ajax_actions) || in_array($method->name, array())))
            {
//                $actions .= str_replace('action','',$method->name).', ';
                $actions .= preg_replace('/action/', '', $method->name, 1).', ';
            }
        }        
        $actions = rtrim($actions, ', ');
        
        if($actions != '')
            return $actions;
        return null;
    }
    
    //only check controller exists
    public static function checkControllerExist($controller, $module=null)
    {
        //check controller name and module name
        if ($module!=null){
            $path='protected'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.'controllers';
        }else{
            $path='protected'.DIRECTORY_SEPARATOR.'controllers';
        }
        $controller = ucfirst($controller).'Controller';
        if(!file_exists($path.DIRECTORY_SEPARATOR.$controller.'.php') )
                return false;
        return true;
    }
    
    //check all controller, module, actions exist
    public static function checkControllerActionsExist($controller, $group_actions, $module=null)
    {
        //check controller name and module name
        if ($module!=null){
            $path='protected'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.'controllers';;
        }else{
            $path='protected'.DIRECTORY_SEPARATOR.'controllers';
        }
        $controller = ucfirst($controller).'Controller';
        if(!file_exists($path.DIRECTORY_SEPARATOR.$controller.'.php') )
                return false;
        
        //check action name
        include_once($path.DIRECTORY_SEPARATOR.$controller.'.php');
        $reflection = new ReflectionClass($controller); 
        $methods = $reflection->getMethods();
        
        $actions=array();
        foreach($methods as $method)
        {           
            if (strpos($method->name,'action')===0 and ctype_upper($method->name[6]))                    
            {
//                $actions[]= strtolower(str_replace('action','',$method->name));old. ANH DUNG fix Aug 08, 2014
                $actions[]= strtolower(preg_replace('/action/', '', $method->name, 1));
            }
        }        
        
        if(is_array($group_actions))
        foreach($group_actions as $key => $string_actions)
        {
            $array_action = array_map('trim',explode(",",trim($string_actions)));
            foreach($array_action as $key2 => $action)
            {
                $action = strtolower($action);
                if(!in_array($action, $actions))
                        return FALSE;                  
            }
        }
        else {
            $array_action = array_map('trim',explode(",",trim($group_actions)));
            foreach($array_action as $key2 => $action)
            {
                $action = strtolower($action);
                if(!in_array($action, $actions))
                        return FALSE;                  
            }
        }
        
        return true;
    }
    
    //get list actions this user can access
    /**
     * $accessRules like [0] => Array
        (
            [0] => allow
            [actions] => Array
                (
                    [0] => Index
                    [1] => View
                    [2] => Delete
                )

            [users] => Array
                (
                    [0] => nguyenhoangthi
                )

        )

    [1] => Array
        (
            [0] => deny
        )
     */
    public static function getListActionsCanAccess($accessRules, $role_id)
    {
        // May 16, 2014 ANH DUNG fix, do PD Quang CODE bậy
        foreach($accessRules as $key => $role)
        {
            // $role is Array ( [0] => allow [actions] => Array ( [0] => View [1] => Index ) [users] => Array ( [0] => @ ) [expression] => Yii::app()->user->role_id == 10 ) 1
            // $role['expression'] is  Yii::app()->user->role_id == 10
            // $role['actions'] is Array ( [0] => View [1] => Index ) 1            
            if(isset($role[0]) && $role[0]=='allow' && isset($role['actions']))
                return $role['actions'];
            //  May 17, 2014  đoạn dưới có thể bỏ 
            if(isset($role['expression']) && isset($role['actions'])){
                $tempEX = explode('==', trim($role['expression'])); // ANH DŨNG FIX 10-17-2013
                if(isset($tempEX[1]) && trim($tempEX[1]) == $role_id)
                    return $role['actions'];
            }
        }
        // May 16, 2014 ANH DUNG fix, do PD Quang CODE bậy
        
        return array();
    }
    
    //trim, lower before in_array
    public static function isAccessAction($action, $listActions)
    {
        foreach($listActions as $key => $value)
        {
            $listActions[$key] = strtolower(trim($value));            
        }
        $action = strtolower(trim($action));
        
        return in_array($action, $listActions);
    }
    
    //in view, show menus base roles
    public static function createMenusRoles($menu_array, $listActions)
    {
        foreach($menu_array as $key => $button)
        {
            if(!ControllerActionsName::isAccessAction($button['url'][0], $listActions))
                unset ($menu_array[$key]);
        }
        $menu_array = array_values($menu_array); //reindex set $menu_array[2] to $menu_array[1] PLEASE!
        return $menu_array;
    }
    
    //view/index shown button
    public static function createIndexButtonRoles($listActions, $default_buttons = array('view','update','delete'))
    {        
        $return_buttons = '';
        foreach($default_buttons as $key => $button)
        {
            if(ControllerActionsName::isAccessAction($button, $listActions))
                $return_buttons.= '&nbsp;&nbsp;{'.$button.'}'; // ANHDUNG ADD &nbsp; ON Aug 17, 2015
        }
        return $return_buttons;
    }
    
    //active-deactive in view/index
    public static function checkVisibleButton($listActions)
    {
        $default_buttons = array('AjaxActivate', 'AjaxDeactivate');        
        
        foreach($default_buttons as $key => $button)
        {
            if(!ControllerActionsName::isAccessAction($button, $listActions))
                return false;
        }
        return true;
    }
    
    //get all controllers
    public static function getControllers($module=null)
    {

        if ($module!=null){
            $path=join(DIRECTORY_SEPARATOR,array(Yii::app()->modulePath,$module,'controllers'));            
        }else{
            $path='protected'.DIRECTORY_SEPARATOR.'controllers';
        }                                
        $controllers = array();
        foreach(scandir($path) as $key => $value)
        {
            if(stripos($value,'Controller.php')!==false)
                    $controllers[] = $value;
        }
        foreach ($controllers as &$c)
        {            
            $c=str_ireplace('Controller.php','',$c);
        }       
        return $controllers;         
    }
    
    //gen controller/actions
    public static function genControllerActions()
    {
        $modules = array('admin', 'member', null);
        foreach($modules as $key => $value)
        {
            
        }
        
        //admin
//        $controllers = ControllerActionsName::getControllers('admin');
//        foreach($controllers as $key => $value)
//        {
//            $controller = new Controllers;
//            $controller->controller_name = $value;
//            
//            $action = ControllerActionsName::getActions($value, "admin");
//            $controller->actions = $action;
//            $controller->module_name = "admin";
//            MyDebug::output($controller->attributes);
//            $controller->save();
//        }
        
        //frontend
        $controllers = ControllerActionsName::getControllers();
        foreach($controllers as $key => $value)
        {
            $controller = new Controllers;
            $controller->controller_name = $value;
            
            $action = ControllerActionsName::getActions($value);
            $controller->actions = $action;
            //$controller->module_name = "admin";
            MyDebug::output($controller->attributes);
            //$controller->save();
        }
    }
    
 
}






