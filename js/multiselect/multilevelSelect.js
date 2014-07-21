/*  
**    ====================================
**    类名：CLASS_MULTISELECT 
**    功能：多级连动菜单  
**    作者：根据网上js修改而来  
**在模板中的使用方法
**---------select联动---------------
** 数据源
**var array=new Array();
** 数据格式 ID，父级ID，名称
**  array['0']=new Array(id,parent_id,name); 
**  ....
** ....
**
**这是调用代码
**var multiLevelSelect = new CLASS_MULTISELECT(array) //设置数据源
**multiLevelSelect.firstSelectChange("0","s1"); //设置第一个选择框
**multiLevelSelect.subSelectChange("s1","s2"); //设置子级选择框
**multiLevelSelect.subSelectChange("s2","s3");
**multiLevelSelect.subSelectChange("s3","s4");
---------seleclt联动---------------
**/  	
  function CLASS_MULTISELECT(array)
  {
   //数组，联动的数据源
  	this.array=array; 
  	this.indexName='';
  	this.obj='';
  	//设置子SELECT
	// 参数：当前onchange的SELECT ID，要设置的SELECT ID
    this.subSelectChange=function(selectName1,selectName2)
  	{
		var obj1=document.getElementById(selectName1);
		var obj2=document.getElementById(selectName2);
		
		var objName=this.toString();
		var me=this;
		obj1.onchange=function()
		{
			me.optionChange(this.options[this.selectedIndex].value,obj2.id)
			
			//取得后面的select，并去掉
			var nextobj = $(obj2).nextAll();
			var nextobjNum = $(nextobj).size();
			if(nextobjNum>0)
			{
				for(i=0;i<nextobjNum;i++)
				{
					nextobj[i].options.length=0;
					nextobj[i].options[0]=new Option("请选择",'');
				}
			}
		}
  	}
  	//设置第一个SELECT
	// 参数：indexName指选中项,selectName指select的ID
  	this.firstSelectChange=function(indexName,selectName)  
  	{
		this.obj=document.getElementById(selectName);
		this.indexName=indexName;
		this.optionChange(this.indexName,this.obj.id)
  	}
  // indexName指选中项,selectName指select的ID
  	this.optionChange=function (indexName,selectName)
  	{
		var obj1=document.getElementById(selectName);
		var me=this;
		obj1.length=0;
		obj1.options[0]=new Option("请选择",'');
		for(var i=0;i<this.array.length;i++)
		{	
			if(this.array[i][1]==indexName)
			{
			//alert(this.array[i][1]+" "+indexName);
				obj1.options[obj1.length]=new Option(this.array[i][2],this.array[i][0]);
			}
		}
  	}
	
	//初始化所有选项
	// 参数：indexName指选中项,selectName指select的ID
  	this.initOption=function(selectName,indexNameArr)  
  	{
		this.obj=document.getElementById(selectName);
		this.indexName=indexNameArr['0'];
		this.optionChange(this.indexName,this.obj.id);
		var nextobj = $(this.obj).nextAll();
		var nextobjNum = $(nextobj).size();
		if(nextobjNum>0)
		{
			for(i=0;i<nextobjNum;i++)
			{
				nobj=nextobj[i];
				nobj.indexName=indexNameArr[i];
				nobj.optionChange(nobj.indexName,nobj.id);
			}
		}
  	}
  }

function initMultiSelect(array,selectIdArr)
{
	 //var multiLevelSelect = new CLASS_MULTISELECT(array) //设置数据源
	 // multiLevelSelect.firstSelectChange("0","s1"); //设置第一个选择框
	  //multiLevelSelect.subSelectChange("s1","s2"); //设置子级选择框
	var multiLevelSelect = new CLASS_MULTISELECT(array) //设置数据源
	for ( var i=0 ; i < selectIdArr.length ; i++ ) 
	{ 
		if(i=='0')
		{
			multiLevelSelect.firstSelectChange("0",selectIdArr[i]); //设置选择框
		}
		else{
			multiLevelSelect.subSelectChange(selectIdArr[i-1],selectIdArr[i]); 
		}
	}
}

function initSelectData(selectIdArr,selectData)
{
	for ( var i=0 ; i < selectIdArr.length ; i++ ) 
	{ 
		$("#"+selectIdArr[i]).val(selectData[i]);

		if(i < selectIdArr.length -1)
		{
			$("#"+selectIdArr[i]).triggerHandler("change");	
		}
	}
}