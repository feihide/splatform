
var Profile = {
    check: function (id) {
        if ($.trim($("#" + id)[0].value) == '') {
            $("#" + id)[0].focus();
            $("#" + id + "_alert").show();

            return false;
        };

        return true;
    },
    validate: function () {
        if (SignUp.check("name") == false) {
            return false;
        }
        if (SignUp.check("email") == false) {
            return false;
        }
        $("#profileForm")[0].submit();
    }
};

var SignUp = {
    check: function (id) {
        if ($.trim($("#" + id)[0].value) == '') {
            $("#" + id)[0].focus();
            $("#" + id + "_alert").show();

            return false;
        };

        return true;
    },
    validate: function () {
        if (SignUp.check("name") == false) {
            return false;
        }
        if (SignUp.check("username") == false) {
            return false;
        }
        if (SignUp.check("email") == false) {
            return false;
        }
        if (SignUp.check("password") == false) {
            return false;
        }
        if ($("#password")[0].value != $("#repeatPassword")[0].value) {
            $("#repeatPassword")[0].focus();
            $("#repeatPassword_alert").show();

            return false;
        }
        $("#registerForm")[0].submit();
    }
}

var Apply = {
    check1: function (id) {
        if ($.trim($("#" + id)[0].value) == '') {
            $("#" + id)[0].focus();
            alert('内容不能为空');

            return false;
        };

        return true;
    },

    check2: function (id) {
        var arr=[];
        $('input:checkbox:checked').each(function() {
            arr.push($(this).val());
        });
        if(arr.length==0){
            alert('请选择需要提交的文件');
            return false;
        }
        return true;
    },


    validate: function () {
        if ( Apply.check1("content") == false) {
            return false;
        }
        if (Apply.check2("repo") == false) {
            return false;
        }
        $("#applyForm")[0].submit();
    }
}


$(document).ready(function () {
    $("#registerForm .alert").hide();
    $("div.profile .alert").hide();
});
