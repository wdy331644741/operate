
    var fixInputMoney = function(obj,max_money)
    {
        var value = $(obj).val();
        if (value =="")
            return false;
        value = value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
        // value = value.replace(/^\./g,""); //验证第一个字符是数字而不是
        value = value.replace(/^\./g,""); //验证第一个字符是数字而不是
        if((/^0\d$/g).test(value)){
            value = value.replace(/\b(0+)/gi, "");
        }
        //
        value = value.replace(/\b/gi, "");
        value = value.replace(/\.{2,}/g,"."); //只保留第一个. 清除多余的
        value = value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
        value = value.replace(/^(\-)*(\d+)\.(\d\d\d\d).*$/,'$1$2.$3');
        if (Math.round(value*100)/100!=parseInt(value))
            value = Math.round(value*100)/100;
        if (parseFloat(value)>max_money)
            value = max_money;
        $(obj).val(value);
    }

function Arabia_to_Chinese(Num){
    for(i=Num.length-1;i>=0;i--)

    {
        Num = Num.replace(",","")//替换tomoney()中的“,”
        Num = Num.replace(" ","")//替换tomoney()中的空格
    }

    Num = Num.replace("￥","")//替换掉可能出现的￥字符


    if(isNaN(Num))

    { //验证输入的字符是否为数字
        alert("请检查小写金额是否正确");
        return;
    }
    //字符处理完毕后开始转换，采用前后两部分分别转换

    part = String(Num).split(".");
    newchar = "";
    //小数点前进行转化
    for(i=part[0].length-1;i>=0;i--)
    {
        if(part[0].length > 10)
        {
            alert("位数过大，无法计算");
            return "";
        }//若数量超过拾亿单位，提示

        tmpnewchar = ""
        perchar = part[0].charAt(i);
        switch(perchar)

            {
            case "0": tmpnewchar="零" + tmpnewchar ;break;
            case "1": tmpnewchar="壹" + tmpnewchar ;break;
            case "2": tmpnewchar="贰" + tmpnewchar ;break;
            case "3": tmpnewchar="叁" + tmpnewchar ;break;
            case "4": tmpnewchar="肆" + tmpnewchar ;break;
            case "5": tmpnewchar="伍" + tmpnewchar ;break;
            case "6": tmpnewchar="陆" + tmpnewchar ;break;
            case "7": tmpnewchar="柒" + tmpnewchar ;break;
            case "8": tmpnewchar="捌" + tmpnewchar ;break;
            case "9": tmpnewchar="玖" + tmpnewchar ;break;

            }
                switch(part[0].length-i-1)

                    {
                    case 0: tmpnewchar = tmpnewchar +"元" ;break;
                    case 1: if(perchar!=0)tmpnewchar= tmpnewchar +"拾" ;break;
                    case 2: if(perchar!=0)tmpnewchar= tmpnewchar +"佰" ;break;
                    case 3: if(perchar!=0)tmpnewchar= tmpnewchar +"仟" ;break;
                    case 4: tmpnewchar= tmpnewchar +"万" ;break;
                    case 5: if(perchar!=0)tmpnewchar= tmpnewchar +"拾" ;break;
                    case 6: if(perchar!=0)tmpnewchar= tmpnewchar +"佰" ;break;
                    case 7: if(perchar!=0)tmpnewchar= tmpnewchar +"仟" ;break;
                    case 8: tmpnewchar= tmpnewchar +"亿" ;break;
                    case 9: tmpnewchar= tmpnewchar +"拾" ;break;
                    }
                        newchar = tmpnewchar + newchar;

                }

                //小数点之后进行转化


                if(Num.indexOf(".")!=-1)

            {
                if(part[1].length > 2)

                {
                    alert("小数点之后只能保留两位,系统将自动截断");
                    part[1] = part[1].substr(0,2)
                }

                for(i=0;i<part[1].length;i++)
                {

                    tmpnewchar = ""
                    perchar = part[1].charAt(i)
                    switch(perchar)

                        {
                        case "0": tmpnewchar="零" + tmpnewchar ;break;
                        case "1": tmpnewchar="壹" + tmpnewchar ;break;
                        case "2": tmpnewchar="贰" + tmpnewchar ;break;
                        case "3": tmpnewchar="叁" + tmpnewchar ;break;
                        case "4": tmpnewchar="肆" + tmpnewchar ;break;
                        case "5": tmpnewchar="伍" + tmpnewchar ;break;
                        case "6": tmpnewchar="陆" + tmpnewchar ;break;
                        case "7": tmpnewchar="柒" + tmpnewchar ;break;
                        case "8": tmpnewchar="捌" + tmpnewchar ;break;
                        case "9": tmpnewchar="玖" + tmpnewchar ;break;
                        }

                            if(i==0)tmpnewchar =tmpnewchar + "角";
                            if(i==1)tmpnewchar = tmpnewchar + "分";
                            newchar = newchar + tmpnewchar;
                    }
                }
                //替换所有无用汉字
                // alert(newchar);
                while(newchar.search("零零") != -1)
                newchar = newchar.replace("零零", "零");
                newchar = newchar.replace("零亿", "亿");
                newchar = newchar.replace("亿万", "亿");
                newchar = newchar.replace("亿万", "亿");
                newchar = newchar.replace("亿万", "亿");
                newchar = newchar.replace("亿万", "亿");
                newchar = newchar.replace("零万", "万");
                newchar = newchar.replace("零元", "元");
                newchar = newchar.replace("零角", "");
                newchar = newchar.replace("零分", "");
                //again
                newchar = newchar.replace("零零", "零");
                newchar = newchar.replace("零亿", "亿");
                newchar = newchar.replace("亿万", "亿");
                newchar = newchar.replace("亿万", "亿");
                newchar = newchar.replace("亿万", "亿");
                newchar = newchar.replace("亿万", "亿");
                newchar = newchar.replace("零万", "万");
                newchar = newchar.replace("零元", "元");
                newchar = newchar.replace("零角", "");
                newchar = newchar.replace("零分", "");

                if (newchar.charAt(newchar.length-1) == "元" || newchar.charAt(newchar.length-1) == "角")
                newchar = newchar+"整"
                return newchar;
            }



//大小写转换
function NoToChinese(num) {
    if (!/^\d*(\.\d*)?$/.test(num)) { alert("Number is wrong!"); return "Number is wrong!"; }
    var AA = new Array("零", "壹", "贰", "叁", "肆", "伍", "陆", "柒", "捌", "玖");
    var BB = new Array("", "拾", "佰", "仟", "萬", "億", "点", "");
    var a = ("" + num).replace(/(^0*)/g, "").split("."), k = 0, re = "";
    for (var i = a[0].length - 1; i >= 0; i--) {
        switch (k) {
            case 0: re = BB[7] + re; break;
            case 4: if (!new RegExp("0{4}\\d{" + (a[0].length - i - 1) + "}$").test(a[0]))
                re = BB[4] + re; break;
            case 8: re = BB[5] + re; BB[7] = BB[5]; k = 0; break;
        }
        if (k % 4 == 2 && a[0].charAt(i + 2) != 0 && a[0].charAt(i + 1) == 0) re = AA[0] + re;
        if (a[0].charAt(i) != 0) re = AA[a[0].charAt(i)] + BB[k % 4] + re; k++;
    }

    if (a.length > 1) //加上小数部分(如果有小数部分)
    {
        re += BB[6];
        for (var i = 0; i < a[1].length; i++) re += AA[a[1].charAt(i)];
    }
    return re;
}

//减
function detail_decday(obj){

    var day = obj.parent().next().val();
    var newday = parseInt(day)-1;
    if(newday < 1){
        newday = 1;
    }
    var input = obj.parent().next();
    input.val(newday);
    $(input).trigger("input");

}

//加
function detail_addday(obj){

    var day = obj.parent().prev().val();
    if(!day){
        day = 1;
    }
    var newday = parseInt(day)+1;
    var input = obj.parent().prev();
    input.val(newday);
    $(input).trigger("input");

}

jQuery(function($) {

    //添加产品分类弹框
    $(".addtype").click(function(){

        $('#myModal').modal('toggle');

    })

    //添加分类
    $(".add_type").click(function(){

        var typename = $(".typename").val();
        if(!typename){
            alert("请填写分类名！");
        }else{

            $.ajax({
                type: "POST",
                data:{ "typename":typename},
                url: "category.php?action=add",
                dataType: "json",
                success: function(data) {

                    if(data.code == 0){

                        var html='<option value='+data.data+'>'+typename+'</option>';
                        $(".category_id").append(html);
                        $('#myModal').modal('hide');

                    }else{
                        alert(data.msg);
                    }
                },
                error:function(){
                    //alert("error");
                }
            });

        }

    })
    
    
    //添加银行弹框
    $(".addbank").click(function(){
    	
    	$('#addBankModal').modal('toggle');
    	
    })
    
    //添加银行
    $(".add_bank").click(function(){
    	var Bankname = $(".bankname").val();
    	if(!Bankname){
    		alert("请填写银行名称!");
    	}else{
    		
    		$.ajax({
    			type: "POST",
    			data:{ "bankname":Bankname},
    			url: "bank.php?action=add",
    			dataType: "json",
    			success: function(data) {
    				if(data.code == 0){
    					
    					var html='<option value='+data.data+'>'+Bankname+'</option>';
    					$("#bank_id").append(html);
    					$('#addBankModal').modal('hide');
    					
    				}else{
    					alert(data.msg);
    				}
    			},
    			error:function(){
    				//alert("error");
    			}
    		});
    		
    	}
    	
    })
    
    
    //添加银行弹框
    $(".addCompany").click(function(){
    	
    	$('#addCompanyModal').modal('toggle');
    	
    })
    
    //添加银行
    $(".add_company").click(function(){
    	var companyname = $(".companyname").val();
    	if(!companyname){
    		alert("请填写公司名称!");
    	}else{
    		
    		$.ajax({
    			type: "POST",
    			data:{ "companyname":companyname},
    			url: "company.php?action=add",
    			dataType: "json",
    			success: function(data) {
    				if(data.code == 0){
    					
    					var html='<option value='+data.data+'>'+companyname+'</option>';
    					$("#companyid").append(html);
    					$('#addCompanyModal').modal('hide');
    					
    				}else{
    					alert(data.msg);
    				}
    			},
    			error:function(){
    				//alert("error");
    			}
    		});
    		
    	}
    	
    })


    //支付方式
    $("[name='pay_method']").click(function(){

        var num =$(this).val();
        if(num == 1 || num == 2){
            $(".month").html("月");
        }else{
            $(".month").html("天");
        }

    })


    //金额转换
    $(".total_amount").keyup(function(){

        fixInputMoney(this,100000000);
        var total_amount = $(this).val();
        var total_amount2 = Arabia_to_Chinese(total_amount);
        $(".total_amount2").html(total_amount2);

    })

    $("#input_rate_end").keyup(function(){

        fixInputMoney(this,24);

    })
    $("[name=extra_rate]").bind('onclick input propertychange',function(){
        fixInputMoney(this,24);
    });


    $("[name=extra_rate_type]").keyup(function(){
        var str=$(this).val();
        str = str.substr(0,5);
        $(this).val(str);
    });


    //黄金标弹框
    $(".adddetail").click(function(){

        var child_id= new Array();
        $(".form-group").each(function() {
            child_id.push($(this).find(".child_id").val());
        });

        $.ajax({
            type: "POST",
            url: "prodetail.php?action=lists",
            data:{'child_id':child_id,'pay_method':pay_method,'period':period,'rate_end':rate_end},
            dataType: "json",
            success: function(data) {

                if(data.code == 0){

                    var html='<table class="table table-striped table-hover">'+
                        '<thead>'+
                        '<tr>'+
                            '<th class="row-selected row-selected"></th>'+
                                '<th colspan="1" rowspan="1">编号</th>'+
                                '<th >名称</th>'+
                                '<th >产品期限</th>'+
                                '<th >借款总额</th>'+
                                '<th >状态</th>'+
//                                '<th >是否符合</th>'+
                            '</tr>'+
                        '</thead>'+
                        '<tbody>';

                    var result = data.data;
                    for(var i in result){

                        html+='<tr id="tr_{$item.id}">'+
                            '<td><input class="ids project_id" type="checkbox" value="'+result[i]['id']+'"></td>'+
                            '<td>'+result[i]['id']+'</td>'+
                            '<td>'+result[i]['name']+'</td>'+
                            '<td>'+result[i]['period_day']+'天</td>'+
                            '<td>'+result[i]['total_amount']+'</td>'+
                            '<td>'+result[i]['status_name']+'</td>'+
//                            '<td>'+result[i]['is_whether']+'</td>'+
                        '</tr>';

                    }
                    html+='</tbody>'+
                        '</table>';

                    $(".content").html(html);
                    $('#myModal').modal('toggle');

                }else{
                    alert(data.msg);
                }
            },
            error:function(){
                //alert("error");
            }
        });

    })

    //选择散标
    $(".add_detail").click(function(){

        var obj = $(this);
        //禁止重复点击
        obj.attr("disabled","disabled");
        var parent_id = $('[name="project_id"]').val();    //产品id
        var detail_arr = new Array();    //所选子标id数组
        var total_amount = 0;            //所选子标总额
        $(".project_id:checked").each(function() {
            detail_arr.push($(this).val());
            total_amount += parseFloat($(this).parent().parent().find("td:eq(4)").html());
        });

        if(parent_id && detail_arr && total_amount > 0){

            if(total_amount%1000 != 0){

                alert("月利宝总额必须是1000的整数倍！");
                obj.removeAttr("disabled");
                return false;
            }

            $.ajax({
                type: "POST",
                data:{ "project_id":parent_id,"detail_arr":detail_arr,"total_amount":total_amount},
                url: "prodetail.php?action=add2",
                dataType: "json",
                success: function(data) {
                    if(data.code == 0){

                        window.location.reload();
                    }else{
                        alert(data.msg);
                    }
                },
                error:function(){
                    //alert("error");
                }
            });
        }else{
            obj.removeAttr("disabled");
            alert("请选择子标！");
            return false;
        }

    })


    //未保存删除
//    $("div").on("click",".detail_del1",function(){
//
//        var surplus = $(".surplus").html();                 //总金额
//        var amount = $(this).parent().prev().html();        //该条金额
//        surplus = parseFloat(surplus) - parseFloat(amount);
//        $(".surplus").html(surplus);
//        $(".total_amount").val(surplus);
//        $('[name="surplus"]').val(surplus);
//        $(this).parent().parent().remove();
//
//    })

    //以保存删除
    $(".detail_del2").click(function(){

        if(confirm("确定要删除该条数据吗？") == true){

            var obj = $(this);
            var prodetail_id = obj.parent().parent().find(".prodetail_id").val();
            var child_id = obj.parent().parent().find(".child_id").val();    //月利宝子标id
            var project_id = $('[name="project_id"]').val();
            var surplus = $(".surplus").html();                 //总金额
            var amount = $(this).parent().prev().prev().html();        //该条金额
            surplus = parseFloat(surplus)-parseFloat(amount);
            $.ajax({
                type: "POST",
                data:{ "prodetail_id":prodetail_id,"child_id":child_id,"total_amount":surplus,"project_id":project_id},
                url: "prodetail.php?action=del",
                dataType: "json",
                success: function(data) {

                    if(data.code == 0){

                        $(".surplus").html(surplus);
                        $(".total_amount").val(surplus);
                        $('[name="surplus"]').val(surplus);
                        obj.parent().parent().remove();

                    }else{
                        alert(data.msg);
                    }
                },
                error:function(){
                    //alert("error");
                }
            });
        }
    })


    //担保细节增加
    $(".add_title").click(function(){

        var num = $("#form04").find(".form-group:last").find("i").html();
        if(num == 5){
            alert("最多添加五条！！！");
            return false;
        }else{
            num = parseInt(num) + 1;
            var html = '';
            html = '<div class="form-group">'+
                '<label class="col-sm-2 control-label">标题<i>'+num+'</i>:</label>'+
                '<div class="col-sm-7">'+
                '<input type="text" class="form-control" name="title[]" value="" placeholder="">'+
                '</div>'+
                '</div>'+
                '<div class="form-group">'+
                '<label class="col-sm-2 control-label">内容<i>'+num+'</i>:</label>'+
                '<div class="col-sm-7">'+
                '<textarea class="form-control col-sm-6" rows="3" name="content[]" id="inputTextarea"></textarea>'+
                '</div>'+
                '</div>';
            $("#form04").append(html);
        }
    })

})
//审核
function examine(project_id,type,total_amount){

    if(type == '0'){
        var r = confirm("您确定取消审核吗？");
    }
//    else if(type == '31'){
//        var r = confirm("您确定该标打款吗？");
//    }
// else if(type == '6'){
//        var r = confirm("您确定该标生成还款计划吗？");
//    }
    else{
        var r = confirm("您确定该标审核通过吗？");
    }
    if(r == true){

        if(total_amount <= 0){
            alert("请先添加月利宝子标！！！");
            return false;
        }
        $.ajax({
            type: "POST",
            data:{ "project_id":project_id,"type":type},
            url: "project_index.php?action=examine",
            dataType: "json",
            success: function(data) {

                if(data.code == 0){
                    if(type == '4'){
                        window.location.href="project_index.php?action=index";
                    }else{
                        window.location.reload();
                    }
                }else{
                    alert(data.msg);
                }
            },
            error:function(){
                //alert("error");
            }
        });
    }
}