<?php
/**
 * Created by PhpStorm.
 * User: gaoruishen
 * Date: 2020/3/27
 * Time: 11:07 AM
 */

namespace GRS\Yii2;


use yii\db\ActiveRecord;

class Model extends ActiveRecord{
    public function __construct(array $config=[]) {
        parent::__construct($config);
    }

    /**
     * 插入新数据
     * 插入失败返回null
     * 插入成功返回成功的model
     * @param array $attributes
     * @return null|static
     */
    public static function add(array $attributes) {
        if(empty($attributes)) return null;
        $model = new static;
        foreach($attributes as $column=>$value) {
            $model->hasAttribute($column) && $model[$column] = $value;
        }
        if($model->save()) return $model;
        return null;
    }
    /**
     * 为参数增加表前缀
     *
     * 输入：['id':=>'0','field1'=>'value1']
     * 输出: [static::tablename().'id':=>‘0’, static::tablename().'field1'=>'value1']
     * @param array $attributes
     */
    public static function addPrefixKey($attributes=[]) {
        if(!is_array($attributes)) {
            return $attributes;
        }
        $prefix = static::tableName();
        $result = [];
        foreach($attributes as $key => $value) {
            if(is_numeric($key) || in_array(strtolower($key),['and','or'])) continue;
            $result[$prefix.'.'.$key] = $value;
        }
        return $result;
    }

    /**
     * 增强insert方法,自动插入c_t,u_t
     */
    public function insert($runValidation = true, $attributes = null) {
        $attrs = $this->getAttributes();
        $attrs = $this->toTimeStamp(['u_t','c_t'],$attrs);
        $this->hasAttribute('u_t') && $this->u_t = $attrs['u_t'];
        $this->hasAttribute('c_t') && $this->c_t = $attrs['c_t'];
        $columns = $this->getTableSchema()->columns;
        foreach($columns as $name=>$column) {
            if($this->getAttribute($name) === null) {
                $this->setAttribute($name,$column->defaultValue);
            }
        }
        return parent::insert($runValidation, $attributes);
    }

    /**
     * 增强yii2的find方法，增加分页paginate
     * 例子：
     * Model::find()->where($params)->paginate($page,$pageSize)
     * 如果需要切换db
     * Model::find()->where($params)->paginate($page,$pageSize,$db)
     * @return Query
     */
    public static function find() {
        return Yii::createObject(Page::className(), [get_called_class()]);
    }

    /**
     * 增强update方法，自动更新u_t
     */
    public function update($runValidation = true, $attributeNames = null) {

        $dirties = $this->getDirtyAttributes();
        if(count($dirties) > 0 ) {
            $keys = static::primaryKey();
            foreach($keys as $key) {
                if(array_key_exists($key,$dirties)) throw new Exception('不允许更新主键');
            }
            $attrs = $this->getAttributes();
            $attrs = $this->toTimeStamp(['u_t'],$attrs);
            $this->hasAttribute('u_t') && $this->u_t = $attrs['u_t'];
        }
        return parent::update($runValidation,$attributeNames);
    }

    /**
     * 增强yii2自动加入u_t
     * @param array $attributes
     * @param string $condition
     * @param array $params
     */
    public static function updateAll($attributes, $condition = '', $params = []) {
        $model = new static;
        $keys = static::primaryKey();
        foreach($keys as $key) {
            if(array_key_exists($key,$attributes)) throw new Exception('不允许更新主键');
        }
        if($model->hasAttribute('u_t')&& is_array($attributes) && !isset($attributes['u_t'])) {
            $attributes['u_t'] = time();
        }
        return parent::updateAll($attributes,$condition,$params);
    }

    /**
     * 增强yii2 批量插入
     * @param $columns
     * @param $values
     * @return bool|int
     * @throws \yii\db\Exception
     */
    public static function batchInsert($columns, $values) {
        $queryBuilder = static::getDb()->getQueryBuilder();
        if(empty($values))  return false;
        $model = new static;
        $time = time();

        if(!in_array('c_t',$columns) && $model->hasAttribute('c_t')) {
            $columns[] = 'c_t';
            foreach($values as &$value) {
                if(!is_array($value))  continue;
                $value[] = $time;
            }
        }

        if(!in_array('u_t',$columns) && $model->hasAttribute('u_t')) {
            $columns[] = 'u_t';
            foreach($values as &$value) {
                if(!is_array($value)) continue;
                $value[] = $time;
            }
        }

        $sql  = $queryBuilder->batchInsert(static::tableName(),$columns,$values);
        $command = static::getDb()->createCommand($sql);
        return $command->execute();

    }

    /**
     * 事务支持，默认对事务进行传播
     * 例子：
     *  $func = funciton() use($params) {
     * };
     * Model::transaction($func,$isolation)
     * @param $run
     * @param null $isolation
     * @return mixed
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public static function transaction($run,$isolation = null) {
        $transaction = static::getDb()->getTransaction();
        if(null !== $transaction) {
            return $run();
        }
        try{
            $transaction =  static::getDb()->beginTransaction($isolation);
            $result = $run();
            $transaction->commit();
            return $result;
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }
}