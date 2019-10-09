<?php

namespace app\common\model;

use think\Model;

/**
 * ============================================================================
 * DSMall多用户商城
 * ============================================================================
 * 版权所有 2014-2028 长沙德尚网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.csdeshang.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 门店店员数据层模型
 */
class Entityclerk extends Model {

    /**
     * 读取地址列表
     * @author csdeshang
     * @param array $condition 查询条件
     * @param type $order 排序
     * @return array  数组格式的返回结果
     */
    public function getEntityclerkList($condition, $page, $limit = '', $order = 'entityclerk_id desc') {
        $field = 'entityclerk.*,entityshop.entityshop_name';
        if ($page) {
            $result = db('entityclerk')->alias('entityclerk')->join('__ENTITYSHOP__ entityshop', 'entityshop.entityshop_id = entityclerk.entityshop_id', 'LEFT')->where($condition)->field($field)->order($order)->paginate($page, false, ['query' => request()->param()]);
            $this->page_info = $result;
            return $result->items();
        } else {
            $result = db('entityclerk')->alias('entityclerk')->join('__ENTITYSHOP__ entityshop', 'entityshop.entityshop_id = entityclerk.entityshop_id', 'LEFT')->where($condition)->field($field)->order($order)->limit($limit)->select();
            return $result;
        }
    }

    /**
     * 新增地址
     * @author csdeshang
     * @param array $data 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function addEntityclerk($data) {
        return db('entityclerk')->insertGetId($data);
    }

    /**
     * 取单个地址
     * @author csdeshang
     * @param int $id 地址ID
     * @return array 数组类型的返回结果
     */
    public function getOneEntityclerk($condition) {
        $result = db('entityclerk')->where($condition)->find();
        return $result;
    }

    /**
     * 更新地址信息
     * @author csdeshang
     * @param array $data 更新数据
     * @param array $condition 更新条件
     * @return bool 布尔类型的返回结果
     */
    public function editEntityclerk($condition, $data) {
        return db('entityclerk')->where($condition)->update($data);
    }

    /**
     * 删除地址
     * @author csdeshang
     * @param array $condition记录ID
     * @return bool 布尔类型的返回结果
     */
    public function delEntityclerk($condition) {
        return db('entityclerk')->where($condition)->delete();
    }

}

?>
