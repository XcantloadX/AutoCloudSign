<?php
//conf.php helper
require_once "vendor/autoload.php";
require_once "lib/scriptmanager.php";
require_once "lib/log.php";
require_once "default/conf.php";
if(file_exists("conf.php")){
    include_once "conf.php";
}
use ScriptManager as SM;
use Nette\PhpGenerator\ClassType;


class ConfHelper{
    /**
     * 向 $storageClass 中添加新的签到脚本项
     */
    private static function addScript(ClassType $storageClass, array $attrs){
        //创建属性
        $property = $storageClass->addProperty($attrs["id"], [])
                    ->setPublic(true)
                    ->setStatic(true);
        $comment = $attrs["name"];
        if(isset($attrs["note"]))
            $comment = $comment."\n注：".$attrs["note"];
        $property->addComment($comment);
    }

    private static function fromDefaultSetting() : ClassType{
        $class = ClassType::from(DefaultSettings::class);
        $class->setName("Settings");
        return $class;
    }

    private static function fromDefaultStorage() : ClassType{
        $class = ClassType::from(DefaultScriptStorage::class);
        $class->setName("ScriptStorage");
        return $class;
    }

    /**
     * 写入配置文件
     */
    private static function write(ClassType $settingsClass, ClassType $storageClass){
        $output = <<<DATA
<?php
//配置文件
//本脚本由程序自动生成
//既可手动修改，也可由程序自动修改

$settingsClass

$storageClass
DATA;
        
        file_put_contents("conf.php", $output);
    }

    /**
     * 初始化 conf.php
     */
    public static function init(){
        if(file_exists("conf.php")){
            return;
        }
    
        $storageClass = ConfHelper::fromDefaultStorage();
        
        $files = scandir("script");
        foreach($files as $file){
            $path = "script"."/".$file;
            if($file != "." && $file != ".." && $file != "base.php" && strpos($file, ".php") > 0){
                //获取脚本属性
                try {
                    $attrs = SM\getAttributes_($path);
                } catch (SM\ScriptException $e) {
                    logError($e);
                    logError("已跳过。");
                    continue;
                }
            
                ConfHelper::addScript($storageClass, $attrs);
            }
        }
        
        $settingsClass = ConfHelper::fromDefaultSetting();
        ConfHelper::write($settingsClass, $storageClass);
    }
    
    /**
     * 自动检查 conf.php 并创建缺少的配置项
     */
    public static function update(){
        $updated = false; //是否已更新
        //----Settings----
        $userVars1 = get_class_vars("Settings");
        $defaultVars1 = get_class_vars("DefaultSettings");
        $userclass1 = ClassType::from(Settings::class);
        $defaultClass1 = ClassType::from(DefaultSettings::class);
        foreach($defaultVars1 as $k=>$v){
            if(array_key_exists($k, $userVars1))
                continue; //如果已设置则跳过
            $userclass1->addProperty($k, $v)
                  ->setStatic(true)
                  ->setPublic(true)
                  ->setComment($defaultClass1->getProperty($k)->getComment());
            logInfo($k);
            $updated = true;
        }

        //----ScriptStorage----
        $userVars2 = get_class_vars("ScriptStorage");
        $userclass2 = ClassType::from(ScriptStorage::class);
        $files = scandir("script");
        foreach($files as $file){ //扫描所有脚本并加上配置里没有的
            $path = "script"."/".$file;
            if($file != "." && $file != ".." && $file != "base.php" && strpos($file, ".php") > 0){
                //获取脚本属性
                try {
                    $attrs = SM\getAttributes_($path);
                } catch (SM\ScriptException $e) {
                    logError($e);
                    logError("已跳过。");
                    continue;
                }

                if(!array_key_exists($attrs["id"], $userVars2)){
                    ConfHelper::addScript($userclass2, $attrs);
                    $updated = true;
                }
            }
        }

        if($updated)
            logInfo("conf.php 中更新了新的配置项，或添加了新的签到脚本，请检查。");
        
        ConfHelper::write($userclass1, $userclass2);
    }
    
    /**
     * 保存配置项
     */
    public static function save(){
        ConfHelper::write(ClassType::from(Settings::class), ClassType::from(ScriptStorage::class));
    }
}



