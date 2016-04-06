<?php use Phalcon\Tag as Tag ?>
<style>
.test, .test table{border-collapse:collapse;margin:-1px;width:100%;}
.test td{word-break:break-all; word-wrap:break-word;vertical-align: middle;color: #333;border:1px solid #ddd
;padding:0;background:#fff;}
.table-bordered tbody:first-child tr:first-child td{border-top:1px solid #ddd;}
.test tbody tr:nth-child(2n+1) td{background:none;}
.test .two div{padding-left: 5px;}
.test .two,.test .two-sub{border-right:0;}
.test .two-sub div{margin: 8px 0;border-bottom:1px solid #ddd;}
.test .two-sub div:last-child{border:0;}
.test .one{text-align:center;}
.test .one, .test .two-nav{width: 15%;}
.test .two, .test .two-sub{width: 85%;}
</style>

<?php echo $this->getContent() ?>
<!--
<ul class="pager">
    <li class="previous pull-left">
        <?php echo Tag::linkTo("products/index", "&larr; Go Back") ?>
    </li>
    <li class="pull-right">
        <?php echo Tag::linkTo(array("products/new", "Create products", "class" => "btn btn-primary")) ?>
    </li>
</ul>
-->
<table class="table table-bordered table-striped" align="center">
    <thead>
        <tr>
            <th>当前状态</th>
            <th>版本库</th>
            <th>备注</th>
            <th>对应文件</th>
            <th>申请</th>
            <th>审核</th>
            <th>上线</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    <?php
        if(isset($page->items)){
            foreach($page->items as $products){ ?>
        <tr>
            <td width="6%"><?php echo $products->getStatus(); ?></td>
            <td width="5%"><?php echo $products->repo; ?></td>
            <td width="15%"><?php echo $products->content ?></td>
            <td width="45%" style="padding:0;">
             <table class="test">
            <?php $detail =  json_decode($products->detail,true);
                foreach($detail as $i){
                    echo '<tr><td class="one"><div>'.$i['revision'].'</div></td><td class="two"><table>';
                    foreach($i['files'] as $type=>$ii){
                        echo '<tr><td class="two-nav"><div>'.$type.'</div></td>	<td class="two-sub">';
                        foreach($ii as $iii){
                            echo '<div>'.$iii.'</div>';
                        }
                        echo '</td></tr>';
                    }
                    echo '</table></td></tr>';
                }
                echo '</table>';

            ?>


            </td>
            <td width="6%"><?php echo $products->apply_name.'<br/>'.date('Y-m-d H:i:s',$products->apply_time); ?></td>
            <td width="6%"><?php if( $products->check_name) echo $products->check_name.'<br/>'.date('Y-m-d H:i:s',$products->check_time); ?></td>
            <td width="6%"><?php if( $products->publish_name) echo $products->publish_name.'<br/>'.date('Y-m-d H:i:s',$products->publish_time); ?></td>

            <td ><?php
            if($role == 'test' && $products->status == 0){
                echo Tag::linkTo(array("apply/check/".$products->id, '测试成功', "class" => "btn"));
                echo Tag::linkTo(array("apply/uncheck/".$products->id, '测试失败', "class" => "btn"));
            }
            if($role == 'oper' && $products->status ==1){
                echo Tag::linkTo(array("apply/publish/".$products->id, '发布', "class" => "btn"));
            }
             ?></td>
        </tr>
    <?php }
        } ?>
    </tbody>
    <tbody>
        <tr>
            <td colspan="8" align="right">
                <div class="btn-group">
                    <?php echo Tag::linkTo(array("apply/index", '<i class="icon-fast-backward"></i> 首页', "class" => "btn")) ?>
                    <?php echo Tag::linkTo(array("apply/index?page=".$page->before, '<i class="icon-step-backward"></i> 上一页', "class" => "btn ")) ?>
                    <?php echo Tag::linkTo(array("apply/index?page=".$page->next, '<i class="icon-step-forward"></i> 下一页', "class" => "btn")) ?>
                    <?php echo Tag::linkTo(array("apply/index?page=".$page->last, '<i class="icon-fast-forward"></i> 末页', "class" => "btn")) ?>
                    <span class="help-inline"><?php echo $page->current, "/", $page->total_pages ?></span>
                </div>
            </td>
        </tr>
    <tbody>
</table>