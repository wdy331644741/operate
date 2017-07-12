<?php
define("ENVIRONMENT","dev"); // "dev" "testing" "production"
define("RUN_MODE","cli"); // 访问模式仅限cli模式
define('__WEBROOT__',__DIR__);
define("__APP_ROOT_PATH__", dirname(__WEBROOT__));
define('__FRAMEWORK_PATH__',dirname(__APP_ROOT_PATH__).'/system');
//define('__FRAMEWORK_PATH__','/home/liuqi/www/wl_framework');
//define('__FRAMEWORK_PATH__','/home/wwwroot/default/wanglibao/demand_operate/system');
define("__PROJECT_NAME__", 'autotools');
$argv = ['autotools', 'generatortable', 'run'];
$argc = count($argv);
require_once __FRAMEWORK_PATH__ . DIRECTORY_SEPARATOR  . "init.php";



/**
 * 校验用户流水和余额是否相符
 * @pageroute
 */
function run()
{
    try {
        $config = include_once __DIR__ . DIRECTORY_SEPARATOR ."config.php";
        $model = new \Model\Model($config);
        if ($config['DB_NAME']) {
            $buildInfo = array();
            $tablesCache = __APP_MODEL_PATH__ . DIRECTORY_SEPARATOR .  'static.tables.php';
            //解析数据库表结构
            $tables = $model->parseTables($config['DB_NAME']);
            ob_start();
            echo '<?php ';
            echo "\r\n";
            echo 'return ';
            var_export($tables);
            $tablesCode = ob_get_clean() . ';';
            if (false !== file_put_contents($tablesCache, $tablesCode))
                $info = array('state' => 'success', 'name' => $tablesCache);

            else
                $info = array('state' => 'error', 'name' => $tablesCache);
            $buildInfo[] = $info;
            //除去分表时重复的表结构
            foreach($tables as $key=>&$val)
            {
                $key = preg_replace('/\d*/','',$key);
                $result[$key] = $val;
            }
            //生成模型文件
            $modelFiles = array_keys($result);
            $preSub = C("DB_PREFIX");//获取表名的前缀
            foreach ($modelFiles as $model) {
                $psrName = '';//model类文件的名
                $className = '';//class类名
                $replacePsr = str_replace($preSub, "", $model);//去掉表名前缀
                $psrArr = explode("_", $replacePsr);
                foreach ($psrArr as $value) {
                    $psrName .= strtolower($value);
                    $className .= ucfirst($value);
                }
                $modelfile = __APP_MODEL_PATH__ . DIRECTORY_SEPARATOR  . $psrName . '.php';
                if (!file_exists($modelfile)) {
                    $phpTpl = <<<'TPL'
<?php
namespace Model;
class :classname extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct(':tablename');
        if ($pkVal)
            $this->initArData($pkVal);
    }
}
TPL;
                    $phpCode = str_replace(array(':classname', ':tablename'), array($className, $replacePsr), $phpTpl);

                    file_put_contents($modelfile, $phpCode);
                    $info = array('state' => 'success', 'name' => $modelfile);
                    $buildInfo[] = $info;

                } else {
                    $info = array('state' => 'continue', 'name' => $modelfile);
                    $buildInfo[] = $info;
                }
            }

        } else
            throw new \Exception('数据库名称未配置，或使用的数据库不存在');

    } catch (\Exception $e) {
        echo $e->getMessage();
    }
    if (is_array($buildInfo) && !empty($buildInfo)) {
        $br = php_sapi_name() == 'cli' ? PHP_EOL : '<br />';
        foreach ($buildInfo as $row) {
            echo $row['name'];
            echo $br;
            echo $row['state'];
            echo $br;
        }
    }
}
