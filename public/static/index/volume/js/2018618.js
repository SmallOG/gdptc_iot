var m_ParaA;
var m_ParaB;
var m_ParaC;
var m_ParaD;

//读取URL传参
function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var para = window.location.search;
    para = para.replace(/amp;/g, "");
    var r = para.substr(1).match(reg);  //匹配目标参数
    if (r != null) return r[2]; return ""; //返回参数值
}

$(document).ready(function () {
    $("#divToken").hide();
    $("#divZhezhao").hide();

    m_ParaA = getUrlParam("a");
    m_ParaB = getUrlParam("b");
    m_ParaC = getUrlParam("c");
    m_ParaD = getUrlParam("d");

    if (typeof (m_ParaA) == "undefined" || m_ParaA == null || m_ParaA.length == 0
        || typeof (m_ParaB) == "undefined" || m_ParaB == null || m_ParaB.length == 0
        || typeof (m_ParaC) == "undefined" || m_ParaC == null || m_ParaC.length == 0
        || typeof (m_ParaD) == "undefined" || m_ParaD == null || m_ParaD.length == 0) {
        $("#divContent").html("页面已过期，请重新回复机器人【商城】获取！");
        return;
    }

});

function GetToken(cuts) {
    if (cuts.length == 0)
        return;
    $("#divToken").show();
    $("#divZhezhao").show();
    $("#spCopyMark").html("123");
    $.ajax({
        type: "POST",
        url: "../Service/ProductFun.aspx?action=make1212",
        data: ($.toJSON({
            a: m_ParaA, b: m_ParaB, c: m_ParaC, d: m_ParaD, type: cuts
        })),
        datatype: "json",
        async: true,
        error: function () { },
        success: function (data) {
            data = $.parseJSON(data);
            if (data.success) {
                if (data.token.length == 0) {
                    if (data.link.length > 0) {
                        window.location.href = data.link;
                    }
                } else {
                    var objSpMark = $("#spTokenMark");
                    objSpMark.attr("data-clipboard-text", data.token);
                    $("#btnGetToken").hide();
                    copyCode("spTokenMark");

                    var objSpCopyMark = $("#spCopyMark");
                    objSpCopyMark.html("☞ 按住-复制本条(全部)文本内容，再打开手机淘宝APP即可进入会场" + data.token);

                    $("#divToken").show();
                    $("#divZhezhao").show();
                }
            }
            else {
                //错误信息
                var objSpCopyMark = $("#spCopyMark");
                objSpCopyMark.html("获取口令失败！");

                $("#divToken").show();
                $("#divZhezhao").show();
            }
        }
    });
}

function copyCode(code) {
    var clipboard = new Clipboard('#' + code);
    clipboard.on('success', function (e) {
        e.clearSelection();
        swal({
            title: '复制成功！',
            
            fontSize: '10em',
            confirmButtonText: '知道了'
        });
    });

    clipboard.on('error', function (e) {
        swal({
            title: '复制失败',
            text: "点击下方返回按钮手动按住复制口令内容再打开淘宝即可领券",
            type: "error",
            fontSize: '10em',
            confirmButtonText: '返回'
        });
    });
}

function CloseToken() {
    $("#divToken").hide();
    $("#divZhezhao").hide();
}