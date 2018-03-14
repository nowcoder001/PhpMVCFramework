一个MVC框架， 这个框架是以前一个同事从前公司带过来的。 实现了MVC等大部分功能， 大部分都做的比较好， 但是模块划分等不是很友好， 当时实现了大概20个页面。然后一个类文件有快2000行逻辑。 比较混乱状态。 
这套框架的基础非常的好， 估计后来被人改错跟用错了。 我做了重新的设计和规划。
重构后我们用这套框架实现了多套系统，现在最多的系统有200个页面， 也能保持非常干净清晰， 有兴趣的同学可以研究。

具体的改进主要是：
1去掉多个页面对应一个action的做法， 改成一个页面对应一个action

class EmailAction extends Action 
{
	function EmailAction() {
		$this->session = Session::getInstance();
		$this->dao = Model::getDao();
		$this->param = new Param();
	}

	public function getList()
	{	
		//对应获得邮件列表ajax
	}
	
	public function sendEmail()
	{
		//对应发送邮件ajax
	}
	
	public function delete()
	{
		//对应删除邮件ajax
	}
}

按照 模块Action.php, 自动映射到模块_Index

Email_Index.html

2重新梳理和规划了一些目录结构。

3规范了代码结构和规范， 强制定义了页面的写法等。

![html页面预览](https://github.com/zhengguo07q/PhpMVCFramework/blob/master/docs/html%E9%A1%B5%E9%9D%A2%E9%A2%84%E8%A7%88.png)


![后台](https://github.com/zhengguo07q/PhpMVCFramework/blob/master/docs/%E5%90%8E%E5%8F%B0.png)

![目录结构预览](https://github.com/zhengguo07q/PhpMVCFramework/blob/master/docs/%E7%9B%AE%E5%BD%95%E7%BB%93%E6%9E%84%E9%A2%84%E8%A7%88.png)