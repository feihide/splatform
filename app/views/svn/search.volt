<?php use Phalcon\Tag as Tag ?>
<style>
 textarea#content{height:34px;width:420px;resize:none;}
</style>
<?php echo $this->getContent() ?>
<?php echo Tag::form(array("apply/new","id"=>'applyForm', "autocomplete" => "off")) ?>
<ul class="pager">
    <li class="previous pull-left">
        <?php echo Tag::linkTo("svn/index", "&larr; 返回") ?>
    </li>
    <li class="pull-right">
        <ul>
            <li class="pull-left" style="margin:0 30px 0 0;">
                <div class="pull-left" style="padding:14px 8px 0 0"> <label class="control-label" for="content">申请备注（必填)</label></div>
                <div class="pull-left" class="controls">
                 <?php  echo Phalcon\Tag::textArea(array("content","require"=>"require", "cols" => 10, "rows" => 4));?>
                </div>
             </li>
             <li class="pull-right">
                 <?php
                 echo Phalcon\Tag::hiddenField(array("repo", "value" => $repo));
                 echo Tag::submitButton(array("提交选中文件", "class" => "btn btn-primary",'onclick'=>'return Apply.validate();')) ?>
             </li>
        </ul>
    </li>
</ul>

<table class="table table-bordered table-striped" align="center">
    <thead>
        <tr>
            <th>目前只提供最新的50条的数据</th>
        </tr>
    </thead>
    <thead>
        <tr>
            <th>版本</th>
            <th>提交代码</th>
            <th>备注</th>
            <th>提交时间</th>
            <th>当前状态 全选（）</th>
        </tr>
    </thead>
    <tbody>
    <?php

            foreach($products as $product){

            ?>
        <tr>
            <td><?php echo $product->revision ?></td>
            <td><?php foreach($product->files as $type=> $file){
                    echo 'svn提交类型'.$type.'<br/>';
                    foreach($file as $code){
                        echo $code.'<br/>';
                    }

            }?></td>
            <td><?php echo $product->msg ?></td>
            <td><?php echo date('Y-m-d H:i:s',$product->date); ?></td>
            <td><?php echo isset($product->isApply)?'已申请':'未申请';
            if(!isset($product->isApply))
                echo  ' |  提交申请'.Tag::checkField(array("revision[]", "value" => $product->_id)); ?></td>
        </tr>
    <?php }
         ?>
    </tbody>
    <!--
    <tbody>
        <tr>
            <td colspan="7" align="right">
                <div class="btn-group">
                    <?php echo Tag::linkTo(array("products/search", '<i class="icon-fast-backward"></i> First', "class" => "btn")) ?>
                    <?php echo Tag::linkTo(array("products/search?page=".$page->before, '<i class="icon-step-backward"></i> Previous', "class" => "btn ")) ?>
                    <?php echo Tag::linkTo(array("products/search?page=".$page->next, '<i class="icon-step-forward"></i> Next', "class" => "btn")) ?>
                    <?php echo Tag::linkTo(array("products/search?page=".$page->last, '<i class="icon-fast-forward"></i> Last', "class" => "btn")) ?>
                    <span class="help-inline"><?php echo $page->current, "/", $page->total_pages ?></span>
                </div>
            </td>
        </tr>
    <tbody>-->
</table>
</form>