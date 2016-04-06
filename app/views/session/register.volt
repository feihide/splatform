<?php use Phalcon\Tag as Tag ?>
{{ content() }}

<div class="page-header">
    <h2>Register for SVN PLATFORM</h2>
</div>

{{ form('session/register', 'id': 'registerForm', 'class': 'form-horizontal', 'onbeforesubmit': 'return false') }}
    <fieldset>
        <div class="control-group">
            <label class="control-label" for="name">姓名</label>
            <div class="controls">
                {{ text_field('name', 'class': 'input-xlarge') }}
                <p class="help-block">(required)</p>
                <div class="alert" id="name_alert">
                    <strong>Warning!</strong> Please enter your full name
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="username">登入名</label>
            <div class="controls">
                {{ text_field('username', 'class': 'input-xlarge') }}
                <p class="help-block">(required)(开发必须填svn的用户名,其他人可填拼音)</p>
                <div class="alert" id="username_alert">
                    <strong>Warning!</strong> Please enter your desired user name
                </div>
            </div>
        </div>
       <!-- <div class="control-group">
            <label class="control-label" for="email">邮箱</label>
            <div class="controls">
                {{ text_field('email', 'class': 'input-xlarge') }}
            </div>
        </div>-->
          <div class="control-group">
                    <label class="control-label" for="role">角色</label>
                    <div class="controls">
                    <?php  $roles =array('dev'=>'开发','test'=>'测试','oper'=>'运维','admin'=>'管理员');?>
                     {{ select("role", roles, 'using': ['id', 'name']) }}
                      <?php // echo Tag::selectStatic(array("role", array('dev'=>'开发','test'=>'测试','oper'=>'运维','admin'=>'管理员'), "useDummy" => true)); ?>
                    </div>
                </div>
        <div class="control-group">
            <label class="control-label" for="password">密码</label>
            <div class="controls">
                {{ password_field('password', 'class': 'input-xlarge') }}
                <p class="help-block">(minimum 8 characters)</p>
                <div class="alert" id="password_alert">
                    <strong>Warning!</strong> Please provide a valid password
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="repeatPassword">确认密码</label>
            <div class="controls">
                {{ password_field('repeatPassword', 'class': 'input-xlarge') }}
                <div class="alert" id="repeatPassword_alert">
                    <strong>Warning!</strong> The password does not match
                </div>
            </div>
        </div>
        <div class="form-actions">
            {{ submit_button('注册', 'class': 'btn btn-primary btn-large', 'onclick': 'return SignUp.validate();') }}
        </div>
    </fieldset>
</form>
