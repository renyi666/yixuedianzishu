<?php
/**
 * Author:  翟小斐
 */

namespace Admin\Model;


use Think\Model;

class BookCategoryRelationModel extends Model
{
    public function update($id = 0){
        $tag = array_unique((array)I('category',''));

        if(empty($id)){
            $id = I('id',0);
        }
        if(is_string($tag) && empty($tag)){
            $where['book_id'] = $id;
            $this->where($where)->delete();
        }elseif (is_array($tag)) {


            $oldTags = $this->getCategorys($id);//旧的标签
            $newTags = $tag;//新的标签

            $needAdd = array_diff($newTags, $oldTags);//需要添加标签集
            $needDel = array_diff($oldTags, $newTags);//需要删除的标签集
            $where['book_id'] = $id;
            //添加新的标签

            foreach ($needAdd as $k=>$v){
                $where['category_id']    = $v;
                $where['book_id'] = $id;
                $count = $this->where($where)->count();
                if($count <= 0){
                    $this->add($where);
                }
            }
            foreach ($needDel as $k=>$v){
                $where['category_id']    = $v;
                $this->where($where)->delete();
            }
        }
    }
    public function getCategorys($bookId){
        $where['book_id'] = $bookId;
        $res = $this->where($where)->select();
        if($res){
            $categroy = '';
            foreach($res as $row){
                $categroy .= $row['category_id'].',';
            }
        }
        return explode(',',mb_strcut($categroy,0,strlen($categroy)-1));
    }
}