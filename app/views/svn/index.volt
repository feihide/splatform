<?php use Phalcon\Tag as Tag ?>

<?php echo $this->getContent() ?>

<?php echo Tag::form(array("svn/search", "autocomplete" => "off")) ?>

<div class="center scaffold">

    <h2>选择版本库</h2>

    <div class="clearfix">
        <label for="id">svn 账号</label>
        <?php echo $username;?>
    </div>

    <div class="clearfix">
        <label for="product_types_id">版本库</label>
        <?php echo Tag::select(array("repos", $repos, "using" => array("id", "name"), "useDummy" => false)) ?>
    </div>

    <div class="clearfix">
        <label for="product_types_id">时间段</label>
        <?php echo Tag::select(array("section", $section, "using" => array("id", "name"), "useDummy" => false)) ?>
    </div>

    <div class="clearfix">
        <?php echo Tag::submitButton(array("Search", "class" => "btn btn-primary")) ?>
    </div>

</div>

</form>
